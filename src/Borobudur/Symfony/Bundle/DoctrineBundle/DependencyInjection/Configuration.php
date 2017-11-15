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

use Borobudur\Infrastructure\Doctrine\EventSubscriber\TablePrefixSubscriber;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('borobudur_doctrine');

        $rootNode
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('table_prefix')->end()
                    ->scalarNode('table_prefix_subscriber_class')
                        ->defaultValue(TablePrefixSubscriber::class)
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
