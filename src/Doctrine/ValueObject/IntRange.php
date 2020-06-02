<?php

namespace App\Doctrine\ValueObject;

class IntRange
{
    private $start;
    private $end;

    public function __construct(int $start, int $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public static function toString(self $intRange): string
    {
        return sprintf('[%s,%s)', $intRange->getStart(), $intRange->getEnd());
    }

    public static function fromString($string): self
    {
        $clean = strtr($string, ['[' => '', ']' => '', '(' => '', ')' => '']);
        [$start, $end] = explode(',', $clean);

        return new self($start, $end);
    }

    public function __toString(): string
    {
        return self::toString($this);
    }
}
