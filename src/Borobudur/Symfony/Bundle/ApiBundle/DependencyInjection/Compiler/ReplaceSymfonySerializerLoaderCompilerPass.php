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

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\DependencyInjection\Compiler;

use Borobudur\Infrastructure\Symfony\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ReplaceSymfonySerializerLoaderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $chainLoader = $container->getDefinition('serializer.mapping.chain_loader');

        /** @var Definition[] $loaders */
        $loaders = $chainLoader->getArgument(0);

        foreach ($loaders as $definition) {
            $definition->setClass(YamlFileLoader::class);
        }

        $chainLoader->replaceArgument(0, $loaders);
        $container->getDefinition('serializer.mapping.cache_warmer')->replaceArgument(0, $loaders);
    }
}
