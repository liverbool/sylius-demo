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

use Toro\Pay\Exception\InvalidResponseException;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
interface HydrationInterface
{
    /**
     * @param array $rawData
     *
     * @return mixed
     *
     * @throws InvalidResponseException
     */
    public function hydrate(array $rawData);

    /**
     * @param $objectName
     *
     * @return string
     */
    public static function getDomainClass($objectName);

    /**
     * @return string
     */
    public static function getDomainAssertionClass();
}
