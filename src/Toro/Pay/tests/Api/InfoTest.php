<?php

namespace Tests\Toro\Pay\Api;

use Tests\Toro\Pay\HttpClientOffline;
use Tests\Toro\Pay\HttpResponse;
use Toro\Pay\Api\Info as Api;
use Toro\Pay\Domain\Info;
use Toro\Pay\Exception\InvalidResponseException;

class InfoTest extends AbstractApiTestCase
{
    /**
     * @throws \Exception
     */
    public function testAccessToCoinInfoWithForUserWhoStillHaveNoBalanceAccount()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('The "balance" has not been found');

        HttpClientOffline::fixture('/coin/info', function (HttpResponse $res) {
            return $res
                ->withJson('coin_info_404.json')
                ->withStatus(404);
        });

        $provider = $this->createResourceProvider(['access_token' => 'ScopedSampleToken404']);

        try {
            Api::create($provider)->getInfo();
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

        $provider = $this->createResourceProvider(['access_token' => 'ScopedSampleToken']);

        try {
            $info = Api::create($provider)->getInfo();

            self::assertInstanceOf(Info::class, $info);
            self::assertEquals('TOR', $info->currency);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
