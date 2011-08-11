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
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class ORMInheritanceSubscriber implements EventSubscriber
{
    protected $map = array();

    public function __construct($map)
    {
        $this->map = $map;
    }

    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata'
        );
    }

    public function loadClassMetadata($eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->name !== 'Application\Sonata\ProductBundle\Entity\Product') {
            return;
        }

        $metadata->setDiscriminatorColumn(array('name' => 'product_type', 'type' => 'string', 'length' => 64));
        $metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE);
        $metadata->setDiscriminatorMap($this->map);
    }
}
