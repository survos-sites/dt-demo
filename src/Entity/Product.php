<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Tests\Fixtures\TestBundle\Entity\Field;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\BabelBundle\Attribute\BabelLocale;
use Survos\BabelBundle\Attribute\BabelStorage;
use Survos\BabelBundle\Attribute\StorageMode;
use Survos\CoreBundle\Entity\RouteParametersInterface;
use Survos\CoreBundle\Entity\RouteParametersTrait;
use Survos\MeiliBundle\Api\Filter\FacetsFieldSearchFilter;
use Survos\MeiliBundle\Metadata\Embedder;
use Survos\MeiliBundle\Metadata\Facet;
use Survos\MeiliBundle\Metadata\FacetWidget;
use Survos\MeiliBundle\Metadata\Fields;
use Survos\MeiliBundle\Metadata\FieldSet;
use Survos\MeiliBundle\Metadata\MeiliIndex;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


use Survos\BabelBundle\Entity\Traits\BabelHooksTrait;

use Doctrine\ORM\Mapping\Column;

use Survos\BabelBundle\Attribute\Translatable;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['product.read', 'product.details'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => ['product.read'],
            ],
//            parameters: [
//                // ?category=foo
//                'brand' => new QueryParameter(
//                    filter: new ExactFilter() // instance, ignored if not registered
//                ),
//            ],
            parameters: [
                // it appears that this won't work without a custom filter
                // create a "tag" entity and then filter on it
                // https://chatgpt.com/share/69357edd-e82c-8010-b119-aa3723ba84da
//                'tags' => new QueryParameter(
//                    filter: new ExactFilter(),
//                    property: 'tags',
//                ),
//                'range[:property]' => new QueryParameter(
//                    filter: new RangeFilter(),
//                    properties: self::RANGE_PROPS
//                ),
                'search[:property]' => new QueryParameter(
                    filter: new PartialSearchFilter(),
                    properties: self::SEARCH_PROPS
                ),
                'filter[:property]' => new QueryParameter(
                    filter: new ExactFilter(),
                    properties: ['category','brand'], // self::FILTER_PROPS
                ),
                'order[:property]' => new QueryParameter(
                    filter: new OrderFilter(),
                    properties: self::SORT_PROPS + ['exactPrice']
                ),
            ]
        )],
    normalizationContext: ['groups' => ['product.read', 'product.details','rp']],
)]

//#[MeiliIndex(
//    // serialization groups for the JSON sent to the index
//    primaryKey: 'sku',
//    persisted: new Fields(
//        fields: ['sku', 'stock', 'price', 'title','brand'],
//        groups: ['product.read', 'product.details', 'product.searchable']
//    ),
//    displayed: ['*'],
//    filterable: new Fields(
//        fields: self::FILTER_PROPS + self::RANGE_PROPS,
////        groups: ['product.read','product.details']
//    ),
//    sortable: new Fields(
//        fields: self::SORT_PROPS + ['price'],
//    ),
//    embedders: ['product']
//)]
#[BabelStorage(StorageMode::Property)]
#[BabelLocale(targetLocales: ['es','fr'])]
class Product implements RouteParametersInterface
{
    use BabelHooksTrait;

    use RouteParametersTrait;

    private const RANGE_PROPS = ['rating', 'stock']; // // meili will also add RANGE_PROPS as filterable
    private const FILTER_PROPS = ['category','brand']; // single values only without custom filter
    private const FILTER_ARRAY_PROPS = ['tags']; // meili can handle these
    private const SEARCH_PROPS = ['titleBacking', 'descriptionBacking'];
    private const SORT_PROPS = ['rating']; // price for meili, , 'exactPrice' for doctrine


    public const UNIQUE_PARAMETERS = ['productId'=>'sku'];
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        #[ORM\Id]
        #[Groups(['product.read'])]
        public ?string $sku,


        #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
//        #[Groups(['product.details'])]
        private(set) array $data


    )
    {
        $this->images = new ArrayCollection();
        $this->stock = $this->data->stock??0;
        $this->rating = round($this->data->rating??0);
    }
    public string $id { get => $this->sku; }

    public function getId(): string
    {
        return $this->sku;
    }


    // virtual property
    #[Groups(['product.read'])]
    #[ORM\Column(nullable: true)]
    #[Facet(label: 'Category', showMoreThreshold: 12)]
    #[ApiProperty("category from extra, virtual but needs index")]
    public ?string $category;

    #[Groups(['product.read'])]
    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Facet(showMoreThreshold: 12)]
    #[ApiProperty("the registered brand name")]
    public ?string $brand;

    #[Groups(['product.read'])]
    #[ApiProperty("thumbnail from data (not sais)")]
    public ?string $thumbnail {
        get => $this->data['thumbnail']??null;
    }


    #[Groups(['product.read'])]
    #[ApiProperty("virtual price, int for meili slider")]
    public ?int $price {
        get => round($this->data['price']??0);
    }

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    #[ApiProperty("exact price, float, for doctrine filters")]
    public ?float $exactPrice;

    #[Groups(['product.read'])]
    #[ApiProperty("rounded rating, for range slider")]
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Range(
        min: 0,
        max: 5
    )]
    #[Facet(widget: FacetWidget::RangeSlider)]
    public int $rating;

    #[Groups(['product.read'])]
    #[ApiProperty("rounded rating, for range slider")]
    #[ORM\Column(type: Types::INTEGER)]
    #[Facet()]
    public int $stock;

    #[Groups(['product.read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    #[ApiProperty("array of tags")]
    #[Facet()]
    public array $tags;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'product', orphanRemoval: true)]
    private(set) Collection $images {
        get {
            return $this->images;
        }
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }


        // <BABEL:TRANSLATABLE:START title>
        #[Column(type: Types::TEXT, nullable: true)]
        private(set) ?string $titleBacking = null;

        #[Translatable(context: NULL)]
        #[Groups(['product.read', 'product.searchable'])]
        public ?string $title {
            get => $this->resolveTranslatable('title', $this->titleBacking, NULL);
            set => $this->titleBacking = $value;
        }
        // <BABEL:TRANSLATABLE:END title>

        // <BABEL:TRANSLATABLE:START description>
        #[Column(type: Types::TEXT, nullable: true)]
        private ?string $descriptionBacking = null;

        #[Translatable(context: NULL)]
        #[Groups(['product.read', 'product.searchable'])]
        public ?string $description {
            get => $this->resolveTranslatable('description', $this->descriptionBacking, NULL);
            set => $this->descriptionBacking = $value;
        }
        public ?string $snippet { get => mb_substr($this->description, 0, 40); }
        // <BABEL:TRANSLATABLE:END description>
}
