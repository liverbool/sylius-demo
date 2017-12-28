<?php

/*
 * This file is part of the PhpMob package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Dos\Payum\Omise\Request;

use Payum\Core\Request\Generic;

class DisplayFailure extends Generic
{
    /**
     * @var string
     */
    private $failureCode;

    /**
     * @var string
     */
    private $failureMessage;

    public function __construct($model)
    {
        parent::__construct($model);

        $this->failureCode = $model['failureCode'];
        $this->failureMessage = $model['failureMessage'];
    }

    /**
     * @return string
     */
    public function getFailureCode(): string
    {
        return $this->failureCode;
    }

    /**
     * @return string
     */
    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }
}
