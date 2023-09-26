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

namespace Datana\Formulario\Api;

use Datana\Formulario\Api\Domain\Value\Dateneingabe;
use Datana\Formulario\Api\Domain\Value\DateneingabeId;
use Datana\Formulario\Api\Domain\Value\DateneingabenCollection;

/**
 * @author Oskar Stark <oskar.stark@googlemail.de>
 */
interface DateneingabenApiInterface
{
    public function byAktenzeichen(string $aktenzeichen): DateneingabenCollection;

    public function byId(DateneingabeId $id): Dateneingabe;
}
