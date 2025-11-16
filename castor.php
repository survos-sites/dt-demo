<?php

use Castor\Attribute\AsTask;

use function Castor\{io,run,capture,import};

import('src/Command/AppLoadDataCommand.php');
import('src/Command/JeopardyCommand.php');

#[AsTask(description: 'Welcome to Castor!')]
function hello(): void
{
    $currentUser = capture('whoami');

    io()->title(sprintf('Hello %s!', $currentUser));
}
