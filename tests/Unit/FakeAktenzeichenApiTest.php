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

namespace Datana\Datapool\Api\Tests\Unit;

use Datana\Datapool\Api\FakeAktenzeichenApi;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Datana\Datapool\Api\FakeAktenzeichenApi
 */
final class FakeAktenzeichenApiTest extends TestCase
{
    use Helper;

    /**
     * @test
     */
    public function new(): void
    {
        $api = new FakeAktenzeichenApi();

        self::assertSame(
            '6GU5DCB',
            $api->new(),
        );
    }
}
