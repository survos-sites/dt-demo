<?php

namespace App\Command;

use App\Entity\Instrument;
use App\Service\NdJsonService;
use Doctrine\ORM\EntityManagerInterface;
use Survos\CoreBundle\Service\LooseObjectMapper;
use Survos\JsonlBundle\IO\JsonlReader;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

#[AsCommand('app:music', 'Import music database from metabrainz')]
class MusicCommand
{
    public function __construct(
        private readonly HttpClientInterface $downloadClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly LooseObjectMapper  $looseObjectMapper,
    )
    {
    }


    public function __invoke(
        SymfonyStyle $io,
        #[Option('the directory for local (downloaded) files')]
//        string       $directory = 'data/music/',
        string       $directory = '/mnt/data/music/',
        #[Option('filter downloaded files, e.g. genre|artist')]
        ?string      $entity = null,
        #[Option('fetch the latest data')] ?bool        $refresh = null,
        #[Option('purge the table first')] ?bool        $reset = null,
        #[Option] int $limit = 50,
        #[Option] ?int $dump = null,
    ): int
    {


        // @todo: fetch /latest
        $latest = trim(file_get_contents('https://data.metabrainz.org/pub/musicbrainz/data/json-dumps/LATEST'));

        $base = 'https://data.metabrainz.org/pub/musicbrainz/data/json-dumps/' . $latest . '/';
        if (true) {
            $html = file_get_contents($base);
            preg_match_all('/"(.*?).tar.xz"/', $html, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                [$path, $code] = $match;
                $io->writeln(sprintf('<info>%s</info>', $path));
            }
        }
        if (!$entity) {
            $entity = 'instrument';
        }

//        $entity ??= 'instrument';

        // to run live we need to map storage.  For dt-demo, we can use public/images/uploads
        $directory .= $entity;
        if (!file_exists($directory)) {
            try {
                mkdir($directory, 0777, true);
            } catch (\Exception $e) {
                $io->error($e->getMessage() . PHP_EOL . $directory);
            }
        }
        if (!str_ends_with($directory, '/')) {
            $directory .= '/';
        }
        $io->writeln("Writing to : $directory");

        $url = $base . $entity . ".tar.xz";
        $io->title("Loading $url to $directory");

        $ndJsonFilename = $this->downloadFile($url, $entity, outPath: $directory, io: $io, refresh: $refresh);
        $class = 'App\\Entity\\' . ucfirst($entity);
        assert(class_exists($class), $class);
        $repo = $this->entityManager->getRepository($class);
        if ($reset) {

            $this->entityManager->createQuery("DELETE FROM $class")->execute();
            $io->warning("$class data purged.");
        }

        foreach (new JsonlReader($ndJsonFilename) as $line => $row) {
            $rows[$line] = $row;
            $pkField = 'id'; // for now
            $pkValue = $row[$pkField];
            /** @var Instrument $entity  */
            if (!$entity = $this->entityManager->getRepository($class)->find($pkValue)) {
                $entity = new $class($pkValue);
//                $entity->$pkField = $pkValue;
                $this->entityManager->persist($entity);
            }

            $this->looseObjectMapper->mapInto($row, $entity, [$pkField]);
            $entity->tags = array_map(fn(array $tag) => $tag['name'], $row['tags']);
            dd($row, $entity);
            dd($row, $class);
        }
        $ndjson = new NdJsonService($ndJsonFilename);


        $objectMapper = new ObjectMapper();
        $count = 0;
        while ($data = $ndjson->readline()) {
            if (!$data->id) {
                break;
            }
            if ($dump && ($dump == $count)) {
                dump($data);
            }
//            $existing = $repo->find($data->id);
//            $existing = $repo->findOneBy(['id' => Uuid::fromString($data->id)]);
            $existing = $repo->findOneBy(['id' => $data->id]);
            try {
                $obj = $objectMapper->map($data, $existing ?? $class);
//                dd($obj);
                $existing || $this->entityManager->persist($obj);
                $this->entityManager->flush();
            } catch (\Exception $e) {
//                dump($data);
                dd($e->getMessage(), $data->id);
            }
            if (($count++ >= $limit) && $limit) {
                break;
            }
        }
        $this->entityManager->flush();
        $io->success("Writing to : $entity $class repo " . $repo->count([]));

        return Command::SUCCESS;
    }



    private function downloadFile(string $url, string $entity, string $outPath, SymfonyStyle $io, ?bool $refresh): string|int
    {
        $outFilename = $outPath . "$entity.tar.xz";
        $ndJson = $outPath . 'mbdump/' . $entity;

        if (file_exists($ndJson)) {
            return  $ndJson;
        }
//        return  $ndJson;
        if ($refresh || !file_exists($outFilename)) {
            if (false === $fp = fopen($outFilename, 'wb')) {
                $io->error("Cannot open $outPath for writing.");
                return Command::FAILURE;
            }
            $response = $this->downloadClient->request('GET', $url, [
                // avoid the default 30s timeout on very large files
                'timeout' => null,
            ]);

            // 1) Kick off the request
            if (200 !== $response->getStatusCode()) {
                $io->error('Could not start download. HTTP status: ' . $response->getStatusCode());
                return Command::FAILURE;
            }

            // 2) Figure out total size (if server provides it)
            $headers = $response->getHeaders(false);
            $totalBytes = isset($headers['content-length'][0]) ? (int)$headers['content-length'][0] : null;

            // 3) Prepare file handle
            // or get the output path from the url

            // 4) Build a progress bar
            $progress = $io->createProgressBar($totalBytes ?? 0);
            if ($totalBytes) {
                $progress->setFormat(' %current%/%max% bytes [%bar%] %percent:3s%%');
                $progress->start($totalBytes);
            } else {
                $progress->setFormat('Downloaded %current% bytes');
                $progress->start();
            }

            $downloaded = 0;

            // 5) Stream the response in chunks
            foreach ($this->downloadClient->stream($response) as $chunk) {
                // skip on timeout events
                if ($chunk->isTimeout()) {
                    continue;
                }

                // getContent(false) returns *only* this chunk, no exception
                $data = $chunk->getContent(false);
                $bytes = strlen($data);
                $downloaded += $bytes;

                fwrite($fp, $data);
                $progress->setProgress($downloaded);
            }

            $progress->finish();
            fclose($fp);
        }


        if (!file_exists($ndJson)) {
            $io->writeln("Extracting $outFilename to $outPath.");
            $process = new Process([
                'tar',
                'xf',
                $outFilename,
                '-C',
                $outPath,
            ]);
            $process->run();


// throw if something went wrong
            if (!$process->isSuccessful()) {
                dump($process->getCommandLine());
                $io->error('Could not extract data from ' . $outFilename);
                throw new ProcessFailedException($process);
            }

            echo "Extraction complete!\n";

            // 1) Read & decompress

            $io->newLine(2);
            $io->success(sprintf(
                'Downloaded %s (%d bytes) › %s',
                basename($url),
                $downloaded,
                $outPath
            ));
        }

        assert(file_exists($ndJson));
        return $ndJson;
    }
}
