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

namespace Borobudur\Infrastructure\Symfony\Http\Routing;

use Borobudur\Component\Http\Controller\InvokableMessageControllerInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('routing');
        $methods = ['GET', 'PUT', 'PATCH', 'POST', 'DELETE'];

        $rootNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('message_property')
                    ->cannotBeEmpty()
                    ->defaultValue('message')
                ->end()
                ->scalarNode('message')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('path')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('controller')
                    ->cannotBeEmpty()
                    ->defaultValue(InvokableMessageControllerInterface::class)
                ->end()
                ->scalarNode('serialization_groups')
                    ->cannotBeEmpty()
                    ->defaultValue('default')
                ->end()
                ->scalarNode('serialization_version')->cannotBeEmpty()->end()
                ->scalarNode('methods')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function($values) use ($methods) {
                            $values = explode(',', $values);
                            foreach ($values as $method) {
                                if (!in_array(strtoupper($method), $methods, true)) {
                                    return true;
                                }
                            }

                            return false;
                        })
                        ->thenInvalid(
                            sprintf(
                                'Unknown method "%%s", available methods [%s]',
                                implode(',', $methods)
                            )
                        )
                    ->end()
                ->end()
                ->scalarNode('serialization_version')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
