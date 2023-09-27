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

namespace Datana\Formulario\Api\Domain\Value;

use Datana\Mandantencockpit\Contracts\Notification\Enum\Priority;
use Datana\Mandantencockpit\Contracts\Notification\Enum\Target;
use Datana\Mandantencockpit\Contracts\Notification\Notifyable;
use Datana\Mandantencockpit\Contracts\Notification\Value\TargetId;
use OskarStark\Value\TrimmedNonEmptyString;
use Webmozart\Assert\Assert;

final class Reminder implements Notifyable
{
    private readonly ?int $id;
    private readonly string $sender;

    private function __construct(
        ?int $id,
        private readonly \DateTimeInterface $date,
        string $sender,
        private readonly DateneingabeId $dateneingabeId,
    ) {
        if (\is_int($id)) {
            Assert::greaterThan($id, 0);
        }
        $this->id = $id;

        $this->sender = TrimmedNonEmptyString::fromString($sender)->toString();
    }

    /**
     * @param array{'id': null|int, 'time': int, 'sender': string} $values
     */
    public static function create(array $values, DateneingabeId $dateneingabeId): self
    {
        Assert::keyExists($values, 'id');
        Assert::nullOrInteger($values['id']);
        Assert::keyExists($values, 'sender');
        Assert::keyExists($values, 'time');
        Assert::integer($values['time']);
        Assert::greaterThan($values['time'], 0);

        $date = \DateTimeImmutable::createFromFormat('!U', (string) $values['time']);
        Assert::isInstanceOf($date, \DateTimeInterface::class);

        return new self(
            $values['id'],
            $date,
            TrimmedNonEmptyString::fromString($values['sender'])->toString(),
            $dateneingabeId,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function dateneingabeId(): DateneingabeId
    {
        return $this->dateneingabeId;
    }

    public function date(): \DateTimeInterface
    {
        return $this->date;
    }

    public function sender(): string
    {
        return $this->sender;
    }

    public function notificationTargetId(): TargetId
    {
        return TargetId::fromString(sprintf(
            '%s-%s',
            $this->dateneingabeId()->toInt(),
            $this->id ?? $this->date->format('U'),
        ));
    }

    public function notificationTarget(): Target
    {
        return Target::DATENEINGABE_REMINDER;
    }

    public function notificationPriority(): Priority
    {
        return Priority::HIGH;
    }
}
