<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCaseTrait;
use Zenstruck\Browser\Test\HasBrowser;

#[AsCommand('app:screenshots', 'create screenshots from a sequence of instructions')]
final class AppScreenshotsCommand
{

//    use PantherTestCaseTrait;
//    use HasBrowser;

    public function __invoke(
        SymfonyStyle $io,
    ): int {

        $_SERVER['BROWSER_SCREENSHOT_DIR'] = './screenshots';
        $_SERVER['PANTHER_CHROME_ARGUMENTS'] = "--disable-dev-shm-usage --window-size=600,800 --no-sandbox --no-headless --hide-scrollbars";

        $client = Client::createChromeClient();
// alternatively, create a Firefox client
//        $client = Client::createFirefoxClient();
        foreach (['dt-demo.wip'] as $uri) {
            $uri = 'dt-demo.survos.com';
            $client->request('GET', "https://$uri");

//            $client->clickLink('Credits');
//            dd($client->getCurrentURL());

            $client->takeScreenshot($fn = "public/$uri.png");

            $base = "https://showcase.wip";
            $link = "$uri.png";
            $io->writeln("<href>$base/$link</href> $link");
        }
        $io->success(self::class .' success: ');


//        $browser = $this->pantherBrowser();
//        $browser->visit('/')
//            ->pause()
//            ->takeScreenshot('homepage.jpg');

//        $browser->visit('/congress/api_grid')
//            ->wait(2000)
//            ->takeScreenshot('congress.jpg');


        $io->success('app:screenshots success.');
        return Command::SUCCESS;
    }
}
