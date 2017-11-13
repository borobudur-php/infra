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

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\View;

use Borobudur\Component\Messaging\Request\ConfigurationInterface;
use Borobudur\Infrastructure\Symfony\View\Api\RendererInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler as RestViewHandler;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Renderer implements RendererInterface
{
    /**
     * @var RestViewHandler
     */
    private $restViewHandler;

    public function __construct(RestViewHandler $restViewHandler)
    {
        $this->restViewHandler = $restViewHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ConfigurationInterface $configuration, View $view): Response
    {
        if (!$configuration->isHtmlRequest()) {
            $this->restViewHandler->setExclusionStrategyGroups(
                $configuration->getSerializationGroups()
            );

            if ($version = $configuration->getSerializationVersion()) {
                $this->restViewHandler->setExclusionStrategyVersion($version);
            }

            $view->getContext()->enableMaxDepth();
        }

        return $this->restViewHandler->handle($view);
    }
}
