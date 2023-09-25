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

namespace Datana\Datapool\Api\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

final class ETerminInfoResponse
{
    public ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getServiceId(): ?string
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'service_id');
        Assert::nullOrString($response['service_id']);

        return $response['service_id'];
    }

    public function getServiceUrl(): ?string
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'service_url');
        Assert::nullOrString($response['service_url']);

        return $response['service_url'];
    }

    /**
     * @return array{'service_id': null|string, 'service_url': null|string}
     */
    public function toArray(): array
    {
        return $this->response->toArray();
    }
}
