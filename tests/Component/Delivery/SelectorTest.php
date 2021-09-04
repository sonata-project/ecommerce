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
use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Delivery\Selector;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Product\ProductInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\ProductBundle\Entity\BaseProduct;

class SelectorTest extends TestCase
{
    public function testEmptyBasket(): void
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $selector = new Selector($deliveryPool, $productPool);
        static::assertEmpty($selector->getAvailableMethods());
    }

    public function testNonExistentProduct(): void
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);

        $address = $this->createMock(AddressInterface::class);

        $selector = new Selector($deliveryPool, $productPool);
        static::assertEmpty($selector->getAvailableMethods($basket, $address));
    }

    /**
     * Test the getAvailableMethods methods with no basket nor address provided.
     */
    public function testGetAvailableMethodsWithoutBasket(): void
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $selector = new Selector($deliveryPool, $productPool);
        static::assertEmpty($selector->getAvailableMethods());
    }

    /**
     * Test the getAvailableMethods methods with an empty basket provided.
     */
    public function testGetAvailableMethodsWithEmptyBasket(): void
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();
        $basket = $this->createMock(Basket::class);
        $basket->expects(static::once())->method('getBasketElements')->willReturn([]);

        $selector = new Selector($deliveryPool, $productPool);
        static::assertEmpty($selector->getAvailableMethods($basket));
    }

    /**
     * Test the getAvailableMethods methods with a product provided but no address and no related delivery methods.
     */
    public function testGetAvailableMethodsWithFilledBasket(): void
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $basket = $this->createMock(Basket::class);
        $basketElement = $this->createMock(BasketElement::class);
        $product = $this->createMock(Product::class);

        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);
        $basketElement->expects(static::once())->method('getProduct')->willReturn($product);
        $product->expects(static::once())->method('getDeliveries')->willReturn([]);

        $selector = new Selector($deliveryPool, $productPool);
        static::assertEmpty($selector->getAvailableMethods($basket));
    }

    /**
     * Provide a delivery method that require an address but none is provided.
     */
    public function testGetAvailableMethodsWithRequiredAddressDelivery(): void
    {
        $deliveryPool = $this->createMock(DeliveryPool::class);
        $productPool = new ProductPool();

        $basket = $this->createMock(Basket::class);
        $basketElement = $this->createMock(BasketElement::class);
        $product = $this->createMock(Product::class);

        $delivery = $this->createMock(DeliveryInterface::class);
        $delivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');

        $serviceDelivery = $this->createMock(BaseServiceDelivery::class);
        $serviceDelivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');
        $serviceDelivery->expects(static::any())->method('getEnabled')->willReturn(true);
        $serviceDelivery->expects(static::any())->method('isAddressRequired')->willReturn(true);
        $deliveryPool->expects(static::any())->method('getMethod')->willReturn($serviceDelivery);

        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);
        $basketElement->expects(static::once())->method('getProduct')->willReturn($product);
        $product->expects(static::once())->method('getDeliveries')->willReturn([$delivery]);

        $selector = new Selector($deliveryPool, $productPool);
        static::assertSame([], $selector->getAvailableMethods($basket));
    }

    /**
     * Provide a delivery method that require an address but none is provided.
     */
    public function testGetAvailableMethodsWithDisabledDelivery(): void
    {
        $deliveryPool = $this->createMock(DeliveryPool::class);
        $productPool = new ProductPool();

        $basket = $this->createMock(Basket::class);
        $basketElement = $this->createMock(BasketElement::class);
        $product = $this->createMock(Product::class);

        $delivery = $this->createMock(DeliveryInterface::class);
        $delivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');

        $serviceDelivery = $this->createMock(BaseServiceDelivery::class);
        $serviceDelivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');
        $deliveryPool->expects(static::any())->method('getMethod')->willReturn($serviceDelivery);
        $serviceDelivery->expects(static::any())->method('getEnabled')->willReturn(false);

        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);
        $basketElement->expects(static::once())->method('getProduct')->willReturn($product);
        $product->expects(static::once())->method('getDeliveries')->willReturn([$delivery]);

        $selector = new Selector($deliveryPool, $productPool);
        static::assertSame([], $selector->getAvailableMethods($basket));
    }

    /**
     * Try to fetch a delivery based on an undefined code.
     */
    public function testGetAvailableMethodsWithUndefinedCode(): void
    {
        $deliveryPool = $this->createMock(DeliveryPool::class);
        $productPool = new ProductPool();

        $basket = $this->createMock(Basket::class);
        $basketElement = $this->createMock(BasketElement::class);
        $product = $this->createMock(Product::class);

        $delivery = $this->createMock(DeliveryInterface::class);
        $delivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');

        $deliveryPool->expects(static::any())->method('getMethod')->willReturn(null);

        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);
        $basketElement->expects(static::once())->method('getProduct')->willReturn($product);
        $product->expects(static::once())->method('getDeliveries')->willReturn([$delivery]);

        $selector = new Selector($deliveryPool, $productPool);
        static::assertSame([], $selector->getAvailableMethods($basket));
    }

    /**
     * Try to fetch a delivery not having a handled country.
     */
    public function testGetAvailableMethodsWithUncoveredCountry(): void
    {
        $deliveryPool = $this->createMock(DeliveryPool::class);
        $productPool = new ProductPool();

        $basket = $this->createMock(Basket::class);
        $basketElement = $this->createMock(BasketElement::class);
        $product = $this->createMock(Product::class);

        $delivery = $this->createMock(DeliveryInterface::class);
        $delivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');
        $delivery->expects(static::any())->method('getCountryCode')->willReturn('US');

        $serviceDelivery = $this->createMock(BaseServiceDelivery::class);
        $serviceDelivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');
        $serviceDelivery->expects(static::any())->method('getEnabled')->willReturn(true);
        $serviceDelivery->expects(static::any())->method('isAddressRequired')->willReturn(true);
        $deliveryPool->expects(static::any())->method('getMethod')->willReturn($serviceDelivery);

        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);
        $basketElement->expects(static::once())->method('getProduct')->willReturn($product);
        $product->expects(static::once())->method('getDeliveries')->willReturn([$delivery]);

        $address = $this->createMock(BaseAddress::class);
        $address->expects(static::exactly(2))->method('getCountryCode')->willReturn('FR');

        $selector = new Selector($deliveryPool, $productPool);
        static::assertSame([], $selector->getAvailableMethods($basket, $address));
    }

    /**
     * Provide twice the same delivery code.
     */
    public function testGetAvailableMethodsWithAlreadySelectedCode(): void
    {
        $deliveryPool = $this->createMock(DeliveryPool::class);

        $basket = $this->createMock(Basket::class);
        $basketElement = $this->createMock(BasketElement::class);
        $product = $this->createMock(Product::class);

        $delivery1 = $this->createMock(DeliveryInterface::class);
        $delivery1->expects(static::any())->method('getCode')->willReturn('deliveryTest');

        $delivery2 = $this->createMock(DeliveryInterface::class);
        $delivery2->expects(static::any())->method('getCode')->willReturn('deliveryTest');

        $serviceDelivery = $this->createMock(BaseServiceDelivery::class);
        $serviceDelivery->expects(static::any())->method('getCode')->willReturn('deliveryTest');
        $serviceDelivery->expects(static::any())->method('getEnabled')->willReturn(true);
        $serviceDelivery->expects(static::any())->method('isAddressRequired')->willReturn(false);
        $deliveryPool->expects(static::once())->method('getMethod')->willReturn($serviceDelivery);

        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);
        $basketElement->expects(static::once())->method('getProduct')->willReturn($product);
        $product->expects(static::once())->method('getDeliveries')->willReturn([$delivery1, $delivery2]);

        $selector = new Selector($deliveryPool, new ProductPool());

        static::assertSame([$serviceDelivery], $selector->getAvailableMethods($basket));
    }

    public function testAvailableMethods(): void
    {
        $deliveryMethod_low = new ServiceDelivery();
        $deliveryMethod_low->setCode('ups_low');
        $deliveryMethod_low->setEnabled(true);
        $deliveryMethod_low->setPriority(1);

        $deliveryMethod_high = new ServiceDelivery();
        $deliveryMethod_high->setCode('ups_high');
        $deliveryMethod_high->setEnabled(true);
        $deliveryMethod_high->setPriority(2);

        $deliveryMethod_high_bis = new ServiceDelivery();
        $deliveryMethod_high_bis->setCode('ups_high_bis');
        $deliveryMethod_high_bis->setEnabled(true);
        $deliveryMethod_high_bis->setPriority(3);

        $deliveryPool = new DeliveryPool();
        $deliveryPool->addMethod($deliveryMethod_low);
        $deliveryPool->addMethod($deliveryMethod_high);
        $deliveryPool->addMethod($deliveryMethod_high_bis);

        $productPool = new ProductPool();

        $productDelivery_low = $this->createMock(DeliveryInterface::class);
        $productDelivery_low->expects(static::any())->method('getCode')->willReturn('ups_low');

        $productDelivery_high = $this->createMock(DeliveryInterface::class);
        $productDelivery_high->expects(static::any())->method('getCode')->willReturn('ups_high');

        $productDelivery_high_bis = $this->createMock(DeliveryInterface::class);
        $productDelivery_high_bis->expects(static::any())->method('getCode')->willReturn('ups_high_bis');

        $product = $this->createMock(ProductInterface::class);
        $product->expects(static::once())->method('getDeliveries')->willReturn([$productDelivery_low, $productDelivery_high, $productDelivery_high_bis]);

        $basketElement = $this->createMock(BasketElementInterface::class);
        $basketElement->expects(static::once())->method('getProduct')->willReturn($product);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getBasketElements')->willReturn([$basketElement]);

        $address = $this->createMock(AddressInterface::class);

        $selector = new Selector($deliveryPool, $productPool);
        $selector->setLogger($this->createMock(LoggerInterface::class));

        $instances = $selector->getAvailableMethods($basket, $address);
        static::assertCount(3, $instances);
        static::assertSame($instances[0]->getCode(), 'ups_high_bis');
        static::assertSame($instances[1]->getCode(), 'ups_high');
        static::assertSame($instances[2]->getCode(), 'ups_low');
    }
}

class ServiceDelivery extends BaseServiceDelivery
{
    public function isAddressRequired()
    {
        return false;
    }
}

class Product extends BaseProduct
{
    protected $id;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
