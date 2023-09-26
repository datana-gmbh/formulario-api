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
use Webmozart\Assert\Assert;

final class StatisticsApi implements StatisticsApiInterface
{
    private FormularioClient $client;
    private LoggerInterface $logger;

    public function __construct(FormularioClient $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    public function numberOfCockpitInvitationMailsSent(): int
    {
        try {
            $response = $this->client->request(
                'GET',
                '/api/customer-cockpit-invitations',
            );

            $array = $response->toArray();

            $this->logger->debug('Response', $array);

            Assert::keyExists($array, 'data');
            Assert::keyExists($array['data'], 0);
            Assert::keyExists($array['data'][0], 'cnt');

            return $array['data'][0]['cnt'];
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            throw $e;
        }
    }
}
