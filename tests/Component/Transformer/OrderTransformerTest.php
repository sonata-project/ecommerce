<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Transformer;

use Sonata\Component\Transformer\OrderTransformer;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Currency\Currency;

class OrderTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasket()
    {
        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $provider->expects($this->once())->method('basketAddProduct')->will($this->returnValue(true));
        $provider->expects($this->once())->method('createBasketElement')->will($this->returnValue($basketElement = new BasketElement()));

        $product = $this->getMock('Sonata\Component\Product\ProductInterface');
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');

        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $pool = $this->getMock('Sonata\Component\Product\Pool');
        $pool->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $pool->expects($this->once())->method('getManager')->will($this->returnValue($manager));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('reset');
        $basket->expects($this->once())->method('buildPrices');

        $orderElement = $this->getMock('Sonata\Component\Order\OrderElementInterface');
        $orderElement->expects($this->exactly(2))->method('getProductType');
        $orderElement->expects($this->exactly(1))->method('getProductId')->will($this->returnValue(2));
        $orderElement->expects($this->exactly(1))->method('getOptions')->will($this->returnValue(array()));
        $orderElement->expects($this->exactly(1))->method('getQuantity')->will($this->returnValue(2));

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->once())->method('getOrderElements')->will($this->returnValue(array($orderElement)));
        $order->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $currency = new Currency();
        $currency->setLabel('EUR');
        $order->expects($this->once())->method('getCurrency')->will($this->returnValue($currency));

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $transformer = new OrderTransformer($pool, $eventDispatcher);
        $transformer->transformIntoBasket($order, $basket);

        $this->assertEquals(2, $basketElement->getQuantity());
    }
}
