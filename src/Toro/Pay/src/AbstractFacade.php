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

namespace Toro\Pay;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
abstract class AbstractFacade
{
    /**
     * @var AbstractApi[]
     */
    protected static $api = [];

    /**
     * @var AbstractModel
     */
    protected $domain;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $class = str_replace('Facade', 'Domain', get_called_class());

        $this->domain = new $class($data);
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function make(array $data = []): self
    {
        return new static($data);
    }

    /**
     * @param AbstractApi $api
     */
    public static function setApi(AbstractApi $api): void
    {
        self::$api[get_called_class()] = $api;
    }

    /**
     * @return AbstractApi
     */
    private static function getApiForClass(): AbstractApi
    {
        return self::$api[get_called_class()];
    }

    /**
     * @param string $method
     *
     * @throws \InvalidArgumentException
     */
    private static function assertApiMethodExists(string $method): void
    {
        if (!method_exists(self::getApiForClass(), $method)) {
            throw new \InvalidArgumentException(
                sprintf('Not found method named `%s` for `%s` api.', $method, get_called_class())
            );
        }
    }

    /**
     * @param string $method
     * @param array $args
     *
     * @return mixed|self
     */
    public static function __callStatic(string $method, array $args = [])
    {
        self::assertApiMethodExists($method);

        return self::getApiForClass()->$method(...$args);
    }

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args = [])
    {
        if (method_exists($this->domain, $method)) {
            return $this->domain->$method(...$args);
        }

        self::assertApiMethodExists($method);

        return self::getApiForClass()->$method($this->domain, ...$args);
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->domain->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $this->domain->$name = $value;
    }
}
