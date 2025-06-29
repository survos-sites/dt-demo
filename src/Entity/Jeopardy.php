<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\JeopardyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\MeiliAdminBundle\Api\Filter\FacetsFieldSearchFilter;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Attribute\Groups;
use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: JeopardyRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()],
//    shortName: 'jeopardy',
    normalizationContext: [
        'groups' => ['jeopardy.read'],
    ]
)]
#[ApiFilter(FacetsFieldSearchFilter::class, properties: ['category', 'value', 'round', 'airDate','show'])]
#[Groups(['jeopardy.read'])]
class Jeopardy
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        #[Map(if: false)] // no id in the question.  We could use the idx, or a hash or slug of the question.
        private(set) ?int $id = null,

        #[ORM\Column(type: Types::TEXT)]
        #[Map(transform: [self::class, 'cleanup'])]
        private(set) string     $question  {
            set => self::cleanup($value);
        },

        #[ORM\Column(type: Types::DATE_MUTABLE)]
        #[Map(source: 'air_date')] // , transform: [\DateTimeImmutable::class, 'createFromFormat'])]
        public \DateTime|string $airDate {
            set => \DateTime::createFromFormat('Y-m-d', $value);
            get => $this->airDate->format('Y-m-d');
        },

        #[ORM\Column(length: 255)]
        public ?string    $answer = null,

        #[ORM\Column(length: 255)]
        private(set) string $category {
            set => self::cleanupCategory($value);
        },

        #[ORM\Column(type: Types::INTEGER, nullable: true)]
        private(set) int|string|null $value {
            set => $value ? (int)str_replace('$', '', $value): 0;
        },

        #[ORM\Column(length: 255)]
        private(set) ?string $round = null ,

        #[ORM\Column]
        #[Map(source: 'show_number')]
        private(set) ?int      $show = null,
    )
    {

    }

    private function cleanupCategory(string $s): string
    {
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
