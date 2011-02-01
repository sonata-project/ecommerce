<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Doctrine\Common\EventSubscriber;

class SonataBasketBundle extends Bundle implements EventSubscriber
{

        /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }

    public function boot()
    {

        $evm = $this->container->get('doctrine.orm.entity_manager')->getEventManager();

        $evm->addEventSubscriber($this);
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

        if ($metadata->name == 'Sonata\BasketBundle\Entity\BaseAddress') {
//            var_dump('hetre'); die();
            $metadata->mapManyToOne(array(
                'fieldName'     => 'user',
                'targetEntity'  => $this->container->getParameter('doctrine_user.user_class'),
                'invertedBy'    => 'addresses',
            ));
        }
    }
}
