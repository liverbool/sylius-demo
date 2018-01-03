<?php

namespace Tests\Toro\Pay\Api;

use Toro\Pay\Api\Info as Api;

class InfoTest extends AbstractApiTestCase
{
    protected $useLiveApi = true;

    public function testInvalidApiRequirements()
    {
        $this->getInvalidApiRequirementsTest();
    }

    public function testValidApiRequirements()
    {
        $this->getValidApiRequirementsTest();
    }

    public function testAccessToCoinInfo()
    {
        $api = new Api($this->createHttpClient(), [
            'client_id' => 'demo_client',
            'client_secret' => 'secret_demo_client',
            'token_provider' => $this->createTokenProvider(),
        ]);

        $api->getInfo();
    }
}
