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

namespace Borobudur\Infrastructure\Symfony\Bundle\ProophServiceBusBundle\DependencyInjection;

use Borobudur\Infrastructure\Symfony\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BorobudurProophServiceBusExtension extends AbstractExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            if ('prooph_service_bus' === $name) {
                $config = [
                    'command_buses' => [
                        'borobudur_command_bus' => [
                            'plugins' => [
                                'borobudur.bus.plugin.handle_command_strategy',
                            ],
                            'router'  => [
                                'type' => 'borobudur.default_command_bus.router',
                            ],
                        ],
                    ],
                ];

                $container->prependExtensionConfig($name, $config);

                break;
            }
        }
    }
}
