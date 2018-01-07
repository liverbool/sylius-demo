<?php

namespace Toro\Pay\Api;

use Toro\Pay\AbstractModel;
use Toro\Pay\Domain\Paginage;
use Toro\Pay\Exception\InvalidResponseException;

trait PaginageTrait
{
    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     *
     * @return mixed|AbstractModel
     *
     * @throws InvalidResponseException
     */
    abstract function doRequest(string $method, string $path, array $data = [], array $headers = []);

    /**
     * @param string $path
     * @param int $page
     *
     * @return Paginage
     *
     * @throws InvalidResponseException
     */
    public function getPage(string $path, int $page = null): Paginage
    {
        null !== $page && $page <= 0 && $page = 1;

        return $this->doRequest('GET', $path, $page ? ['page' => $page] : []);
    }

    /**
     * @param Paginage $paginage
     *
     * @return Paginage
     *
     * @throws InvalidResponseException
     */
    public function getFirstPage(Paginage $paginage): Paginage
    {
        return $this->doRequest('GET', $paginage->getFirstUrl());
    }

    /**
     * @param Paginage $paginage
     *
     * @return Paginage
     *
     * @throws InvalidResponseException
     */
    public function getNextPage(Paginage $paginage): Paginage
    {
        return $this->doRequest('GET', $paginage->getNextUrl());
    }

    /**
     * @param Paginage $paginage
     *
     * @return Paginage
     *
     * @throws InvalidResponseException
     */
    public function getPrevPage(Paginage $paginage): Paginage
    {
        return $this->doRequest('GET', $paginage->getPrevUrl());
    }

    /**
     * @param Paginage $paginage
     *
     * @return Paginage
     *
     * @throws InvalidResponseException
     */
    public function reload(Paginage $paginage): Paginage
    {
        return $this->doRequest('GET', $paginage->getCurrencyUrl());
    }
}
