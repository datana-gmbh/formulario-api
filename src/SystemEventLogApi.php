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
final class SystemEventLogApi implements SystemEventLogApiInterface
{
    private DatapoolClient $client;
    private LoggerInterface $logger;

    public function __construct(DatapoolClient $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    public function log(string $key, string $info, \DateTimeInterface $timestamp, string $creator, ?array $context = null, ?string $ttl = null): bool
    {
        $values = [
            'key' => TrimmedNonEmptyString::fromString($key, '"key" must not be empty.')->toString(),
            'info' => TrimmedNonEmptyString::fromString($info, '"info" must not be empty.')->toString(),
            'timestamp' => $timestamp->format('Y-m-d H:i:s'),
            'creator' => TrimmedNonEmptyString::fromString($creator, '"creator" must not be empty.')->toString(),
        ];

        if (null !== $ttl) {
            $ttl = TrimmedNonEmptyString::fromString(
                $ttl,
                'If provided, value of "ttl" must not be empty, provide "null" instead.',
            );

            $values['keepUntil'] = \Safe\date('Y-m-d H:i:s', \Safe\strtotime($ttl->toString()));
        }

        if (null !== $context) {
            $values['context'] = $context;
        }

        $this->logger->debug('Log to SystemEventLog', $values);

        try {
            $response = $this->client->request(
                'POST',
                '/api/system-event-log',
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
