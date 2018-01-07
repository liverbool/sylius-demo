<?php

declare(strict_types=1);

namespace Toro\Pay\Api;

use Toro\Pay\AbstractApi;
use Toro\Pay\Exception\InvalidResponseException;
use Toro\Pay\Domain\Currency as Domain;
use Toro\Pay\Domain\Paginage;

class Currency extends AbstractApi
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
        return $this->getPage('/currencies', $page);
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
        return $this->doRequest('GET', '/currencies/' . $id);
    }
}
