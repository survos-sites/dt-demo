<?php

namespace App\Command;

use App\Entity\Jeopardy;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Castor\Attribute\AsSymfonyTask;

#[AsCommand('app:jeopardy', 'Import 200k jeopardy questions')]
#[AsSymfonyTask('jeopardy')]
class JeopardyCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }


    public function __invoke(
        SymfonyStyle                                           $io,
        #[Argument('path or url to json file')]
        string                                                 $filename = 'data/jeopardy.tsv',
        #[Option('limit the number of records imported')] ?int $limit = null,
        #[Option('dump the nth record')] ?int                  $dump = null,
        #[Option('batch size for flush')] int                  $batch = 1000,
        #[Option('purge the table first')] ?bool               $reset = null,
    ): int
    {
        $url = 'https://github.com/jwolle1/jeopardy_clue_dataset/raw/refs/heads/main/combined_season1-40.tsv';
        if (!file_exists($filename)) {
            $io->writeln("Fetching " . $filename);
            file_put_contents($filename, file_get_contents($url));
        }
        // @todo: don't always reset.
        $this->entityManager->getRepository(Jeopardy::class)->createQueryBuilder('jeopardy')->delete();
        if ($reset) {
        }

        $csv = Reader::createFromPath($filename, 'r');
        $csv
            ->setDelimiter("\t")
            ->setHeaderOffset(0);

        $header = $csv->getHeader(); //returns the CSV header record
        $mapper = new ObjectMapper();
        $progressBar = new ProgressBar($io, 515890);

        foreach ($csv->getRecords() as $idx => $record) {
            $progressBar->advance();
            if ($dump && $idx === $dump) {
                dd($record);
            }
            $answer = $record['answer'];
            if (empty($answer)) {
                continue;
            }
//            if (strlen($answer) < strlen($q=$record['question']) ) {
//                $io->warning($answer . " < " . $q);
//            }
            $record = (object)$record;
//            if (str_contains($answer, $record['question'])) {}
            $entity = $mapper->map($record, Jeopardy::class);

            $this->entityManager->persist($entity);
            if (($progressBar->getProgress() % ($batch)) === 0)
            {
                try {
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    dd($record, $idx, $e->getMessage());
                }
                $this->entityManager->clear();
            }

            if ($limit && ($progressBar->getProgress() >= $limit)) {
                dump($limit);
                break;
            }
        }
        $this->entityManager->flush();
        $progressBar->finish();


        $io->success(self::class . " success.");
        return Command::SUCCESS;
    }
}
