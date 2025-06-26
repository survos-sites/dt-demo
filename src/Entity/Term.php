<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TermRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: TermRepository::class)]
//https://github.com/api-platform/api-platform/issues/2329?utm_source=chatgpt.com
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ]
)]
class Term
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
//    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'terms')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Official $official = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $stateAbbreviation = null;

    #[ORM\Column(length: 12, nullable: true)]
    #[Assert\Length(min: 0, max: 12)]
    private ?string $party = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfficial(): ?Official
    {
        return $this->official;
    }

    public function setOfficial(?Official $official): static
    {
        $this->official = $official;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStateAbbreviation(): ?string
    {
        return $this->stateAbbreviation;
    }

    public function setStateAbbreviation(?string $stateAbbreviation): static
    {
        $this->stateAbbreviation = $stateAbbreviation;

        return $this;
    }

    public function getParty(): ?string
    {
        return $this->party;
    }

    public function setParty(?string $party): static
    {
        $this->party = $party;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): static
    {
        $this->district = $district;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }
}
