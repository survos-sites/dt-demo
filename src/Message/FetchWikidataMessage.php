<?php

namespace App\Message;

final class FetchWikidataMessage
{
     public function __construct(private string $wikidataId)
     {
     }

    public function getWikidataId(): string
    {
        return $this->wikidataId;
    }

}
