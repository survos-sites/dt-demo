<?php

namespace App\Command;

use App\Entity\Official;
use App\Entity\Term;
use App\Repository\OfficialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Zenstruck\Console\Attribute\Option;
use Zenstruck\Console\ConfigureWithAttributes;
use Zenstruck\Console\InvokableServiceCommand;
use Zenstruck\Console\IO;
use Zenstruck\Console\RunsCommands;
use Zenstruck\Console\RunsProcesses;

#[AsCommand('app:load-detail-data', 'Load the basic congressional data')]
final class AppLoadDetailDataCommand extends InvokableServiceCommand
{
    use RunsCommands;
    use RunsProcesses;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CacheInterface     $cache,
        string                     $name = null)
    {
        parent::__construct($name);
    }

    public function __invoke(
        IO                                                                          $io,
        EntityManagerInterface                                                      $manager,
        SerializerInterface                                                         $serializer,
        OfficialRepository                                                          $officialRepository,
        #[Option(description: 'reload the json even if already in the cache')] bool $refresh = false,
        #[Option(description: 'details and images')] bool                           $details = false,
        #[Option(description: 'max records to load')] int                           $limit = 0,
    ): void
    {
        foreach ($officialRepository->findAll() as $official) {
            // we could use messenger for this, but easier to just embed everything here.
            dd($official->getWikidataId());
        }
    }
}
