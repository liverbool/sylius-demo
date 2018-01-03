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

namespace Toro\Pay\Domain;

use Toro\Pay\AbstractModel;

/**
 * @property string message
 * @property string code
 */
class Error extends AbstractModel
{
    /**
     * @var string
     */
    protected $idAttribute = 'code';
}
