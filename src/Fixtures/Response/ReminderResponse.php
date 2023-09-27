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

use Faker\Factory;
use function Safe\array_replace_recursive;

final class ReminderResponse
{
    /**
     * @param array<mixed> $parameters
     *
     * @return array{'id': null|int, 'time': int, 'sender': 'string'} $values
     */
    public static function create(array $parameters = []): array
    {
        $faker = Factory::create('de_DE');

        $date = $faker->dateTimeThisYear();

        $response = [
            'id' => $faker->randomElement([$faker->numberBetween(1), null]),
            'time' => (int) $date->format('U'),
            'sender' => $faker->randomElement([
                $faker->word(),
                'Formulario',
                'Mandantencockpit',
            ]),
        ];

        return array_replace_recursive(
            $response,
            $parameters,
        );
    }
}
