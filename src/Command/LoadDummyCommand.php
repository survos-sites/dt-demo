<?php

namespace App\Command;

use App\Controller\AppController;
use App\Entity\Image;
use App\Entity\Product;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Castor\Attribute\AsSymfonyTask;
use Doctrine\ORM\EntityManagerInterface;
use Survos\SaisBundle\Model\AccountSetup;
use Survos\SaisBundle\Service\SaisClientService;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand('load:dummy', 'Load the Product and Image entities from dummyjson.com')]
#[AsSymfonyTask('load:dummy')]
class LoadDummyCommand
{
	public function __construct(
        #[Autowire('%kernel.project_dir%/data/products.json')] private string $filename,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly ImageRepository $imageRepository,
    )
	{
	}


	public function __invoke(
		SymfonyStyle $io,
        #[Argument('the url to dummyJson')] ?string $url = 'https://dummyjson.com/products?limit=200',

		#[Option('max number of records to import')] ?int $limit = null,
		#[Option('purge Products')] ?bool $purge = null,
	): int
	{
        $url ??= $this->filename;
		if ($limit) {
		    $io->writeln("Option limit: $limit");
		}
        if ($purge) {
            //$io show "Purging Products";
            $io->writeln("Purging Images and Products");
            foreach ([Image::class, Product::class] as $className) {
                $count = $this->entityManager->getRepository(Image::class)->createQueryBuilder('qb')->delete()->getQuery()->execute();
                $io->writeln("Purging $count $className");
            }
//            $this->entityManager->getRepository(Product::class)->createQueryBuilder('qb')->delete()->getQuery()->execute();
//            assert($this->entityManager->getRepository(Image::class)->count() == 0, "didnt purge");
//            $this->entityManager->flush();
        }

        // wget https://dummyjson.com/products -O data/products.json
        foreach (json_decode(file_get_contents($url))->products as $idx => $data) {
            // object Mapper?
            if (!$product = $this->productRepository->findOneBy(['sku' => $data->sku])) {
                $product = new Product(sku: $data->sku, data: (array) $data);
                $this->entityManager->persist($product);
            }
            $product->title = $data->title;
            $product->description = $data->description;
            $product->brand = $data->brand??null;
            $product->tags = $data->tags??null;
            $product->exactPrice = $data->price??null;
            $product->rating = round($data->rating);
            $product->category = $data->category;

            foreach ($data->images as $imageUrl) {
                if (!$image = $this->imageRepository->findOneBy([
                    'product' => $product,
                    'code' => $imageCode = Image::calculateCode($imageUrl),
                ])) {
                    $image = new Image($product, $imageUrl, $imageCode);
                    $this->entityManager->persist($image);
                }
            }

            if ($limit && ($idx >= $limit - 1)) {
                break;
            }

        }
        $this->entityManager->flush();

        $io->success(self::class . " success. " . $this->productRepository->count());
		return Command::SUCCESS;
	}
}
