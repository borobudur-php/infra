<?php
declare(strict_types=1);

namespace Borobudur\Infrastructure\Symfony\Bundle\ProophMessagingBundle\DependencyInjection;

use Borobudur\Component\Messaging\Message\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $configRootName = 'borobudur_messaging';

    /**
     * @var string
     */
    protected $servicePrefix = 'borobudur';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->configRootName);

        $rootNode
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('settings')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('service_prefix')
                                ->cannotBeEmpty()
                                ->defaultValue($this->servicePrefix)
                            ->end()
                            ->booleanNode('enable_messaging')
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
