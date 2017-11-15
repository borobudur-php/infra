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

use Borobudur\Infrastructure\Symfony\Doctrine\EnableDoctrineInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $config, ContainerBuilder $container): void
    {
        if ($this instanceof EnableDoctrineInterface) {
            $this->enableDoctrine($container);
        }

        $this->loadServices($container);
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->loadMessages($container);
    }

    /**
     * Load the service.
     *
     * @param ContainerBuilder $container
     */
    protected function loadServices(ContainerBuilder $container): void
    {
        $path = $this->getConfigPath();
        $file = sprintf('%s/services.xml', $path);

        if (file_exists($file)) {
            $loader = new XmlFileLoader($container, new FileLocator($path));
            $loader->load('services.xml');
        }
    }

    /**
     * Load messages.
     *
     * @param ContainerBuilder $container
     */
    protected function loadMessages(ContainerBuilder $container): void
    {
        $path = $this->getConfigPath();
        $file = sprintf('%s/messages.yaml', $path);

        if (file_exists($file)) {
            $messages = Yaml::parseFile(
                $file,
                Yaml::PARSE_CONSTANT | Yaml::PARSE_CUSTOM_TAGS
            );

            if ($messages) {
                $container->prependExtensionConfig(
                    $this->getAppName(),
                    ['messages' => $messages]
                );
            }
        }
    }

    /**
     * Gets the config path.
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return $this->getDir() . '/../Resources/config';
    }

    /**
     * Gets current directory.
     *
     * @return string
     */
    protected function getDir(): string
    {
        $reflection = new \ReflectionClass(get_called_class());

        return dirname($reflection->getFileName());
    }

    /**
     * Gets the application name.
     *
     * @return string
     */
    protected function getModuleName(): string
    {
        return Container::underscore(
            substr(strrchr(get_class($this), '\\'), 1, -6)
        );
    }

    /**
     * Gets the application root name.
     *
     * @return string
     */
    protected function getAppName(): string
    {
        $class = get_class($this);

        return Container::underscore(
            substr($class, 0, strpos($class, '\\'))
        );
    }
}
