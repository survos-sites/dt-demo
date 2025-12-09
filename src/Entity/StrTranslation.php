<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Survos\BabelBundle\Entity\Base\StrTranslationBase;
use App\Repository\StrTranslationRepository;

#[ORM\Entity(repositoryClass: StrTranslationRepository::class)]
class StrTranslation extends StrTranslationBase {}