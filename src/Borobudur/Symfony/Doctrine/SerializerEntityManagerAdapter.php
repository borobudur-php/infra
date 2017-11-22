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

namespace Borobudur\Infrastructure\Symfony\Doctrine;

use Borobudur\Infrastructure\Symfony\Serializer\EntityManagerInterface;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class SerializerEntityManagerAdapter implements EntityManagerInterface
{
    /**
     * @var DoctrineEntityManagerInterface
     */
    private $entityManager;

    public function __construct(DoctrineEntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function hasRelation(string $modelClass, string $attribute): bool
    {
        $classMetadata = $this->entityManager->getClassMetadata($modelClass);

        return $classMetadata->hasAssociation($attribute);
    }
}
