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

namespace Borobudur\Infrastructure\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TablePrefixSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    protected $prefix;

    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Append prefix table when load class metadata.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();
        if (strlen($this->prefix)) {
            if (0 !== strpos($classMetadata->getTableName(), $this->prefix)) {
                $classMetadata->setTableName(
                    $this->prefix . $classMetadata->getTableName()
                );
            }
        }
        $associations = $classMetadata->getAssociationMappings();

        foreach ($associations as $fieldName => $mapping) {

            if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $mappedField = &$classMetadata->associationMappings[$fieldName];

                if (!isset($mappedField['joinTable'])) {
                    continue;
                }

                $mappedTableName = $mappedField['joinTable']['name'];

                if (0 !== strpos($mappedTableName, $this->prefix)) {
                    $fieldName = $this->prefix . $mappedTableName;
                    $mappedField['joinTable']['name'] = $fieldName;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['loadClassMetadata'];
    }
}
