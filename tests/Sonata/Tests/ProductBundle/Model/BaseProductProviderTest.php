<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Test\ProductBundle\Model;

use Sonata\ProductBundle\Model\BaseProductProvider;
use Sonata\Component\Basket\BasketElement;

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

/**
 * Class BaseProductProviderTest
 *
 * @package Sonata\Test\ProductBundle
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class BaseProductProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetProductFromRaw()
    {
    }

    public function testCreateVariation()
    {
        $productProvider = $this->createNewProductProvider();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')
                ->disableOriginalConstructor()
                ->getMock();
        $product->expects($this->any())
            ->method('isVariation')
            ->will($this->returnValue(true));

        try {
            $productProvider->createVariation($product); // Product simulates a variation
        }
        catch (\Exception $e) {
            $this->assertInstanceOf('RuntimeException', $e);
        }
    }

    public function testBuildBasketElement()
    {
        $basketElement = new BasketElement();
        $productProvider = $this->createNewProductProvider();

        // First test without product
        $productProvider->buildBasketElement($basketElement, null, array('test' => true));
        $this->assertTrue($basketElement->getOption('test', null));

        // Second test with product
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->disableOriginalConstructor()->getMock();
        $productProvider->buildBasketElement($basketElement, $product, array('test2' => true));
        $this->assertTrue($basketElement->getOption('test2', null));
        $this->assertNull($basketElement->getOption('test', null));
    }

    public function testValidateFormBasketElement()
    {
        $productProvider = $this->createNewProductProvider();
        $errorElement = $this->getMockBuilder('Sonata\AdminBundle\Validator\ErrorElement')->disableOriginalConstructor()->getMock();
        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();

        // With a deleted element
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $basketElement->expects($this->any())
            ->method('getDelete')
            ->will($this->returnValue(true));

        $this->assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));

        // Without a product
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $basketElement->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue(false));

        $this->assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));

        // With a disabled product
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $product->expects($this->any())
            ->method('getEnabled')
            ->will($this->returnValue(false));

        $basketElement->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($product));
        $this->assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));

        // With a non numeric quantity
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $basketElement->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($product));
        $basketElement->expects($this->any())
            ->method('getQuantity')
            ->will($this->returnValue('invalid value'));

        $this->assertNull($productProvider->validateFormBasketElement($errorElement, $basketElement, $basket));
    }

    public function testBasketAddProduct()
    {
        $productProvider = $this->createNewProductProvider();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();

        // Simulate a product already in the basket
        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();
        $basket->expects($this->any())
            ->method('hasProduct')
            ->will($this->returnValue(true));

        $this->assertFalse($productProvider->basketAddProduct($basket, $product, $basketElement));

        // Test with product having options
        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();
        $basket->expects($this->any())
            ->method('hasProduct')
            ->will($this->returnValue(false));

        $basketElement = new BasketElement();
        $product->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue(array('even' => true, 'more' => true, 'tests' => true)));
        $result = $productProvider->basketAddProduct($basket, $product, $basketElement);

        $this->assertTrue($basketElement->hasOption('even'));
        $this->assertTrue($basketElement->hasOption('more'));
        $this->assertTrue($basketElement->hasOption('tests'));
        $this->assertInstanceOf('Sonata\Component\Basket\BasketElementInterface', $result);
    }

    public function testBasketMergeProduct()
    {
        // Test a product not in the basket
        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $productProvider = $this->createNewProductProvider();

        $this->assertFalse($productProvider->basketMergeProduct($basket, $product, $basketElement));

        // Test an invalid product ID in the basket
        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $productProvider = $this->createNewProductProvider();
        $basket->expects($this->any())
            ->method('getElement')
            ->will($this->returnValue(null));

        try {
            $productProvider->basketMergeProduct($basket, $product, $basketElement);
            $this->fail('->basketMergeProduct() should throw a \RuntimeException for an invalid product ID');
        }
        catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);
        }

        // Test a valid workflow
        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $newBasketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $productProvider = $this->createNewProductProvider();
        $basket->expects($this->any())
            ->method('hasProduct')
            ->will($this->returnValue(true));
        $basket->expects($this->any())
            ->method('getElement')
            ->will($this->returnValue($basketElement));

        $this->assertInstanceOf('Sonata\Component\Basket\BasketElementInterface', $productProvider->basketMergeProduct($basket, $product, $newBasketElement));
    }

    public function testIsValidBasketElement()
    {
        $productProvider = $this->createNewProductProvider();

        // Test invalid product
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $basketElement->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue(false));
        $this->assertFalse($productProvider->isValidBasketElement($basketElement));

        // Test valid product
        $basketElement = $this->getMockBuilder('Sonata\Component\Basket\BasketElementInterface')->getMock();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $basketElement->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($product));
        $this->assertTrue($productProvider->isValidBasketElement($basketElement));
    }

    public function testIsAddableToBasket()
    {
        $basket = $this->getMockBuilder('Sonata\Component\Basket\BasketInterface')->getMock();
        $product = $this->getMockBuilder('Sonata\Component\Product\ProductInterface')->getMock();
        $productProvider = $this->createNewProductProvider();

        $this->assertTrue($productProvider->isAddableToBasket($basket, $product));
    }

    /**
     * @return ProductProviderTest
     */
    private function createNewProductProvider()
    {
        $serializer = $this->getMockBuilder('JMS\Serializer\Serializer')->disableOriginalConstructor()->getMock();

        return  new ProductProviderTest($serializer);
    }
}