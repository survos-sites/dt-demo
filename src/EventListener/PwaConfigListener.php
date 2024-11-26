<?php

namespace App\EventListener;

use SpomkyLabs\PwaBundle\Event\PreManifestCompileEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class PwaConfigListener
{
    #[AsEventListener(event: PreManifestBuildEvent::class)]
    public function onPreCompile($event): void
    {
        $config = $event->getConfig();
        if (empty($config['description'])) {
            $composerData = json_decode(file_get_contents(__DIR__.'/../../composer.json'), true);
            $config['description'] = $composerData['description'];
            $event->setConfig($config);
        }
        // ...
    }

    #[AsEventListener(event: Builder::class)]
    public function onPreBuilder($event): void
    {
        // ...
    }

}
