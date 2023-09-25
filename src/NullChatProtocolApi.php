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

use Webmozart\Assert\Assert;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class NullChatProtocolApi implements ChatProtocolApiInterface
{
    /**
     * @param array<mixed> $conversation
     */
    public function save(string $aktenzeichen, string $conversationId, array $conversation, \DateTimeInterface $createdAt): bool
    {
        Assert::stringNotEmpty($aktenzeichen);
        Assert::stringNotEmpty($conversationId);
        Assert::notEmpty($conversation);

        return true;
    }
}
