<?php

/*
 * This file is part of the PhpMob package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Dos\Payum\Omise\Action;

use Payum\Core\Exception\UnsupportedApiException;
use PhpMob\Omise\OmiseApi;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
trait ApiAwareTrait
{
    /**
     * @var OmiseApi
     */
    protected $api;

    /**
     * {@inheritdoc}
     */
    public function setApi($api)
    {
        if (false === $api instanceof OmiseApi) {
            throw new UnsupportedApiException(
                sprintf('Not supported api given. It must be an instance of %s', OmiseApi::class)
            );
        }

        $this->api = $api;
    }
}
