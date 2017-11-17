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

namespace Borobudur\Infrastructure\Symfony\Serializer\Mapping\Loader;

use Borobudur\Component\Parameter\Parameter;
use Borobudur\Infrastructure\Symfony\Serializer\Mapping\AttributeMetadata;
use Borobudur\Infrastructure\Symfony\Serializer\Mapping\AttributeMetadataInterface;
use Borobudur\Infrastructure\Symfony\Serializer\Mapping\ClassMetadataInterface;
use Borobudur\Infrastructure\Symfony\Serializer\Mapping\Exception\MappingException;
use Borobudur\Infrastructure\Symfony\Serializer\Mapping\Version;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface as SymfonyClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader as SymfonyYamlFileLoader;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class YamlFileLoader extends SymfonyYamlFileLoader
{
    private $yamlParser;

    /**
     * An array of YAML class descriptions.
     *
     * @var array
     */
    private $classes = null;

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(SymfonyClassMetadataInterface $classMetadata)
    {
        if (null === $this->classes) {
            $this->classes = $this->getClassesFromYaml();
        }

        if (!$this->classes) {
            return false;
        }

        if (!isset($this->classes[$classMetadata->getName()])) {
            return false;
        }

        $yaml = $this->classes[$classMetadata->getName()];

        if (isset($yaml['attributes']) && is_array($yaml['attributes'])) {
            $attributesMetadata = $classMetadata->getAttributesMetadata();

            foreach ($yaml['attributes'] as $attribute => $data) {
                if (isset($attributesMetadata[$attribute])) {
                    $attributeMetadata = $attributesMetadata[$attribute];
                } else {
                    $attributeMetadata = new AttributeMetadata($attribute);
                    $classMetadata->addAttributeMetadata($attributeMetadata);
                }

                if (isset($data['groups'])) {
                    if (!is_array($data['groups'])) {
                        throw new MappingException(
                            sprintf(
                                'The "groups" key must be an array of strings in "%s" for the attribute "%s" of the class "%s".',
                                $this->file,
                                $attribute,
                                $classMetadata->getName()
                            )
                        );
                    }

                    foreach ($data['groups'] as $group) {
                        if (!is_string($group)) {
                            throw new MappingException(
                                sprintf(
                                    'Group names must be strings in "%s" for the attribute "%s" of the class "%s".',
                                    $this->file,
                                    $attribute,
                                    $classMetadata->getName()
                                )
                            );
                        }

                        $attributeMetadata->addGroup($group);
                    }
                }

                if (isset($data['max_depth'])) {
                    if (!is_int($data['max_depth'])) {
                        throw new MappingException(
                            sprintf(
                                'The "max_depth" value must be an integer in "%s" for the attribute "%s" of the class "%s".',
                                $this->file,
                                $attribute,
                                $classMetadata->getName()
                            )
                        );
                    }

                    $attributeMetadata->setMaxDepth($data['max_depth']);
                }

                if ($attributeMetadata instanceof AttributeMetadataInterface) {
                    $this->setVersion($attributeMetadata, $attribute, $classMetadata, $data);
                }
            }
        }

        if ($classMetadata instanceof ClassMetadataInterface) {
            if (isset($yaml['options'])) {
                if (!is_array($yaml['options'])) {
                    throw new MappingException(
                        sprintf(
                            'The "options" value must be an array in "%s"for the class "%s".',
                            $this->file,
                            $classMetadata->getName()
                        )
                    );
                }

                $classMetadata->setOptions(new Parameter($yaml['options']));
            }
        }

        return true;
    }

    private function setVersion(AttributeMetadataInterface $metadata, string $attribute, SymfonyClassMetadataInterface $classMetadata, array $data): void
    {
        if (isset($data['version'])) {
            $version = new Version();

            if (isset($data['version']['since'])) {
                if (!is_float($data['version']['since']) && !is_int($data['version']['since'])) {
                    throw new MappingException(
                        sprintf(
                            'The "since" value must be an float or integer in "%s" for the attribute "%s" of the class "%s".',
                            $this->file,
                            $attribute,
                            $classMetadata->getName()
                        )
                    );
                }

                $version->setSince((float) $data['version']['since']);
            }

            if (isset($data['version']['until'])) {
                if (!is_float($data['version']['until']) && !is_int($data['version']['until'])) {
                    throw new MappingException(
                        sprintf(
                            'The "until" value must be an float or integer in "%s" for the attribute "%s" of the class "%s".',
                            $this->file,
                            $attribute,
                            $classMetadata->getName()
                        )
                    );
                }

                $version->setUntil((float) $data['version']['until']);
            }

            $metadata->setVersion($version);
        }
    }

    private function getClassesFromYaml(): array
    {
        if (!stream_is_local($this->file)) {
            throw new MappingException(
                sprintf('This is not a local file "%s".', $this->file)
            );
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new Parser();
        }

        $classes = $this->yamlParser->parse(
            file_get_contents($this->file),
            Yaml::PARSE_KEYS_AS_STRINGS
        );

        if (empty($classes)) {
            return [];
        }

        if (!is_array($classes)) {
            throw new MappingException(
                sprintf('The file "%s" must contain a YAML array.', $this->file)
            );
        }

        return $classes;
    }
}
