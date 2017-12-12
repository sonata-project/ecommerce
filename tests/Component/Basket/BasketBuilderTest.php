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

namespace Sonata\Component\Tests\Basket;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketBuilder;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Payment\Pool as PaymentPool;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Product\ProductDefinition;

class BasketBuilderTest extends TestCase
{
    public function testBuildWithInvalidProductCode(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The product code is empty');

        $productPool = new ProductPool();

        $deliveryPool = new DeliveryPool();

        $paymentPool = new PaymentPool();

        $addressManager = $this->createMock('Sonata\Component\Customer\AddressManagerInterface');

        $basketBuilder = new BasketBuilder($productPool, $addressManager, $deliveryPool, $paymentPool);

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');

        $basketElements = [$basketElement];

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($basketElements));

        $basketBuilder->build($basket);
    }

    public function testBuildWithNonExistentProductCode(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The product definition `non_existent_product_code` does not exist!');

        $productPool = new ProductPool();

        $deliveryPool = new DeliveryPool();

        $paymentPool = new PaymentPool();

        $addressManager = $this->createMock('Sonata\Component\Customer\AddressManagerInterface');

        $basketBuilder = new BasketBuilder($productPool, $addressManager, $deliveryPool, $paymentPool);

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->exactly(2))->method('getProductCode')->will($this->returnValue('non_existent_product_code'));

        $basketElements = [$basketElement];

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($basketElements));

        $basketBuilder->build($basket);
    }

    public function testBuild(): void
    {
        $productProvider = $this->createMock('Sonata\Component\Product\ProductProviderInterface');
        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');

        $definition = new ProductDefinition($productProvider, $productManager);

        $productPool = new ProductPool();
        $productPool->addProduct('test', $definition);
        $deliveryPool = new DeliveryPool();

        $paymentPool = new PaymentPool();

        $address = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $addressManager = $this->createMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->exactly(2))->method('findOneBy')->will($this->returnValue($address));
        $basketBuilder = new BasketBuilder($productPool, $addressManager, $deliveryPool, $paymentPool);

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->exactly(2))->method('getProductCode')->will($this->returnValue('test'));
        $basketElement->expects($this->once())->method('setProductDefinition');

        $basketElements = [$basketElement];

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($basketElements));

        $basket->expects($this->once())->method('getDeliveryAddressId')->will($this->returnValue(1));
        $basket->expects($this->once())->method('getDeliveryMethodCode')->will($this->returnValue('ups'));
        $basket->expects($this->once())->method('getBillingAddressId')->will($this->returnValue(2));
        $basket->expects($this->once())->method('getPaymentMethodCode')->will($this->returnValue('credit_cart'));

        $basket->expects($this->once())->method('setDeliveryAddress');
        $basket->expects($this->once())->method('setDeliveryMethod');
        $basket->expects($this->once())->method('setBillingAddress');
        $basket->expects($this->once())->method('setPaymentMethod');

        $basket->expects($this->once())->method('buildPrices');

        $basketBuilder->build($basket);
    }
}
