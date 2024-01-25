<?php

namespace App\Listener;

use App\Entity\Official;
use Doctrine\ORM\EntityManagerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleImage;
use Presta\SitemapBundle\Sitemap\Url\GoogleImageUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideo;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Yann Eugoné <yeugone@prestaconcept.net>
 */
class SitemapListener implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $doctrine, private readonly UrlGeneratorInterface $router)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populateSitemap',
        ];
    }

    public function populateSitemap(SitemapPopulateEvent $event): void
    {
        if (\in_array($event->getSection(), ['congress', null], true)) {
            $this->registerPages($event->getUrlContainer());
        }
        if (\in_array($event->getSection(), ['blog', null], true)) {
//            $this->registerBlogPosts($event->getUrlContainer());
        }
    }

    private function registerPages(UrlContainerInterface $urlContainer): void
    {
        $pages = $this->doctrine->getRepository(Official::class)->findAll();

        /** @var Official $official */
        foreach ($pages as $official) {
            $url = $this->url(
                'app_congress_show',
                $official->getrp()
            );
            if (count($official->getImageCodes()) > 0) {
                $url = new GoogleImageUrlDecorator($url);
                foreach ($official->getImageCodes() as $idx => $image) {
                    $actualUrl = $image['url'];
                    $url->addImage(
                        new GoogleImage($actualUrl, sprintf('%s - %d', $official->getOfficialName(), $idx + 1))
                    );
//                    dd($actualUrl, $googleImageUrl);
                }
            }

            $urlContainer->addUrl($url, 'congress');

        }
    }

    private function registerBlogPosts(UrlContainerInterface $urlContainer): void
    {
        $posts = $this->doctrine->getRepository(BlogPost::class)->findAll();

        /** @var BlogPost $post */
        foreach ($posts as $post) {
            $url = $this->url(
                'blog',
                ['slug' => $post->getSlug()]
            );

            if (count($post->getImages()) > 0) {
                $url = new GoogleImageUrlDecorator($url);
                foreach ($post->getImages() as $idx => $image) {
                    $url->addImage(
                        new GoogleImage($image, sprintf('%s - %d', $post->getTitle(), $idx + 1))
                    );
                }
            }

            if ($post->getVideo() !== null) {
                parse_str(parse_url((string)$post->getVideo(), PHP_URL_QUERY), $parameters);
                $url = new GoogleVideoUrlDecorator($url);
                $url->addVideo(
                    $video = new GoogleVideo(
                        sprintf('https://img.youtube.com/vi/%s/0.jpg', $parameters['v']),
                        $post->getTitle(),
                        $post->getTitle(),
                        ['content_location' => $post->getVideo()]
                    )
                );
            }

            $urlContainer->addUrl($url, 'blog');
        }
    }

    private function url(string $route, array $parameters = []): UrlConcrete
    {
        return new UrlConcrete(
            $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }
}
