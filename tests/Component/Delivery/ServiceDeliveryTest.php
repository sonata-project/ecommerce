<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\tests\Component\Delivery;

use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Delivery\FreeDelivery;
use Sonata\Component\Delivery\Pool;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\ProductBundle\Entity\BaseDelivery;

class ServiceDeliveryTest extends \PHPUnit_Framework_TestCase
{
    public function testPool()
    {
        $pool = new Pool();

        $delivery = new FreeDelivery(true);
        $delivery->setCode('free_1');

        $pool->addMethod($delivery);

        $delivery = new FreeDelivery(true);
        $delivery->setCode('free_2');

        $pool->addMethod($delivery);

        $delivery = new FreeDelivery(true);
        $delivery->setCode('free_2');  // same code

        $pool->addMethod($delivery);

        $this->assertSame(2, count($pool->getMethods()), 'Pool return 2 elements');
        $this->assertInstanceOf('Sonata\\Component\\Delivery\\FreeDelivery', $pool->getMethod('free_2'), 'Pool return an FreeDelivery Instance');
    }

    public function testGetStatusList()
    {
        $statusList = array(
            ServiceDeliveryInterface::STATUS_OPEN          => 'status_open',
            ServiceDeliveryInterface::STATUS_PENDING       => 'status_pending',
            ServiceDeliveryInterface::STATUS_SENT          => 'status_sent',
            ServiceDeliveryInterface::STATUS_CANCELLED     => 'status_cancelled',
            ServiceDeliveryInterface::STATUS_COMPLETED     => 'status_completed',
            ServiceDeliveryInterface::STATUS_RETURNED      => 'status_returned',
        );
        $this->assertSame($statusList, BaseDelivery::getStatusList());
        $this->assertSame($statusList, BaseServiceDelivery::getStatusList());
    }

    public function testGetOption()
    {
        $delivery = new FreeDelivery(true);

        $delivery->setOptions(array('option1' => 'value1'));

        $this->assertSame('value1', $delivery->getOption('option1'));
        $this->assertSame('default', $delivery->getOption('unexisting', 'default'));
        $this->assertNull($delivery->getOption('unexisting'));
    }
}
