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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class ChatProtocolApi implements ChatProtocolApiInterface
{
    private DatapoolClient $client;
    private LoggerInterface $logger;

    public function __construct(DatapoolClient $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param array<mixed> $conversation
     */
    public function save(string $aktenzeichen, string $conversationId, array $conversation, \DateTimeInterface $createdAt): bool
    {
        $values = [
            'aktenzeichen' => TrimmedNonEmptyString::fromString($aktenzeichen, '"aktenzeichen" must not be empty.')->toString(),
            'conversationId' => TrimmedNonEmptyString::fromString($conversationId, '"conversationId" must not be empty.')->toString(),
            'conversation' => $conversation,
            'createdAt' => $createdAt->format('Y-m-d H:i:s'),
        ];

        $this->logger->debug('Save ChatProtocol', $values);

        try {
            $response = $this->client->request(
                'POST',
                '/api/chat-protocol',
                [
                    'json' => $values,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            if (!\in_array($response->getStatusCode(), [200, 201], true)) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
