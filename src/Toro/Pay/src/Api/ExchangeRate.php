<?php

declare(strict_types=1);

namespace Toro\Pay\Api;

use Toro\Pay\AbstractApi;
use Toro\Pay\Exception\InvalidResponseException;
use Toro\Pay\Domain\ExchangeRate as Domain;
use Toro\Pay\Domain\Paginage;

class ExchangeRate extends AbstractApi
{
    use PaginageTrait;

    /**
     * @param int $page
     *
     * @return Paginage
     *
     * @throws InvalidResponseException
     */
    public function getList(int $page = null): Paginage
    {
        return $this->getPage('/exchange-rates', $page);
    }

    /**
     * @param int $id
     *
     * @return Domain
     *
     * @throws InvalidResponseException
     */
    public function show(int $id): Domain
    {
        return $this->doRequest('GET', '/exchange-rates/' . $id);
    }
}
