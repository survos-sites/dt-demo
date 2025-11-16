<?php

namespace App\Entity;

use Adbar\Dot;
use App\Repository\InstrumentRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\JeopardyRepository;
use Doctrine\DBAL\Types\Types;
use Survos\MeiliBundle\Api\Filter\FacetsFieldSearchFilter;
use Survos\MeiliBundle\Metadata\Facet;
use Survos\MeiliBundle\Metadata\Fields;
use Survos\MeiliBundle\Metadata\Select;
use Survos\MeiliBundle\Metadata\MeiliIndex;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Attribute\Groups;
use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: InstrumentRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()],
    normalizationContext: [
        'groups' => ['instrument.read'],
    ]
)]
// old way, use API Platform
//#[ApiFilter(FacetsFieldSearchFilter::class, properties: ['type', 'genres','countries'])]
// since no relations, this gets ALL the properties
#[Groups(['instrument.read'])]
#[MeiliIndex(
    persisted: new Fields(
        groups: ['instrument.read','marking.read'],
    ),
    filterable: ['type', 'genres','countries']
)]
class Instrument
{
    #[Map(if: false)]
    #[Facet(format: 'flag', showMoreThreshold: 9)]
    #[ORM\Column(type: Types::JSON, options: ['jsonb' => true], nullable: true)]
    public array $countries=[]; // really country codes
    public function __construct(
        #[ORM\Id]
        #[ORM\Column]
        private(set) ?string $id, // probably uuid

        #[ORM\Column(type: Types::TEXT)]
        #[Map(transform: [self::class, 'cleanup'])]
        public /* private(set) */string $name  {
            set => self::cleanup($value);
        },

        #[ORM\Column(type: Types::TEXT)]
        private(set) ?string $description {
            set => strip_tags($value);
        },

        #[ORM\Column(length: 255, nullable: true)]
        public /* private(set) */ ?string $type,

        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true], nullable: true)]

        public private(set) array $tags,

        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true], nullable: true)]
        public private(set) array $genres
        {
            set => self::cleanupGenres($value);
//            get => self::cleanupGenres($value);
        },

//        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true], nullable: true)]
        public private(set) ?array $relations
            {
                set => self::addRelations($value);
//            get => self::cleanupGenres($value);
            }

    )
    {

    }

    public string $snippet { get => mb_substr($this->description, 0, 60); }

    private function addRelations(array $relations): array {
    {
        foreach ($relations as $relation) {
            if ($relation->area??null) {
                $this->countries = array_merge($this->countries, $relation->area->{'iso-3166-1-codes'}??[]);
            }
        }
        return $relations;
    }

    }

    private function cleanupGenres(array $genres): array
    {
        $x = [];
        foreach ($genres as $genre) {
            $x[] = $genre->name;
        }
        if (count($x) > 0) {
//            dd($x, $this->id);
        }
        return $x;
        dd($genres);
        $s = u($s)->lower()->title($s)->wordwrap(32)->split("\n")[0]->toString();
        return $s;
    }
    private function cleanup(string $s): string
    {
        // or markdown?
        $s = strip_tags($s);
        $s = trim($s, "'");
        return $s;
    }


}
