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

use Borobudur\Component\Http\Request;
use Borobudur\Component\Http\RequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Zend\Diactoros\ServerRequestFactory as DiactorosRequestFactory;
use Zend\Diactoros\Stream as DiactorosStream;
use Zend\Diactoros\UploadedFile as DiactorosUploadedFile;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BorobudurRequestFactory implements BorobudurRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(SymfonyRequest $symfonyRequest): RequestInterface
    {
        $serverParams = $symfonyRequest->server->all();
        $server = DiactorosRequestFactory::normalizeServer($serverParams);
        $headers = $symfonyRequest->headers->all();
        $files = DiactorosRequestFactory::normalizeFiles(
            $this->getFiles($symfonyRequest->files->all())
        );

        if (PHP_VERSION_ID < 50600) {
            $body = new DiactorosStream('php://temp', 'wb+');
            $body->write($symfonyRequest->getContent());
        } else {
            $body = new DiactorosStream($symfonyRequest->getContent(true));
        }

        $request = new Request(
            $server,
            $files,
            $symfonyRequest->getUri(),
            $symfonyRequest->getMethod(),
            $body,
            $headers
        );
        $request = $request
            ->withCookieParams($symfonyRequest->cookies->all())
            ->withQueryParams($symfonyRequest->query->all())
            ->withParsedBody($symfonyRequest->request->all());

        foreach ($symfonyRequest->attributes->all() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    }

    /**
     * Gets uploaded files.
     *
     * @param array $uploadedFiles
     *
     * @return UploadedFileInterface[]
     */
    private function getFiles(array $uploadedFiles): array
    {
        $files = [];
        foreach ($uploadedFiles as $key => $value) {
            if (null === $value) {
                $files[$key] = new DiactorosUploadedFile(
                    null,
                    0,
                    UPLOAD_ERR_NO_FILE,
                    null,
                    null
                );
                continue;
            }

            if ($value instanceof UploadedFile) {
                $files[$key] = $this->createUploadedFile($value);
            } else {
                $files[$key] = $this->getFiles($value);
            }
        }

        return $files;
    }

    /**
     * Transform symfony uploaded file to zend diactoros.
     *
     * @param UploadedFile $symfonyUploadedFile
     *
     * @return DiactorosUploadedFile
     */
    private function createUploadedFile(UploadedFile $symfonyUploadedFile): DiactorosUploadedFile
    {
        return new DiactorosUploadedFile(
            $symfonyUploadedFile->getRealPath(),
            $symfonyUploadedFile->getClientSize(),
            $symfonyUploadedFile->getError(),
            $symfonyUploadedFile->getClientOriginalName(),
            $symfonyUploadedFile->getClientMimeType()
        );
    }
}
