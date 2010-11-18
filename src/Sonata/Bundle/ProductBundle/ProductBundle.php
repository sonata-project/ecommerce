<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Common\EventSubscriber;

class ProductBundle extends Bundle implements EventSubscriber {

    public function boot() {

        $evm = $this->container->getDoctrine_Orm_EntityManagerService()->getEventManager();

        $evm->addEventSubscriber($this);
    }

    public function getSubscribedEvents() {
        return array(
            'loadClassMetadata'
        );
    }

    public function loadClassMetadata($eventArgs) {

        $metadata = $eventArgs->getClassMetadata();

        if($metadata->name !== 'Application\ProductBundle\Entity\Product') {

            return;
        }

        $metadata->setDiscriminatorColumn(array('name' => 'type', 'type' => 'string'));
        $metadata->setInheritanceType(\Doctrine\ORM\Mapping\ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE);

        $map = array();

        foreach($this->container->getSonata_Product_PoolService()->getProductDefinitions() as $code => $definition) {
            $map[$code] = $definition['class'];
        }

        $metadata->setDiscriminatorMap($map);
    }
}
