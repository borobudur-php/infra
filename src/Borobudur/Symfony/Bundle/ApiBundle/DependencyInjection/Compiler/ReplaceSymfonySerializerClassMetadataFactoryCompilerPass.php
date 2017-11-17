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

use Borobudur\Infrastructure\Symfony\Serializer\Mapping\ClassMetadataFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ReplaceSymfonySerializerClassMetadataFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $metadataFactoryDefinition = $container->getDefinition('serializer.mapping.class_metadata_factory');
        $metadataFactoryDefinition->setClass(ClassMetadataFactory::class);
    }
}
