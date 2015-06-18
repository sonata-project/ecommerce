<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class ORMInheritanceSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $map = array();
    protected $productClass;

    /**
     * @param array  $map
     * @param string $productClass
     */
    public function __construct($map, $productClass)
    {
        $this->map          = $map;
        $this->productClass = $productClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->name !== $this->productClass) {
            return;
        }

        $metadata->setDiscriminatorColumn(array('name' => 'product_type', 'type' => 'string', 'length' => 64));
        $metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE);
        $metadata->setDiscriminatorMap($this->map);
    }
}
