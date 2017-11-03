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

namespace Borobudur\Infrastructure\Symfony\Http\Response;

use Borobudur\Component\Http\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface BorobudurResponseFactoryInterface
{
    /**
     * Factory create a response from symfony to borobudur response.
     *
     * @param SymfonyResponse $response
     *
     * @return ResponseInterface
     */
    public function createResponse(SymfonyResponse $response): ResponseInterface;
}
