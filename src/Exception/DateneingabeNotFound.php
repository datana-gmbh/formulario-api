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

namespace Datana\Formulario\Api\Exception;

use Datana\Formulario\Api\Domain\Value\DateneingabeId;

final class DateneingabeNotFound extends \LogicException
{
    public static function withDateneingabeId(DateneingabeId $dateneingabeId): self
    {
        return new self(sprintf(
            'Cannot find Dateneingabe with DateneingabeId: %s',
            $dateneingabeId->toInt(),
        ));
    }
}
