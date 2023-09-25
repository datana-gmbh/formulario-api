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

namespace Datana\Datapool\Api\Tests\Unit\Domain\Value;

use Datana\Datapool\Api\Domain\Value\Token;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @covers \Datana\Datapool\Api\Domain\Value\Token
 */
final class TokenTest extends TestCase
{
    use Helper;

    /**
     * @test
     *
     * @dataProvider fromStringProvider
     */
    public function fromString(string $value, string $expected): void
    {
        self::assertSame(
            $expected,
            Token::fromString($value)->toString(),
        );
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public static function fromStringProvider(): iterable
    {
        $token = self::faker()->sha256();

        yield 'trimmed' => [$token, $token];
        yield 'untrimmed-start' => [' '.$token, $token];
        yield 'untrimmed-end' => [$token.' ', $token];
        yield 'untrimmed-start-and-end' => [' '.$token.' ', $token];
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function fromStringThrowsException(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Token::fromString($value);
    }

    /**
     * @test
     */
    public function fromResponseThrowsExceptionIfKeyTokenDoesNotExist(): void
    {
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('toArray')->willReturn([]);

        $this->expectException(\InvalidArgumentException::class);

        Token::fromResponse($httpResponse);
    }

    /**
     * @test
     */
    public function fromResponseWithValidResponse(): void
    {
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('toArray')->willReturn([
            'token' => $token = self::faker()->sha256(),
        ]);

        self::assertSame(
            $token,
            Token::fromResponse($httpResponse)->toString(),
        );
    }
}
