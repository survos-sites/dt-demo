<?php

namespace App\Command;

use App\Entity\Jeopardy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapper;

#[AsCommand('app:jeopardy', 'Import 200k jeopardy questions')]
class JeopardyCommand
{
	public function __construct(
        private EntityManagerInterface $entityManager,
    )
	{
	}


	public function __invoke(
		SymfonyStyle $io,
		#[Argument('path or url to json file')]
		string $filename = 'data/jeopardy.json',
		#[Option('limit the number of records imported')] ?int $limit = null,
		#[Option('batch size for flush')] int $batch = 10000,
		#[Option('purge the table first')] ?bool $reset = null,
	): int
	{
        // @todo: don't always reset.
            $this->entityManager->getRepository(Jeopardy::class)->createQueryBuilder('jeopardy')->delete();
        if ($reset) {
        }

        $mapper = new ObjectMapper();
//        $filename = 'data/jeopardy.json';
//        $filename = 'data/sample.json';
        // $product = new Product();
        // $manager->persist($product);
        $data = json_decode(file_get_contents($filename));
        $progressBar = new ProgressBar($io, count($data));
        foreach ($data as $idx => $value) {
            $progressBar->advance();
            $entity = $mapper->map($value, Jeopardy::class);
            $this->entityManager->persist($entity);
////            }
            if ( $progressBar->getProgress() % ($batch-1) === 0) {
                try {
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    dd($value, $idx, $e->getMessage());
                }
                if ($io->isVerbose()) {
                    dump($entity, idx: $idx);
                }
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $progressBar->finish();

		$io->success(self::class . " success.");
		return Command::SUCCESS;
	}
}
