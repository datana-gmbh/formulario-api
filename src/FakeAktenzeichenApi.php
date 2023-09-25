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

namespace Datana\Datapool\Api;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class FakeAktenzeichenApi implements AktenzeichenApiInterface
{
    public function new(): string
    {
        return '6GU5DCB';
    }
}
