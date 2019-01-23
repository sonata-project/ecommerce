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

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\ProductBundle\Model\BaseProductProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

class BasketElementTest extends TestCase
{
    /**
     * Sets up unit test.
     */
    public function setUp()
    {
        bcscale(3);
    }

    public function getBasketElement($product = null)
    {
        $product = $this->getMockBuilder(ProductInterface::class)
            ->setMockClassName('BasketTest_Product')
            ->getMock();
        $product->expects($this->any())->method('getId')->will($this->returnValue(42));
        $product->expects($this->any())->method('getName')->will($this->returnValue('Product name'));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(15));
        $product->expects($this->any())->method('isPriceIncludingVat')->will($this->returnValue(false));
        $product->expects($this->any())->method('getVatRate')->will($this->returnValue(19.6));
        $product->expects($this->any())->method('getOptions')->will($this->returnValue(['option1' => 'toto']));
        $product->expects($this->any())->method('getDescription')->will($this->returnValue('product description'));

        $productProvider = new ProductProviderTest($this->createMock(SerializerInterface::class));
        $productProvider->setCurrencyPriceCalculator(new CurrencyPriceCalculator());
        $productProvider->setEventDispatcher($this->createMock(EventDispatcherInterface::class));
        $productManager = $this->createMock(ProductManagerInterface::class);

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);
        $basketElement->setProductDefinition($productDefinition);

        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();
        $basket->expects($this->any())->method('getCurrency')->will($this->returnValue($currency));

        $productProvider->updateComputationPricesFields($basket, $basketElement, $product);

        return $basketElement;
    }

    public function testPrice()
    {
        $basketElement = $this->getBasketElement();

        $this->assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        $this->assertSame(1, $basketElement->getQuantity(), 'BasketElement returns the correct default quantity');

        $this->assertSame(15, $basketElement->getUnitPrice(), 'BasketElement return the correct price w/o VAT');
        $this->assertSame(17.940, $basketElement->getUnitPrice(true), 'BasketElement return the correct price w/ VAT');

        $this->assertSame(15, $basketElement->getTotal(), 'BasketElement return the correct price w/o VAT');
        $this->assertSame(17.940, $basketElement->getTotal(true), 'BasketElement return the correct price w VAT');

        $this->assertSame(2.940, $basketElement->getVatAmount(), 'BasketElement returns the correct VAT amount');
    }

    public function testPriceIncludingVat()
    {
        $basketElement = $this->getBasketElement();
        $basketElement->setPriceIncludingVat(true);

        $this->assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        $this->assertSame(1, $basketElement->getQuantity(), 'BasketElement returns the correct default quantity');

        $this->assertSame(12.541, $basketElement->getUnitPrice(), 'BasketElement return the correct price w/o VAT');
        $this->assertSame(15, $basketElement->getUnitPrice(true), 'BasketElement return the correct price w/ VAT');

        $this->assertSame(12.541, $basketElement->getTotal(), 'BasketElement return the correct price w/o VAT');
        $this->assertSame(15, $basketElement->getTotal(true), 'BasketElement return the correct price w VAT');

        $this->assertSame(2.459, $basketElement->getVatAmount(), 'BasketElement returns the correct VAT amount');
    }

    public function testOptions()
    {
        $basketElement = $this->getBasketElement();

        $this->assertTrue($basketElement->hasOption('option1'), 'BasketElement has one option : option1');
        $this->assertFalse($basketElement->hasOption('fake'), 'BasketElement has not option : fake');

        $this->assertSame('toto', $basketElement->getOption('option1'), 'option1 option = toto');
        $this->assertNull($basketElement->getOption('fake'), 'fake option = null');
    }

    public function testQuantity()
    {
        $basketElement = $this->getBasketElement();
        $basketElement->setQuantity(10);

        $this->assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        $this->assertSame(150, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT');
        $this->assertSame(179.4, $basketElement->getTotal(true), 'BasketElement returns the correct price w/ VAT');

        $basketElement->setQuantity(-10);
        $this->assertSame(15, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT when negative quantity set');

        $basketElement->setQuantity(0);
        $this->assertSame(0, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT when no quantity set');
    }

    public function testQuantityWithPriceIncludingVat()
    {
        $basketElement = $this->getBasketElement();
        $basketElement->setQuantity(10);
        $basketElement->setPriceIncludingVat(true);

        $this->assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        $this->assertSame(125.410, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT');
        $this->assertSame(150, $basketElement->getTotal(true), 'BasketElement returns the correct price w/ VAT');

        $basketElement->setQuantity(-10);
        $this->assertSame(12.541, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT when negative quantity set');

        $basketElement->setQuantity(0);
        $this->assertSame(0, $basketElement->getTotal(false), 'BasketElement returns the correct price w/ VAT when no quantity set');
    }

    public function testValidity()
    {
        $product = $this->getMockBuilder(ProductInterface::class)
            ->setMockClassName('BasketTest_Product')
            ->getMock();
        $product->expects($this->once())->method('getEnabled')->will($this->returnValue(true));

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $this->assertTrue($basketElement->isValid(), 'BasketElement is valid');

        $product = $this->getMockBuilder(ProductInterface::class)
            ->setMockClassName('BasketTest_Product')
            ->getMock();
        $product->expects($this->once())->method('getEnabled')->will($this->returnValue(false));
        $basketElement->setProduct('product_code', $product);

        $this->assertFalse($basketElement->isValid(), 'BasketElement returns the correct default quantity');
    }

    public function testGettersSetters()
    {
        $currency = $this->createMock(Currency::class);
        $productProvider = $this->createMock(ProductProviderInterface::class);
        $productManager = $this->createMock(ProductManagerInterface::class);

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProductDefinition($productDefinition);

        $this->assertSame(0, $basketElement->getVatRate());
        $this->assertSame(0, $basketElement->getUnitPrice($currency));
        $this->assertFalse($basketElement->isValid());

        $provider = $this->createMock(ProductProviderInterface::class);
        $manager = $this->createMock(ProductManagerInterface::class);

        $productDefinition = new ProductDefinition($provider, $manager);

        // Tests getProduct
        $this->assertNull($basketElement->getProduct());

        $basketElement->setProductDefinition($productDefinition);

        $this->assertNull($basketElement->getProduct());

        $product = $this->createMock(ProductInterface::class);
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(42));

        $basketElement->setProduct('product_code', $product);
        $this->assertSame($product, $basketElement->getProduct());

        // Tests setProductId
        $basketElement->setProductId(42);
        $this->assertSame(42, $basketElement->getProductId());

        $basketElement->setProductId(24);
        $this->assertNull($basketElement->getProductId());

        $manager->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($product));

        $basketElement->setProductDefinition(new ProductDefinition($provider, $manager));

        $basketElement->setProductId(42);
        $basketElement->setProduct('product_code', $product); // Done by the provider hereby mocked, hence we do it manually
        $this->assertSame($product->getId(), $basketElement->getProductId());

        // Options
        $options = ['option1' => 'value1', 'option2' => 'value2'];
        $basketElement->setOptions($options);
        $this->assertNull($basketElement->getOption('unexisting_option'));
        $this->assertSame(42, $basketElement->getOption('unexisting_option', 42));
        $this->assertSame('value1', $basketElement->getOption('option1'));
        $this->assertSame($options, $basketElement->getOptions());

        $basketElement->setOption('option3', 'value3');
        $this->assertSame('value3', $basketElement->getOption('option3'));

        // Other getters & setters
        $this->assertSame($provider, $basketElement->getProductProvider());
        $this->assertSame($manager, $basketElement->getProductManager());
        $this->assertSame('product_code', $basketElement->getProductCode());

        $basketElement->setDelete(false);
        $this->assertFalse($basketElement->getDelete());
    }
}
