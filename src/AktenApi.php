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

use Datana\Datapool\Api\Domain\Value\DatapoolId;
use Datana\Datapool\Api\Response\AktenResponse;
use Datana\Datapool\Api\Response\ETerminInfoResponse;
use Datana\Datapool\Api\Response\KtAktenInfoResponse;
use Datana\Datapool\Api\Response\SachstandResponse;
use Datana\Datapool\Api\Response\SimplyBookInfoResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;
use function Safe\sprintf;

final class AktenApi implements AktenApiInterface
{
    private DatapoolClient $client;
    private LoggerInterface $logger;

    public function __construct(DatapoolClient $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getByAktenzeichen(string $aktenzeichen): ResponseInterface
    {
        Assert::stringNotEmpty($aktenzeichen);

        try {
            $response = $this->client->request(
                'GET',
                '/api/akten',
                [
                    'query' => [
                        'aktenzeichen' => $aktenzeichen,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function getByFahrzeugIdentifikationsnummer(string $fahrzeugIdentifikationsnummer): ResponseInterface
    {
        Assert::stringNotEmpty($fahrzeugIdentifikationsnummer);

        try {
            $response = $this->client->request(
                'GET',
                '/api/akten',
                [
                    'query' => [
                        'fahrzeugIdentifikationsnummer' => $fahrzeugIdentifikationsnummer,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function getOneByAktenzeichen(string $aktenzeichen): AktenResponse
    {
        return new AktenResponse($this->getByAktenzeichen($aktenzeichen));
    }

    public function search(string $searchTerm): ResponseInterface
    {
        Assert::stringNotEmpty($searchTerm);

        try {
            $response = $this->client->request(
                'GET',
                '/api/akten',
                [
                    'query' => [
                        'searchstring' => $searchTerm,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function getById(DatapoolId $datapoolId): ResponseInterface
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('/api/akte/%s', $datapoolId->toInt()),
            );

            $this->logger->debug('Response', $response->toArray(false));

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function getKtAktenInfo(DatapoolId $datapoolId): KtAktenInfoResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('/api/akte/%s/kt-akte-info', $datapoolId->toInt()),
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new KtAktenInfoResponse($response);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function getETerminInfo(DatapoolId $datapoolId): ETerminInfoResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('/api/akte/%s/e-termin-info', $datapoolId->toInt()),
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new ETerminInfoResponse($response);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function getSimplyBookInfo(DatapoolId $datapoolId): SimplyBookInfoResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('/api/akte/%s/simply-book-info', $datapoolId->toInt()),
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new SimplyBookInfoResponse($response);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    public function getSachstand(DatapoolId $datapoolId): SachstandResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('/api/akte/%s/sachstand', $datapoolId->toInt()),
            );

            $this->logger->debug('Response', $response->toArray(false));

            return new SachstandResponse($response);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setValueNutzerMandantencockpit(DatapoolId $datapoolId, bool $value): bool
    {
        $this->logger->debug(sprintf(
            'Set value "Nutzer Mandantencockpit" to: %s',
            $value ? 'true' : 'false',
        ), [
            'datapool_id' => $datapoolId->toInt(),
        ]);

        try {
            $response = $this->client->request(
                'PUT',
                sprintf('/api/akte/%s/set-value-nutzer-mandantencockpit', $datapoolId->toInt()),
                [
                    'json' => [
                        'value' => $value,
                    ],
                ],
            );

            $this->logger->debug('Response', $response->toArray(false));

            if (200 !== $response->getStatusCode()) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
