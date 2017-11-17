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

namespace Borobudur\Infrastructure\Symfony\Serializer\Mapping;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Version
{
    /**
     * @var float
     */
    private $since;

    /**
     * @var float
     */
    private $until;

    public function getSince(): ?float
    {
        return $this->since;
    }

    public function setSince(float $since): void
    {
        $this->since = $since;
    }

    public function getUntil(): ?float
    {
        return $this->until;
    }

    public function setUntil(float $until): void
    {
        $this->until = $until;
    }

    public function isOutsideRange(float $version): bool
    {
        return (null !== $this->since && $this->since > $version) || (null !== $this->until && $this->until < $version);
    }
}
