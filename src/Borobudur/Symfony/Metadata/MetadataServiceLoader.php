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

namespace Borobudur\Infrastructure\Symfony\Metadata;

use Borobudur\Component\Messaging\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MetadataServiceLoader
{
    /**
     * Register services to service locator.
     *
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     * @param string            $appName
     */
    public static function load(ContainerBuilder $container, MetadataInterface $metadata, string $appName): void
    {
        $locators = [
            'factory' => $container->findDefinition(
                $appName . '.messaging.metadata_service_locator'
            ),
            'handler' => $container->findDefinition(
                $appName . '.bus.message_service_locator'
            ),
        ];

        foreach ($locators as $service => $locator) {
            self::addService($metadata, $container, $locator, $service);
        }
    }

    /**
     * Register services in metadata.
     *
     * @param MetadataInterface $metadata
     * @param ContainerBuilder  $container
     * @param Definition        $serviceLocator
     * @param string            $service
     */
    private static function addService(MetadataInterface $metadata, ContainerBuilder $container, Definition $serviceLocator, string $service): void
    {
        $serviceId = $metadata->getServiceId($service);
        $arguments = $serviceLocator->getArguments();
        $services = count($arguments) ? (array) $arguments[0] : [];
        $definition = null;

        if ($container->hasDefinition($serviceId)) {
            $definition = $container->findDefinition($serviceId);
        }

        switch ($service) {
            case 'factory':
                if (null === $definition) {
                    $definition = new Definition($metadata->getFactoryClass());
                }
                break;

            case 'handler':
                if (null === $definition) {
                    $definition = new Definition($metadata->getHandlerClass());
                }

                $services[$metadata->getMessageClass()] = new Reference(
                    $serviceId
                );
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf('Service type "%s" not found', $service)
                );
        }

        $container->setDefinition(
            $serviceId,
            $definition->setPublic(false)->setAutowired(true)
        );
        $services[$serviceId] = new Reference($serviceId);

        if (count($arguments)) {
            $serviceLocator->replaceArgument(0, $services);
        } else {
            $serviceLocator->addArgument($services);
        }
    }
}
