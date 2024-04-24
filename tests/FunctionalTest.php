<?php

namespace App\Tests;

use Pierstoval\SmokeTesting\FunctionalSmokeTester;
use Pierstoval\SmokeTesting\FunctionalTestData;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    use FunctionalSmokeTester;

    public function testRouteApiEntrypointWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/api')
                ->withMethod('GET')
                ->expectRouteName('api_entrypoint')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/api'))
        );
    }

    public function testRouteApiErrorsWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/api/errors')
                ->withMethod('GET')
                ->expectRouteName('api_errors')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/api/errors'))
        );
    }

    public function testRouteApiDocWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/api/docs')
                ->withMethod('GET')
                ->expectRouteName('api_doc')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/api/docs'))
        );
    }

    public function testRouteFosJsRoutingJsWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/js/routing')
                ->withMethod('GET')
                ->expectRouteName('fos_js_routing_js')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/js/routing'))
        );
    }

    public function testRouteSurvosAuthWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/auth/auth')
                ->withMethod('GET')
                ->expectRouteName('survos_auth')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/auth/auth'))
        );
    }

    public function testRouteOauthProfileWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/auth/profile')
                ->withMethod('GET')
                ->expectRouteName('oauth_profile')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/auth/profile'))
        );
    }

    public function testRouteOauthProvidersWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/auth/oauth_providers')
                ->withMethod('GET')
                ->expectRouteName('oauth_providers')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/auth/oauth_providers'))
        );
    }

    public function testRouteSurvosCommandsWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/commands')
                ->withMethod('GET')
                ->expectRouteName('survos_commands')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/admin/commands'))
        );
    }

    public function testRouteSurvosCrawlerResultsWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/crawler-results')
                ->withMethod('GET')
                ->expectRouteName('survos_crawler_results')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/admin/crawler-results'))
        );
    }

    public function testRouteProfilerHomeWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/_profiler/')
                ->withMethod('GET')
                ->expectRouteName('_profiler_home')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/_profiler/'))
        );
    }

    public function testRouteProfilerSearchWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/_profiler/search')
                ->withMethod('GET')
                ->expectRouteName('_profiler_search')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/_profiler/search'))
        );
    }

    public function testRouteProfilerSearchBarWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/_profiler/search_bar')
                ->withMethod('GET')
                ->expectRouteName('_profiler_search_bar')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/_profiler/search_bar'))
        );
    }

    public function testRouteProfilerPhpinfoWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/_profiler/phpinfo')
                ->withMethod('GET')
                ->expectRouteName('_profiler_phpinfo')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/_profiler/phpinfo'))
        );
    }

    public function testRouteProfilerXdebugWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/_profiler/xdebug')
                ->withMethod('GET')
                ->expectRouteName('_profiler_xdebug')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/_profiler/xdebug'))
        );
    }

    public function testRouteProfilerOpenFileWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/_profiler/open')
                ->withMethod('GET')
                ->expectRouteName('_profiler_open_file')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/_profiler/open'))
        );
    }

    public function testRouteAppHomepageWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/')
                ->withMethod('GET')
                ->expectRouteName('app_homepage')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/'))
        );
    }

    public function testRouteAppSimpleWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/simple')
                ->withMethod('GET')
                ->expectRouteName('app_simple')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/simple'))
        );
    }

    public function testRouteAppWikidataWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/wikidata')
                ->withMethod('GET')
                ->expectRouteName('app_wikidata')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/wikidata'))
        );
    }

    public function testRouteAppGridWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/grid')
                ->withMethod('GET')
                ->expectRouteName('app_grid')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/grid'))
        );
    }

    public function testRouteCongressCrudIndexWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/congress/crud_index')
                ->withMethod('GET')
                ->expectRouteName('congress_crud_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/congress/crud_index'))
        );
    }

    public function testRouteAppCongressSimpleDatatablesWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/congress/simple_datatables')
                ->withMethod('GET')
                ->expectRouteName('app_congress_simple_datatables')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/congress/simple_datatables'))
        );
    }

    public function testRouteAppCongressGridWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/congress/grid')
                ->withMethod('GET')
                ->expectRouteName('app_congress_grid')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/congress/grid'))
        );
    }

    public function testRouteCongressApiGridWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/congress/api_grid')
                ->withMethod('GET')
                ->expectRouteName('congress_api_grid')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/congress/api_grid'))
        );
    }

    public function testRouteAppCongressNewWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/congress/new')
                ->withMethod('GET')
                ->expectRouteName('app_congress_new')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/congress/new'))
        );
    }

    public function testRouteAppCongressNewWithMethodPost(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/congress/new')
                ->withMethod('POST')
                ->expectRouteName('app_congress_new')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('POST', '/congress/new'))
        );
    }

    public function testRouteAppCreditWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/credit')
                ->withMethod('GET')
                ->expectRouteName('app_credit')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/credit'))
        );
    }

    public function testRouteFlysystemBrowseDefaultWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/flysystem_default')
                ->withMethod('GET')
                ->expectRouteName('flysystem_browse_default')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/flysystem_default'))
        );
    }

    public function testRouteZenstruckMessengerMonitorDashboardWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/messenger')
                ->withMethod('GET')
                ->expectRouteName('zenstruck_messenger_monitor_dashboard')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/admin/messenger'))
        );
    }

    public function testRouteZenstruckMessengerMonitorHistoryWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/messenger/history')
                ->withMethod('GET')
                ->expectRouteName('zenstruck_messenger_monitor_history')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/admin/messenger/history'))
        );
    }

    public function testRouteZenstruckMessengerMonitorTransportWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/messenger/transport')
                ->withMethod('GET')
                ->expectRouteName('zenstruck_messenger_monitor_transport')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/admin/messenger/transport'))
        );
    }

    public function testRouteZenstruckMessengerMonitorScheduleWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/messenger/schedule')
                ->withMethod('GET')
                ->expectRouteName('zenstruck_messenger_monitor_schedule')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/admin/messenger/schedule'))
        );
    }

    public function testRouteAppLoginWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/login')
                ->withMethod('GET')
                ->expectRouteName('app_login')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/login'))
        );
    }

    public function testRouteAppTermCrudIndexWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/term/crud/')
                ->withMethod('GET')
                ->expectRouteName('app_term_crud_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/term/crud/'))
        );
    }

    public function testRouteAppTermCrudNewWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/term/crud/new')
                ->withMethod('GET')
                ->expectRouteName('app_term_crud_new')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/term/crud/new'))
        );
    }

    public function testRouteAppTermCrudNewWithMethodPost(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/term/crud/new')
                ->withMethod('POST')
                ->expectRouteName('app_term_crud_new')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('POST', '/term/crud/new'))
        );
    }

    public function assertStatusCodeLessThan500(string $method, string $url): \Closure
    {
        return function (KernelBrowser $browser) use ($method, $url) {
            $statusCode = $browser->getResponse()->getStatusCode();
            $routeName = $browser->getRequest()->attributes->get('_route', 'unknown');

            static::assertLessThan(
                500,
                $statusCode,
                sprintf('Request "%s %s" for %s route returned an internal error.', $method, $url, $routeName),
            );
        };
    }
}
