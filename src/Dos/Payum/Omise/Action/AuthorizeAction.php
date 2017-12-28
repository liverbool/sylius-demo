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

namespace Dos\Payum\Omise\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class AuthorizeAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param Authorize $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        throw new HttpRedirect($model['authorizeUri']);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Authorize &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
