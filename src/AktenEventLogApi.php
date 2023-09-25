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
final class AktenEventLogApi implements AktenEventLogApiInterface
{
    private DatapoolClient $client;
    private LoggerInterface $logger;

    public function __construct(DatapoolClient $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    public function log(string $key, string $aktenzeichen, string $info, \DateTimeInterface $timestamp, string $creator, ?string $text = null, ?string $html = null, ?array $context = null, ?string $foreignId = null, ?string $foreignType = null): bool
    {
        $values = [
            'key' => TrimmedNonEmptyString::fromString($key, '"key" must not be empty.')->toString(),
            'aktenzeichen' => TrimmedNonEmptyString::fromString($aktenzeichen, '"aktenzeichen" must not be empty.')->toString(),
            'info' => TrimmedNonEmptyString::fromString($info, '"info" must not be empty.')->toString(),
            'timestamp' => $timestamp->format('Y-m-d H:i:s'),
            'creator' => TrimmedNonEmptyString::fromString($creator, '"creator" must not be empty.')->toString(),
        ];

        if (null !== $text) {
            $values['text'] = TrimmedNonEmptyString::fromString(
                $text,
                'If provided, value of "text" must not be empty, provide "null" instead.',
            )->toString();
        }

        if (null !== $html) {
            $values['html'] = TrimmedNonEmptyString::fromString(
                $html,
                'If provided, value of "html" must not be empty, provide "null" instead.',
            )->toString();
        }

        if (null !== $context) {
            $values['context'] = $context;
        }

        if (null !== $foreignId) {
            $values['foreignId'] = TrimmedNonEmptyString::fromString(
                $foreignId,
                'If provided, value "foreignId" must not be empty, provide "null" instead.',
            )->toString();
        }

        if (null !== $foreignType) {
            $values['foreignType'] = TrimmedNonEmptyString::fromString(
                $foreignType,
                'If provided, value "foreignType" must not be empty, provide "null" instead.',
            )->toString();
        }

        $this->logger->debug('Log to AktenEventLog', $values);

        try {
            $response = $this->client->request(
                'POST',
                '/api/event-log',
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
