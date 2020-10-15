<?php

declare(strict_types=1);

/*
 * (c)Copyright 2007-2020 UGroupMedia Inc. <dev@ugroupmedia.com>
 * This source file is part of PNP Project and is subject to
 * copyright. It can not be copied and/or distributed without
 * the express permission of UGroupMedia Inc.
 * If you get a copy of this file without explicit authorization,
 * please contact us to the email above.
 */

namespace Ugm\SwooleGrpc\Server\HttpFoundation;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class Request extends BaseRequest
{
    /**
     * Checks whether or not the method is safe.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.1
     *
     * @return bool
     */
    public function isMethodSafe()
    {
        return $this->getMethod() === 'POST' || parent::isMethodSafe();
    }

    /**
     * Checks whether the method is cacheable or not.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.3
     *
     * @return bool True for GET, HEAD and POST, false otherwise
     */
    public function isMethodCacheable()
    {
        return $this->getMethod() === 'POST' || parent::isMethodCacheable();
    }

}
