<?php

declare(strict_types=1);

namespace Datana\Formulario\Api;

/**
 * @author Oskar Stark <oskar.stark@googlemail.de>
 */
interface StatisticsApiInterface
{
    public function numberOfCockpitInvitationMailsSent(): int;
}
