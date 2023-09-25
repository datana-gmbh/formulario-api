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
 * @author Oskar Stark <oskar.stark@googlemail.de>
 */
interface AktenzeichenApiInterface
{
    public function new(): string;
}
