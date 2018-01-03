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

namespace Borobudur\Infrastructure\Symfony\Serializer\Normalizer;

use Borobudur\Component\Transformer\Registry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TransformerNormalizer implements NormalizerInterface
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $transformer = $this->registry->get($object);
        $data = $transformer->transform($object, $context);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $this->registry->support($data);
    }
}
