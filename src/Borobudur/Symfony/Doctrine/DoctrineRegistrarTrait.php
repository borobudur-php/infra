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

use Borobudur\Infrastructure\Symfony\Exception\UnknownDriverException;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait DoctrineRegistrarTrait
{
    /**
     * @var string
     */
    protected $mappingFormat = DoctrineBundleInterface::MAPPING_ANNOTATION;

    /**
     * Scan and register doctrine orm.
     *
     * @param ContainerBuilder $container
     *
     * @throws UnknownDriverException
     */
    public function registerDoctrine(ContainerBuilder $container): void
    {
        foreach ($this->getSupportDrivers() as $driver) {
            $compilerMap = $this->getMappingCompilerPassInfo($driver);
            list($compilerPassClass, $compilerPassMethod) = $compilerMap;

            if (class_exists($compilerPassClass)) {
                if (!method_exists($compilerPassClass, $compilerPassMethod)) {
                    throw new InvalidConfigurationException(
                        'The "mappingFormat" value is invalid, must be "xml", "yml" or "annotation".'
                    );
                }

                switch ($this->mappingFormat) {
                    case DoctrineBundleInterface::MAPPING_XML:
                    case DoctrineBundleInterface::MAPPING_YAML:
                        $configFilesPath = $this->getConfigFilesPath();
                        $modelNamespace = $this->getModelNamespace();

                        $container->addCompilerPass(
                            $compilerPassClass::$compilerPassMethod(
                                [$configFilesPath => $modelNamespace],
                                [$this->getObjectManagerParameter()],
                                sprintf(
                                    '%s.driver.%s',
                                    $this->getBundlePrefix(),
                                    $driver
                                )
                            )
                        );
                        break;

                    case DoctrineBundleInterface::MAPPING_ANNOTATION:
                        $container->addCompilerPass(
                            $compilerPassClass::$compilerPassMethod(
                                [$this->getModelNamespace()],
                                [$this->getConfigFilesPath()],
                                [
                                    sprintf(
                                        '%s.object_manager',
                                        $this->getBundlePrefix()
                                    ),
                                ],
                                sprintf(
                                    '%s.driver.%s',
                                    $this->getBundlePrefix(),
                                    $driver
                                )
                            )
                        );
                        break;
                }
            }
        }
    }

    abstract public function getPath();

    abstract public function getBundlePrefix(): string;

    /**
     * Gets the mapping compiler pass based on driver.
     *
     * @param string $driver
     *
     * @return array
     * @throws UnknownDriverException
     */
    protected function getMappingCompilerPassInfo(string $driver): array
    {
        switch ($driver) {
            case DoctrineBundleInterface::DRIVER_DOCTRINE_ORM:
                $mappingPassClass = DoctrineOrmMappingsPass::class;
                break;
            default:
                throw new UnknownDriverException($driver);
        }

        $compilerPassMethod = sprintf(
            'create%sMappingDriver',
            ucfirst($this->mappingFormat)
        );

        return [$mappingPassClass, $compilerPassMethod];
    }

    /**
     * Gets the mapping directory to scan.
     *
     * @return string
     */
    protected function getMappingDirectory(): string
    {
        return 'Doctrine/Model';
    }

    /**
     * Gets the config file.
     *
     * @return string
     */
    protected function getConfigFilesPath(): string
    {
        return sprintf(
            '%s/../../../%s',
            $this->getPath(),
            $this->getMappingDirectory()
        );
    }

    /**
     * Gets the support drivers.
     *
     * @return array
     */
    protected function getSupportDrivers(): array
    {
        return [DoctrineBundleInterface::DRIVER_DOCTRINE_ORM];
    }

    /**
     * Gets the object manager parameter.
     *
     * @return string
     */
    protected function getObjectManagerParameter(): string
    {
        return sprintf('%s.object_manager', $this->getBundlePrefix());
    }

    abstract protected function getModelNamespace(): string;
}
