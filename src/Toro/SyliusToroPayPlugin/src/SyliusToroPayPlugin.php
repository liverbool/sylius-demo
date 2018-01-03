<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusToroPayPlugin extends Bundle
{
    use SyliusPluginTrait;
}
