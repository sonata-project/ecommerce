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

namespace Sonata\ProductBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\InvalidProductException;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Product\ProductInterface;
use Sonata\Form\Validator\ErrorElement;
use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Model\BaseProductProvider;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductProviderTest extends BaseProductProvider
{
    /**
     * @return string
     */
    public function getBaseControllerName()
    {
        return 'DumbTestController';
    }

    public function getTemplatesPath(): string
    {
        return '';
    }
}

class ProductTest extends BaseProduct implements ProductInterface
{
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function isVariation()
    {
        return false;
    }
}

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class BaseProductProviderTest extends TestCase
{
    public function testCreateVariation(): void
    {
        $productProvider = $this->createNewProductProvider();
        $product = $this->getMockBuilder(ProductInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        $product->expects(static::any())
            ->method('isVariation')
            ->willReturn(true);

        try {
            $productProvider->createVariation($product); // Product simulates a variation
        } catch (\Exception $e) {
            static::assertInstanceOf('RuntimeException', $e);
        }
    }

    public function testVariationSkuDuplication(): void
    {
        $productProvider = $this->createNewProductProvider();
        $product = new ProductTest();
        $product->setSku('TESTING_SKU');
        $variation = $productProvider->createVariation($product);

        static::assertSame('TESTING_SKU_DUPLICATE', $variation->getSku());
    }

    public function testBuildBasketElement(): void
    {
        $basketElement = new BasketElement();
        $productProvider = $this->createNewProductProvider();

        // First test without product
        $productProvider->buildBasketElement($basketElement, null, ['test' => true]);
        static::assertTrue($basketElement->getOption('test', null));

        // Second test with product
        $product = $this->createMock(ProductInterface::class);
        $productProvider->buildBasketElement($basketElement, $product, ['test2' => true]);
        static::assertTrue($basketElement->getOption('test2', null));
        static::assertNull($basketElement->getOption('test', null));
    }

    public function testValidateFormBasketElement(): void
    {
        $productProvider = $this->createNewProductProvider();
        $errorElement = $this->createErrorElement();
        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();

        // With a deleted element
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $basketElement->expects(static::any())
            ->method('getDelete')
            ->willReturn(true);

        static::assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));

        // Without a product
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $basketElement->expects(static::any())
            ->method('getProduct')
            ->willReturn(false);

        static::assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));

        // With a disabled product
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $product->expects(static::any())
            ->method('getEnabled')
            ->willReturn(false);

        $basketElement->expects(static::any())
            ->method('getProduct')
            ->willReturn($product);
        static::assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));

        // With a non numeric quantity
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $basketElement->expects(static::any())
            ->method('getProduct')
            ->willReturn($product);
        $basketElement->expects(static::any())
            ->method('getQuantity')
            ->willReturn('invalid value');

        static::assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));
    }

    public function testBasketAddProduct(): void
    {
        $productProvider = $this->createNewProductProvider();
        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();

        // Simulate a product already in the basket
        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();
        $basket->expects(static::any())
            ->method('hasProduct')
            ->willReturn(true);

        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket->expects(static::any())->method('getCurrency')->willReturn($currency);

        static::assertFalse($productProvider->basketAddProduct($basket, $product, $basketElement));

        // Test with product having options
        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();
        $basket->expects(static::any())
            ->method('hasProduct')
            ->willReturn(false);

        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket->expects(static::any())->method('getCurrency')->willReturn($currency);

        $basketElement = new BasketElement();
        $product->expects(static::any())
            ->method('getOptions')
            ->willReturn(['even' => true, 'more' => true, 'tests' => true]);
        $result = $productProvider->basketAddProduct($basket, $product, $basketElement);

        static::assertTrue($basketElement->hasOption('even'));
        static::assertTrue($basketElement->hasOption('more'));
        static::assertTrue($basketElement->hasOption('tests'));
        static::assertInstanceOf(BasketElementInterface::class, $result);
    }

    public function testBasketAddProductInvalid(): void
    {
        $this->expectException(InvalidProductException::class);
        $this->expectExceptionMessage('You can\'t add \'product_sku\' to the basket as it is a master product with variations.');

        $productProvider = $this->createNewProductProvider();

        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $product->expects(static::once())->method('isMaster')->willReturn(true);
        $product->expects(static::once())->method('getSku')->willReturn('product_sku');
        $product->expects(static::once())->method('getVariations')->willReturn([1]);

        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();

        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();

        $productProvider->basketAddProduct($basket, $product, $basketElement);
    }

    public function testBasketMergeProduct(): void
    {
        // Test a product not in the basket
        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();

        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket->expects(static::any())->method('getCurrency')->willReturn($currency);

        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $basketElement->expects(static::any())->method('getQuantity')->willReturn(1);
        $productProvider = $this->createNewProductProvider();

        static::assertFalse($productProvider->basketMergeProduct($basket, $product, $basketElement));

        // Test an invalid product ID in the basket
        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();

        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket->expects(static::any())->method('getCurrency')->willReturn($currency);

        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $basketElement->expects(static::any())->method('getQuantity')->willReturn(1);
        $productProvider = $this->createNewProductProvider();
        $basket->expects(static::any())
            ->method('getElement')
            ->willReturn(null);

        try {
            $productProvider->basketMergeProduct($basket, $product, $basketElement);
            static::fail('->basketMergeProduct() should throw a \RuntimeException for an invalid product ID');
        } catch (\Exception $e) {
            static::assertInstanceOf(\RuntimeException::class, $e);
        }

        // Test a valid workflow
        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();

        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket->expects(static::any())->method('getCurrency')->willReturn($currency);

        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $basketElement->expects(static::any())->method('getQuantity')->willReturn(1);
        $newBasketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $productProvider = $this->createNewProductProvider();
        $basket->expects(static::any())
            ->method('hasProduct')
            ->willReturn(true);
        $basket->expects(static::any())
            ->method('getElement')
            ->willReturn($basketElement);

        static::assertInstanceOf(BasketElementInterface::class, $productProvider->basketMergeProduct($basket, $product, $newBasketElement));
    }

    public function testIsValidBasketElement(): void
    {
        $productProvider = $this->createNewProductProvider();

        // Test invalid product
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $basketElement->expects(static::any())
            ->method('getProduct')
            ->willReturn(false);
        static::assertFalse($productProvider->isValidBasketElement($basketElement));

        // Test valid product
        $basketElement = $this->getMockBuilder(BasketElementInterface::class)->getMock();
        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $basketElement->expects(static::any())
            ->method('getProduct')
            ->willReturn($product);
        static::assertTrue($productProvider->isValidBasketElement($basketElement));
    }

    public function testIsAddableToBasket(): void
    {
        $basket = $this->getMockBuilder(BasketInterface::class)->getMock();
        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $productProvider = $this->createNewProductProvider();

        static::assertTrue($productProvider->isAddableToBasket($basket, $product));
    }

    public function testHasVariationsValidCase(): void
    {
        $productMock = new ProductTest();
        $variationMock = new ProductTest();
        $productMock->addVariation($variationMock);

        $productProvider = $this->createNewProductProvider();
        $productProvider->setVariationFields(['test']);

        static::assertTrue($productProvider->hasVariations($productMock));
    }

    public function testHasVariationsWithNoVariation(): void
    {
        $productMock = new ProductTest();

        $productProvider = $this->createNewProductProvider();
        $productProvider->setVariationFields(['test']);

        static::assertFalse($productProvider->hasVariations($productMock));
    }

    public function testHasEnabledVariationsWithNoVariation(): void
    {
        $productMock = new ProductTest();
        $productProvider = $this->createNewProductProvider();
        static::assertFalse($productProvider->hasEnabledVariations($productMock));
    }

    public function testHasEnabledVariationsWithNoEnabledVariation(): void
    {
        $productMock = new ProductTest();
        $productMock->setEnabled(true);
        $variationMock = new ProductTest();
        $variationMock->setEnabled(false);
        $productMock->addVariation($variationMock);

        $productProvider = $this->createNewProductProvider();
        $productProvider->setVariationFields(['test']);

        static::assertFalse($productProvider->hasEnabledVariations($productMock));
    }

    public function testHasEnabledVariationsWithEnabledVariation(): void
    {
        $productMock = new ProductTest();
        $productMock->setEnabled(true);
        $variationMock = new ProductTest();
        $variationMock->setEnabled(true);
        $productMock->addVariation($variationMock);

        $productProvider = $this->createNewProductProvider();
        $productProvider->setVariationFields(['test']);

        static::assertTrue($productProvider->hasEnabledVariations($productMock));
    }

    public function testGetEnabledVariationWithNoVariation(): void
    {
        $productMock = new ProductTest();
        $provider = $this->createNewProductProvider();
        $provider->setVariationFields(['test']);

        $variations = $provider->getEnabledVariations($productMock);
        static::assertInstanceOf(ArrayCollection::class, $variations);
        static::assertCount(0, $variations);
    }

    public function testGetEnabledVariationWithVariation(): void
    {
        $productMock = new ProductTest();
        $variationMock = new ProductTest();
        $variationMock->setEnabled(true);
        $productMock->addVariation($variationMock);

        $provider = $this->createNewProductProvider();
        $provider->setVariationFields(['test']);

        $variations = $provider->getEnabledVariations($productMock);
        static::assertInstanceOf(ArrayCollection::class, $variations);
        static::assertCount(1, $variations);
        static::assertInstanceOf(ProductInterface::class, $variations[0]);
    }

    public function testGetCheapestEnabledVariationWithNoVariation(): void
    {
        $product = new ProductTest();
        $provider = $this->createNewProductProvider();
        $provider->setVariationFields(['test']);

        static::assertNull($provider->getCheapestEnabledVariation($product));
    }

    public function testGetCheapestEnabledVariationWithNoEnabledVariation(): void
    {
        $product = new ProductTest();
        $variationMock = new ProductTest();
        $variationMock->setEnabled(false);
        $product->addVariation($variationMock);

        $provider = $this->createNewProductProvider();
        $provider->setVariationFields(['test']);

        static::assertNull($provider->getCheapestEnabledVariation($product));
    }

    public function testGetCheapestEnabledVariationWithVariations(): void
    {
        $product = new ProductTest();
        $variationA = new ProductTest();
        $variationA->setEnabled(false);
        $variationA->setPrice(20);
        $variationB = new ProductTest();
        $variationB->setEnabled(true);
        $variationB->setPrice(1000);
        $product->addVariation($variationA);
        $product->addVariation($variationB);

        $provider = $this->createNewProductProvider();
        $provider->setVariationFields(['test']);

        static::assertSame($variationB, $provider->getCheapestEnabledVariation($product));
    }

    public function testCalculatePrice(): void
    {
        $product = new ProductTest();
        $product->setPrice(42);

        $currency = new Currency();
        $currency->setLabel('EUR');

        $provider = $this->createNewProductProvider();

        static::assertSame((float) 42 * 4, $provider->calculatePrice($product, $currency, false, 4));
        static::assertSame((float) 42, $provider->calculatePrice($product, $currency, false));
    }

    public function testCalculatePriceException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Expected integer >= 1 for quantity, 4.32 given.');

        $product = new ProductTest();

        $currency = new Currency();

        $provider = $this->createNewProductProvider();

        $provider->calculatePrice($product, $currency, false, 4.32);
    }

    public function testCalculatePriceExceptionLessThanOne(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Expected integer >= 1 for quantity, 0.32 given.');

        $product = new ProductTest();

        $currency = new Currency();

        $provider = $this->createNewProductProvider();

        $provider->calculatePrice($product, $currency, false, 0.32);
    }

    public function testGetVariationsChoices(): void
    {
        $product = new ProductTest();
        $product->setEnabled(true);

        $variation = new ProductTest();
        $variation->setEnabled(true);
        $variation->setName('variation');
        $variation->setPrice(84);

        $variation2 = clone $variation;
        $variation2->setName('avariation');
        $variation2->setPrice(42);

        $provider = $this->createNewProductProvider();

        static::assertSame([], $provider->getVariationsChoices($product));

        $product->addVariation($variation);
        $product->addVariation($variation2);

        $provider->setVariationFields(['price', 'name']);

        $expected = [
            'price' => [
                1 => 42,
                0 => 84,
            ],
            'name' => [
                1 => 'avariation',
                0 => 'variation',
            ],
        ];

        static::assertSame($expected, $provider->getVariationsChoices($product));
    }

    public function testGetVariatedProperties(): void
    {
        $product = new ProductTest();
        $product->setEnabled(true);

        $variation = new ProductTest();
        $variation->setEnabled(true);
        $variation->setName('variation');
        $variation->setPrice(84);

        $variation2 = clone $variation;
        $variation2->setName('avariation');
        $variation2->setPrice(42);

        $provider = $this->createNewProductProvider();

        static::assertSame([], $provider->getVariatedProperties($product));

        $product->addVariation($variation);
        $product->addVariation($variation2);

        $provider->setVariationFields(['price', 'name']);

        $expected = [
            'price' => 84,
            'name' => 'variation',
        ];

        static::assertSame($expected, $provider->getVariatedProperties($variation));
    }

    public function testGetVariation(): void
    {
        $product = new ProductTest();
        $product->setEnabled(true);

        $variation = new ProductTest();
        $variation->setEnabled(true);
        $variation->setName('variation');
        $variation->setPrice(84);

        $variation2 = clone $variation;
        $variation2->setName('avariation');
        $variation2->setPrice(42);

        $provider = $this->createNewProductProvider();

        $product->addVariation($variation);
        $product->addVariation($variation2);

        $provider->setVariationFields(['price', 'name']);

        $expected = [
            'price' => 84,
            'name' => 'variation',
        ];

        static::assertSame($variation2, $provider->getVariation($product, ['price' => 42, 'name' => 'avariation']));
    }

    private function createErrorElement(): ErrorElement
    {
        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext
            ->method('buildViolation')
            ->willReturn($this->createConstraintBuilder());

        return new ErrorElement(
            '',
            $this->createStub(ConstraintValidatorFactoryInterface::class),
            $executionContext,
            'group'
        );
    }

    /**
     * @return Stub&ConstraintViolationBuilderInterface
     */
    private function createConstraintBuilder(): object
    {
        $constraintBuilder = $this->createStub(ConstraintViolationBuilderInterface::class);
        $constraintBuilder
            ->method('atPath')
            ->willReturn($constraintBuilder);
        $constraintBuilder
            ->method('setParameters')
            ->willReturn($constraintBuilder);
        $constraintBuilder
            ->method('setTranslationDomain')
            ->willReturn($constraintBuilder);
        $constraintBuilder
            ->method('setInvalidValue')
            ->willReturn($constraintBuilder);

        return $constraintBuilder;
    }

    /**
     * @return ProductProviderTest
     */
    private function createNewProductProvider()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $provider = new ProductProviderTest($serializer);

        $provider->setCurrencyPriceCalculator(new CurrencyPriceCalculator());
        $provider->setEventDispatcher($this->createMock(EventDispatcherInterface::class));

        return $provider;
    }
}
