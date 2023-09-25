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

use OskarStark\Value\TrimmedNonEmptyString;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class NullSystemEventLogApi implements SystemEventLogApiInterface
{
    public function log(string $key, string $info, \DateTimeInterface $timestamp, string $creator, ?array $context = null, ?string $ttl = null): bool
    {
        TrimmedNonEmptyString::fromString($key);
        TrimmedNonEmptyString::fromString($info);
        TrimmedNonEmptyString::fromString($creator);

        if (null !== $ttl) {
            TrimmedNonEmptyString::fromString($ttl);
        }

        return true;
    }
}
