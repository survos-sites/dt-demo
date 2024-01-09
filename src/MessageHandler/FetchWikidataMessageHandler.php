<?php

namespace App\MessageHandler;

use App\Entity\Official;
use App\Message\FetchWikidataMessage;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Survos\WikiBundle\Service\WikiService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tightenco\Collect\Support\Collection;
use Wikidata\Value;

#[AsMessageHandler]
final class FetchWikidataMessageHandler
{

    public function __construct(
        private WikiService                                $wikiService,
        private EntityManagerInterface                     $entityManager,
        private HttpClientInterface                        $httpClient,
        private FilesystemOperator                         $defaultStorage,
        private LoggerInterface                            $logger,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
    )
    {
    }

    public function __invoke(FetchWikidataMessage $message)
    {
        $filesystem = $this->defaultStorage;
        $client = $this->httpClient;
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
                $code = sprintf("%s/%s.%s", $wikidataId, md5($url), pathinfo($url, PATHINFO_EXTENSION));
                if (!$filesystem->has($code)) {
                    $this->logger->info("Fetching image $url");
                    $response = $this->httpClient->request('GET', $url, []);
                    // https://symfony.com/doc/current/http_client.html#streaming-responses
                    if ($response->getStatusCode() == 200) {
                        $filesystem->writeStream($code, $response->toStream());
                    } else {
                        dd($response);
                    }

                    try {
                        $exif = exif_read_data($response->toStream());
                        if ($exif) {
                            $this->logger->info("Orientation : " . ($exif['Orientation']??'--'));
                        }
                    } catch (\Exception $exception) {
                        // we probably need to write a temp file and read exif there
                        $this->logger->error("Unable to get exif: " . $url);
                    }

                } else {
                    $this->logger->info("$code already exists");
                }
//                $filesystem->fileExists($path);
                $meta = $filesystem->fileExists($code);
                /** @var  Filesystem $filesystem */

//                dd($meta,
//                    exif_read_data($filesystem->readStream($code)),
//                    $filesystem::class, $filesystem->mimeType($code));
                $images[] = [
                    'code' => $code,
                    'url' => $url
                ];

                // create thumbnail
            }
            $official->setImageCodes($images);

        }


//        $official->setWikiData($wikiData);
        $this->entityManager->flush();

        return $wikiData;

        // set results for the message monitor?
    }
}
