<?php

namespace App\MessageHandler;

use App\Entity\Official;
use App\Message\FetchWikidataMessage;
use Doctrine\ORM\EntityManagerInterface;
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
        private WikiService $wikiService,
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
        private FilesystemOperator $defaultStorage,
        private LoggerInterface $logger,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
    )
    {
    }

    public function __invoke(FetchWikidataMessage $message)
    {
        $wikidataId = $message->getWikidataId();
        $wikiData = $this->wikiService->fetchWikidataPage($wikidataId);
        $official = $this->entityManager->getRepository(Official::class)->findOneBy(['wikidataId' => $wikidataId]);
        $p18 = $wikiData->properties['P18'];
        /** @var Collection $values */
        $values = $p18->values;
//        dump($p18, $values->getIterator());
        /** @var Value $item */
        $images = [];
        foreach ($values->getIterator() as $item) {
            // we could do this in an async message, too.
            $url = $item->id;
//            $downloadedFileLocation = sprintf('%s/public/images/%s/', $this->projectDir, $wikidataId);
//            // this will fail on production, unless /public/images is linked to persistent storage
//            if (!is_dir($downloadedFileLocation)) {
//                try {
//                    mkdir($downloadedFileLocation, recursive: true);
//                } catch (\Exception $exception) {
//                    dd($downloadedFileLocation, $exception);
//                }
//            }
            $code = md5($url) . '.' . pathinfo($url, PATHINFO_EXTENSION);
//            $path = $downloadedFileLocation . $code;
            if (!$this->defaultStorage->has($code)) {
                $this->logger->warning("Fetching $url");
                $request = $this->httpClient->request('GET', $url, [
//                    'User-Agent' => 'SurvosBot/0.1 (tacman@gmail.com) generic-library/0.0'
                ]);
                if ($request->getStatusCode() == 200) {
                    $this->defaultStorage->write($code, $request->getContent());

//                    file_put_contents($path, $request->getContent());
                } else {
                    dd($request);
                }
            } else {
                $this->logger->warning("$code already exists");
            }
            $images[] = $code;

            // create thumbnail
        }


//        $official->setWikiData($wikiData);
        $official->setImageCodes($images);
        $this->entityManager->flush();

        return $wikiData;

        // set results for the message monitor?
    }
}
