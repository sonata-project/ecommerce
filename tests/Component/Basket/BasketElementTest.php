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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BasketElementTest extends TestCase
{
    /**
     * Sets up unit test.
     */
    protected function setUp(): void
    {
        bcscale(3);
    }

    public function getBasketElement($product = null)
    {
        $product = $this->getMockBuilder(ProductInterface::class)
            ->setMockClassName('BasketTest_Product')
            ->getMock();
        $product->expects(static::any())->method('getId')->willReturn(42);
        $product->expects(static::any())->method('getName')->willReturn('Product name');
        $product->expects(static::any())->method('getPrice')->willReturn(15);
        $product->expects(static::any())->method('isPriceIncludingVat')->willReturn(false);
        $product->expects(static::any())->method('getVatRate')->willReturn(19.6);
        $product->expects(static::any())->method('getOptions')->willReturn(['option1' => 'toto']);
        $product->expects(static::any())->method('getDescription')->willReturn('product description');

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
        $basket->expects(static::any())->method('getCurrency')->willReturn($currency);

        $productProvider->updateComputationPricesFields($basket, $basketElement, $product);

        return $basketElement;
    }

    public function testPrice(): void
    {
        $basketElement = $this->getBasketElement();

        static::assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        static::assertSame(1, $basketElement->getQuantity(), 'BasketElement returns the correct default quantity');

        static::assertSame(
            '15',
            $basketElement->getUnitPrice(),
            'BasketElement return the correct price w/o VAT'
        );
        static::assertSame(
            '17.940',
            $basketElement->getUnitPrice(true),
            'BasketElement return the correct price w/ VAT'
        );

        static::assertSame(
            0,
            bccomp('15', $basketElement->getTotal(), 3),
            'BasketElement return the correct price w/o VAT'
        );
        static::assertSame(
            0,
            bccomp('17.940', $basketElement->getTotal(true)),
            'BasketElement return the correct price w VAT'
        );

        static::assertSame(
            0,
            bccomp('2.940', $basketElement->getVatAmount()),
            'BasketElement returns the correct VAT amount'
        );
    }

    public function testPriceIncludingVat(): void
    {
        $basketElement = $this->getBasketElement();
        $basketElement->setPriceIncludingVat(true);

        static::assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        static::assertSame(1, $basketElement->getQuantity(), 'BasketElement returns the correct default quantity');

        static::assertSame(
            '12.541',
            $basketElement->getUnitPrice(),
            'BasketElement return the correct price w/o VAT'
        );
        static::assertSame(
            '15',
            $basketElement->getUnitPrice(true),
            'BasketElement return the correct price w/ VAT'
        );

        static::assertSame(
            0,
            bccomp('12.541', $basketElement->getTotal()),
            'BasketElement return the correct price w/o VAT'
        );
        static::assertSame(
            0,
            bccomp('15', $basketElement->getTotal(true)),
            'BasketElement return the correct price w VAT'
        );

        static::assertSame(
            0,
            bccomp('2.459', $basketElement->getVatAmount()),
            'BasketElement returns the correct VAT amount'
        );
    }

    public function testOptions(): void
    {
        $basketElement = $this->getBasketElement();

        static::assertTrue($basketElement->hasOption('option1'), 'BasketElement has one option : option1');
        static::assertFalse($basketElement->hasOption('fake'), 'BasketElement has not option : fake');

        static::assertSame('toto', $basketElement->getOption('option1'), 'option1 option = toto');
        static::assertNull($basketElement->getOption('fake'), 'fake option = null');
    }

    public function testQuantity(): void
    {
        $basketElement = $this->getBasketElement();
        $basketElement->setQuantity(10);

        static::assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        static::assertSame(
            0,
            bccomp('150', $basketElement->getTotal(false)),
            'BasketElement returns the correct price w/ VAT'
        );
        static::assertSame(
            0,
            bccomp('179.400', $basketElement->getTotal(true)),
            'BasketElement returns the correct price w/ VAT'
        );

        $basketElement->setQuantity(-10);
        static::assertSame(
            0,
            bccomp('15', $basketElement->getTotal(false)),
            'BasketElement returns the correct price w/ VAT when negative quantity set'
        );

        $basketElement->setQuantity(0);
        static::assertSame(
            0,
            bccomp('0', $basketElement->getTotal(false)),
            'BasketElement returns the correct price w/ VAT when no quantity set'
        );
    }

    public function testQuantityWithPriceIncludingVat(): void
    {
        $basketElement = $this->getBasketElement();
        $basketElement->setQuantity(10);
        $basketElement->setPriceIncludingVat(true);

        static::assertSame(19.6, $basketElement->getVatRate(), 'BasketElement returns the correct VAT');
        static::assertSame(
            0,
            bccomp('125.410', $basketElement->getTotal(false)),
            'BasketElement returns the correct price w/ VAT'
        );
        static::assertSame(
            0,
            bccomp('150', $basketElement->getTotal(true)),
            'BasketElement returns the correct price w/ VAT'
        );

        $basketElement->setQuantity(-10);
        static::assertSame(
            0,
            bccomp('12.541', $basketElement->getTotal(false)),
            'BasketElement returns the correct price w/ VAT when negative quantity set'
        );

        $basketElement->setQuantity(0);
        static::assertSame(
            0,
            bccomp('0.000', $basketElement->getTotal(false)),
            'BasketElement returns the correct price w/ VAT when no quantity set'
        );
    }

    public function testValidity(): void
    {
        $product = $this->getMockBuilder(ProductInterface::class)
            ->setMockClassName('BasketTest_Product')
            ->getMock();
        $product->expects(static::once())->method('getEnabled')->willReturn(true);

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        static::assertTrue($basketElement->isValid(), 'BasketElement is valid');

        $product = $this->getMockBuilder(ProductInterface::class)
            ->setMockClassName('BasketTest_Product')
            ->getMock();
        $product->expects(static::once())->method('getEnabled')->willReturn(false);
        $basketElement->setProduct('product_code', $product);

        static::assertFalse($basketElement->isValid(), 'BasketElement returns the correct default quantity');
    }

    public function testGettersSetters(): void
    {
        $currency = $this->createMock(Currency::class);
        $productProvider = $this->createMock(ProductProviderInterface::class);
        $productManager = $this->createMock(ProductManagerInterface::class);

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProductDefinition($productDefinition);

        static::assertNull($basketElement->getVatRate());
        static::assertSame('0.000', $basketElement->getUnitPrice($currency));
        static::assertFalse($basketElement->isValid());

        $provider = $this->createMock(ProductProviderInterface::class);
        $manager = $this->createMock(ProductManagerInterface::class);

        $productDefinition = new ProductDefinition($provider, $manager);

        // Tests getProduct
        static::assertNull($basketElement->getProduct());

        $basketElement->setProductDefinition($productDefinition);

        static::assertNull($basketElement->getProduct());

        $product = $this->createMock(ProductInterface::class);
        $product->expects(static::any())
            ->method('getId')
            ->willReturn(42);

        $basketElement->setProduct('product_code', $product);
        static::assertSame($product, $basketElement->getProduct());

        // Tests setProductId
        $basketElement->setProductId(42);
        static::assertSame(42, $basketElement->getProductId());

        $basketElement->setProductId(24);
        static::assertNull($basketElement->getProductId());

        $manager->expects(static::any())
            ->method('findOneBy')
            ->willReturn($product);

        $basketElement->setProductDefinition(new ProductDefinition($provider, $manager));

        $basketElement->setProductId(42);
        $basketElement->setProduct('product_code', $product); // Done by the provider hereby mocked, hence we do it manually
        static::assertSame($product->getId(), $basketElement->getProductId());

        // Options
        $options = ['option1' => 'value1', 'option2' => 'value2'];
        $basketElement->setOptions($options);
        static::assertNull($basketElement->getOption('unexisting_option'));
        static::assertSame(42, $basketElement->getOption('unexisting_option', 42));
        static::assertSame('value1', $basketElement->getOption('option1'));
        static::assertSame($options, $basketElement->getOptions());

        $basketElement->setOption('option3', 'value3');
        static::assertSame('value3', $basketElement->getOption('option3'));

        // Other getters & setters
        static::assertSame($provider, $basketElement->getProductProvider());
        static::assertSame($manager, $basketElement->getProductManager());
        static::assertSame('product_code', $basketElement->getProductCode());

        $basketElement->setDelete(false);
        static::assertFalse($basketElement->getDelete());
    }
}
