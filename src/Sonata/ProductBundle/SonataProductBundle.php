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

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Sonata\ProductBundle\DependencyInjection\AddProductProviderPass;


class SonataProductBundle extends Bundle implements EventSubscriber
{
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

        if ($metadata->name !== 'Application\Sonata\ProductBundle\Entity\Product') {
            return;
        }

        $metadata->setDiscriminatorColumn(array('name' => 'type', 'type' => 'string', 'length' => 64));
        $metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE);

        $map = array();

        foreach ($this->container->get('sonata.product.pool')->getProducts() as $code => $products) {
            $map[$code] = $products->getManager()->getClass();
        }

        $metadata->setDiscriminatorMap($map);
    }

    public function build(ContainerBuilder $container)
    {

        parent::build($container);
        $container->addCompilerPass(new AddProductProviderPass());
    }
}
