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

namespace Borobudur\Infrastructure\Symfony\Bundle\MessagingBundle\DependencyInjection;

use Borobudur\Component\Messaging\Metadata\Metadata;
use Borobudur\Infrastructure\Symfony\DependencyInjection\AbstractExtension;
use Borobudur\Infrastructure\Symfony\Metadata\MetadataServiceLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BorobudurMessagingExtension extends AbstractExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration(
            $this->getConfiguration($configs, $container),
            $configs
        );
        $appName = $configs['settings']['service_prefix'];
        $settings = [];

        if (isset($configs['settings']['message'])) {
            $settings = $configs['settings']['message'];
        }

        $container->setParameter(
            sprintf('%s.messaging.settings', $appName),
            $settings
        );

        $this->loadServices($container);

        if ($configs['settings']['enable_messaging']) {
            $this->registerMetadata($container, $configs);
        }
    }

    /**
     * Register metadata service.
     *
     * @param ContainerBuilder $container
     * @param array            $configs
     */
    private function registerMetadata(ContainerBuilder $container, array $configs): void
    {
        $appName = $configs['settings']['service_prefix'];
        $registryService = sprintf('%s.messaging.metadata_registry', $appName);
        $registry = $container->findDefinition($registryService);

        foreach ($configs['messages'] as $alias => $config) {
            $metadata = Metadata::fromAliasAndConfiguration($alias, $config);

            $registry->addMethodCall(
                'addFromAliasAndConfiguration',
                [$alias, $config]
            );

            MetadataServiceLoader::load($container, $metadata, $appName);
        }
    }
}
