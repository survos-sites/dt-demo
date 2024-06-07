<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Panther\PantherTestCaseTrait;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Console\ConfigureWithAttributes;
use Zenstruck\Console\InvokableServiceCommand;
use Zenstruck\Console\IO;
use Zenstruck\Console\RunsCommands;
use Zenstruck\Console\RunsProcesses;

#[AsCommand('app:screenshots', 'create screenshots from a sequence of instructions')]
final class AppScreenshotsCommand extends InvokableServiceCommand
{
    use RunsCommands;
    use RunsProcesses;

    use PantherTestCaseTrait;
    use HasBrowser;

    public function __invoke(
        IO $io,
    ): void {

        $_SERVER['BROWSER_SCREENSHOT_DIR'] = './screenshots';
        $_SERVER['PANTHER_CHROME_ARGUMENTS'] = "--disable-dev-shm-usage --window-size=600,800 --no-sandbox --headless --hide-scrollbars";

        $browser = $this->pantherBrowser();
        $browser->visit('/')
            ->takeScreenshot('homepage.jpg');

//        $browser->visit('/congress/api_grid')
//            ->wait(2000)
//            ->takeScreenshot('congress.jpg');


        $io->success('app:screenshots success.');
    }
}
