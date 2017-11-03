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

namespace Borobudur\Infrastructure\Symfony\Http\Kernel;

use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractKernel extends HttpKernel
{
    use MicroKernelTrait;

    public const VERSION = '1.0.0-dev';

    public const VERSION_ID = '10000';

    public const MAJOR_VERSION = '1';

    public const MINOR_VERSION = '0';

    public const RELEASE_VERSION = '0';

    public const EXTRA_VERSION = 'dev';

    /**
     * Register bundles.
     *
     * @return iterable
     */
    final public function registerBundles(): iterable
    {
        foreach ($this->bundles() as $bundleConfigFile) {
            $bundles = $this->loadBundleDependencies(require $bundleConfigFile);
            foreach ($bundles as $class => $envs) {
                if (isset($envs['all']) || isset($envs[$this->environment])) {
                    yield new $class();
                }
            }
        }
    }

    /**
     * Load bundle dependencies.
     *
     * @param array $bundles
     *
     * @return array
     */
    protected function loadBundleDependencies(array $bundles): array
    {
        foreach (array_keys($bundles) as $bundle) {
            $reflection = new ReflectionClass($bundle);
            $path = dirname($reflection->getFileName());
            $prefixPath = $this->getBundlesPrefixPath();
            $configName = $this->getBundlesConfigFilename();

            $bundleConfig = $path . '/' . $prefixPath . '/' . $configName;

            if (file_exists($bundleConfig)) {
                array_merge(
                    $bundles,
                    $this->loadBundleDependencies(
                        require $bundleConfig
                    )
                );
            }
        }

        return $bundles;
    }

    protected function getBundlesPrefixPath(): string
    {
        return 'Resources/config';
    }

    protected function getBundlesConfigFilename(): string
    {
        return 'bundles.php';
    }

    /**
     * Gets all registered bundles.
     *
     * @return array
     */
    abstract protected function bundles(): array;
}
