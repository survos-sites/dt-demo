<?php

use Castor\Attribute\AsTask;

use function Castor\{io,run,capture,import};

import('src/Command/AppLoadDataCommand.php');
import('src/Command/LoadDummyCommand.php');
import('src/Command/JeopardyCommand.php');
try {
    import('.castor/vendor/tacman/castor-tools/castor.php');
} catch (Throwable $e) {
    io()->error("castor composer install");
    io()->error($e->getMessage());
}


#[AsTask('congress:details', description: 'Fetch details from wikipedia')]
function congress_details(): void
{
    run('bin/console state:iterate Official --marking=new --transition=fetch_wiki');
    run('bin/console mess:stats');
    io()->writeln("make sure the message consumer is running");
}

#[AsTask('load', description: 'Loads the database')]
function load_database(
    #[\Castor\Attribute\AsArgument()] string $code='',
    #[Opt()] ?int $limit=null,
): void
{
    if (!file_exists('data/wam.csv')) {
        $zip = new ZipArchive();
        /* The original wam had errors.  This was applied before being zipped.

          mkdir data/wam/raw -p
          wget "https://data.museum.wa.gov.au/sites/default/files/Dywer-and-Mackay/index.csv" -O data/wam/raw/wam-dywer.csv
          # "Notes" is duplicated
          sed -i '1s/"Notes","Original/"Notes2","Original/' data/wam/raw/wam-dywer.csv

          sed -i '1{
          s/ /_/g;
          s/\([a-z0-9]\)\([A-Z]\)/\1_\2/g;
          y/ABCDEFGHIJKLMNOPQRSTUVWXYZ/abcdefghijklmnopqrstuvwxyz/
          }' data/wam/raw/wam-dywer.csv


        */
        if ($zip->open('zip/wam.zip') === true) {
            io()->writeln("Unzipping wam.zip");
            // Extract just the one file
            $zip->extractTo($csvFile = __DIR__ . '/data/', 'wam-dywer.csv');
            $zip->close();
            io()->writeln("Wam was extracted to " . $csvFile);
        } else {
            throw new Exception('Failed to open ZIP file');
        }
    }
    $map = [
        'Wam' => 'data/wam-dywer.csv',
        'Wine' => 'data/wine.json',
        'Movie' => 'data/movies.csv',
        'Car' => 'data/cars.csv --auto',
        'Book' => 'data/goodreads-books.csv',
    ];
    if (!array_key_exists($code, $map)) {
        io()->error("The code '{$code}' does not exist: " . implode('|', array_keys($map)));
        return;
    }
    $command = sprintf('bin/console import:entities App\\\\Entity\\\\%s --file %s ' . ($limit ? ' --limit=' . $limit : ''),
        $code, $map[$code]);
    io()->writeln($command);
    run($command);
    run('symfony open:local --path=/meiliAdmin/meiliAdmin/instant-search/index/meili_' . strtolower($code));

}
