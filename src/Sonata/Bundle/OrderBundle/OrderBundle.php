<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\OrderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Common\EventSubscriber;

class OrderBundle extends Bundle implements EventSubscriber {

    public function boot() {

        $evm = $this->container->get('doctrine.orm.entity_manager')->getEventManager();

        $evm->addEventSubscriber($this);
    }

    public function getSubscribedEvents() {
        return array(
            'loadClassMetadata'
        );
    }

    public function loadClassMetadata($eventArgs) {
        $metadata = $eventArgs->getClassMetadata();

        if($metadata->name == 'Sonata\Bundle\OrderBundle\Entity\BaseOrder') {
            
            $metadata->mapManyToOne(array(
                'fieldName'     => 'user',
                'targetEntity'  => $this->container->getParameter('doctrine_user.user_class'),
                'invertedBy'    => 'orders',
            ));
        }
    }
}
