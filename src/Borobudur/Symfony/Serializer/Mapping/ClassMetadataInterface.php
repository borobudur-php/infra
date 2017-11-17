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

use Borobudur\Component\Parameter\ParameterInterface;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface as SymfonyClassMetadataInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ClassMetadataInterface extends SymfonyClassMetadataInterface
{
    public function getOptions(): ?ParameterInterface;

    public function setOptions(ParameterInterface $options): void;
}
