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

namespace Borobudur\Infrastructure\Symfony\Bundle;

use Borobudur\Infrastructure\Symfony\Doctrine\DoctrineBundleInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        if ($this instanceof DoctrineBundleInterface) {
            $this->registerDoctrine($container);
        }
    }

    /**
     * Gets the bundle prefix name.
     *
     * @return string
     */
    public function getBundlePrefix(): string
    {
        return Container::underscore(
            substr(strrchr(get_class($this), '\\'), 1, -6)
        );
    }
}
