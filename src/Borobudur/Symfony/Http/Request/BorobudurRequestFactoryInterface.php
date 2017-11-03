<?php
/**
 * This file is part of the Borobudur package.
 *
 * (c) 2017 Borobudur <http://borobudur.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Borobudur\Infrastructure\Symfony\Http\Request;

use Borobudur\Component\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface BorobudurRequestFactoryInterface
{
    /**
     * Factory create a request from symfony to borobudur request.
     *
     * @param SymfonyRequest $request
     *
     * @return RequestInterface
     */
    public function createRequest(SymfonyRequest $request): RequestInterface;
}
