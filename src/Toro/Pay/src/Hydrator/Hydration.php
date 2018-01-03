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
use Toro\Pay\Exception\InvalidResponseException;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class Hydration implements HydrationInterface
{
    /**
     * @param array $data
     *
     * @return AbstractModel|array
     */
    private function doHydrate(array &$data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::doHydrate($data[$key]);
            }
        }

        if (empty($data) || empty($data['object'])) {
            return $data;
        }

        $domain = static::getDomainClass($data['object']);

        return new $domain($data);
    }

    /**
     * @param array $data
     *
     * @return AbstractModel
     *
     * @throws InvalidResponseException
     */
    public function hydrate(array $data): AbstractModel
    {
        $domain = $this->doHydrate($data);
        $assertingClass = static::getDomainAssertionClass();

        if (!$domain instanceof $assertingClass) {
            throw new InvalidResponseException(new Error([
                'code' => 'unsupported_format',
                'message' => 'Unsupported format.',
                'data' => $domain,
            ]));
        }

        return $domain;
    }

    /**
     * @param $className
     *
     * @return string
     */
    public static function getDomainClass($className): string
    {
        return 'Toro\\Pay\\Domain\\' . ucfirst($className === 'list' ? 'Pagination' : $className);
    }

    /**
     * @return string
     */
    public static function getDomainAssertionClass(): string
    {
        return AbstractModel::class;
    }
}
