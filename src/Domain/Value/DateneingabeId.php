<?php

declare(strict_types=1);

namespace Datana\Formulario\Api\Domain\Value;

use App\External\Value\Traits\JustDont;
use Webmozart\Assert\Assert;

final readonly class DateneingabeId
{
    private int $id;

    private function __construct(
        private int $value,
    ) {
        Assert::greaterThan($value, 0);
        $this->id = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $other->toInt() === $this->id;
    }

    public function toInt(): int
    {
        return $this->id;
    }
}
