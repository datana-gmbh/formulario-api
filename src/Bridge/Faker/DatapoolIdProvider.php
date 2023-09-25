<?php

declare(strict_types=1);

/**
 * This file is part of Datapool-Api.
 *
 * (c) Datana GmbH <info@datana.rocks>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Datana\Datapool\Api\Bridge\Faker;

use Datana\Datapool\Api\Domain\Value\DatapoolId;
use Faker\Provider\Base as BaseProvider;

final class DatapoolIdProvider extends BaseProvider
{
    public function datapoolId(): DatapoolId
    {
        return DatapoolId::fromInt(
            $this->datapoolIdInteger(),
        );
    }

    public function datapoolIdInteger(): int
    {
        return $this->generator->numberBetween(1);
    }
}
