<?php

namespace Tests\Toro\Pay\Api;

use Toro\Pay\Api\Info as Api;
use Toro\Pay\Exception\InvalidResponseException;

class InfoTest extends AbstractApiTestCase
{
    protected $useLiveApi = true;

    /*public function testAccessToCoinInfo()
    {
        $provider = $this->createLiveValidResourceProvider('ScopedSampleTokenExpired');

        Api::create($provider)->getInfo();
    }*/

    public function testAccessToCoinInfoWithForUserWhoStillHaveNoBalanceAccount()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('The "balance" has not been found');

        $provider = $this->createLiveValidResourceProvider('ScopedSampleToken404');

        Api::create($provider)->getInfo();
    }
}
