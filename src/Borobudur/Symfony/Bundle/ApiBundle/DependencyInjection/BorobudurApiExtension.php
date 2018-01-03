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

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\DependencyInjection;

use Borobudur\Component\Transformer\TransformerInterface;
use Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\DependencyInjection\Compiler\RegisterTransformerCompilerPass;
use Borobudur\Infrastructure\Symfony\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BorobudurApiExtension extends AbstractExtension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(TransformerInterface::class)
            ->addTag(RegisterTransformerCompilerPass::TRANSFORMER_SERVICE_TAG);

        parent::load($config, $container);
    }
}
