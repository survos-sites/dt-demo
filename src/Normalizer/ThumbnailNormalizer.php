<?php

namespace App\Normalizer;

use App\Entity\Official;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsDecorator('api_platform.jsonld.normalizer.item')]
class ThumbnailNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    public function __construct(private NormalizerInterface $normalizer,
                                private CacheManager $cacheManager,
                                private Security $security)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if ($object['imageCount']) {
            foreach ($object['imageCodes'] as $idx => $imageData) {
                // if the thumbnail exists, return the cached link, otherwise return the resolve link.
                // use getBrowserPath, ->resolve will actually create the cached file.
                $thumbnailUrl = $this->cacheManager->getBrowserPath($imageData['code'], 'squared_thumbnail_tiny');
                $object['imageCodes'][$idx]['thumb'] = $thumbnailUrl;
            }
        }
        return $object;
//        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        // this is the collection, not the item!
//        if (is_array($data)  && array_is_list($data)) {
//            return false;
//        }
        if (is_array($data) && !array_is_list($data) && $format == 'meili') {
            return true;
        }
        return false;
        return false;
        return in_array($format, ['meili']);
        $x =  $this->normalizer->supportsNormalization($data, $format);
        dd($x, $data, $format);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->normalizer instanceof SerializerAwareInterface) {
            $this->normalizer->setSerializer($serializer);
        }
    }
}
