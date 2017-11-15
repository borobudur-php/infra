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

namespace Borobudur\Infrastructure\Symfony\Bundle\DoctrineBundle\DependencyInjection;

use Borobudur\Infrastructure\Symfony\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BorobudurDoctrineExtension extends AbstractExtension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration(
            $this->getConfiguration($config, $container),
            $config
        );

        $definition = new Definition(
            $configs['table_prefix_subscriber_class'],
            [$configs['table_prefix']]
        );

        $definition->addTag('doctrine.event_subscriber');

        $container->setDefinition(
            'borobudur_doctrine.table_prefix_subscriber',
            $definition
        );
    }
}
