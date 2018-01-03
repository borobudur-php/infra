<?php
/**
 * This file is part of the Borobudur package.
 *
 * (c) 2018 Borobudur <http://borobudur.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Borobudur\Infrastructure\Symfony\Hateoas;

use Borobudur\Component\Hateoas\RouterInterface;
use Symfony\Component\Routing\RouterInterface as SymfonyRouterInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class SymfonyRouterAdapter implements RouterInterface
{
    /**
     * @var SymfonyRouterInterface
     */
    private $router;

    public function __construct(SymfonyRouterInterface $router)
    {
        $this->router = $router;
    }

    public function generate(string $name, array $params = []): string
    {
        return $this->router->generate($name, $params);
    }

    public function getMethods(string $name): array
    {
        return $this->router->getRouteCollection()->get($name)->getMethods();
    }
}
