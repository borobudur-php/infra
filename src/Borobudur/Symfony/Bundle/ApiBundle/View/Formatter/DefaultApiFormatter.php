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

namespace Borobudur\Infrastructure\Symfony\Bundle\ApiBundle\View\Formatter;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class DefaultApiFormatter extends AbstractFormatter
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    protected function transform(View $view, Request $request, string $format): array
    {
        return [
            'meta' => [
                'version' => $this->getVersion($request),
                'status'  => $this->getStatusCode($view, $view->getData()),
            ],
            'data' => $this->serializer->normalize(
                $view->getData(),
                $format,
                $this->convertContext($view->getContext())
            )
        ];
    }
}
