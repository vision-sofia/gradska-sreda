<?php

namespace App\Doctrine\ValueObject;

class IntRange
{
    private ?int $start;
    private ?int $end;

    public function __construct(?int $start, ?int $end)
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

    public static function fromString(string $string): self
    {
        if ($string === 'empty') {
            return new self(null, null);
        }

        $clean = strtr($string, ['[' => '', ']' => '', '(' => '', ')' => '']);
        [$start, $end] = explode(',', $clean);

        return new self(
            empty($start) ? null : (int) $start,
            empty($end) ? null : (int) $end,
        );
    }

    public function __toString(): string
    {
        return self::toString($this);
    }
}
