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

use Borobudur\Component\Http\Response;
use Borobudur\Component\Http\ResponseInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zend\Diactoros\Stream as DiactorosStream;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BorobudurResponseFactory implements BorobudurResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse(SymfonyResponse $symfonyResponse): ResponseInterface
    {
        if ($symfonyResponse instanceof BinaryFileResponse) {
            $stream = new DiactorosStream(
                $symfonyResponse->getFile()->getPathname(), 'r'
            );
        } else {
            $stream = new DiactorosStream('php://temp', 'wb+');
            if ($symfonyResponse instanceof StreamedResponse) {
                ob_start(
                    function ($buffer) use ($stream) {
                        $stream->write($buffer);

                        return false;
                    }
                );
                $symfonyResponse->sendContent();
                ob_end_clean();
            } else {
                $stream->write($symfonyResponse->getContent());
            }
        }

        $headers = $symfonyResponse->headers->all();
        $cookies = $symfonyResponse->headers->getCookies();

        if (!empty($cookies)) {
            $headers['Set-Cookie'] = [];
            foreach ($cookies as $cookie) {
                $headers['Set-Cookie'][] = (string) $cookie;
            }
        }

        $response = new Response(
            $stream,
            $symfonyResponse->getStatusCode(),
            $headers
        );
        $protocolVersion = $symfonyResponse->getProtocolVersion();

        if ('1.1' !== $protocolVersion) {
            $response = $response->withProtocolVersion($protocolVersion);
        }

        return $response;
    }
}
