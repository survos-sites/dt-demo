<?php

namespace App\Command;

use App\Entity\Official;
use App\Entity\Term;
use App\Message\FetchWikidataMessage;
use App\Repository\OfficialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Survos\SaisBundle\Model\AccountSetup;
use Survos\SaisBundle\Service\SaisClientService;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCommand('init:congress', 'Load the congressional data')]
final class AppLoadDataCommand
{
    public const SAIS_CLIENT='dtDemo_Officials';

    public function __construct(
        private readonly ValidatorInterface  $validator,
        private readonly CacheInterface      $cache,
        private readonly MessageBusInterface $bus,
        private EntityManagerInterface       $manager,
        private SerializerInterface          $serializer,
        private OfficialRepository           $officialRepository,
        private readonly SaisClientService $saisClientService,
        ?string                              $name = null)
    {
//        parent::__construct($name);
    }

    public function __invoke(
        SymfonyStyle   $io,
        #[Option(description: 'reload the json even if already in the cache')] bool $refresh = false,
        #[Option(description: 'max records to load')] int $limit=0,
        #[Option(description: 'purge database first')] bool $purge=false,
        #[Option(description: 'dispatch request for details')] bool $details=false,
        #[Option(description: 'url to json')] string $url='https://unitedstates.github.io/congress-legislators/legislators-current.json',
    ): int
    {
        if ($purge) {
            $count = $this->officialRepository->createQueryBuilder('o')
                ->delete()
                ->getQuery()
                ->execute();
            if ($count) {
                $io->success("$count records deleted");
            }
        }

        $io->info("fetching data..." . $url);
        $json = $this->cache->get(md5($url), fn(CacheItem $cacheItem) => (string)file_get_contents($url));
//        dd($json);

        $slugger = new AsciiSlugger();
        $ids = []; // save for dispatching detail messages until after flush()
        $congressData = json_decode($json);
        $progressBar = new ProgressBar($io, count($congressData));
        foreach ($congressData as $idx => $record) {
            $progressBar->advance();
//
//            $record['ids'] = $record['id'];
//            $record['id'] = $record['id']['wikidata'];
//            $terms = $record['terms'];
//            unset($record['terms']);
//            $serializer->denormalize($record, Official::class);
//
//            $official = $serializer->denormalize(
//                $record,
//                Official::class
//            );
//            dd($official);
//
//
//
//            $normalizers = [new ObjectNormalizer()];
//            $serializer = new Serializer($normalizers, []);
            $name = $record->name; // an object with name parts
            $bio = $record->bio; // a bio with gender, etc.
            $id = $record->id->wikidata;
            if (!$official = $this->officialRepository->findOneBy(['wikidataId' => $id])) {
                $official = (new Official($id))
                    ->setWikidataId($id);
            }

            $manager = $this->manager;

            $official
//                ->setBirthday(new \DateTimeImmutable($bio->birthday))
                ->setGender($bio->gender)
                ->setFirstName($name->first)
                ->setLastName($name->last)
                ->setOfficialName($officialName = $name->official_full ?? "$name->first $name->last")
                ->setCode($slugger->slug($officialName))
            ;
            $this->manager->persist($official);

            foreach ($record->terms as $t) {
                $term = (new Term())
                    ->setType($t->type)
                    ->setStateAbbreviation($t->state)
                    ->setParty($t->party ?? null)
                    ->setDistrict($t->district ?? null)
                    ->setStartDate(new \DateTimeImmutable($t->start))
                    ->setEndDate(new \DateTimeImmutable($t->end));
                $official
                    ->setDistrict($term->getDistrict())
                    ->setState($term->getStateAbbreviation())
                    ->setHouse($term->getType())
                    ->setCurrentParty($term->getParty());
                $manager->persist($term);
                $official->addTerm($term);
                $errors = $this->validator->validate($term);
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->io()->error($error->getMessage() . ':' .  $error->getInvalidValue());
                        break;
                    }
                }
            }

            $ids[] = $official->getWikidataId();

            if ($limit && ($progressBar->getProgress() >= $limit)) {
                break;
            }


        }
        $manager->flush();
        $progressBar->finish();

            // moved to OfficialWorkflow
//        if ($details) {
//            $progressBar = new ProgressBar($io, count($ids));
//                foreach ($ids as $id) {
//                $progressBar->advance();
//                $this->bus->dispatch(new FetchWikidataMessage($id));
//            }
//            $progressBar->finish();
//        }

        $io->success(self::class . ' ' . sprintf('app:load-data success, %s records processed', count($ids)));
        return Command::SUCCESS;
    }
}
