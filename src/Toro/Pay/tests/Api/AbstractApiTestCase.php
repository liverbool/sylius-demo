<?php

namespace Tests\Toro\Pay\Api;

use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Tests\Toro\Pay\MockerTrait;
use Toro\Pay\AbstractApi;
use Toro\Pay\Api\Info;

class AbstractApiTestCase extends TestCase
{
    use MockerTrait;

    protected function getInvalidApiRequirementsTest()
    {
        $this->expectException(MissingOptionsException::class);

        Info::create($this->createHttpClient(), []);
    }

    protected function getValidApiRequirementsTest()
    {
        $infoApi = Info::create($this->createHttpClient(), [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'token_provider' => $this->createTokenProvider(),
        ]);

        $this->assertInstanceOf(AbstractApi::class, $infoApi);
    }
}
