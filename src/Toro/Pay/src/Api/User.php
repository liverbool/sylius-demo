<?php

declare(strict_types=1);

namespace Toro\Pay\Api;

use Toro\Pay\AbstractApi;
use Toro\Pay\Exception\InvalidResponseException;
use Toro\Pay\Domain\User as Domain;

class User extends AbstractApi
{
    /**
     * @return Domain
     *
     * @throws InvalidResponseException
     */
    public function getInfo()
    {
        return $this->doRequest('GET', '/user/info');
    }
}
