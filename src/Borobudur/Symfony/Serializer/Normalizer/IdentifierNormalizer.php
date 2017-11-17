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

namespace Borobudur\Infrastructure\Symfony\Serializer\Normalizer;

use Borobudur\Component\Identifier\Identifier;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class IdentifierNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($object instanceof Identifier) {
            return $object->getScalarValue();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        if (!is_object($data)) {
            return false;
        }

        return $this->supports(get_class($data));
    }

    private function supports(string $class): bool
    {
        return in_array(Identifier::class, class_implements($class));
    }
}
