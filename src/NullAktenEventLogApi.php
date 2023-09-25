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
final class NullAktenEventLogApi implements AktenEventLogApiInterface
{
    public function log(string $key, string $aktenzeichen, string $info, \DateTimeInterface $timestamp, string $creator, ?string $text = null, ?string $html = null, ?array $context = null, ?string $foreignId = null, ?string $foreignType = null): bool
    {
        TrimmedNonEmptyString::fromString($key);
        TrimmedNonEmptyString::fromString($aktenzeichen);
        TrimmedNonEmptyString::fromString($info);
        TrimmedNonEmptyString::fromString($creator);

        if (null !== $text) {
            TrimmedNonEmptyString::fromString($text);
        }

        if (null !== $html) {
            TrimmedNonEmptyString::fromString($html);
        }

        if (null !== $foreignId) {
            TrimmedNonEmptyString::fromString($foreignId);
        }

        if (null !== $foreignType) {
            TrimmedNonEmptyString::fromString($foreignType);
        }

        return true;
    }
}
