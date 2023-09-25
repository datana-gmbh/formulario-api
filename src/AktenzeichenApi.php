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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class AktenzeichenApi implements AktenzeichenApiInterface
{
    private DatapoolClient $client;
    private LoggerInterface $logger;

    public function __construct(DatapoolClient $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    public function new(): string
    {
        $this->logger->debug('Request new Aktenzeichen');

        try {
            $response = $this->client->request(
                'POST',
                '/api/aktenzeichen/new',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'body' => \Safe\json_encode([]),
                ],
            );

            $result = $response->toArray();

            $this->logger->debug('Response', $result);

            return $result['aktenzeichen'];
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
