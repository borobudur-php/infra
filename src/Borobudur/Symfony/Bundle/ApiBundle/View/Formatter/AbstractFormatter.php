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

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\View\Formatter;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractFormatter
{
    public function createResponse(ViewHandler $viewHandler, View $view, Request $request, string $format): Response
    {
        if (null !== $data = $view->getData()) {
            $view->setData($this->transform($view, $request, $format));
        }

        return $viewHandler->createResponse($view, $request, $format);
    }

    abstract protected function transform(View $view, Request $request, string $format): array;

    protected function getVersion(Request $request): float
    {
        return (float) str_replace(
            'v',
            '',
            $request->attributes->get('version')
        );
    }

    protected function getStatusCode(View $view, $data = null): int
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

    protected function getFormFromView(View $view)
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

    protected function convertContext(Context $context): array
    {
        $newContext = array();
        foreach ($context->getAttributes() as $key => $value) {
            $newContext[$key] = $value;
        }

        if (null !== $context->getGroups()) {
            $newContext['groups'] = $context->getGroups();
        }
        $newContext['version'] = $context->getVersion();
        $newContext['maxDepth'] = $context->getMaxDepth(false);
        $newContext['enable_max_depth'] = $context->isMaxDepthEnabled();

        return $newContext;
    }
}
