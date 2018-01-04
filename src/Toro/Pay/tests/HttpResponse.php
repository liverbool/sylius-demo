<?php

namespace Tests\Toro\Pay;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class HttpResponse extends Response
{
    public function withJson($file)
    {
        return $this->withBody(new Stream(fopen(__DIR__ . '/fixtures/' . $file, 'r+')));
    }

    public function withData(array $data)
    {
        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write(json_encode($data));

        return $this->withBody($stream);
    }
}
