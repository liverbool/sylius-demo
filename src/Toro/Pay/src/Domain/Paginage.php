<?php

declare(strict_types=1);

namespace Toro\Pay\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Toro\Pay\AbstractModel;

/**
 * @property int pages
 * @property int page
 * @property int total
 * @property int limit
 * @property AbstractModel[]|Collection $items
 */
class Paginage extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public function __get(string $name)
    {
        if ('items' === $name) {
            return new ArrayCollection($this->store['_embedded']['items']);
        }

        return parent::__get($name);
    }

    /**
     * @return string
     */
    public function getCurrencyUrl(): string
    {
        return $this->store['_links']['self']['href'];
    }

    /**
     * @return string
     */
    public function getFirstUrl(): string
    {
        return $this->store['_links']['first']['href'];
    }

    /**
     * @return string
     */
    public function getNextUrl(): string
    {
        return $this->store['_links']['next']['href'] ?? $this->getLastUrl();
    }

    /**
     * @return string
     */
    public function getPrevUrl(): string
    {
        return $this->store['_links']['prev']['href'] ?? $this->getFirstUrl();
    }

    /**
     * @return string
     */
    public function getLastUrl(): string
    {
        return $this->store['_links']['last']['href'];
    }

    /**
     * @return int
     */
    public function getCurrencyPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getNextPage(): int
    {
        return $this->page === $this->pages ? $this->page : $this->page + 1;
    }

    /**
     * @return int
     */
    public function getPrevPage(): int
    {
        return $this->page === 1 ? $this->page : $this->page - 1;
    }

    /**
     * @return int
     */
    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->pages;
    }
}
