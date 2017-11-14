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

namespace Borobudur\Infrastructure\Symfony\EventSubscriber;

use Borobudur\Component\Http\RequestInterface;
use Borobudur\Component\Messaging\Message\FactoryInterface;
use Borobudur\Component\Messaging\Metadata\MetadataInterface;
use Borobudur\Component\Messaging\Request\ConfigurationFactoryInterface;
use Borobudur\Infrastructure\Symfony\Http\Request\BorobudurRequestFactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageInjectorSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ConfigurationFactoryInterface
     */
    private $requestConfigurationFactory;

    /**
     * @var BorobudurRequestFactoryInterface
     */
    private $borobudurRequestFactory;

    public function __construct(ContainerInterface $container, ConfigurationFactoryInterface $requestConfigurationFactory, BorobudurRequestFactoryInterface $borobudurRequestFactory)
    {
        $this->container = $container;
        $this->requestConfigurationFactory = $requestConfigurationFactory;
        $this->borobudurRequestFactory = $borobudurRequestFactory;
    }

    /**
     * Inject message to current request.
     *
     * @param FilterControllerEvent $event
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onKernelRequest(FilterControllerEvent $event): void
    {
        $request = $event->getRequest();
        $request = $this->borobudurRequestFactory->createRequest($request);
        $parameters = $request->getAttribute('_borobudur', []);

        if (!empty($parameters)) {
            $configuration = $this->requestConfigurationFactory->createFrom(
                $request
            );
            $metadata = $configuration->getMetadata();
            $factory = $this->getFactory($metadata);
            $message = $factory->createFromMetadata(
                $metadata,
                $this->parseRequestData($request)
            );

            $event->getRequest()->attributes->set(
                $parameters['message_property'],
                $message
            );
            $event->getRequest()->attributes->set(
                'requestConfiguration',
                $configuration
            );
        }
    }

    /**
     * Gets the factory service from message metadata.
     *
     * @param MetadataInterface $metadata
     *
     * @return FactoryInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getFactory(MetadataInterface $metadata): FactoryInterface
    {
        return $this->container->get($metadata->getServiceId('factory'));
    }

    /**
     * Parse request data.
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    private function parseRequestData(RequestInterface $request): array
    {
        $attributes = array_diff_key(
            $request->getAttributes(),
            array_flip(
                [
                    'media_type',
                    'version',
                    '_controller',
                    '_borobudur',
                    '_route',
                    '_route_params',
                ]
            )
        );

        return array_merge_recursive(
            (array) $request->getParsedBody(),
            $attributes,
            $request->getQueryParams()
        );
    }
}
