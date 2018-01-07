<?php

namespace Tests\Toro\Pay\Api;

use Tests\Toro\Pay\HttpClientOffline;
use Tests\Toro\Pay\HttpResponse;
use Toro\Pay\Api\Currency as Api;
use Toro\Pay\Domain\Currency as Domain;
use Toro\Pay\Domain\Paginage;

class CurrencyTest extends AbstractApiTestCase
{
    /**
     * @throws \Exception
     */
    public function testGetFirstPage()
    {
        HttpClientOffline::fixture('/currencies', function (HttpResponse $res) {
            return $res->withJson('currency_index.json');
        });

        try {
            $object = Api::create($this->createValidTokenResourceProvider())->getList();

            self::assertInstanceOf(Paginage::class, $object);
            self::assertEquals('paginage', $object->getResourceName());
            self::assertEquals(1, $object->page);

            /** @var Domain $currency */
            $currency = $object->items->first();
            self::assertEquals('currency', $currency->getResourceName());

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
