<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Delivery;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Delivery\FreeDelivery;
use Sonata\Component\Delivery\Pool;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\ProductBundle\Entity\BaseDelivery;

class ServiceDeliveryTest extends TestCase
{
    public function testPool(): void
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

        $this->assertCount(2, $pool->getMethods(), 'Pool return 2 elements');
        $this->assertInstanceOf(FreeDelivery::class, $pool->getMethod('free_2'), 'Pool return an FreeDelivery Instance');
    }

    public function testGetStatusList(): void
    {
        $statusList = [
            ServiceDeliveryInterface::STATUS_OPEN => 'status_open',
            ServiceDeliveryInterface::STATUS_PENDING => 'status_pending',
            ServiceDeliveryInterface::STATUS_SENT => 'status_sent',
            ServiceDeliveryInterface::STATUS_CANCELLED => 'status_cancelled',
            ServiceDeliveryInterface::STATUS_COMPLETED => 'status_completed',
            ServiceDeliveryInterface::STATUS_RETURNED => 'status_returned',
        ];
        $this->assertSame($statusList, BaseDelivery::getStatusList());
        $this->assertSame($statusList, BaseServiceDelivery::getStatusList());
    }

    public function testGetOption(): void
    {
        $delivery = new FreeDelivery(true);

        $delivery->setOptions(['option1' => 'value1']);

        $this->assertSame('value1', $delivery->getOption('option1'));
        $this->assertSame('default', $delivery->getOption('unexisting', 'default'));
        $this->assertNull($delivery->getOption('unexisting'));
    }
}
