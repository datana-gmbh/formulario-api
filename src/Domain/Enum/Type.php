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

namespace Datana\Formulario\Api\Domain\Enum;

use OskarStark\Enum\Trait\Comparable;

enum Type: string
{
    use Comparable;

    case HEARING_RECORD = 'hearing_record';
    case LEGACY_VWV = 'legacy_vwv';
    case REGISTRATION = 'registration';
    case REGULAR = 'regular';
}
