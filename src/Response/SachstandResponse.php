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

use OskarStark\Value\TrimmedNonEmptyString;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

final class SachstandResponse
{
    public ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getHeadline(): string
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'headline');

        return TrimmedNonEmptyString::fromString($response['headline'])->toString();
    }

    public function getParagraphs(): array
    {
        $response = $this->toArray();

        Assert::notEmpty($response);
        Assert::keyExists($response, 'paragraphs');
        Assert::isArray($response['paragraphs']);

        return $response['paragraphs'];
    }

    /**
     * @return array{'headline': string, 'paragraphs': array<int, string>}
     */
    public function toArray(): array
    {
        return $this->response->toArray();
    }
}
