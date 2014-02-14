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
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class ORMInheritanceSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $map = array();

    /**
     * @param array $map
     */
    public function __construct($map)
    {
        $this->map = $map;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata'
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
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
