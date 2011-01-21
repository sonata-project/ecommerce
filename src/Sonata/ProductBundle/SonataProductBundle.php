<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Common\EventSubscriber;

class SonataProductBundle extends Bundle implements EventSubscriber
{

    public function boot() {

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

        if($metadata->name !== 'Application\Sonata\ProductBundle\Entity\Product') {

            return;
        }

        $metadata->setDiscriminatorColumn(array('name' => 'type', 'type' => 'string', 'length' => 16));
        $metadata->setInheritanceType(\Doctrine\ORM\Mapping\ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE);

        $map = array();

        foreach($this->container->get('sonata.product.pool')->getProductDefinitions() as $code => $definition) {
            $map[$code] = $definition['class'];
        }

        $metadata->setDiscriminatorMap($map);
    }


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
}
