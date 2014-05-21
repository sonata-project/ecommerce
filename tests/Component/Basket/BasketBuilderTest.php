<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Basket;

use Sonata\Component\Basket\BasketBuilder;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Payment\Pool as PaymentPool;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Customer\AddressInterface;

class BasketBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage The product code is empty
     */
    public function testBuildWithInvalidProductCode()
    {
        $productPool = new ProductPool;

        $deliveryPool = new DeliveryPool;

        $paymentPool = new PaymentPool;

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');

        $basketBuilder = new BasketBuilder($productPool, $addressManager, $deliveryPool, $paymentPool);

        $basketElement_1 = $this->getMock('Sonata\Component\Basket\BasketElementInterface');

        $basketElements = array($basketElement_1);

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($basketElements));

        $basketBuilder->build($basket);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage  The product `non_existent_product_code` does not exist!
     */
    public function testBuildWithNonExistentProductCode()
    {
        $productPool = new ProductPool;

        $deliveryPool = new DeliveryPool;

        $paymentPool = new PaymentPool;

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');

        $basketBuilder = new BasketBuilder($productPool, $addressManager, $deliveryPool, $paymentPool);

        $basketElement_1 = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement_1->expects($this->exactly(2))->method('getProductCode')->will($this->returnValue('non_existent_product_code'));

        $basketElements = array($basketElement_1);

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($basketElements));

        $basketBuilder->build($basket);
    }

    public function testBuild()
    {
        $productProvider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $productManager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');

        $definition = new ProductDefinition($productProvider, $productManager);

        $productPool = new ProductPool;
        $productPool->addProduct('test', $definition);
        $deliveryPool = new DeliveryPool;

        $paymentPool = new PaymentPool;

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->exactly(2))->method('findOneBy')->will($this->returnValue($address));
        $basketBuilder = new BasketBuilder($productPool, $addressManager, $deliveryPool, $paymentPool);

        $basketElement_1 = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement_1->expects($this->exactly(2))->method('getProductCode')->will($this->returnValue('test'));
        $basketElement_1->expects($this->once())->method('setProductDefinition');

        $basketElements = array($basketElement_1);

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
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
