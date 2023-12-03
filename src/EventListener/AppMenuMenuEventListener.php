<?php

namespace App\EventListener;

use App\Controller\CongressController;
use App\Controller\TermCrudController;
use App\Entity\Official;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Survos\BootstrapBundle\Service\ContextService;
use Survos\BootstrapBundle\Traits\KnpMenuHelperInterface;
use Survos\BootstrapBundle\Traits\KnpMenuHelperTrait;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use function Symfony\Component\String\u;

#[AsEventListener(event: KnpMenuEvent::SIDEBAR_MENU, method: 'sidebarMenu')]
#[AsEventListener(event: KnpMenuEvent::PAGE_MENU, method: 'pageMenu')]
#[AsEventListener(event: KnpMenuEvent::NAVBAR_MENU, method: 'startNavbarMenu')]
#[AsEventListener(event: KnpMenuEvent::NAVBAR_MENU2, method: 'midNavbarMenu')]
#[AsEventListener(event: KnpMenuEvent::NAVBAR_MENU3, method: 'lastNavbarMenu')]
#[AsEventListener(event: KnpMenuEvent::FOOTER_MENU, method: 'footerMenu')]
final class AppMenuMenuEventListener implements KnpMenuHelperInterface
{
    use KnpMenuHelperTrait;

    public function __construct(
        private ContextService                 $contextService,
        private ?AuthorizationCheckerInterface $security = null,
    )
    {
    }

    public function midNavbarMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $options = $event->getOptions();
        $this->add($menu, 'app_credit', label: "Javascript Packages");
//        foreach (['app_credit'] as $route) {
//            $this->add($menu, $route, label: u($route)->after('app_'));
//        }
    }

    public function lastNavbarMenu(KnpMenuEvent $event): void
    {
//        <li class="nav-item">
//                        <a target="_blank" rel="noopener" class="nav-link"
//                           href="https://github.com/thomaspark/bootswatch/"><i class="bi bi-github"></i><span
//                                    class="d-lg-none ms-2">GitHub</span></a>
//                    </li>
//                    <li class="nav-item">
//                        <a target="_blank" rel="noopener" class="nav-link" href="https://twitter.com/bootswatch"><i
//                                    class="bi bi-twitter"></i><span class="d-lg-none ms-2">Twitter</span></a>
//                    </li>
//

        $menu = $event->getMenu();
        foreach ($this->contextService->getConfig()['app']['social'] ?? [] as $platform => $value) {
            $this->add($menu, uri: $value, label: $platform, external: true, icon: 'bi bi-' . $platform);
        }
        $this->add($menu, label: ' ', dividerAppend: true);

        if (0) {
            $nested = $this->addSubmenu($menu, 'github', icon: 'bi bi-github');
            $this->add($nested, label: 'repo', uri: $this->contextService->getConfig()['app']['social']['github']);
            $this->add($nested, label: 'issues', uri: $this->contextService->getConfig()['app']['social']['github'] . '/issues');
        }
    }

    public function startNavbarMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $options = $event->getOptions();


        $nestedMenu = $this->addSubmenu($menu, 'App');
        foreach (['app_homepage', 'app_credit', 'app_simple', 'app_grid'] as $route)
        {
            $this->add($nestedMenu, $route); // label: u($route)->after('app_')
        }
        $this->add($nestedMenu, 'survos_commands');
        // for nested menus, don't add a route, just a label, then use it for the argument to addMenuItem

//        $nestedMenu = $this->addSubmenu($menu, 'Credits');
//        foreach (['bundles', 'javascript'] as $type) {
//            // $this->addMenuItem($nestedMenu, ['route' => 'survos_base_credits', 'rp' => ['type' => $type], 'label' => ucfirst($type)]);
//            $this->addMenuItem($nestedMenu, ['uri' => "#$type", 'label' => ucfirst($type)]);
//        }

        foreach ([CongressController::class,
//                     TermCrudController::class
                 ] as $controllerClass) {
            $controllerMenu = $this->addSubmenu($menu,
                label: (new \ReflectionClass($controllerClass))->getShortName());
            foreach (['grid', 'api_grid', 'simple_datatables',
//                         'index',
//                         'crud_index'
                     ] as $controllerRoute) {
                $this->add($controllerMenu, $controllerClass . '::' . $controllerRoute,
                    label: $controllerRoute);
            }

        }
    }

    public function pageMenu(KnpMenuEvent $event): void
    {
    }

    public function footerMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $options = $event->getOptions();

        foreach (['app_homepage', 'app_credit', 'app_simple', 'app_grid'] as $route) {
            $this->add($menu, $route);
        }
        $nestedMenu = $this->addSubmenu($menu, 'Credits');
        foreach (['bundles', 'javascript'] as $type) {
            // $this->addMenuItem($nestedMenu, ['route' => 'survos_base_credits', 'rp' => ['type' => $type], 'label' => ucfirst($type)]);
            $this->addMenuItem($nestedMenu, ['uri' => "#$type", 'label' => ucfirst($type)]);
        }

    }

    public function sidebarMenu(KnpMenuEvent $event): void
    {
    }
}
