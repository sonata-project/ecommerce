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

namespace Sonata\Component\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\Component\Transformer\OrderTransformer;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderTransformerTest extends TestCase
{
    public function testBasket(): void
    {
        $provider = $this->createMock(ProductProviderInterface::class);
        $provider->expects($this->once())->method('basketAddProduct')->willReturn(true);
        $provider->expects($this->once())->method('createBasketElement')->willReturn($basketElement = new BasketElement());

        $product = $this->createMock(ProductInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $manager = $this->createMock(ProductManagerInterface::class);
        $manager->expects($this->once())->method('findOneBy')->willReturn($product);

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->once())->method('getProvider')->willReturn($provider);
        $pool->expects($this->once())->method('getManager')->willReturn($manager);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('reset');
        $basket->expects($this->once())->method('buildPrices');

        $orderElement = $this->createMock(OrderElementInterface::class);
        $orderElement->expects($this->exactly(2))->method('getProductType');
        $orderElement->expects($this->exactly(1))->method('getProductId')->willReturn(2);
        $orderElement->expects($this->exactly(1))->method('getOptions')->willReturn([]);
        $orderElement->expects($this->exactly(1))->method('getQuantity')->willReturn(2);

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())->method('getOrderElements')->willReturn([$orderElement]);
        $order->expects($this->once())->method('getCustomer')->willReturn($customer);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $order->expects($this->once())->method('getCurrency')->willReturn($currency);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $transformer = new OrderTransformer($pool, $eventDispatcher);
        $transformer->transformIntoBasket($order, $basket);

        $this->assertSame(2, $basketElement->getQuantity());
    }
}
