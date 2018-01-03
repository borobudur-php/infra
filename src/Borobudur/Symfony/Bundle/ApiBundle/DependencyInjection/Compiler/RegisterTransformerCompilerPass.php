<?php
/**
 * This file is part of the Borobudur package.
 *
 * (c) 2018 Borobudur <http://borobudur.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RegisterTransformerCompilerPass implements CompilerPassInterface
{
    const TRANSFORMER_SERVICE_TAG = 'borobudur.transformer';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('borobudur.transformer.registry');

        $transformerServices = $container->findTaggedServiceIds(self::TRANSFORMER_SERVICE_TAG);

        foreach ($transformerServices as $id => $tags) {
            $registry->addMethodCall('add', [new Reference($id)]);
        }
    }
}
