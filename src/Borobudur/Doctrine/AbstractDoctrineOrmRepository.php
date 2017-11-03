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

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractDoctrineOrmRepository implements Selectable
{
    /**
     * @var string
     */
    protected $_entityName;

    /**
     * @var EntityManager
     */
    protected $_em;

    /**
     * @var Mapping\ClassMetadata
     */
    protected $_class;

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager         $em    The EntityManager to use.
     * @param Mapping\ClassMetadata $class The class descriptor.
     */
    public function __construct($em, Mapping\ClassMetadata $class)
    {
        $this->_entityName = $class->name;
        $this->_em = $em;
        $this->_class = $class;
    }

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity
     * name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        return $this->_em->createQueryBuilder()
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy);
    }

    /**
     * Creates a new result set mapping builder for this entity.
     *
     * The column naming strategy is "INCREMENT".
     *
     * @param string $alias
     *
     * @return ResultSetMappingBuilder
     */
    public function createResultSetMappingBuilder($alias)
    {
        $rsm = new ResultSetMappingBuilder(
            $this->_em,
            ResultSetMappingBuilder::COLUMN_RENAMING_INCREMENT
        );
        $rsm->addRootEntityFromClassMetadata($this->_entityName, $alias);

        return $rsm;
    }

    /**
     * Creates a new Query instance based on a predefined metadata named query.
     *
     * @param string $queryName
     *
     * @return Query
     * @throws Mapping\MappingException
     */
    public function createNamedQuery($queryName)
    {
        return $this->_em->createQuery(
            $this->_class->getNamedQuery($queryName)
        );
    }

    /**
     * Creates a native SQL query.
     *
     * @param string $queryName
     *
     * @return NativeQuery
     * @throws Mapping\MappingException
     */
    public function createNativeNamedQuery($queryName)
    {
        $queryMapping = $this->_class->getNamedNativeQuery($queryName);
        $rsm = new Query\ResultSetMappingBuilder($this->_em);
        $rsm->addNamedNativeQueryMapping($this->_class, $queryMapping);

        return $this->_em->createNativeQuery($queryMapping['query'], $rsm);
    }

    /**
     * Clears the repository, causing all managed entities to become detached.
     *
     * @return void
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function clear()
    {
        $this->_em->clear($this->_class->rootEntityName);
    }

    /**
     * Adds support for magic finders.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return array|object The found entity/entities.
     *
     * @throws ORMException
     * @throws \BadMethodCallException If the method called is an invalid find*
     *                                 method or no find* method at all and
     *                                 therefore an invalid method call.
     */
    public function __call($method, $arguments)
    {
        switch (true) {
            case (0 === strpos($method, 'findBy')):
                $by = substr($method, 6);
                $method = 'findBy';
                break;

            case (0 === strpos($method, 'findOneBy')):
                $by = substr($method, 9);
                $method = 'findOneBy';
                break;

            default:
                throw new \BadMethodCallException(
                    "Undefined method '$method'. The method name must start with "
                    .
                    "either findBy or findOneBy!"
                );
        }

        if (empty($arguments)) {
            throw ORMException::findByRequiresParameter($method.$by);
        }

        $fieldName = lcfirst(\Doctrine\Common\Util\Inflector::classify($by));

        if ($this->_class->hasField($fieldName)
            || $this->_class->hasAssociation($fieldName)
        ) {
            switch (count($arguments)) {
                case 1:
                    return $this->$method([$fieldName => $arguments[0]]);

                case 2:
                    return $this->$method(
                        [$fieldName => $arguments[0]],
                        $arguments[1]
                    );

                case 3:
                    return $this->$method(
                        [$fieldName => $arguments[0]],
                        $arguments[1],
                        $arguments[2]
                    );

                case 4:
                    return $this->$method(
                        [$fieldName => $arguments[0]],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3]
                    );

                default:
                    // Do nothing
            }
        }

        throw ORMException::invalidFindByCall(
            $this->_entityName,
            $fieldName,
            $method.$by
        );
    }

    /**
     * @return string
     */
    protected function getEntityName()
    {
        return $this->_entityName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->getEntityName();
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->_em;
    }

    /**
     * @return Mapping\ClassMetadata
     */
    protected function getClassMetadata()
    {
        return $this->_class;
    }

    /**
     * Select all elements from a selectable that match the expression and
     * return a new collection containing these elements.
     *
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function matching(Criteria $criteria)
    {
        $persister = $this->_em->getUnitOfWork()->getEntityPersister(
            $this->_entityName
        );

        return new LazyCriteriaCollection($persister, $criteria);
    }
}
