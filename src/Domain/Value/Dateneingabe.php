<?php

declare(strict_types=1);

namespace Datana\Formulario\Api\Domain\Value;

use App\Domain\Enum\Dateneingabe\Type;
use App\Domain\Enum\Notification\Priority;
use App\Domain\Enum\Notification\Target;
use App\Domain\Value\Akte\Aktenzeichen;
use App\Domain\Value\Notification\TargetId;
use App\External\Value\Dateneingabe\Reminder;
use App\External\Value\Traits\JustDont;
use App\Notification\Notifyable;
use OskarStark\Value\TrimmedNonEmptyString;
use Safe\DateTimeImmutable;
use Webmozart\Assert\Assert;
use function Safe\date;

final class Dateneingabe implements Notifyable
{
    public readonly DateneingabeId $id;
    public readonly Aktenzeichen $aktenzeichen;
    public readonly ?string $url;
    public readonly string $type;
    public readonly string $state;
    public readonly string $name;
    public readonly int $steps;
    public readonly int $finishedSteps;
    public readonly DateTimeImmutable $createdAt;
    public readonly ?DateTimeImmutable $finishedAt;
    public readonly ?DateTimeImmutable $deadlineAt;
    public readonly ?DateTimeImmutable $abortedAt;
    public readonly ?string $summaryDownloadUrl;
    public readonly string $reference;
    private readonly string $token;
    private readonly bool $deadlineHard;

    /**
     * @var Reminder[]
     */
    private array $reminders;

    /**
     * @param array<mixed> $value
     */
    private function __construct(
        private readonly array $value,
    ) {
        Assert::keyExists($value, 'id', 'Key "id" must exist.');
        Assert::integer($value['id'], 'Value of "id" must be an integer. Got: %s');
        $this->id = DateneingabeId::fromInt($value['id']);

        Assert::keyExists($value, 'case_reference', 'Key "case_reference" must exist.');
        Assert::string($value['case_reference'], 'Value of "case_reference" must be a string. Got: %s');
        $this->aktenzeichen = Aktenzeichen::fromString($value['case_reference']);

        Assert::keyExists($value, 'configuration', 'Key "configuration" must exist.');

        Assert::keyExists(
            $value['configuration'],
            'data_enquiry_title',
            'Key "data_enquiry_title" must exist.',
        );

        $this->name = TrimmedNonEmptyString::fromString(
            $value['configuration']['data_enquiry_title'],
            'Value of "data_enquiry_title" must be a non empty string. Got: %s',
        )->toString();

        Assert::keyExists($value, 'type', 'The key "type" does not exist.');

        $type = TrimmedNonEmptyString::fromString(
            $value['type'],
            'Value of "type" must be a non empty string. Got: %s',
        )->toString();

        $typeAsEnum = Type::from($type);
        $this->type = $type;

        Assert::keyExists($value, 'steps', 'Key "steps" must exist.');
        $this->steps = is_countable($value['steps']) ? \count($value['steps']) : 0;

        Assert::keyExists($value, 'state', 'The key "state" does not exist.');
        $this->state = TrimmedNonEmptyString::fromString(
            $value['state'],
            'Value of "state" must be a non empty string',
        )->toString();

        Assert::keyExists($value, 'finished_at', 'key "finished_at" must exist.');

        $finishedAt = null;

        if (null !== $value['finished_at']) {
            Assert::integer($value['finished_at'], 'Value of "finished_at" must be an integer. Got: %s');
            $finishedAt = new DateTimeImmutable(date('Y-m-d H:i:s', $value['finished_at']));
        }
        $this->finishedAt = $finishedAt;

        if (null !== $this->finishedAt) {
            $finishedSteps = $this->steps;
        } else {
            $finishedSteps = \count((array) \array_filter($value['steps'], static fn (array $step): bool => 'finished' === $step['state']));
        }
        $this->finishedSteps = $finishedSteps;

        Assert::keyExists($value, 'created_at', 'Key "created_at" must exist.');
        Assert::integer($value['created_at'], 'Value of "created_at" must be an integer. Got: %s');
        $this->createdAt = new DateTimeImmutable(date('Y-m-d H:i:s', $value['created_at']));

        Assert::keyExists($value, 'reference', 'Key "reference" must exist.');
        Assert::notNull($value['reference'], 'Value of "reference" must not be null.');

        if (\is_int($value['reference'])) {
            $reference = (string) $value['reference'];
        } else {
            $reference = $value['reference'];
        }

        Assert::stringNotEmpty($reference, 'Value of "reference" must be an not empty string. Got: %s');
        $this->reference = $reference;

        $abortedAt = null;

        if (\array_key_exists('aborted_at', $value)) {
            Assert::nullOrInteger($value['aborted_at'], 'Value of "reference" must be null or an integer. Got: %s');

            if (null !== $value['aborted_at']) {
                $abortedAt = new DateTimeImmutable(date('Y-m-d H:i:s', $value['aborted_at']));
            }
        }

        $this->abortedAt = $abortedAt;

        Assert::keyExists($value, 'deadline_at', 'Key "deadline_at" must exist.');
        $deadlineAt = null;

        if (null !== $value['deadline_at']) {
            Assert::integer($value['deadline_at']);
            $deadlineAt = new DateTimeImmutable(date('Y-m-d H:i:s', $value['deadline_at']));
        }

        $this->deadlineAt = $deadlineAt;

        $deadlineHard = false;

        if (\array_key_exists('deadline_hard', $value)) {
            Assert::notNull($value['deadline_hard'], 'Value of "deadline_hard" must not be null.');
            Assert::boolean($value['deadline_hard'], 'Key "deadline_hard" must be a boolean. Got: %s');
            $deadlineHard = $value['deadline_hard'];
        }
        $this->deadlineHard = $deadlineHard;

        Assert::keyExists($value, 'summary_download_url', 'Key "summary_download_url" must exist.');
        $this->summaryDownloadUrl = $value['summary_download_url'];

        $this->reminders = [];

        if (\array_key_exists('reminder', $value)
            && \is_array($value['reminder'])
            && \count($value['reminder']) > 0
        ) {
            foreach ($value['reminder'] as $reminder) {
                $this->reminders[] = Reminder::create($reminder, $this->id);
            }
        }

        $url = null;

        if ($typeAsEnum->equals(Type::REGULAR)) {
            Assert::keyExists($value, 'token', 'Key "token" must exist in a Datenabfrage of type: "regular"');
            $this->token = TrimmedNonEmptyString::fromString($value['token'], 'Value of "token" must not be an empty string in a Datenabfrage of type: "regular"')->toString();

            $url = sprintf(
                'https://formulare.gansel-rechtsanwaelte.de/?abfrage=%s&token=%s',
                $this->reference,
                $this->token,
            );
        } else {
            Assert::keyExists(
                $value,
                'legacy_link',
                sprintf(
                    'Key "legacy_link" must exist in a Datenabfrage of type "legacy_vwv" ID: " %s"',
                    $this->id->toInt(),
                ),
            );

            if (null !== $value['legacy_link'] && '' !== trim((string) $value['legacy_link'])) {
                $url = $value['legacy_link'];
            }
        }
        $this->url = $url;
    }

    /**
     * @param array<mixed> $value
     */
    public static function fromArray(array $value): self
    {
        return new self($value);
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->value;
    }

    public function showOpenLink(): bool
    {
        if (null === $this->finishedAt
            && $this->simplifiedState() === 'todo'
        ) {
            return true;
        }

        if ($this->finishedAt instanceof DateTimeImmutable) {
            $expiresAt = clone $this->finishedAt;
            $expiresAt->modify('+2 days');

            return (new DateTimeImmutable()) < $expiresAt;
        }

        return false;
    }

    public function simplifiedState(): string
    {
        if (\in_array($this->state, ['created', 'invitation_sent', 'noticed', 'opened', 'filing', 'document_upload'], true)) {
            return 'todo';
        }

        if (\in_array($this->state, ['filed', 'exported', 'export_error'], true)) {
            return 'done';
        }

        return 'todo_closed';
    }

    public function showSummaryDownloadLink(): bool
    {
        return $this->hasSummaryDownloadUrl()
            && $this->simplifiedState() !== 'todo';
    }

    public function hasSteps(): bool
    {
        return 0 < $this->steps;
    }

    public function stepsAsString(): string
    {
        return sprintf(
            '%s von %s %s erledigt',
            $this->finishedSteps,
            $this->steps,
            1 === $this->steps ? 'Schritt' : 'Schritten',
        );
    }

    public function hasDeadline(): bool
    {
        return $this->deadlineAt instanceof DateTimeImmutable;
    }

    public function isDeadlineHard(): bool
    {
        return $this->deadlineHard;
    }

    public function isFinished(): bool
    {
        return null !== $this->finishedAt;
    }

    public function hasSummaryDownloadUrl(): bool
    {
        return null !== $this->summaryDownloadUrl;
    }

    public function hasReminders(): bool
    {
        return [] !== $this->reminders;
    }

    /**
     * @return Reminder[]
     */
    public function reminders(): array
    {
        return $this->reminders;
    }

    public function latestReminderNumber(): int
    {
        foreach ($this->reminders as $key => $reminder) {
            if (!\array_key_exists($key + 1, $this->reminders)) {
                return $key + 1; // we need to increase by one, because arrays start with 0
            }
        }

        throw new \InvalidArgumentException('No reminders available, cannot get latest reminder number');
    }

    public function notificationTargetId(): TargetId
    {
        return TargetId::fromInt($this->id->toInt());
    }

    public function notificationTarget(): Target
    {
        return Target::DATENEINGABE;
    }

    public function notificationPriority(): Priority
    {
        if ($this->hasDeadline()) {
            return Priority::HIGH;
        }

        return Priority::NORMAL;
    }

    public function isAborted(): bool
    {
        return null !== $this->abortedAt;
    }
}
