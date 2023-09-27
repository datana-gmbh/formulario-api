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

namespace Datana\Formulario\Api\Tests\Unit\Domain\Value;

use Datana\Formulario\Api\Domain\Value\Reminder;
use Datana\Formulario\Api\Fixtures\Response\ReminderResponse;
use Datana\Formulario\Api\Tests\Util\Helper;
use Datana\Mandantencockpit\Contracts\Notification\Enum\Priority;
use Datana\Mandantencockpit\Contracts\Notification\Enum\Target;
use Datana\Mandantencockpit\Contracts\Notification\Value\TargetId;
use PHPUnit\Framework\TestCase;

final class ReminderTest extends TestCase
{
    use Helper;

    /**
     * @test
     */
    public function id(): void
    {
        $faker = self::faker();

        $response = ReminderResponse::create([
            'id' => $reminderId = $faker->biasedNumberBetween(1),
        ]);

        self::assertSame(
            $reminderId,
            Reminder::create($response, self::faker()->dateneingabeId())->id(),
        );
    }

    /**
     * @test
     */
    public function idKeyNotExists(): void
    {
        $response = ReminderResponse::create();
        unset($response['id']);

        self::expectException(\InvalidArgumentException::class);

        Reminder::create($response, self::faker()->dateneingabeId());
    }

    /**
     * @test
     */
    public function idIsString(): void
    {
        $faker = self::faker();

        $response = ReminderResponse::create([
            'id' => $faker->word(),
        ]);

        self::expectException(\InvalidArgumentException::class);

        Reminder::create($response, $faker->dateneingabeId());
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::lessThanZero()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::zero()
     */
    public function throwsInvalidArgumentExceptionIfIdIsZeorOrLessThanZero(int $value): void
    {
        $response = ReminderResponse::create([
            'id' => $value,
        ]);

        self::expectException(\InvalidArgumentException::class);

        Reminder::create($response, self::faker()->dateneingabeId());
    }

    /**
     * @test
     */
    public function time(): void
    {
        $faker = self::faker();

        $date = $faker->dateTime();

        $response = ReminderResponse::create([
            'time' => (int) $date->format('U'),
        ]);

        self::assertSameDateTime(
            $date,
            Reminder::create($response, $faker->dateneingabeId())->date(),
        );
    }

    /**
     * @test
     */
    public function timeKeyNotSet(): void
    {
        $response = ReminderResponse::create();
        unset($response['time']);

        $this->expectException(\InvalidArgumentException::class);

        Reminder::create($response, self::faker()->dateneingabeId())->date();
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::lessThanZero()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::zero()
     */
    public function throwsInvalidArgumentExceptionIfTimeIsZeroOrLessThanZero(int $value): void
    {
        $response = ReminderResponse::create([
            'time' => $value,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        Reminder::create($response, self::faker()->dateneingabeId())->date();
    }

    /**
     * @test
     */
    public function sender(): void
    {
        $response = ReminderResponse::create([
            'sender' => $sender = self::faker()->word(),
        ]);

        self::assertSame(
            $sender,
            Reminder::create($response, self::faker()->dateneingabeId())->sender(),
        );
    }

    /**
     * @test
     */
    public function senderKeyNotExists(): void
    {
        $response = ReminderResponse::create();
        unset($response['sender']);

        self::expectException(\InvalidArgumentException::class);

        Reminder::create($response, self::faker()->dateneingabeId());
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function throwsInvalidArgumentExceptionIfSenderIsEmptyOrBlank(string $value): void
    {
        $response = ReminderResponse::create([
            'sender' => $value,
        ]);

        self::expectException(\InvalidArgumentException::class);

        Reminder::create($response, self::faker()->dateneingabeId());
    }

    /**
     * @test
     */
    public function notificationTargetId(): void
    {
        $dateneingabeId = self::faker()->dateneingabeId();

        $reminder = Reminder::create(
            ReminderResponse::create(['id' => self::faker()->numberBetween(1)]),
            $dateneingabeId,
        );

        $expected = TargetId::fromString(sprintf(
            '%s-%s',
            $dateneingabeId->toInt(),
            $reminder->id(),
        ));

        self::assertTrue($reminder->notificationTargetId()->equals($expected));
    }

    /**
     * @test
     */
    public function notificationTargetIdIfIdIsNull(): void
    {
        $dateneingabeId = self::faker()->dateneingabeId();

        $reminder = Reminder::create(
            ReminderResponse::create(['id' => null, 'time' => $time = (int) self::faker()->dateTime()->format('U')]),
            $dateneingabeId,
        );

        $expected = TargetId::fromString(sprintf(
            '%s-%s',
            $dateneingabeId->toInt(),
            $time,
        ));

        self::assertTrue($reminder->notificationTargetId()->equals($expected));
    }

    /**
     * @test
     */
    public function notificationTarget(): void
    {
        $reminder = Reminder::create(ReminderResponse::create(), self::faker()->dateneingabeId());

        self::assertTrue(Target::DATENEINGABE_REMINDER->equals($reminder->notificationTarget()));
    }

    /**
     * @test
     */
    public function notificationPriority(): void
    {
        $reminder = Reminder::create(ReminderResponse::create(), self::faker()->dateneingabeId());

        self::assertTrue(Priority::HIGH->equals($reminder->notificationPriority()));
    }
}
