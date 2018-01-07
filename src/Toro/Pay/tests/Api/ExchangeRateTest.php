<?php

namespace Tests\Toro\Pay\Api;

use Tests\Toro\Pay\HttpClientOffline;
use Tests\Toro\Pay\HttpResponse;
use Toro\Pay\Api\ExchangeRate as Api;
use Toro\Pay\Domain\ExchangeRate as Domain;
use Toro\Pay\Domain\Paginage;

class ExchangeRateTest extends AbstractApiTestCase
{
    /**
     * @throws \Exception
     */
    public function testEmptyExchangeRates()
    {
        if ($this->useLiveApi) {
            self::assertTrue(true);

            return;
        }

        HttpClientOffline::fixture('/exchange-rates', function (HttpResponse $res) {
            return $res->withJson('exchange_rates_empty.json');
        });

        try {
            $object = Api::create($this->createValidTokenResourceProvider())->getList();

            self::assertInstanceOf(Paginage::class, $object);
            self::assertEquals('paginage', $object->getResourceName());
            self::assertEquals(0, $object->total);
            self::assertEquals(1, $object->pages);
            self::assertEquals(1, $object->page);
            self::assertEquals(null, $object->items->first());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function testGetFirstPage()
    {
        HttpClientOffline::fixture('/exchange-rates', function (HttpResponse $res) {
            return $res->withJson('exchange_rates_index.json');
        });

        try {
            $object = Api::create($this->createValidTokenResourceProvider())->getList();

            self::assertInstanceOf(Paginage::class, $object);
            self::assertEquals('paginage', $object->getResourceName());

            /** @var Domain $exchangeRate */
            $exchangeRate = $object->items->first();
            self::assertEquals('exchange_rate', $exchangeRate->getResourceName());

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function testGetSingleExchangeRate()
    {
        HttpClientOffline::fixture('/exchange-rates/1', function (HttpResponse $res) {
            return $res->withJson('exchange_rates_show.json');
        });

        try {
            $object = Api::create($this->createValidTokenResourceProvider())->show(1);

            self::assertInstanceOf(Domain::class, $object);
            self::assertEquals('exchange_rate', $object->getResourceName());

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
