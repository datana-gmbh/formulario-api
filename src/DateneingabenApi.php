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

use Datana\Formulario\Api\Domain\Value\Dateneingabe;
use Datana\Formulario\Api\Domain\Value\DateneingabeId;
use Datana\Formulario\Api\Domain\Value\DateneingabenCollection;
use Datana\Formulario\Api\Exception\DateneingabeNotFound;
use Datana\Formulario\Api\Exception\NonUniqueResult;
use OskarStark\Value\TrimmedNonEmptyString;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class DateneingabenApi implements DateneingabenApiInterface
{
    private FormularioClient $client;
    private LoggerInterface $logger;

    public function __construct(FormularioClient $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    public function byAktenzeichen(string $aktenzeichen): DateneingabenCollection
    {
        $aktenzeichen = TrimmedNonEmptyString::fromString($aktenzeichen, '$aktenzeichen must not be an empty string');

        try {
            $response = $this->client->request(
                'GET',
                '/api/data-enquiries',
                [
                    'query' => [
                        'sort' => [
                            [
                                'property' => 'created_at',
                                'order' => 'desc',
                            ],
                        ],
                        'filter' => [
                            [
                                'property' => 'case_reference',
                                'expression' => '=',
                                'value' => $aktenzeichen->toString(),
                            ],
                            [
                                'property' => 'state',
                                'expression' => '!=',
                                'value' => 'aborted',
                            ],
                        ],
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray());

            return DateneingabenCollection::fromArray($response->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function byId(DateneingabeId $id): Dateneingabe
    {
        try {
            $response = $this->client->request(
                'GET',
                '/api/data-enquiries',
                [
                    'query' => [
                        'filter' => [
                            [
                                'property' => 'id',
                                'expression' => '=',
                                'value' => $id->toInt(),
                            ],
                        ],
                    ],
                ],
            );

            $this->logger->info('Got Dateneingaben from Formulario and persisted them in cache');
            $this->logger->debug('Response', $response->toArray());

            $collection = DateneingabenCollection::fromArray($response->toArray());

            if ($collection->empty()) {
                throw DateneingabeNotFound::withDateneingabeId($id);
            }

            if ($collection->count() !== 1) {
                throw NonUniqueResult::withDateneingabeId($id);
            }

            return $collection->latest();
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
