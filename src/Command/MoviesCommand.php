<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCommand('app:movies', 'Load 10K tmdb database')]
class MoviesCommand
{
	public function __construct(
    )
	{
	}


	public function __invoke(
		SymfonyStyle $io,
		#[Argument('the url to the csv data')]
		string $url = 'https://raw.githubusercontent.com/sanjeevai/Investigate_a_dataset/master/tmdb-movies.csv',
		#[Option('purge the data first')]
		?bool $reset = null,
	): int
	{
        // 6M, otherwise we'd use HttpClient, etc.
        if (!file_exists('data/movies.csv')) {
            file_put_contents('data/movies.csv', file_get_contents($url));
        }



		if ($url) {
		    $io->writeln("Argument url: $url");
		}
		if ($reset) {
		    $io->writeln("Option reset: $reset");
		}
		$io->success(self::class . " success.");
		return Command::SUCCESS;
	}
}
