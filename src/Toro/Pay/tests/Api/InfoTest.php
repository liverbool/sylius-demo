<?php

namespace Tests\Toro\Pay\Api;

use Toro\Pay\Api\Info as Api;

class InfoTest extends AbstractApiTestCase
{
    protected $useLiveApi = true;

    public function testAccessToCoinInfo()
    {

        $provider = $this->createLiveValidResourceProvider('ScopedSampleTokenExpired');

        $api = new Api($provider);

        dump($api->getInfo());
    }
}
