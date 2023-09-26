<?php

declare(strict_types=1);

/**
 * This file is part of datana-gmbh/formulario-api.
 *
 * (c) Datana GmbH <info@datana.rocks>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Datana\Formulario\Api\Tests\Unit\Domain\Value;

use Datana\Formulario\Api\Domain\Value\DateneingabeId;
use Datana\Formulario\Api\Tests\Util\Helper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Datana\Formulario\Api\Domain\Value\DateneingabeId
 */
final class DateneingabeIdTest extends TestCase
{
    use Helper;

    /**
     * @test
     */
    public function fromInt(): void
    {
        $id = self::faker()->numberBetween(1);

        self::assertSame(
            $id,
            DateneingabeId::fromInt($id)->toInt(),
        );
    }

    /**
     * @test
     */
    public function equalsReturnsTrue(): void
    {
        $id = self::faker()->dateneingabeIdInt();

        $dateneingabeId = DateneingabeId::fromInt($id);
        $other = DateneingabeId::fromInt($id);

        self::assertTrue($dateneingabeId->equals($other));
        self::assertTrue($other->equals($dateneingabeId));
    }

    /**
     * @test
     */
    public function equalsReturnsFalse(): void
    {
        $dateneingabeId = DateneingabeId::fromInt(1);
        $other = DateneingabeId::fromInt(2);

        self::assertFalse($dateneingabeId->equals($other));
        self::assertFalse($other->equals($dateneingabeId));
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::lessThanZero()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::zero()
     */
    public function fromIntThrowsInvalidArgumentException(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DateneingabeId::fromInt($value);
    }
}
