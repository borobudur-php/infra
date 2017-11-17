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

use Symfony\Component\Serializer\Mapping\AttributeMetadata as SymfonyAttributeMetadata;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface as SymfonyAttributeMetadataInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AttributeMetadata extends SymfonyAttributeMetadata implements AttributeMetadataInterface
{
    /**
     * @var Version
     */
    private $version;

    public function getVersion(): ?Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(SymfonyAttributeMetadataInterface $attributeMetadata): void
    {
        parent::merge($attributeMetadata);

        if ($attributeMetadata instanceof AttributeMetadataInterface) {
            if (null !== $this->getVersion()) {
                $this->setVersion($attributeMetadata->getVersion());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['version']);
    }
}
