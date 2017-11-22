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
use Borobudur\Infrastructure\Symfony\Serializer\EntityManagerInterface;
use Borobudur\Infrastructure\Symfony\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class HalModelNormalizer extends ObjectNormalizer
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager = null, ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $this->entityManager = $entityManager;

        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        if (!isset($context['cache_key'])) {
            $context['cache_key'] = $this->getCacheKey($format, $context);
        }

        if ($this->isCircularReference($object, $context)) {
            return $this->handleCircularReference($object);
        }

        $data = [];
        $stack = [];
        $attributes = $this->getAttributes($object, $format, $context);
        $relations = [];
        $class = get_class($object);
        $attributesMetadata = $this->classMetadataFactory ? $this->classMetadataFactory->getMetadataFor($class)
            ->getAttributesMetadata() : null;

        if ($attributesMetadata instanceof ClassMetadataInterface) {
            $relations = $attributesMetadata->getOptions()->get('relations', []);
        }

        foreach ($attributes as $attribute) {
            if (null !== $attributesMetadata
                && $this->isMaxDepthReached(
                    $attributesMetadata,
                    $class,
                    $attribute,
                    $context
                )
            ) {
                continue;
            }

            $attributeValue = $this->getAttributeValue($object, $attribute, $format, $context);

            if (isset($this->callbacks[$attribute])) {
                $attributeValue = call_user_func($this->callbacks[$attribute], $attributeValue);
            }

            if (null !== $attributeValue && !is_scalar($attributeValue)) {
                $stack[$attribute] = $attributeValue;
            }

            $data = $this->updateData($data, $attribute, $attributeValue);
        }

        foreach ($stack as $attribute => $attributeValue) {
            if (!$this->serializer instanceof NormalizerInterface) {
                throw new LogicException(
                    sprintf(
                        'Cannot normalize attribute "%s" because the injected serializer is not a normalizer',
                        $attribute
                    )
                );
            }

            if (null !== $this->entityManager && isset($relations[$attribute]) && $attributeValue instanceof Model) {
                if (isset($relations[$attribute]['embedded']) && true === $relations[$attribute]['embedded']) {
                    if (!$this->entityManager->hasRelation($class, $attribute)) {
                        throw new LogicException(
                            sprintf(
                                'Attribute "%s" not related on class "%s"',
                                $attribute,
                                $class
                            )
                        );
                    }

                    if (!isset($data['_embedded'])) {
                        $data['_embedded'] = [];
                    }

                    $data['_embedded'] = $this->updateData(
                        $data['_embedded'],
                        $attribute,
                        $this->serializer->normalize(
                            $attributeValue,
                            $format,
                            $this->createChildContext($context, $attribute)
                        )
                    );

                    unset($data[$attribute]);
                } else {
                    $data[$attribute] = $attributeValue->getId()->getScalarValue();
                }

                continue;
            }

            $data = $this->updateData(
                $data,
                $attribute,
                $this->serializer->normalize($attributeValue, $format, $this->createChildContext($context, $attribute))
            );
        }

        return $data;
    }

    protected function getOriginAttributes($classOrObject, array $context, $attributesAsString = false): array
    {
        $allowed = parent::getAllowedAttributes($classOrObject, $context, $attributesAsString);
        $attributes = [];

        foreach ($allowed as $attribute) {
            if ($this->entityManager->hasRelation($attribute)) {
                continue;
            }

            $attributes[] = $attribute;
        }

        return $attributes;
    }

    private function updateData(array $data, $attribute, $attributeValue): array
    {
        if ($this->nameConverter) {
            $attribute = $this->nameConverter->normalize($attribute);
        }

        $data[$attribute] = $attributeValue;

        return $data;
    }

    private function getCacheKey($format, array $context)
    {
        try {
            return md5($format . serialize($context));
        } catch (\Exception $exception) {
            // The context cannot be serialized, skip the cache
            return false;
        }
    }

    private function isMaxDepthReached(array $attributesMetadata, $class, $attribute, array &$context): bool
    {
        if (!isset($context[static::ENABLE_MAX_DEPTH]) || !isset($attributesMetadata[$attribute])
            || null === $maxDepth = $attributesMetadata[$attribute]->getMaxDepth()
        ) {
            return false;
        }

        $key = sprintf(static::DEPTH_KEY_PATTERN, $class, $attribute);
        if (!isset($context[$key])) {
            $context[$key] = 1;

            return false;
        }

        if ($context[$key] === $maxDepth) {
            return true;
        }

        ++$context[$key];

        return false;
    }
}
