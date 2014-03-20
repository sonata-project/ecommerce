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

use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Product\ProductDefinition;
use Sonata\ProductBundle\Model\BaseProductProvider;

class ProductProviderTest extends BaseProductProvider
{
    /**
     * @return string
     */
    public function getBaseControllerName()
    {
        return 'DumbTestController';
    }

}

class BasketElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up unit test
     */
    public function setUp()
    {
        bcscale(3);
    }

    public function getBasketElement($product = null)
    {
        $product = $this->getMock('Sonata\Component\Product\ProductInterface', array(), array(), 'BasketTest_Product');
        $product->expects($this->any())->method('getId')->will($this->returnValue(42));
        $product->expects($this->any())->method('getName')->will($this->returnValue('Product name'));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(15));
        $product->expects($this->any())->method('isPriceIncludingVat')->will($this->returnValue(false));
        $product->expects($this->any())->method('getVatRate')->will($this->returnValue(19.6));
        $product->expects($this->any())->method('getOptions')->will($this->returnValue(array('option1' => 'toto')));
        $product->expects($this->any())->method('getDescription')->will($this->returnValue('product description'));

        $productProvider = new ProductProviderTest($this->getMock('JMS\Serializer\SerializerInterface'));
        $productProvider->setCurrencyPriceCalculator(new CurrencyPriceCalculator());
        $productProvider->setEventDispatcher($this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface'));
        $productManager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);
        $basketElement->setProductDefinition($productDefinition);

        $currency = new Currency();
        $currency->setLabel("EUR");

        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();
        $basket->expects($this->any())->method('getCurrency')->will($this->returnValue($currency));

        $productProvider->updateComputationPricesFields($basket, $basketElement, $product);

        return $basketElement;
    }

    public function testPrice()
    {
        $basketElement = $this->getBasketElement();

        $this->assertEquals(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        $this->assertEquals(1, $basketElement->getQuantity(), 'BasketElement returns the correct default quantity');

        $this->assertEquals(15, $basketElement->getUnitPrice(), 'BasketElement return the correct price w/o VAT');
        $this->assertEquals(17.940, $basketElement->getUnitPrice(true), 'BasketElement return the correct price w/ VAT');

        $this->assertEquals(15, $basketElement->getTotal(), 'BasketElement return the correct price w/o VAT');
        $this->assertEquals(17.940, $basketElement->getTotal(true), 'BasketElement return the correct price w VAT');

        $this->assertEquals(2.940, $basketElement->getVatAmount(), 'BasketElement returns the correct VAT amount');
    }

    public function testOptions()
    {
        $basketElement = $this->getBasketElement();

        $this->assertEquals(true, $basketElement->hasOption('option1'), 'BasketElement has one option : option1');
        $this->assertEquals(false, $basketElement->hasOption('fake'), 'BasketElement has not option : fake');

        $this->assertEquals('toto', $basketElement->getOption('option1'), 'option1 option = toto');
        $this->assertEquals(null, $basketElement->getOption('fake'), 'fake option = null');
    }

    public function testQuantity()
    {
        $basketElement = $this->getBasketElement();
        $basketElement->setQuantity(10);

        $this->assertEquals(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        $this->assertEquals(150, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT');
        $this->assertEquals(179.4, $basketElement->getTotal(true), 'BasketElement returns the correct price w/ VAT');

        $basketElement->setQuantity(-10);
        $this->assertEquals(15, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT when negative quantity set');

        $basketElement->setQuantity(0);
        $this->assertEquals(0, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT when no quantity set');
    }

    public function testValidity()
    {
        $product = $this->getMock('Sonata\Component\Product\ProductInterface', array(), array(), 'BasketTest_Product');
        $product->expects($this->once())->method('getEnabled')->will($this->returnValue(true));

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $this->assertEquals(true, $basketElement->isValid(), 'BasketElement is valid');

        $product = $this->getMock('Sonata\Component\Product\ProductInterface', array(), array(), 'BasketTest_Product');
        $product->expects($this->once())->method('getEnabled')->will($this->returnValue(false));
        $basketElement->setProduct('product_code', $product);

        $this->assertEquals(false, $basketElement->isValid(), 'BasketElement returns the correct default quantity');
    }

    public function testGettersSetters()
    {
        $currency = $this->getMock('Sonata\Component\Currency\Currency');
        $productProvider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $productManager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProductDefinition($productDefinition);

        $this->assertEquals(0, $basketElement->getVatRate());
        $this->assertEquals(0, $basketElement->getUnitPrice($currency));
        $this->assertFalse($basketElement->isValid());

        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');

        $productDefinition = new ProductDefinition($provider, $manager);

        // Tests getProduct
        $this->assertNull($basketElement->getProduct());

        $basketElement->setProductDefinition($productDefinition);

        $this->assertNull($basketElement->getProduct());

        $product = $this->getMock('Sonata\Component\Product\ProductInterface');
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(42));

        $basketElement->setProduct('product_code', $product);
        $this->assertEquals($product, $basketElement->getProduct());

        // Tests setProductId
        $basketElement->setProductId(42);
        $this->assertEquals(42, $basketElement->getProductId());

        $basketElement->setProductId(24);
        $this->assertNull($basketElement->getProductId());

        $manager->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($product));

        $basketElement->setProductDefinition(new ProductDefinition($provider, $manager));

        $basketElement->setProductId(42);
        $basketElement->setProduct('product_code', $product); // Done by the provider hereby mocked, hence we do it manually
        $this->assertEquals($product->getId(), $basketElement->getProductId());

        // Options
        $options = array('option1' => 'value1', 'option2' => 'value2');
        $basketElement->setOptions($options);
        $this->assertNull($basketElement->getOption('unexisting_option'));
        $this->assertEquals(42, $basketElement->getOption('unexisting_option', 42));
        $this->assertEquals('value1', $basketElement->getOption('option1'));
        $this->assertEquals($options, $basketElement->getOptions());

        $basketElement->setOption('option3', 'value3');
        $this->assertEquals('value3', $basketElement->getOption('option3'));

        // Other getters & setters
        $this->assertEquals($provider, $basketElement->getProductProvider());
        $this->assertEquals($manager, $basketElement->getProductManager());
        $this->assertEquals('product_code', $basketElement->getProductCode());

        $basketElement->setDelete(false);
        $this->assertFalse($basketElement->getDelete());
    }
}
