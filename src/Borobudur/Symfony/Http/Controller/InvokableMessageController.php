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

namespace Borobudur\Infrastructure\Symfony\Http\Controller;

use Borobudur\Component\Http\Controller\InvokableMessageControllerInterface;
use Borobudur\Component\Http\ResponseInterface;
use Borobudur\Component\Messaging\Bus\MessageBusInterface;
use Borobudur\Component\Messaging\Message\MessageInterface;
use Borobudur\Component\Messaging\Request\ConfigurationInterface;
use Borobudur\Infrastructure\Symfony\Http\Response\BorobudurResponseFactoryInterface;
use Borobudur\Infrastructure\Symfony\View\Api\RendererInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class InvokableMessageController extends Controller implements InvokableMessageControllerInterface
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var BorobudurResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(MessageBusInterface $bus, RendererInterface $renderer, BorobudurResponseFactoryInterface $responseFactory)
    {
        $this->bus = $bus;
        $this->renderer = $renderer;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(MessageInterface $message, ConfigurationInterface $requestConfiguration): ResponseInterface
    {
        $result = $this->bus->dispatch($message);
        $view = View::create($result);
        $response = $this->renderer->handle($requestConfiguration, $view);

        return $this->createResponse($response);
    }

    protected function createResponse(SymfonyResponse $symfonyResponse): ResponseInterface
    {
        return $this->responseFactory->createResponse($symfonyResponse);
    }
}
