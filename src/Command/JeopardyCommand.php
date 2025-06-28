<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand('app:jeopardy', 'Import 200k jeopardy questions')]
class JeopardyCommand
{
	public function __construct()
	{
	}


	public function __invoke(
		SymfonyStyle $io,
		#[Argument('path or url to json file')]
		string $filename = 'data/jeopardy.json',
		#[Option('limit the number of records imported')]
		?int $limit = null,
	): int
	{
        foreach (json_decode(file_get_contents($filename), true) as $key => $value) {
            dd($key, $value);
        }
		if ($filename) {
		    $io->writeln("Argument filename: $filename");
		}
		if ($limit) {
		    $io->writeln("Option limit: $limit");
		}
		$io->success(self::class . " success.");
		return Command::SUCCESS;
	}
}
