<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\OfficialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\ApiGrid\Api\Filter\FacetsFieldSearchFilter;
use Survos\ApiGrid\Api\Filter\MultiFieldSearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfficialRepository::class)]
#[ApiResource]
#[ApiFilter(OrderFilter::class, properties: ['id',
    'firstName',
    'lastName',
    'officialName',
    'gender',
    'currentParty',
    'birthday'
])]
#[ApiFilter(MultiFieldSearchFilter::class, properties: ['firstName', 'lastName', 'officialName'])]
#[ApiFilter(FacetsFieldSearchFilter::class, properties: ['gender', 'currentParty'])]
class Official
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 32)]
    private ?string $lastName = null;

    #[ORM\Column(length: 48)]
    private ?string $officialName = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birthday = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $gender = null;

    #[ORM\OneToMany(mappedBy: 'offical', targetEntity: Term::class, orphanRemoval: true)]
    private Collection $terms;

    #[ORM\Column(length: 12, nullable: true)]
    #[Assert\Length(min: 0, max: 12)]
    private ?string $currentParty = null;

    public function __construct()
    {
        $this->terms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getOfficialName(): ?string
    {
        return $this->officialName;
    }

    public function setOfficialName(string $officialName): static
    {
        $this->officialName = $officialName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeImmutable $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Term>
     */
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    public function addTerm(Term $term): static
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->setOffical($this);
        }

        return $this;
    }

    public function removeTerm(Term $term): static
    {
        if ($this->terms->removeElement($term)) {
            // set the owning side to null (unless already changed)
            if ($term->getOffical() === $this) {
                $term->setOffical(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getOfficialName();
    }

    public function getCurrentParty(): ?string
    {
        return $this->currentParty;
    }

    public function setCurrentParty(?string $currentParty): static
    {
        $this->currentParty = $currentParty;

        return $this;
    }
}
