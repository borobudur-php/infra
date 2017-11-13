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

use Borobudur\Infrastructure\Symfony\Bundle\MessagingBundle\DependencyInjection\Configuration as MessagingConfiguration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Configuration extends MessagingConfiguration implements ConfigurationInterface
{
    public function __construct($appName = null)
    {
        if (null !== $appName) {
            $this->configRootName = $appName;
        }
    }
}
