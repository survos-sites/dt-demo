<?php
//
//// src/Entity/ProcessedMessage.php
//
namespace App\Entity;
//
use Zenstruck\Messenger\Monitor\History\Model\ProcessedMessage as BaseProcessedMessage;
use Doctrine\ORM\Mapping as ORM;
//
#[ORM\Entity(readOnly: true)]
#[ORM\Table('processed_messages')]
class ProcessedMessage extends BaseProcessedMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column]
    private ?int $id = null;

    public function id(): ?int
    {
        return $this->id;
    }
}
