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

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\View;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ApiViewHandler
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createResponse(ViewHandler $viewHandler, View $view, Request $request, string $format): Response
    {
        if (null !== $data = $view->getData()) {
            $version = str_replace(
                'v',
                '',
                $request->attributes->get('version')
            );

            $normalized = [
                'meta' => [
                    'version' => (float) $version,
                    'status'  => $this->getStatusCode($view, $data),
                ],
            ];

            $normalized['data'] = $data;

            $view->setData($normalized);
        }

        return $viewHandler->createResponse($view, $request, $format);
    }

    private function getStatusCode(View $view, $data = null): int
    {
        $form = $this->getFormFromView($view);

        if ($form && $form->isSubmitted() && !$form->isValid()) {
            return Response::HTTP_BAD_REQUEST;
        }

        $statusCode = $view->getStatusCode();
        if (null !== $statusCode) {
            return $statusCode;
        }

        return null !== $data ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
    }

    private function getFormFromView(View $view)
    {
        $data = $view->getData();

        if ($data instanceof FormInterface) {
            return $data;
        }

        if (is_array($data)
            && isset($data['form'])
            && $data['form'] instanceof FormInterface
        ) {
            return $data['form'];
        }

        return false;
    }
}
