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

namespace Datana\Formulario\Api\Tests\Unit\Domain\Enum;

use Datana\Formulario\Api\Domain\Enum\Type;
use OskarStark\Enum\Test\EnumTestCase;

final class TypeTest extends EnumTestCase
{
    protected static function getClass(): string
    {
        return Type::class;
    }

    protected static function getNumberOfValues(): int
    {
        return 4;
    }
}
