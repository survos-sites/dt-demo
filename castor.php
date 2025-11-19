<?php

use Castor\Attribute\AsTask;

use function Castor\{io,run,capture,import,http_download};

$autoloadCandidates = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];
foreach ($autoloadCandidates as $autoload) {
    if (is_file($autoload)) { require_once $autoload; break; }
}

use Survos\MeiliBundle\Model\Dataset;

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
    $map = [
        'wcma' => new Dataset(name: 'wcma',
            url: 'https://github.com/wcmaart/collection/raw/refs/heads/master/wcma-collection.csv',
            target: 'data/wcma.csv'
        ),
        'wine' => new Dataset('wine', 'https://github.com/algolia/datasets/raw/refs/heads/master/wine/bordeaux.json', 'data/wine.json')
    ];
    foreach ($map as $dataset) {
        if (!file_exists($dataset->target)) {
            http_download($dataset->url, $dataset->target);
            io()->writeln(realpath($dataset->target) . ' written');
        }
    }
    if (!file_exists($relativeFilename = 'zip/marvel.zip')) {
        http_download('https://github.com/algolia/marvel-search/archive/refs/heads/master.zip', $relativeFilename);
        io()->writeln(realpath($relativeFilename));
    }
    if (!file_exists('data/marvel.jsonl')) {
        // c json:convert:dir zip/marvel.zip data/marvel.jsonl --force -vv --path=marvel-search-master/records/
        run('bin/console json:convert:dir zip/marvel.zip data/marvel.jsonl --slugify=name --pk=code --path=marvel-search-master/records/');
        // https://github.com/algolia/marvel-search/tree/master/records
    }
    // https://community.algolia.com/marvel-search/
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
    // https://github.com/algolia/datasets
    $map = [
        'Wam' => 'data/wam-dywer.csv',
        'Wine' => 'data/wine.json --auto',
        'Movie' => 'data/movies.csv',
        'Car' => 'data/cars.csv --auto',
        'Book' => 'data/goodreads-books.csv',
        'Marvel' => 'data/marvel.jsonl',
    ];
    if (!array_key_exists($code, $map)) {
        io()->error("The code '{$code}' does not exist: " . implode('|', array_keys($map)));
        return;
    }
    $command = sprintf('bin/console import:entities App\\\\Entity\\\\%s --file %s ' . ($limit ? ' --limit=' . $limit : ''),
        $code, $map[$code]);
    io()->writeln($command);
    run($command);
//    run('symfony open:local --path=/meiliAdmin/meiliAdmin/instant-search/index/meili_' . strtolower($code));

}
