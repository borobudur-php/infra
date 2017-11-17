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

namespace Borobudur\Infrastructure\Symfony\Serializer\Normalizer;

use Borobudur\Component\Ddd\Model;
use Borobudur\Infrastructure\Symfony\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as SymfonyObjectNormalizer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ObjectNormalizer extends SymfonyObjectNormalizer
{
    /**
     * {@inheritdoc}
     */
    protected function handleCircularReference($object)
    {
        if ($object instanceof Model) {
            return $object->getId()->getScalarValue();
        }

        return null;
    }

    protected function getAllowedAttributes($classOrObject, array $context, $attributesAsString = false)
    {
        if (!$this->classMetadataFactory) {
            return false;
        }

        $groups = false;
        if (isset($context[static::GROUPS]) && is_array($context[static::GROUPS])) {
            $groups = $context[static::GROUPS];
        } elseif (!isset($context[static::ALLOW_EXTRA_ATTRIBUTES]) || $context[static::ALLOW_EXTRA_ATTRIBUTES]) {
            return false;
        }

        $allowedAttributes = [];
        $metadata = $this->classMetadataFactory->getMetadataFor($classOrObject);
        $attributesMetadata = $metadata->getAttributesMetadata();

        foreach ($attributesMetadata as $attributeMetadata) {
            $name = $attributeMetadata->getName();

            if ((false === $groups || count(array_intersect($attributeMetadata->getGroups(), $groups)))
                && $this->isAllowedAttribute($classOrObject, $name, null, $context)
            ) {
                if ($attributeMetadata instanceof AttributeMetadataInterface) {
                    if (isset($context['version']) && null !== $version = $attributeMetadata->getVersion()) {
                        if ($version->isOutsideRange((float) $context['version'])) {
                            continue;
                        }
                    }
                }

                $allowedAttributes[] = $attributesAsString ? $name : $attributeMetadata;
            }
        }

        return $allowedAttributes;
    }
}
