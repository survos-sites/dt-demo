<?php declare(strict_types=1);

// File: src/Enhance/WcmaIiifEnhancementListener.php
// App Enhancement v0.4
// This iteration: listen to EnhanceRecordEvent and enrich WCMA records with citation + IIIF URLs.

namespace App\Enhance;

use Survos\JsonlBundle\Event\EnhanceRecordEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsEventListener(event: EnhanceRecordEvent::class)]
final class WcmaIiifEnhancementListener
{
    private const DATASET = 'wcma';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {}

    public function __invoke(EnhanceRecordEvent $event): void
    {
        if ($event->dataset !== self::DATASET) {
            return;
        }

        $record = $event->record;

        $id = isset($record['id']) && $record['id'] !== '' ? (int) $record['id'] : null;
        if (! $id) {
            return;
        }

        $citationUrl = "https://egallery.williams.edu/objects/$id";
        $manifestUrl = "https://egallery.williams.edu/apis/iiif/presentation/v2/1-objects-$id/manifest";

        $record['citation_url']      = $citationUrl;
        $record['iiif_manifest_url'] = $manifestUrl;

        try {
            $data = $this->httpClient->request('GET', $manifestUrl)->toArray();

            $thumbnailUrl = $data['thumbnail'][0]['@id'] ?? null;
            $imageUrl     = $data['sequences'][0]['canvases'][0]['images'][0]['resource']['@id'] ?? null;

            if (\is_string($thumbnailUrl)) {
                $record['iiif_thumbnail_url'] = $thumbnailUrl;
            }

            if (\is_string($imageUrl)) {
                $record['iiif_image_url'] = $imageUrl;
            }
        } catch (\Throwable) {
            // Swallow errors; we still keep citation + manifest URL.
        }

        // Mutate event in-place
        $event->record = $record;
    }
}
