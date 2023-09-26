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

namespace Datana\Formulario\Api\Tests\Util;

use Faker\Factory;
use Faker\Generator;

/**
 * @method static void assertSame($expected, $actual, string $message = '')
 * @method static void fail(string $message = '')
 */
trait Helper
{
    final protected static function faker(string $locale = 'de_DE'): Generator
    {
        static $fakers = [];

        if (!\array_key_exists($locale, $fakers)) {
            $faker = Factory::create($locale);

            $faker->seed(9001);

            $fakers[$locale] = $faker;
        }

        return $fakers[$locale];
    }

    final protected static function assertSameDateTime(\DateTimeInterface $expected, ?\DateTimeInterface $actual): void
    {
        if (null === $actual) {
            self::fail(sprintf(
                'Failed asserting that null equals DateTime "%s".',
                $expected->format('Y-m-d H:i:s'),
            ));
        } else {
            self::assertSame($expected->format('U'), $actual->format('U'));
        }
    }
}
