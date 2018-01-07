<?php

namespace Tests\Toro\Pay\Api;

use Tests\Toro\Pay\HttpClientOffline;
use Tests\Toro\Pay\HttpResponse;
use Toro\Pay\Api\User as Api;
use Toro\Pay\Domain\User as Domain;

class UserTest extends AbstractApiTestCase
{
    /**
     * @throws \Exception
     */
    public function testGetUserInfo()
    {
        HttpClientOffline::fixture('/user/info', function (HttpResponse $res) {
            return $res->withJson('user_info.json');
        });

        try {
            $object = Api::create($this->createValidTokenResourceProvider())->getInfo();

            self::assertInstanceOf(Domain::class, $object);
            self::assertEquals('user', $object->getResourceName());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
