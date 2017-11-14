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

namespace Borobudur\Infrastructure\Symfony\Bundle\ProophMessagingBundle;

use Borobudur\Infrastructure\Symfony\Bundle\ProophMessagingBundle\DependencyInjection\BorobudurProophMessagingExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BorobudurProophMessagingBundle extends Bundle
{
    public function getContainerExtension()
    {
        $this->extension = new BorobudurProophMessagingExtension();

        return $this->extension;
    }
}
