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

namespace Borobudur\Infrastructure\Symfony\Serializer\Mapping;

use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface as BaseAttributeMetadataInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AttributeMetadataInterface extends BaseAttributeMetadataInterface
{
    public function setVersion(Version $version): void;

    public function getVersion(): ?Version;
}
