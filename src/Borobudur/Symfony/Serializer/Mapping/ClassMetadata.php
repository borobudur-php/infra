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

use Borobudur\Component\Parameter\ParameterInterface;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface as SymfonyAttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\ClassMetadata as SymfonyClassMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface as SymfonyClassMetadataInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ClassMetadata extends SymfonyClassMetadata implements ClassMetadataInterface
{
    /**
     * @var ParameterInterface
     */
    private $options;

    public function getOptions(): ?ParameterInterface
    {
        return $this->options;
    }

    public function setOptions(ParameterInterface $options): void
    {
        $this->options = $options;
    }

    public function addAttributeMetadata(SymfonyAttributeMetadataInterface $attributeMetadata)
    {
        if (false === $attributeMetadata instanceof AttributeMetadataInterface) {
            $newAttributeMetadata = new AttributeMetadata($attributeMetadata->getName());
            $newAttributeMetadata->merge($attributeMetadata);
            $attributeMetadata = $newAttributeMetadata;
        }

        parent::addAttributeMetadata($attributeMetadata);
    }

    public function merge(SymfonyClassMetadataInterface $classMetadata)
    {
        parent::merge($classMetadata);

        if ($classMetadata instanceof ClassMetadataInterface) {
            if (null !== $options = $classMetadata->getOptions()) {
                $this->options = $options;
            }
        }
    }
}
