<?php

declare(strict_types=1);

/**
 * This file is part of Datapool-Api.
 *
 * (c) Datana GmbH <info@datana.rocks>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Datana\Datapool\Api\Tests\Unit\Domain\Value;

use Datana\Datapool\Api\Domain\Value\DatapoolId;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Datana\Datapool\Api\Domain\Value\DatapoolId
 */
final class DatapoolIdTest extends TestCase
{
    use Helper;

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::greaterThanZero()
     */
    public function fromIntGreaterThanZero(int $value): void
    {
        self::assertSame(
            $value,
            DatapoolId::fromInt($value)->toInt(),
        );
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::lessThanZero()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::zero()
     */
    public function fromIntThrowsException(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DatapoolId::fromInt($value);
    }
}
