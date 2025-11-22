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

import('src/Command/LoadCongressCommand.php');
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
    /** @var <string,Dataset> $map */
    $map = [];
    // https://github.com/Ambrosiani/museums-on-github
    // https://github.com/algolia/datasets?tab=readme-ov-file
    // https://community.algolia.com/
    // https://medium.com/@canning.erin/museum-collections-as-data-comparing-collection-stats-across-four-datasets-in-four-days-32b98aa2b9c0

//    citation: https://egallery.williams.edu/objects/14202
    // iifi: sprintf('https://egallery.williams.edu/apis/iiif/presentation/v2/1-objects-%d/manifest', $obj->id);
    // parse this file and extra the images

    //         run('
    //bin/console json:convert:dir data/wcma.csv  data/wcma-with-images.jsonl --dispatch --slugify=name --pk=code ');
    $dataSets  = [
        new Dataset(name: 'wcma',
            url: 'https://github.com/wcmaart/collection/raw/refs/heads/master/wcma-collection.csv',
            target: 'data/wcma.csv',
        ),
        new Dataset('car',
        'https://corgis-edu.github.io/corgis/datasets/csv/cars/cars.csv',
        'data/cars.csv'
        ),
        new Dataset('wine',
            'https://github.com/algolia/datasets/raw/refs/heads/master/wine/bordeaux.json',
            'data/wine.json'),
        new Dataset('marvel',
            'https://github.com/algolia/marvel-search/archive/refs/heads/master.zip', 'zip/marvel.zip'
        ),
        new Dataset('wam', null, "data/wam-dywer.csv"),
    ];
    foreach ($dataSets as $dataset) {
        $map[$dataset->name] = $dataset;
        if (!file_exists($dataset->target)) {
            http_download($dataset->url, $dataset->target);
            io()->writeln(realpath($dataset->target) . ' written');
        }
    }
    // https://community.algolia.com/marvel-search/
    // actually, we can now read from a ZIP file!  But it's looking for zipped json objects, might need to tweak
    if (!file_exists('data/wam.csv')) {
        $zip = new ZipArchive();
        /* The original wam had errors.  This was applied before being zipped.

          we need a home for these! museado/datasets?

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
//    $map = [
//        'wam' => 'data/wam-dywer.csv',
//        'wine' => 'data/wine.json',
//        'movie' => 'data/movies.csv',
//        // https://corgis-edu.github.io/corgis/csv/cars/
//        'car' => 'data/cars.csv',
//        'book' => 'data/goodreads-books.csv',
//        'marvel' => 'data/marvel.jsonl',
//    ];
    if (!array_key_exists($code, $map)) {
        io()->error("The code '{$code}' does not exist: " . implode('|', array_keys($map)));
        return;
    }
    /** @var DataSet $dataset */
    $dataset = $map[$code];
    $command = sprintf('bin/console json:convert %s %s --dispatch', $dataset->target, $dataset->jsonl);
    run($command);

    $command = sprintf('bin/console import:entities App\\\\Entity\\\\%s %s ' . ($limit ? ' --limit=' . $limit : ''),
        ucfirst($code), $dataset->jsonl);
    io()->writeln($command);
    run($command);
//    run('symfony open:local --path=/meiliAdmin/meiliAdmin/instant-search/index/meili_' . strtolower($code));

}

class X {
    public function __construct(
        public string $code,
        public ?string $input=null,
        public ?string $output=null,
    )
    {

    }
}
