<?php

namespace Tests\Toro\Pay\Api;

use Tests\Toro\Pay\HttpClientOffline;
use Tests\Toro\Pay\HttpResponse;
use Toro\Pay\Api\Info as Api;
use Toro\Pay\Domain\Info as Domain;
use Toro\Pay\Exception\InvalidResponseException;

class InfoTest extends AbstractApiTestCase
{
    /**
     * @throws \Exception
     */
    public function TODO_testAccessToCoinInfoWithForUserWhoStillHaveNoBalanceAccount()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('The "balance" has not been found');

        HttpClientOffline::fixture('/coin/info', function (HttpResponse $res) {
            return $res
                ->withJson('coin_info_404.json')
                ->withStatus(404);
        });

        try {
            Api::create($this->create404TokenResourceProvider())->getInfo();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function testAccessToCoinInfo()
    {
        HttpClientOffline::fixture('/coin/info', function (HttpResponse $res) {
            return $res->withJson('coin_info.json');
        });

        try {
            $object = Api::create($this->createValidTokenResourceProvider())->getInfo();

            self::assertInstanceOf(Domain::class, $object);
            self::assertEquals('info', $object->getResourceName());
            self::assertEquals('user', $object->user->getResourceName());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
