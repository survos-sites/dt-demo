<?php

namespace App\Service;

use Sentry\HttpClient\HttpClient;
use Survos\CoreBundle\Service\SurvosUtils;
use Survos\JsonlBundle\Event\JsonlConvertStartedEvent;
use Survos\JsonlBundle\Event\JsonlRecordEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Symfony\Component\String\u;
use Survos\ImportBundle\Event\ImportConvertRowEvent;
class EnhanceRecordService
{
    private $seen = [];
    public function __construct(
        private SluggerInterface $asciiSlugger,
        private HttpClientInterface $httpClient,
    ) { }

    #[AsEventListener(event: JsonlConvertStartedEvent::class)]
    final public function convertStarted(JsonlConvertStartedEvent $event): void
    {

    }
    #[AsEventListener(event: ImportConvertRowEvent::class)]
    final public function tweakRecord(ImportConvertRowEvent $event): void
    {
        $record = $event->row;
        $record = SurvosUtils::removeNullsAndEmptyArrays($record);
//        foreach ($event->tags as $tag)
        {
            switch ($event->dataset) {
                case 'wcma':
                    $id = $record['id'];
                    if (in_array($id, $this->seen)) {
                        $record = null;
                        dump($id . ' already seen');
                    } else {
                        $record['citation_url'] =
                            sprintf('https://egallery.williams.edu/objects/%d', (int) $id);
                        $this->seen[] = $id;
                        $id = (int)$record['id'];
                        $record['id'] = $id; // make int
                        $manifest = sprintf('https://egallery.williams.edu/apis/iiif/presentation/v2/1-objects-%d/manifest', $record['id']);
                        $record['manifest'] = $manifest;
                    }
                    break;
                case 'amst_en':
                case 'amst_nl':
                    // bad: https://statics.belowthesurface.amsterdam/vondst/600/NZC1.00001MTL001(01).png
                    // YES: https://statics.belowthesurface.amsterdam/vondst/600/NZD1.00048FAU017-02(01).png
//                    https://statics.belowthesurface.amsterdam/vondst/600/NZC1.00001MTL001(01).png
//                    https://statics.belowthesurface.amsterdam/vondst/600/NZD1.00048FAU017-02(01).png
                    $code = $record['vondstnummer']; // with the .
                    if ($record['website']) {
                        $image = $record['image'] = sprintf('https://statics.belowthesurface.amsterdam/vondst/600/%s(01).png',
                            $code);
//                        $response = $this->httpClient->request('GET', $image);
//                        if ($response->getStatusCode() <> 200) {
//                            dd($response, $image);
//                        } else {
//                            dump($image);
//                        }
//                        dd($response->getStatusCode());
//                        dd(array_keys($record));
                    }
                    $record['citation_url'] = sprintf('https://belowthesurface.amsterdam/nl/vondst/%s', $code);
//                    dd($record['citation_url']);
                    $code = str_replace('.', '-', $code);
                    if (in_array($code, $this->seen)) {
                        $event->row = null;
                        dump($code . ' already seen', $event);
                        return;
                    }
                    $this->seen[] = $code;
                    $record['code'] = $code;
//                    $record['vondstnummer'] = $code;
//                    dump($record);
//                    dump('https://statics.belowthesurface.amsterdam/vondst/600/NZD1.00048FAU017-02(01).png');
//                    dd($record['image'], $record['citation_url']);
                    break;

                    case 'wine':
                    $code = $this->asciiSlugger->slug(join('-', [$record['name'], $record['year'], $record['domain']]))->toString() . "-" . $event->index;
                    if (in_array($code, $this->seen)) {
                        $event->row = null;
                        dump($code . ' already seen', $event);
                        return;
                    }
                    $this->seen[] = $code;

                    $record['code'] = $code;

                    $this->saveBase64Image($record['image'], "public/images/uploads/$code.jpg");
                    $record['image'] = "/images/uploads/$code.jpg"; // url

                    break;
                case 'marvel':
                    $code = $this->asciiSlugger->slug($record['name'])->toString();
                    $record['code'] = $code;
                    if (in_array($code, $this->seen)) {
                        $record = null;
                        dump($code . ' already seen');
                    }
                    $this->seen[] = $code;
                    break;
                case 'car':
                    $record['id'] = $event->index+1;
                    break;
                case 'wam':
                    $code = $record['registrationNumber'];
                    if (in_array($code, $this->seen)) {
                        $record = null;
                        dump($code . ' already seen');
                    }
                    $this->seen[] = $code;
                    break;
            }

        }

        $event->row = $record;
    }

    // More complete example with validation
    function saveBase64Image(string $base64String, string $outputPath): bool
    {
        $dir = dirname($outputPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        // Remove the data URI prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $imageType = $matches[1];
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }

        // Decode the base64 string
        $imageData = base64_decode($base64String, strict: true);

        if ($imageData === false) {
            return false; // Invalid base64
        }

        // Save to file
        return file_put_contents($outputPath, $imageData) !== false;
    }
}
