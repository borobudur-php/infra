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

use Borobudur\Component\Hateoas\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SymfonyNormalizerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class SymfonyNormalizerAdapter implements NormalizerInterface
{
    /**
     * @var SymfonyNormalizerInterface
     */
    private $normalizer;

    public function __construct(SymfonyNormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($data, array $context = [])
    {
        return $this->normalizer->normalize($data, null, $context);
    }
}
