<?php

declare(strict_types=1);

namespace Toro\Pay\Api;

use Toro\Pay\AbstractApi;
use Toro\Pay\Exception\InvalidResponseException;
use Toro\Pay\Domain\Info as Domain;

class UserInfo extends AbstractApi
{
    /**
     * {@inheritdoc}
     */
    protected function getDomainClass(): string
    {
        return Domain::class;
    }

    /**
     * @return Domain
     *
     * @throws InvalidResponseException
     */
    public function getInfo()
    {
        return $this->doRequest('GET', '/coin/info');
    }
}