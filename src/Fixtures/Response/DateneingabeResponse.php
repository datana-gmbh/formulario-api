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

namespace Datana\Formulario\Api\Fixtures\Response;

use Datana\Formulario\Api\Domain\Enum\Type;
use Faker\Factory;
use function Safe\array_replace_recursive;

final class DateneingabeResponse
{
    /**
     * @param array<mixed> $parameters
     *
     * @return array<mixed>
     */
    public static function create(array $parameters = [], ?int $maxSteps = null): array
    {
        $faker = Factory::create('de_DE');

        $createdAt = $faker->dateTimeThisMonth();
        $finishedAt = clone $createdAt;
        $finishedAt->add(new \DateInterval(sprintf('P%sD', $faker->numberBetween(0, 5))));

        $summaryDownloadUrl = $faker->boolean() ? $faker->url() : null;

        $setAbortedAt = false;

        if ($faker->boolean(60)) {
            $setAbortedAt = true;
            $abortedAt = $faker->boolean() ? $finishedAt->getTimestamp() : null;
            $summaryDownloadUrl = null;
        }

        $setFinishedAt = $faker->boolean();

        if ($setFinishedAt) {
            $summaryDownloadUrl = $faker->url();
        }

        $state = $faker->randomElement(['created', 'invitation_sent', 'noticed', 'opened', 'filing', 'document_upload', 'filed', 'exported', 'export_error', 'aborted']);

        if (null === $maxSteps) {
            $maxSteps = $faker->numberBetween(1, 4);
        }

        $steps = [];

        for ($s = 0; $s < $maxSteps; ++$s) {
            $steps[] = [
                'step_id' => (string) $s,
                'name' => $faker->word(),
                'category' => (string) $s,
                'state' => $faker->randomElement(['outstanding', 'finished', 'current']),
            ];
        }

        $response = [
            'id' => $faker->numberBetween(1),
            'case_reference' => $faker->word(),
            'created_at' => $createdAt->getTimestamp(),
            'finished_at' => $setFinishedAt ? $finishedAt->getTimestamp() : null,
            'reference' => $faker->boolean() ? $faker->word() : $faker->numberBetween(1, 9999),
            'token' => $faker->sha256(),
            'state' => $state,
            'configuration' => [
                'data_enquiry_title' => $faker->word(),
            ],
            'steps' => $steps,
            'deadline_at' => null,
            'reminders' => [],
            'summary_download_url' => $summaryDownloadUrl,
            'legacy_link' => $faker->randomElement([null, '', ' ']),
        ];

        if ($faker->boolean(80)) {
            $response['deadline_at'] = $faker->boolean() ? $finishedAt->getTimestamp() : null;
        }

        $type = Type::REGULAR;

        if ($faker->boolean(25)) {
            $type = Type::LEGACY_VWV;
            $response['legacy_link'] = $faker->boolean() ? $faker->url() : $faker->randomElement([null, '', ' ']);
            $response['steps'] = [];
        }

        $response['type'] = $type->value;

        if ($faker->boolean(60)) {
            $response['deadline_hard'] = $faker->boolean(30);
        }

        if ($setAbortedAt) {
            $response['aborted_at'] = $abortedAt;
        }

        if ($faker->boolean()) {
            $reminders = [];

            for ($r = 0; $faker->numberBetween(1, 3) > $r; ++$r) {
                $reminders[] = ReminderResponse::create();
            }

            $response['reminders'] = $reminders;
        }

        return array_replace_recursive(
            $response,
            $parameters,
        );
    }
}
