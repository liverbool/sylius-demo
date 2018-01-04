<?php

/*
 * This file is part of the ToroPay package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Toro\Pay\Hydrator;

use Toro\Pay\AbstractModel;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
interface HydrationInterface
{
    /**
     * @param array $rawData
     *
     * @return AbstractModel
     */
    public function hydrate(array $rawData): AbstractModel;

    /**
     * @param $resourceName
     *
     * @return string
     */
    public static function getDomainClass($resourceName);

    /**
     * @return string
     */
    public static function getDomainAssertionClass();
}
