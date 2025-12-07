<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Survos\MeiliBundle\Bridge\EasyAdmin\MeiliEasyAdminDashboardHelper;
use Survos\MeiliBundle\Bridge\EasyAdmin\MeiliEasyAdminMenuFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\Translation\t;

#[AdminDashboard('/', self::MEILI_ROUTE)]
final class MeiliDashboardController extends AbstractDashboardController
{
    public const MEILI_ROUTE = 'meili_admin';

    public function __construct(
        private readonly MeiliEasyAdminDashboardHelper $dashboardHelper,
        private readonly MeiliEasyAdminMenuFactory $menuFactory
    ) {
    }

    public function index(): Response
    {
        return $this->render(
            $this->dashboardHelper->getDashboardTemplate(),
            $this->dashboardHelper->getDashboardParameters(self::MEILI_ROUTE)
        );
    }

    public function configureDashboard(): Dashboard
    {
        return $this->dashboardHelper
            ->configureDashboard(Dashboard::new())
            ->setTranslationDomain('amst');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->useCustomIconSet() // use ux_icons
            ;
    }


    /**
     * @return iterable<MenuItem>
     */
    public function configureMenuItems(): iterable
    {
        $translationDomain = 'meili'; /// change this for application-specific translations

        // Main navigation
        yield MenuItem::linkToDashboard(
            t('page_title.dashboard', [], 'EasyAdminBundle'),
            $this->dashboardHelper->getIcon('home')
        );

//        yield MenuItem::section('content_management', 'fas fa-folder-open');
        yield from $this->menuFactory->createIndexMenus(self::MEILI_ROUTE);

//        yield MenuItem::section('tools', 'fas fa-wrench');
        yield from $this->menuFactory->createToolsMenuItems();

//        yield MenuItem::linkToUrl('search_analytics', 'fas fa-chart-line', '#')
////            ->setPermission('ROLE_ADMIN')
//        ;
    }
}
