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

use App\Tests\Fixture\Formulario\Response\DateneingabeResponse;
use App\Tests\Fixture\Formulario\Response\ReminderResponse;
use Datana\Formulario\Api\Domain\Value\Dateneingabe;
use Datana\Formulario\Api\Domain\Value\Type;
use Datana\Formulario\Api\Tests\Util\Helper;
use Datana\Mandantencockpit\Contracts\Notification\Enum\Target;
use Datana\Mandantencockpit\Contracts\Notification\Value\TargetId;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use function Safe\date;
use function Safe\strtotime;

final class DateneingabeTest extends TestCase
{
    use Helper;

    /**
     * @test
     */
    public function state(): void
    {
        $value = DateneingabeResponse::create([
            'state' => $state = self::faker()->word(),
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSame($state, $dateneingabe->state);
    }

    /**
     * @test
     */
    public function stateWhenKeyNotSet(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['state']);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function stateBlankOrEmpty(string $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        $value = DateneingabeResponse::create([
            'state' => $value,
        ]);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function typeLegacy(): void
    {
        $type = Type::LEGACY_VWV;

        $value = DateneingabeResponse::create([
            'type' => $type->value,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertTrue($type->equals(Type::from($dateneingabe->type)));
    }

    /**
     * @test
     */
    public function typeWhenKeyNotSet(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['type']);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function typeBlankOrEmpty(string $value): void
    {
        $value = DateneingabeResponse::create([
            'type' => $value,
        ]);

        self::expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     *
     * @dataProvider invalidTypeProvider
     */
    public function typeInvalidValue(string $value): void
    {
        $value = DateneingabeResponse::create([
            'type' => $value,
        ]);

        self::expectException(\ValueError::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @return iterable<string, string[]>
     */
    public static function invalidTypeProvider(): iterable
    {
        yield 'Unknown Type' => [
            self::faker()->word(),
        ];
    }

    /**
     * @test
     */
    public function urlSetIfTypeRegular(): void
    {
        $value = DateneingabeResponse::create([
            'type' => Type::REGULAR->value,
            'token' => $token = self::faker()->md5(),
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertStringContainsString($token, $dateneingabe->url);
    }

    /**
     * @test
     */
    public function keyLegacyLinkNotSetAndTypeNotRegular(): void
    {
        $value = DateneingabeResponse::create([
            'type' => Type::LEGACY_VWV->value,
        ]);
        unset($value['legacy_link']);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function urlLegacyLinkIsSetAndTypeIsNotRegular(): void
    {
        $value = DateneingabeResponse::create([
            'type' => Type::LEGACY_VWV->value,
            'legacy_link' => $url = self::faker()->url(),
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSame($url, $dateneingabe->url);
    }

    /**
     * @test
     */
    public function urlLegacyLinkIsNullAndTypeIsNotRegular(): void
    {
        $value = DateneingabeResponse::create([
            'type' => Type::LEGACY_VWV->value,
            'legacy_link' => null,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertNull($dateneingabe->url);
    }

    /**
     * @test
     */
    public function urlLegacyLinkIsEmptyStringAndTypeIsNotRegular(): void
    {
        $value = DateneingabeResponse::create([
            'type' => Type::LEGACY_VWV->value,
            'legacy_link' => '',
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertNull($dateneingabe->url);
    }

    /**
     * @test
     */
    public function urlLegacyLinkIsUntrimmedEmptyStringAdTypeIsNotRegular(): void
    {
        $value = DateneingabeResponse::create([
            'type' => Type::LEGACY_VWV->value,
            'legacy_link' => ' ',
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertNull($dateneingabe->url);
    }

    /**
     * @test
     */
    public function notificationTargetId(): void
    {
        $value = DateneingabeResponse::create();

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertTrue($dateneingabe->notificationTargetId()->equals(TargetId::fromInt($value['id'])));
    }

    /**
     * @test
     */
    public function isDeadlineHard(): void
    {
        $value = DateneingabeResponse::create([
            'deadline_hard' => $deadlineHard = self::faker()->boolean(),
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSame($deadlineHard, $dateneingabe->isDeadlineHard());
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\NullProvider::null()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function deadlineHardInvalid(?string $value): void
    {
        $value = DateneingabeResponse::create([
            'deadline_hard' => $value,
        ]);

        self::expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function deadlineHardNotSet(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['deadline_hard']);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertFalse($dateneingabe->isDeadlineHard());
    }

    /**
     * @test
     */
    public function notificationTarget(): void
    {
        $value = DateneingabeResponse::create();

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertTrue($dateneingabe->notificationTarget()->equals(Target::DATENEINGABE));
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyIdIsMissing(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['id']);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyIdContainsNUll(): void
    {
        $value = DateneingabeResponse::create([
            'id' => null,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCanBeConstructedAndUsesValueFromKeyId(): void
    {
        $value = DateneingabeResponse::create([
            'id' => $id = self::faker()->numberBetween(1),
        ]);

        self::assertSame($id, Dateneingabe::fromArray($value)->id->toInt());
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyCaseReferenceIsMissing(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['case_reference']);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyCaseReferenceContainsNUll(): void
    {
        $value = DateneingabeResponse::create([
            'case_reference' => null,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCanBeConstructedAndUsesValueFromKeyCaseReference(): void
    {
        $value = DateneingabeResponse::create([
            'case_reference' => $aktenzeichen = self::faker()->aktenzeichenString(),
        ]);

        self::assertSame($aktenzeichen, Dateneingabe::fromArray($value)->aktenzeichen->toString());
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeyAbortedAtContainsTimestamp(): void
    {
        $time = time();
        $expectedDateTime = new DateTimeImmutable(date('Y-m-d H:i:s', $time));

        $value = DateneingabeResponse::create([
            'aborted_at' => $time,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSameDateTime($expectedDateTime, $dateneingabe->abortedAt);
        self::assertTrue($dateneingabe->isAborted());
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeyAbortedAtContainsNull(): void
    {
        $value = DateneingabeResponse::create([
            'aborted_at' => null,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);
        self::assertNull($dateneingabe->abortedAt);
        self::assertFalse($dateneingabe->isAborted());
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::arbitrary()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function invalidDeadlineAt(string $value): void
    {
        $value = DateneingabeResponse::create([
            'aborted_at' => $value,
        ]);

        self::expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeyDeadlineAtContainsNull(): void
    {
        $value = DateneingabeResponse::create([
            'deadline_at' => null,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertNull($dateneingabe->deadlineAt);
        self::assertFalse($dateneingabe->hasDeadline());
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeyDeadlineAtContainsTimestamp(): void
    {
        $time = time();
        $expectedDateTime = new DateTimeImmutable(date('Y-m-d H:i:s', $time));

        $value = DateneingabeResponse::create([
            'deadline_at' => $time,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSameDateTime($expectedDateTime, Dateneingabe::fromArray($value)->deadlineAt);
        self::assertTrue($dateneingabe->hasDeadline());
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeySummaryDownloadUrlIsMissing(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['summary_download_url']);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeySummaryDownloadUrlContainsNull(): void
    {
        $value = DateneingabeResponse::create([
            'summary_download_url' => null,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertNull($dateneingabe->summaryDownloadUrl);
        self::assertFalse($dateneingabe->hasSummaryDownloadUrl());
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeySummaryDownloadUrlContainsString(): void
    {
        $value = DateneingabeResponse::create([
            'summary_download_url' => $url = self::faker()->url(),
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSame($url, Dateneingabe::fromArray($value)->summaryDownloadUrl);
        self::assertTrue($dateneingabe->hasSummaryDownloadUrl());
    }

    /**
     * @test
     *
     * @dataProvider fromArrayProvider
     *
     * @param array{0: string, 1: array} $value
     */
    public function fromArray(string $expected, array $value): void
    {
        self::assertSame(
            $expected,
            Dateneingabe::fromArray($value)->stepsAsString(),
        );
    }

    /**
     * @return iterable<string, array{0: string, 1: array}>
     */
    public static function fromArrayProvider(): iterable
    {
        $faker = self::faker();

        yield 'Complete => finished_at isset' => [
            '2 von 2 Schritten erledigt',
            DateneingabeResponse::create([
                'finished_at' => time(),
                'steps' => [
                    ['state' => 'finished'],
                    ['state' => 'current'],
                ],
            ], 2),
        ];

        yield 'Incomplete => finished_at is not set and step not finished plural' => [
            '1 von 2 Schritten erledigt',
            DateneingabeResponse::create([
                'finished_at' => null,
                'steps' => [
                    ['state' => 'finished'],
                    ['state' => $faker->randomElement(['current', 'open'])],
                ],
            ], 2),
        ];

        yield 'Incomplete => finished_at is not set and step not finished singular' => [
            '0 von 1 Schritt erledigt',
            DateneingabeResponse::create([
                'finished_at' => null,
                'steps' => [
                    ['state' => $faker->randomElement(['current', 'open'])],
                ],
            ], 1),
        ];
    }

    /**
     * @test
     */
    public function fromStringThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray([]);
    }

    /**
     * @test
     *
     * @dataProvider showOpenLinkProvider
     */
    public function showOpenLink(bool $expected, array $value): void
    {
        self::assertSame(
            $expected,
            Dateneingabe::fromArray($value)->showOpenLink(),
        );
    }

    /**
     * @return iterable<string, array{0: bool, 1: array}>
     */
    public static function showOpenLinkProvider(): iterable
    {
        yield 'true => finished_at is null and simplified state is "todo"' => [
            true,
            DateneingabeResponse::create([
                'finished_at' => null,
                'state' => self::faker()->randomElement(['created', 'invitation_sent', 'noticed', 'opened', 'filing', 'document_upload']),
            ]),
        ];

        yield 'false => finished_at is older than 2 days' => [
            false,
            DateneingabeResponse::create([
                'finished_at' => strtotime('-3 days'),
            ]),
        ];

        yield 'true => finished_at is 2 days in future' => [
            true,
            DateneingabeResponse::create([
                'finished_at' => strtotime('+3 days'),
            ]),
        ];
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeyRemindersIsMissing(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['reminder']);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertFalse($dateneingabe->hasReminders());
        self::assertSame([], $dateneingabe->reminders());
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeyRemindersIsEmpty(): void
    {
        $value = DateneingabeResponse::create();
        $value['reminder'] = [];

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertFalse($dateneingabe->hasReminders());
        self::assertSame([], $dateneingabe->reminders());
    }

    /**
     * @test
     *
     * @dataProvider invalidReminderProvider
     */
    public function responseCanBeConstructedIfKeyRemindersIsNoArray(mixed $value): void
    {
        $value = DateneingabeResponse::create([
            'reminder' => $value,
        ]);

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertFalse($dateneingabe->hasReminders());
        self::assertSame([], $dateneingabe->reminders());
    }

    /**
     * @return \Generator<string, array<int, null|bool|int|object|string>>
     */
    public static function invalidReminderProvider(): iterable
    {
        $faker = self::faker();

        yield 'empty string' => [''];
        yield 'string' => [$faker->word()];
        yield 'null' => [null];
        yield 'object' => [new \stdClass()];
        yield 'int' => [$faker->randomNumber()];
        yield 'bool' => [$faker->boolean()];
    }

    /**
     * @test
     */
    public function responseCanBeConstructedWithReminders(): void
    {
        $value = DateneingabeResponse::create();
        $value['reminder'] = [
            ReminderResponse::create(),
            ReminderResponse::create(),
        ];

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertTrue($dateneingabe->hasReminders());
        self::assertCount(2, $dateneingabe->reminders());
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyRemindersExistsButKeyIdIsMissing(): void
    {
        $value = DateneingabeResponse::create();

        $reminder = ReminderResponse::create([]);
        unset($reminder['id']);

        $value['reminder'] = [
            $reminder,
        ];

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyRemindersExistsButKeySenderIsMissing(): void
    {
        $value = DateneingabeResponse::create();

        $reminder = ReminderResponse::create([]);
        unset($reminder['sender']);

        $value['reminder'] = [
            $reminder,
        ];

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCanBeConstructedIfKeyRemindersExistsAndKeyIdIsNull(): void
    {
        $value = DateneingabeResponse::create();

        $reminder = ReminderResponse::create([
            'id' => null,
        ]);

        $value['reminder'] = [
            $reminder,
        ];

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertTrue($dateneingabe->hasReminders());
        self::assertCount(1, $dateneingabe->reminders());
        self::assertNull($dateneingabe->reminders()[0]->id());
    }

    /**
     * @test
     *
     * @dataProvider openProvider
     */
    public function simplifiedStateTodo(string $state): void
    {
        $dateneingabe = Dateneingabe::fromArray(DateneingabeResponse::create([
            'state' => $state,
        ]));

        self::assertSame('todo', $dateneingabe->simplifiedState());
    }

    /**
     * @test
     *
     * @dataProvider doneProvider
     */
    public function simplifiedStateDone(string $state): void
    {
        $dateneingabe = Dateneingabe::fromArray(DateneingabeResponse::create([
            'state' => $state,
        ]));

        self::assertSame('done', $dateneingabe->simplifiedState());
    }

    /**
     * @test
     *
     * @dataProvider notDoneProvider
     */
    public function simplifiedState(string $state): void
    {
        $dateneingabe = Dateneingabe::fromArray(DateneingabeResponse::create([
            'state' => $state,
        ]));

        self::assertSame('todo_closed', $dateneingabe->simplifiedState());
    }

    /**
     * @return iterable<string, string[]>
     */
    public static function openProvider(): iterable
    {
        yield 'created' => ['created'];
        yield 'invitation_sent' => ['invitation_sent'];
        yield 'noticed' => ['noticed'];
        yield 'opened' => ['opened'];
        yield 'filing' => ['filing'];
        yield 'document_upload' => ['document_upload'];
    }

    /**
     * @return iterable<string, string[]>
     */
    public static function doneProvider(): iterable
    {
        yield 'filed' => ['filed'];
        yield 'exported' => ['exported'];
        yield 'export_error' => ['export_error'];
    }

    /**
     * @return iterable<string, string[]>
     */
    public static function notDoneProvider(): iterable
    {
        yield 'aborted' => ['aborted'];
        yield 'not known state' => ['not known state'];
    }

    /**
     * @test
     */
    public function latestReminderNumberWithOneEntry(): void
    {
        $faker = self::faker();

        $value = DateneingabeResponse::create();
        $value['reminder'] = [
            ReminderResponse::create(['id' => $id = $faker->numberBetween(1)]),
        ];

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSame(1, $dateneingabe->latestReminderNumber());
        self::assertSame($id, $dateneingabe->reminders()[0]->id());
    }

    /**
     * @test
     */
    public function latestReminderNumberWithTwoEntries(): void
    {
        $faker = self::faker();

        $value = DateneingabeResponse::create();
        $value['reminder'] = [
            ReminderResponse::create(['id' => $faker->numberBetween(1)]),
            ReminderResponse::create(['id' => $id2 = $faker->numberBetween(1)]),
        ];

        $dateneingabe = Dateneingabe::fromArray($value);

        self::assertSame(2, $dateneingabe->latestReminderNumber());
        self::assertSame($id2, $dateneingabe->reminders()[1]->id());
    }

    /**
     * @test
     */
    public function latestReminderNumberThrowsInvalidArgumentExceptionIfNoRemindersAvailable(): void
    {
        $value = DateneingabeResponse::create();
        $value['reminder'] = [];

        $dateneingabe = Dateneingabe::fromArray($value);

        self::expectException(\InvalidArgumentException::class);

        $dateneingabe->latestReminderNumber();
    }

    /**
     * @test
     *
     * @dataProvider showSummaryDownloadLinkProvider
     */
    public function showSummaryDownload(bool $expected, ?string $state, ?string $url): void
    {
        $dateneingabe = Dateneingabe::fromArray(DateneingabeResponse::create([
            'state' => $state,
            'summary_download_url' => $url,
        ]));

        self::assertSame($expected, $dateneingabe->showSummaryDownloadLink());
    }

    /**
     * @return iterable<int, array{0: bool, 1: null|string, 2: null|string}>
     */
    public static function showSummaryDownloadLinkProvider(): iterable
    {
        $faker = self::faker();

        $randomState = $faker->word();
        $url = $faker->url();

        yield [false, 'created', $url];
        yield [false, 'invitation_sent', $url];
        yield [false, 'noticed', $url];
        yield [false, 'opened', $url];
        yield [false, 'filing', $url];
        yield [false, 'document_upload', $url];
        yield [true, 'filed', $url];
        yield [true, 'exported', $url];
        yield [true, 'export_error', $url];
        yield [true, 'aborted', $url];
        yield [true, $randomState, $url];

        yield [false, 'created', null];
        yield [false, 'invitation_sent', null];
        yield [false, 'noticed', null];
        yield [false, 'opened', null];
        yield [false, 'filing', null];
        yield [false, 'document_upload', null];
        yield [false, 'filed', null];
        yield [false, 'exported', null];
        yield [false, 'export_error', null];
        yield [false, 'aborted', null];
        yield [false, $randomState, null];
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyReferenceIsMissing(): void
    {
        $value = DateneingabeResponse::create();
        unset($value['reference']);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCannotBeConstructedIfKeyReferenceContainsNUll(): void
    {
        $value = DateneingabeResponse::create([
            'reference' => null,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        Dateneingabe::fromArray($value);
    }

    /**
     * @test
     */
    public function responseCanBeConstructedAndUsesValueFromKeyCaseReferenceIfValueIsString(): void
    {
        $value = DateneingabeResponse::create([
            'reference' => $reference = self::faker()->word(),
        ]);

        self::assertSame($reference, Dateneingabe::fromArray($value)->reference);
    }

    /**
     * @test
     */
    public function responseCanBeConstructedAndUsesValueFromKeyCaseReferenceIfValueIsInteger(): void
    {
        $value = DateneingabeResponse::create([
            'reference' => $reference = self::faker()->numberBetween(1, 9999),
        ]);

        self::assertSame((string) $reference, Dateneingabe::fromArray($value)->reference);
    }
}
