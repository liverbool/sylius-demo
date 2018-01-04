<?php

namespace Tests\Toro\Pay;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Toro\Pay\ToroPay;

class HttpClientOffline extends Client implements ClientInterface
{
    static $liveMode = false;
    static $fixtures = [];
    static $prefixPath = '/api/v1';

    public static function fixture(string $path, \Closure $closure)
    {
        static::$fixtures[$path] = $closure(static::$fixtures[$path] ?? (static::$fixtures[$path] = new HttpResponse()));
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function send(RequestInterface $request, array $options = [])
    {
        if (static::$liveMode) {
            if (!preg_match(sprintf("|%s|", preg_quote(ToroPay::BASE_URL_SANDBOX)), (string)$request->getUri())) {
                throw new \InvalidArgumentException("Allow live mode only sandbox testing.");
            }

            return parent::send($request, $options);
        }

        $path = str_replace(static::$prefixPath, '', $request->getUri()->getPath());

        if (!array_key_exists($path, static::$fixtures)) {
            throw new \InvalidArgumentException("Not found fixture for path: `$path`.");
        }

        return static::$fixtures[$path];
    }
}
