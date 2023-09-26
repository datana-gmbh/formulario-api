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

namespace Datana\Formulario\Api;

/**
 * @author Oskar Stark <oskar.stark@googlemail.de>
 */
interface ChatProtocolApiInterface
{
    /**
     * @param array<mixed> $conversation
     */
    public function save(string $aktenzeichen, string $conversationId, array $conversation, \DateTimeInterface $createdAt): bool;
}
