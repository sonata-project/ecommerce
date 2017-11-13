<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Transformer\OrderTransformer;

class OrderTransformerTest extends TestCase
{
    public function testBasket()
    {
        $provider = $this->createMock('Sonata\Component\Product\ProductProviderInterface');
        $provider->expects($this->once())->method('basketAddProduct')->will($this->returnValue(true));
        $provider->expects($this->once())->method('createBasketElement')->will($this->returnValue($basketElement = new BasketElement()));

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');

        $manager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $pool = $this->createMock('Sonata\Component\Product\Pool');
        $pool->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $pool->expects($this->once())->method('getManager')->will($this->returnValue($manager));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('reset');
        $basket->expects($this->once())->method('buildPrices');

        $orderElement = $this->createMock('Sonata\Component\Order\OrderElementInterface');
        $orderElement->expects($this->exactly(2))->method('getProductType');
        $orderElement->expects($this->exactly(1))->method('getProductId')->will($this->returnValue(2));
        $orderElement->expects($this->exactly(1))->method('getOptions')->will($this->returnValue([]));
        $orderElement->expects($this->exactly(1))->method('getQuantity')->will($this->returnValue(2));

        $order = $this->createMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->once())->method('getOrderElements')->will($this->returnValue([$orderElement]));
        $order->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $currency = new Currency();
        $currency->setLabel('EUR');
        $order->expects($this->once())->method('getCurrency')->will($this->returnValue($currency));

        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $transformer = new OrderTransformer($pool, $eventDispatcher);
        $transformer->transformIntoBasket($order, $basket);

        $this->assertEquals(2, $basketElement->getQuantity());
    }
}
