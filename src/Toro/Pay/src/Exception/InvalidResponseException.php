<?php

/*
 * This file is part of the ToroPay package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Toro\Pay\Exception;

use Toro\Pay\Domain\Error;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
final class InvalidResponseException extends \Exception
{
    /**
     * @var Error
     */
    public $error;

    public function __construct(Error $error, $code = 0, \Throwable $previous = null)
    {
        $this->error = $error;

        parent::__construct($error->message, $code, $previous);
    }
}
