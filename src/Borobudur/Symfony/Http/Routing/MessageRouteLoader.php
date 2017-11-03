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

namespace Borobudur\Infrastructure\Symfony\Http\Routing;

use Borobudur\Component\Messaging\Metadata\MetadataInterface;
use Borobudur\Component\Messaging\Metadata\RegistryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageRouteLoader implements LoaderInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $processor = new Processor();
        $configuration = new MessageConfiguration();
        $config = Yaml::parse($resource);
        $config = $processor->processConfiguration(
            $configuration,
            ['routing' => $config]
        );
        $routes = new RouteCollection();

        $path = $this->parsePath(sprintf('/%s', $config['path']));
        $methods = explode(',', $config['methods']);
        $indexRoute = $this->createRoute($config, $path, $methods);

        $routes->add(
            $this->getRouteName(
                $this->registry->get($config['message']),
                $config
            ),
            $indexRoute
        );

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'borobudur.message';
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        // Intentionally left blank.
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        // Intentionally left blank.
    }

    /**
     * Create a route.
     *
     * @param array $config
     * @param string $path
     * @param array $methods
     *
     * @return Route
     */
    private function createRoute(array $config, string $path, array $methods): Route
    {
        $controller = $config['controller'];
        $config['serialization_groups'] = explode(
            ',',
            $config['serialization_groups']
        );

        $defaults = [
            '_controller' => $controller,
            '_borobudur'  => $config,
        ];

        return new Route($path, $defaults, [], [], '', [], $methods);
    }

    /**
     * Gets the route name based on metadata and configuration.
     *
     * @param MetadataInterface $metadata
     * @param array             $config
     *
     * @return string
     */
    private function getRouteName(MetadataInterface $metadata, array $config): string
    {
        $section = isset($config['section']) ? $config['section'] : null;
        $sectionPrefix = $section ? $section . '_' : '';
        $availableMessages = ['query', 'command', 'event', 'message'];
        $names = [];

        foreach (explode('.', $metadata->getName()) as $name) {
            if (in_array($name, $availableMessages, true)) {
                continue;
            }

            $names[] = $name;
        }

        return sprintf(
            '%s_%s%s',
            $metadata->getApplicationName(),
            $sectionPrefix,
            implode('_', $names)
        );
    }

    /**
     * Parse given path.
     *
     * @param string $path
     *
     * @return string
     */
    private function parsePath(string $path): string
    {
        preg_match_all('/(:[a-zA-Z0-9_]+)/', $path, $matches);

        if (isset($matches[1]) && count($matches[1])) {
            $patterns = [];
            $replaces = [];
            foreach ($matches[1] as $param) {
                $param = str_replace(':', '', $param);
                $patterns[] = sprintf(':%s', $param);
                $replaces[] = sprintf('{%s}', $param);
            }

            $path = str_replace($patterns, $replaces, $path);
        }

        return $path;
    }
}
