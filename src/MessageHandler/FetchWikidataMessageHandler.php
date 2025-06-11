<?php

namespace App\MessageHandler;

use App\Entity\Official;
use App\Message\FetchWikidataMessage;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Survos\ImageClientBundle\Service\ImageClientService;
use Survos\SaisBundle\Model\ProcessPayload;
use Survos\SaisBundle\Service\SaisClientService;
use Survos\WikiBundle\Service\WikiService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Illuminate\Support\Collection;
use Wikidata\Value;

final class FetchWikidataMessageHandler
{

    public function __construct(
        private WikiService                                $wikiService,
        private EntityManagerInterface                     $entityManager,
        private SaisClientService                          $imageClientService,
        private FilesystemOperator                         $defaultStorage,
        private LoggerInterface                            $logger,
        private UrlGeneratorInterface                      $urlGenerator, // could be moved to somewhere else and inject the callback here.
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
        private readonly SaisClientService $saisClientService,
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(FetchWikidataMessage $message)
    {
        $filesystem = $this->defaultStorage;
        $wikidataId = $message->getWikidataId();
        $this->wikiService->setCacheTimeout(60 * 60 * 24);
        $wikiData = $this->wikiService->fetchWikidataPage($wikidataId);
        $official = $this->entityManager->getRepository(Official::class)->findOneBy(['wikidataId' => $wikidataId]);

//        dd($wikiData->properties->has('P18'), $wikiData->properties);
        if ($wikiData->properties->has('P18')) {
            $p18 = $wikiData->properties['P18'];
            /** @var Collection $values */
            $values = $p18->values;
//        dump($p18, $values->getIterator());
            /** @var Value $item */
            $images = [];
            foreach ($values->getIterator() as $item) {
                // we could do this in an async message, too.
                $url = $item->id;
                // trigger the download.  we could batch this, too.
                $response = $this->imageClientService->dispatchProcess(
                    new ProcessPayload(
                        'dt-demo', // hack! ROOT
                        [$url],
                        ['small','medium','large'],
                        mediaCallbackUrl: $this->urlGenerator->generate('app_webhook', [
                            'id' => $official->getId(),
                        ], $this->urlGenerator::ABSOLUTE_URL)
                    )
                );
                // we won't get a callback if it's already loaded, so we need to load the images that already exist.
                $imageCodes = $official->getImageCodes()??[];
                foreach ($response as $responseImage) {
                    if ($responseImage['path']??false) {
                        $imageCodes[$responseImage['path']] = $responseImage['resized'];
                    } else {
                        // queued, @todo: configure callback
//                        dd($response);
                    }
                }
                $official->setImageCodes($imageCodes);
                $this->entityManager->flush();
            }
        }


//        $official->setWikiData($wikiData);
        $this->entityManager->flush();

        return $wikiData;

        // set results for the message monitor?
    }
}
