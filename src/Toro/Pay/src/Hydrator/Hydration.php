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
use Toro\Pay\Domain\Error;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class Hydration implements HydrationInterface
{
    /**
     * @param array $data
     *
     * @return array|AbstractModel
     */
    private function doHydrate(array &$data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::doHydrate($data[$key]);
            }
        }

        if (empty($data) || empty($data['resource'])) {
            return (array) $data;
        }

        $domain = static::getDomainClass($data['resource']);

        return new $domain($data);
    }

    /**
     * @param array $data
     *
     * @return AbstractModel
     */
    public function hydrate(array $data): AbstractModel
    {
        $domain = $this->doHydrate($data);
        $assertingClass = static::getDomainAssertionClass();

        if (!$domain instanceof $assertingClass) {
            return new Error([
                'code' => 'unsupported_format',
                'message' => 'Unsupported format.',
                'data' => $domain,
            ]);
        }

        return $domain;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDomainClass($resourceName): string
    {
        if ('error' === strtolower($resourceName)) {
            $resourceName = Error::class;
        }

        return $resourceName;
    }

    /**
     * @return string
     */
    public static function getDomainAssertionClass(): string
    {
        return AbstractModel::class;
    }
}
