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
interface SystemEventLogApiInterface
{
    /**
     * @param null|array<mixed> $context
     * @param null|string       $ttl     "time to live" in strtotime format, from now on, use "null" to keep the log entry forever
     */
    public function log(string $key, string $info, \DateTimeInterface $timestamp, string $creator, ?array $context = null, ?string $ttl = null): bool;
}
