<?php

declare(strict_types=1);

namespace Datana\Formulario\Api\Domain\Value;

use Datana\Formulario\Api\Exception\DateneingabeNotFound;
use Webmozart\Assert\Assert;

final class DateneingabenCollection implements \Countable
{
    /**
     * @var Dateneingabe[]
     */
    private array $values = [];

    private function __construct(array $response)
    {
        Assert::keyExists($response, 'data');

        foreach ($response['data'] as $dateneingabe) {
            $this->values[] = Dateneingabe::fromArray($dateneingabe);
        }
    }

    public static function fromArray(array $response): self
    {
        return new self($response);
    }

    /**
     * @return Dateneingabe[]
     */
    public function values(): array
    {
        return $this->values;
    }

    public function count(): int
    {
        return \count($this->values);
    }

    public function empty(): bool
    {
        return [] === $this->values;
    }

    public function byId(DateneingabeId $id): Dateneingabe
    {
        if ($this->empty()) {
            throw DateneingabeNotFound::withDateneingabeId($id);
        }

        foreach ($this->values as $value) {
            if ($value->id->equals($id)) {
                return $value;
            }
        }

        throw DateneingabeNotFound::withDateneingabeId($id);
    }

    public function latest(): Dateneingabe
    {
        if ($this->empty()) {
            throw new \RuntimeException('DateneingabeCollection has no values!');
        }

        return $this->values[0];
    }

    /**
     * @return Dateneingabe[]
     */
    public function open(): array
    {
        return array_filter(
            $this->values,
            static fn (Dateneingabe $dateneingabe) => $dateneingabe->simplifiedState() === 'todo',
        );
    }
}
