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

namespace Datana\Datapool\Api\Domain\Value;

use OskarStark\Value\TrimmedNonEmptyString;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class Token
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = TrimmedNonEmptyString::fromString($value)->toString();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        $values = $response->toArray();

        Assert::keyExists($values, 'token');

        return new self($values['token']);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
