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

use Doctrine\Common\Inflector\Inflector;
use Toro\Pay\AbstractFacade;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class FacadeHydration extends Hydration
{
    /**
     * {@inheritdoc}
     */
    public static function getDomainClass($resourceName): string
    {
        if ('error' === strtolower($resourceName)) {
            return parent::getDomainClass($resourceName);
        }

        return "Toro\Pay\Facade\\" . Inflector::classify($resourceName);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDomainAssertionClass(): string
    {
        return AbstractFacade::class;
    }
}
