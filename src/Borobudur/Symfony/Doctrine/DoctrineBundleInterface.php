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

namespace Borobudur\Infrastructure\Symfony\Doctrine;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface DoctrineBundleInterface
{
    public const DRIVER_DOCTRINE_ORM = 'doctrine/orm';

    public const MAPPING_XML = 'xml';

    public const MAPPING_YAML = 'yaml';

    public const MAPPING_ANNOTATION = 'annotation';

    /**
     * Scan and register doctrine orm.
     *
     * @param ContainerBuilder $container
     */
    public function registerDoctrine(ContainerBuilder $container): void;
}
