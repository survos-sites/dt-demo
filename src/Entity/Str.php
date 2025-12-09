<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Survos\BabelBundle\Entity\Base\StrBase;
use App\Repository\StrRepository;

#[ORM\Entity(repositoryClass: StrRepository::class)]
class Str extends StrBase {}