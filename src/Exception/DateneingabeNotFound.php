<?php

declare(strict_types=1);

namespace Datana\Formulario\Api\Exception;

use App\Exception\NotFoundException;
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
