<?php declare(strict_types=1);

// File: src/Enhance/GlobalImageUrlEnhancementListener.php
// App Enhancement v0.4
// This iteration: listen to EnhanceRecordEvent and attach image_urls array when URLs are found.

namespace App\Enhance;

use Survos\JsonlBundle\Event\EnhanceRecordEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: EnhanceRecordEvent::class)]
final class GlobalImageUrlEnhancementListener
{
    public function __invoke(EnhanceRecordEvent $event): void
    {
        $record = $event->record;
        $urls   = [];

        foreach ($record as $value) {
            if (!\is_string($value)) {
                continue;
            }

            if (\preg_match('#https?://.+\.(jpe?g|png|gif|tif|webp)(\?|$)#i', $value)) {
                $urls[] = $value;
            }
        }

        if ($urls !== []) {
            $record['image_urls'] = array_values(array_unique($urls));
        }

        $event->record = $record;
    }
}
