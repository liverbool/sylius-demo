<?php

declare(strict_types=1);

namespace Toro\Pay;

final class ToroPay
{
    const VERSION = '1.0-dev';
    const SERVICE_NAME = 'toropay';
    const ENDPOINT = 'https://toropay.co/api';
    const ENDPOINT_TOKEN = 'https://toropay.co/oauth/v2/token';
    const ENDPOINT_SANDBOX = 'https://d860913e.ngrok.io/api';
    const ENDPOINT_TOKEN_SANDBOX = 'https://d860913e.ngrok.io/oauth/v2/token';
}
