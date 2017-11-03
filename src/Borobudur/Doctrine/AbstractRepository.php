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

namespace Borobudur\Infrastructure\Doctrine;

use Borobudur\Component\Ddd;
use Borobudur\Component\Ddd\CollectionInterface;
use Borobudur\Component\Ddd\Lock\LockingInterface;
use Borobudur\Component\Ddd\Lock\OptimisticLock;
use Borobudur\Component\Ddd\Lock\PessimisticLock;
use Borobudur\Component\Ddd\Model;
use Borobudur\Component\Ddd\RepositoryInterface;
use Borobudur\Component\Identifier\Identifier;
use Doctrine\DBAL\LockMode;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractRepository extends AbstractDoctrineOrmRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function find(Identifier $id, LockingInterface $lockMode = null): ?Ddd\Model
    {
        $locking = null;
        $lockVersion = null;

        if ($lockMode instanceof PessimisticLock) {
            switch ($lockMode->getMode()) {
                case PessimisticLock::WRITE:
                    $locking = LockMode::PESSIMISTIC_WRITE;
                    break;
                case PessimisticLock::READ:
                    $locking = LockMode::PESSIMISTIC_READ;
                    break;
            }
        } elseif ($lockMode instanceof OptimisticLock) {
            $locking = LockMode::OPTIMISTIC;
            $lockVersion = $lockMode->getVersion();
        }

        /** @var mixed $model */
        $model = $this->_em->find(
            $this->_entityName,
            $id->getScalarValue(),
            $locking,
            $lockVersion
        );

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?Ddd\Model
    {
        $persister = $this->_em->getUnitOfWork()->getEntityPersister(
            $this->_entityName
        );

        /** @var Ddd\Model $model */
        $model = $persister->load($criteria, null, null, [], null, 1, $orderBy);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): Ddd\CollectionInterface
    {
        $persister = $this->_em->getUnitOfWork()->getEntityPersister(
            $this->_entityName
        );

        /** @var Ddd\CollectionInterface $collection */
        $collection = $persister->loadAll($criteria, $orderBy, $limit, $offset);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): CollectionInterface
    {
        return $this->findAllBy([]);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Model $model): void
    {
        $this->_em->persist($model);
        $this->_em->flush();
    }
}
