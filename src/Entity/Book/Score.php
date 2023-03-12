<?php

namespace App\Entity\Book;

use InvalidArgumentException;

class Score
{
    public ?int $value = null;

    public function __construct(?int $value = null)
    {
        $this->assertValueIsValid($value);
        $this->value = $value;
    }

    public static function create(?int $value = null): self
    {
        return new self($value);
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    private function assertValueIsValid(?int $value)
    {
        if ($value === null) {
            return null;
        }
        if ($value > 5 || $value < 0) {
            throw new InvalidArgumentException('The score value needs to be between 0 and 5');
        }
    }
}
