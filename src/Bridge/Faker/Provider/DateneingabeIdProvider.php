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

namespace Datana\Formulario\Api\Bridge\Faker\Provider;

use Datana\Formulario\Api\Domain\Value\DateneingabeId;
use Faker\Provider\Base as BaseProvider;

final class DateneingabeIdProvider extends BaseProvider
{
    public function dateneingabeId(): DateneingabeId
    {
        return DateneingabeId::fromInt(
            $this->dateneingabeIdInt(),
        );
    }

    public function dateneingabeIdInt(): int
    {
        return $this->generator->biasedNumberBetween(1);
    }
}
