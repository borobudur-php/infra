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

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle;

use Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\DependencyInjection\Compiler\RegisterTransformerCompilerPass;
use Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\DependencyInjection\Compiler\ReplaceSymfonySerializerClassMetadataFactoryCompilerPass;
use Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\DependencyInjection\Compiler\ReplaceSymfonySerializerLoaderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BorobudurApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ReplaceSymfonySerializerClassMetadataFactoryCompilerPass());
        $container->addCompilerPass(new ReplaceSymfonySerializerLoaderCompilerPass());
        $container->addCompilerPass(new RegisterTransformerCompilerPass());
    }
}
