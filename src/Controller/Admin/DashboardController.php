<?php

namespace App\Controller\Admin;

use App\Entity\Instrument;
use App\Entity\Jeopardy;
use App\Entity\Official;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Meilisearch\Meilisearch;
use Survos\MeiliBundle\Service\MeiliService;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly MeiliService $meiliService,
    )
    {
    }

    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
//         return $this->redirectToRoute('survos_workflow_entities');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dt Demo');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        foreach ($this->meiliService->indexedByClass() as $class => $indexes) {
            $shortName = new \ReflectionClass($class)->getShortName();
            yield MenuItem::linkToCrud($shortName, 'fas fa-database', $class)
                ->setBadge($this->entityManager->getRepository($class)->count());
            foreach ($indexes as $indexName => $index) {
                yield MenuItem::linkToRoute(
                    $index['rawName'],
                    'fas fa-search',
                    'meili_insta',
                    ['indexName' => $indexName])
                    ->setLinkTarget('_blank');
            }
        }
        yield MenuItem::linkToRoute('Home', 'fas fa-home', 'app_homepage');
    }
}
