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

use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Tests\Component\Product\Product;

class Delivery extends BaseServiceDelivery
{
    public function isAddressRequired()
    {
        return true;
    }

    public function getName()
    {
        return "delivery 1";
    }

    public function getVatRate()
    {
        return 19.60;
    }

    public function getPrice()
    {
        return 120;
    }

}

class BasketTest extends \PHPUnit_Framework_TestCase
{
    public function getMockProduct()
    {
        $product = $this->getMock('Sonata\Component\Product\ProductInterface', array(), array(), 'BasketTest_Product');
        $product->expects($this->any())->method('getId')->will($this->returnValue(42));
        $product->expects($this->any())->method('getName')->will($this->returnValue('Product name'));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(15));
        $product->expects($this->any())->method('isPriceIncludingVat')->will($this->returnValue(false));
        $product->expects($this->any())->method('getVatRate')->will($this->returnValue(19.6));
        $product->expects($this->any())->method('getOptions')->will($this->returnValue(array('foo' => 'bar')));
        $product->expects($this->any())->method('getDescription')->will($this->returnValue('product description'));
        $product->expects($this->any())->method('getEnabled')->will($this->returnValue(true));

        return $product;
    }

    public function getMockAddress()
    {
        $address = $this->getMock('Sonata\Component\Customer\AddressInterface', array(), array(), 'BasketTest_Address');
        $address->expects($this->any())->method('getName')->will($this->returnValue('Product name'));
        $address->expects($this->any())->method('getAddress1')->will($this->returnValue('Address1'));
        $address->expects($this->any())->method('getAddress2')->will($this->returnValue('Address2'));
        $address->expects($this->any())->method('getAddress3')->will($this->returnValue('Address3'));
        $address->expects($this->any())->method('getPostcode')->will($this->returnValue("75001"));
        $address->expects($this->any())->method('getCity')->will($this->returnValue("Paris"));
        $address->expects($this->any())->method('getCountryCode')->will($this->returnValue("FR"));
        $address->expects($this->any())->method('getPhone')->will($this->returnValue("0123456789"));

        return $address;
    }

    public function testTotal()
    {
        $currency = $this->getMock('Sonata\Component\Currency\Currency');

        $basket = new Basket;
        $basket->setCurrency($currency);

        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->any())->method('getClass')->will($this->returnValue('BasketTest_Product'));

        $productProvider = new ProductProviderTest($this->getMock('JMS\Serializer\SerializerInterface'));
        $productProvider->setCurrencyPriceCalculator(new CurrencyPriceCalculator());
        $productProvider->setEventDispatcher($this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface'));

        $productDefinition = new ProductDefinition($productProvider, $manager);

        $product = $this->getMockProduct();

        $product->expects($this->any())
            ->method('isRecurrentPayment')
            ->will($this->returnValue(false));

        $pool = new Pool;
        $pool->addProduct('product_code', $productDefinition);

        $basket->setProductPool($pool);

        $this->assertFalse($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        $basketElement = new BasketElement();
        $basketElement->setProductDefinition($productDefinition);
        $basketElement->setProduct('product_code', $product);

        $basket->addBasketElement($basketElement);

        $this->assertTrue($basket->hasProduct($product), '::hasProduct() - The product is present in the basket');

        $this->assertEquals(1, $basketElement->getQuantity(), '::getQuantity() - return 1');
        $this->assertEquals(15, $basketElement->getUnitPrice(false), '::getQuantity() - return 2');
        $this->assertEquals(15, $basketElement->getTotal(false), '::getQuantity() - return 2');

        $this->assertEquals(15, $basket->getTotal(false), '::getTotal() w/o vat return 15');
        $this->assertEquals(17.940, $basket->getTotal(true), '::getTotal() w/ vat return 18');

        $basketElement->setQuantity(2);

        $this->assertEquals(2, $basketElement->getQuantity(), '::getQuantity() - return 2');
        $this->assertEquals(15, $basketElement->getUnitPrice(false), '::getQuantity() - return 2');
        $this->assertEquals(30, $basketElement->getTotal(false), '::getQuantity() - return 2');
        $this->assertEquals(30, $basket->getTotal(false), '::getTotal() w/o vat return 30');
        $this->assertEquals(35.880, $basket->getTotal(true), '::getTotal() w/ vat return true');

        // Recurrent payments
        $this->assertEquals(0, $basket->getTotal(false, true), '::getTotal() for recurrent payments only');

        $newProduct = $this->getMockProduct();
        $newProduct->expects($this->any())
            ->method('isRecurrentPayment')
            ->will($this->returnValue(true));

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $newProduct);

        $basket->addBasketElement($basketElement);

        $this->assertEquals(30, $basket->getTotal(false, false), '::getTotal() for non-recurrent payments only');

        $basket->removeElement($basketElement);

        // Delivery
        $delivery = new Delivery();
        $basket->setDeliveryMethod($delivery);

        $this->assertEquals(150, $basket->getTotal(false), '::getTotal() - return 150');
        $this->assertEquals(179.400, $basket->getTotal(true), '::getTotal() w/o vat return 179.40');
        $this->assertEquals(29.400, $basket->getVatAmount(),  '::getVatAmount() w/o vat return 29.4');
    }

    public function testBasket()
    {
        $basket = $this->getPreparedBasket();

        $product = $this->getMockProduct();

        // check if the product is part of the basket
        $this->assertFalse($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $basket->addBasketElement($basketElement);

        $this->assertTrue($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        // Covering all of the isValid method
        $this->assertTrue($basket->isValid(true), '::isValid() return true for element only');
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check because payment address is invalid');

        $invalidBasketElement = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $invalidBasketElement->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));
        $invalidBasketElement->expects($this->any())
            ->method('getPosition')
            ->will($this->returnValue(1));
        $invalidBasketElement->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $basket->addBasketElement($invalidBasketElement);
        $this->assertFalse($basket->isValid(true), '::isValid() return false if an element is invalid');

        $basket->setBasketElements(array());
        $basket->addBasketElement($basketElement);
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check because payment address is invalid');

        $basket->setBillingAddress($this->getMockAddress());
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check because payment method is invalid');

        $basket->setPaymentMethod($this->getMock('Sonata\Component\Payment\PaymentInterface'));
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check because delivery method is invalid');

        $deliveryMethod = $this->getMock('Sonata\Component\Delivery\ServiceDeliveryInterface');
        $deliveryMethod->expects($this->any())
            ->method('isAddressRequired')
            ->will($this->returnValue(false));
        $basket->setDeliveryMethod($deliveryMethod);
        $this->assertTrue($basket->isValid(), '::isValid() return true for the complete check because delivery method doesn\'t require an address');

        $requiredDelivery = $this->getMock('Sonata\Component\Delivery\ServiceDeliveryInterface');
        $requiredDelivery->expects($this->any())
            ->method('isAddressRequired')
            ->will($this->returnValue(true));
        $basket->setDeliveryMethod($requiredDelivery);
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check because delivery address is invalid');

        $basket->setDeliveryAddress($this->getMockAddress());
        $this->assertTrue($basket->isValid(), '::isValid() return true for the complete check because everything is fine');

        $this->assertTrue($basket->isAddable($product), '::isAddable() return true');
        $this->assertFalse($basket->hasRecurrentPayment(), '::hasRecurrentPayment() return false');

        $this->assertTrue($basket->hasProduct($product), '::hasProduct() return true');

        $this->assertTrue($basket->hasBasketElements(), '::hasElement() return true ');
        $this->assertEquals(1, $basket->countBasketElements(), '::countElements() return 1');
        $this->assertNotEmpty($basket->getBasketElements(), '::getElements() is not empty');

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $element = $basket->getElement($product), '::getElement() - return a BasketElement');

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $basket->removeElement($element), '::removeElement() - return the removed BasketElement');

        $this->assertFalse($basket->hasBasketElements(), '::hasElement() return false');
        $this->assertEquals(0, $basket->countBasketElements(), '::countElements() return 0');
        $this->assertEmpty($basket->getBasketElements(), '::getElements() is empty');

        $basket->reset();
        $this->assertFalse($basket->isValid(), '::isValid() return false after reset');
    }

    public function testSerialize()
    {
        $product = $this->getMockProduct();

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->any())->method('getClass')->will($this->returnValue('BasketTest_Product'));

        $definition = new ProductDefinition($provider, $manager);

        $pool = new Pool;
        $pool->addProduct('product_code', $definition);

        $basket = new Basket;

        $basket->setProductPool($pool);

        $basket->addBasketElement($basketElement);

        $basket->setDeliveryAddress($this->getMockAddress());
        $basket->setBillingAddress($this->getMockAddress());
        $basket->setCustomer($this->getMock('Sonata\Component\Customer\CustomerInterface'));

        $data = $basket->serialize();

        $this->assertTrue(is_string($data));
        $this->assertStringStartsWith('a:11:', $data, 'the serialized array has 11 elements');

        // Ensuring all needed keys are present
        $expectedKeys = array(
            'basketElements',
            'positions',
            'deliveryMethodCode',
            'paymentMethodCode',
            'cptElement',
            'options',
            'locale',
            'currency',
            'deliveryAddress',
            'billingAddress',
            'customer'
        );

        $basketData = unserialize($data);

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $basketData);
        }

        $basket->setDeliveryAddressId(1);
        $basket->setBillingAddressId(2);
        $basket->setCustomerId(3);

        $data = $basket->serialize();

        $this->assertTrue(is_string($data));
        $this->assertStringStartsWith('a:11:', $data, 'the serialized array has 11 elements');

        // Ensuring all needed keys are present
        $expectedKeys = array(
            'basketElements',
            'positions',
            'deliveryMethodCode',
            'paymentMethodCode',
            'cptElement',
            'options',
            'locale',
            'currency',
            'deliveryAddressId',
            'billingAddressId',
            'customerId'
        );

        $basketData = unserialize($data);

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $basketData);
        }

        $basket->reset();
        $this->assertTrue(count($basket->getBasketElements()) == 0, '::reset() remove all elements');
        $basket->unserialize($data);
        $this->assertTrue(count($basket->getBasketElements()) == 1, '::unserialize() restore elements');
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The product does not exist
     */
    public function testGetElementRaisesException()
    {
        $basket = new Basket();
        $basket->getElement(new Product());
    }

    public function testHasRecurrentPayment()
    {
        $basket = $this->getPreparedBasket();

        $product = $this->getMockProduct();
        $product->expects($this->once())
            ->method('isRecurrentPayment')
            ->will($this->returnValue(true));

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $basket->addBasketElement($basketElement);

        $this->assertTrue($basket->hasRecurrentPayment());
    }

    public function testHasProduct()
    {
        $basket = $this->getPreparedBasket();

        $product = $this->getMockProduct();

        $this->assertFalse($basket->hasProduct($product), '::hasProduct false because basket is empty');

        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->any())->method('getProduct')->will($this->returnValue($product));
        $basketElement->expects($this->any())->method('getPosition')->will($this->returnValue(1042));

        $basket->addBasketElement($basketElement);

        $this->assertFalse($basket->hasProduct($product), '::hasProduct false because position invalid');

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $basket->addBasketElement($basketElement);

        $this->assertTrue($basket->hasProduct($product), '::hasProduct true');
    }

    public function testBuildPrices()
    {
        $basket = $this->getPreparedBasket();

        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockAddress()));
        $basketElement->expects($this->any())->method('getPosition')->will($this->returnValue(0));

        $basket->addBasketElement($basketElement);

        $basket->buildPrices();

        $this->assertEquals(0, count($basket->getBasketElements()));
    }

    public function testClean()
    {
        $basket = $this->getPreparedBasket();

        $product = $this->getMockProduct();

        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->any())->method('getProduct')->will($this->returnValue($product));
        $basketElement->expects($this->any())->method('getPosition')->will($this->returnValue(0));

        $deletedBasketElement = clone $basketElement;
        $deletedBasketElement->expects($this->any())->method('getDelete')->will($this->returnValue(true));

        $basket->addBasketElement($basketElement);
        $basket->addBasketElement($deletedBasketElement);

        $basket->clean();

        $this->assertEquals(1, count($basket->getBasketElements()));
    }

    public function testGettersSetters()
    {
        $basket = $this->getPreparedBasket();

        $product = $this->getMockProduct();

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $basket->setBasketElements(array($basketElement));
        $this->assertEquals(array($basketElement), $basket->getBasketElements());

        $basket->setDeliveryAddressId(1);
        $this->assertEquals(1, $basket->getDeliveryAddressId());

        $basket->setBillingAddressId(1);
        $this->assertEquals(1, $basket->getBillingAddressId());

        $deliveryMethod = $this->getMock('Sonata\Component\Delivery\ServiceDeliveryInterface');
        $deliveryMethod->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue(1));
        $basket->setDeliveryMethod($deliveryMethod);
        $this->assertEquals(1, $basket->getDeliveryMethodCode());

        $paymentMethod = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $paymentMethod->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue(1));
        $basket->setPaymentMethod($paymentMethod);
        $this->assertEquals(1, $basket->getPaymentMethodCode());

        $basket->setCustomerId(1);
        $this->assertEquals(1, $basket->getCustomerId());

        $options = array('option1' => 'value1', 'option2' => 'value2');
        $basket->setOptions($options);
        $this->assertNull($basket->getOption('unexisting_option'));
        $this->assertEquals(42, $basket->getOption('unexisting_option', 42));
        $this->assertEquals('value1', $basket->getOption('option1'));
        $this->assertEquals($options, $basket->getOptions());

        $basket->setOption('option3', 'value3');
        $this->assertEquals('value3', $basket->getOption('option3'));

        $basket->setLocale('en');
        $this->assertEquals('en', $basket->getLocale());
    }

    protected function getPreparedBasket()
    {
        $basket = new Basket();

        // create the provider mock
        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');

        $provider->expects($this->any())
            ->method('basketCalculatePrice')
            ->will($this->returnValue(15));

        $provider->expects($this->any())
            ->method('isAddableToBasket')
            ->will($this->returnValue(true));

        // create the product manager mock
        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->any())->method('getClass')->will($this->returnValue('BasketTest_Product'));

        $definition = new ProductDefinition($provider, $manager);

        $pool = new Pool;
        $pool->addProduct('product_code', $definition);

        $basket->setProductPool($pool);

        return $basket;
    }
}
