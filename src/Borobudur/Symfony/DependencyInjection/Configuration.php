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

namespace Borobudur\Infrastructure\Symfony\DependencyInjection;

use Borobudur\Component\Messaging\Message\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $appName;

    /**
     * Constructor.
     *
     * @param string $appName
     */
    public function __construct($appName = 'borobudur')
    {
        $this->appName = $appName;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->appName);

        $rootNode
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('settings')
                        ->children()
                            ->scalarNode('metadata_service')
                                ->cannotBeEmpty()
                                ->defaultValue('borobudur.messaging.metadata_registry')
                            ->end()
                            ->booleanNode('enable_messaging')
                                ->cannotBeEmpty()
                                ->defaultTrue()
                            ->end()
                            ->arrayNode('message')
                                ->prototype('array')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('messages')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->arrayNode('classes')
                                    ->children()
                                        ->scalarNode('message')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('handler')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('factory')
                                            ->defaultValue(Factory::class)
                                            ->cannotBeEmpty()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
