<?php

declare(strict_types=1);

namespace Toro\Pay;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Toro\Pay\Domain\Error;
use Toro\Pay\Exception\InvalidRequestArgumentException;
use Toro\Pay\Exception\InvalidResponseException;
use Toro\Pay\Hydrator\Hydration;
use Toro\Pay\Hydrator\HydrationInterface;
use Toro\Pay\Provider\OAuth2ClientProviderInterface;

abstract class AbstractApi
{
    /**
     * @var OAuth2ClientProviderInterface
     */
    protected $provider;

    /**
     * @var HydrationInterface
     */
    private $hydration;

    /**
     * @param OAuth2ClientProviderInterface $provider
     * @param array $options
     * @param HydrationInterface $hydration
     */
    public function __construct(OAuth2ClientProviderInterface $provider, array $options, HydrationInterface $hydration = null)
    {
        $this->provider = $provider;
        $this->hydration = $hydration ?: new Hydration();
    }

    /**
     * @param OAuth2ClientProviderInterface $provider
     * @param array $options
     * @param HydrationInterface|null $hydration
     *
     * @return static
     */
    public static function create(OAuth2ClientProviderInterface $provider, array $options, HydrationInterface $hydration = null)
    {
        static $instance;

        if ($instance) {
            return $instance;
        }

        return $instance = new static($provider, $options, $hydration);
    }

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
    protected function doRequest($method, $path, array $data = [], array $headers = [])
    {
        try {
            $contentBody = $this->provider->getResource($method, $path, $data, $headers);

            return $this->hydrateResponse($contentBody);
        } catch (IdentityProviderException $e) {
            throw $this->throwError((string)$e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param string $code
     * @param string $message
     *
     * @return InvalidResponseException
     */
    private function throwError(string $code, string $message)
    {
        return new InvalidResponseException(new Error([
            'code' => $code,
            'message' => $message,
        ]));
    }

    /**
     * @param array $content
     *
     * @return mixed|AbstractModel
     *
     * @throws InvalidResponseException
     */
    private function hydrateResponse(array $content)
    {
        // oauth2 error
        if (isset($content['error']) && isset($content['error_description'])) {
            throw $this->throwError($content['error'], $content['error_description']);
        }

        $result = $this->hydration->hydrate($content);

        if ($result instanceof Error) {
            throw new InvalidResponseException($result);
        }

        return $result;
    }

    /**
     * @param mixed $value
     * @param string $message
     *
     * @throws InvalidRequestArgumentException
     */
    protected static function assertNotEmpty($value, string $message = 'Assert value not empty.')
    {
        if (empty($value)) {
            throw new InvalidRequestArgumentException($message);
        }
    }
}
