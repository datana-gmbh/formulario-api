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

final class KtAktenInfoResponse
{
    public ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getId(): int
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'id');
        Assert::integer($response['id']);

        return $response['id'];
    }

    public function getUrl(): ?string
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'url');
        Assert::string($response['url']);

        return $response['url'];
    }

    public function getInstance(): ?string
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'instance');
        Assert::string($response['instance']);

        return $response['instance'];
    }

    public function getGroup(): ?string
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'group');
        Assert::string($response['group']);

        return $response['group'];
    }

    /**
     * @return array{'id': int, 'url': string, 'instance': string, 'group': string}
     */
    public function toArray(): array
    {
        return $this->response->toArray();
    }
}
