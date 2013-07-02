<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Product;

use Sonata\ProductBundle\Model\BaseProductProvider;
use Sonata\OrderBundle\Entity\BaseOrderElement;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Order\OrderInterface;

use Sonata\ProductBundle\Entity\BaseProduct;

class Product extends BaseProduct
{
    public $enabled = true;
    public $id = 1;
    public $name = 'fake name';
    public $price = 15;
    public $vat = 19.6;

    public function getOptions()
    {
        return array(
            'option1' => 'toto',
        );
    }

    public function isRecurrentPayment()
    {
        return false;
    }

    public function getElementOptions()
    {
        return array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }
}

class OrderElement extends BaseOrderElement
{

}

class BaseProductServiceTest_ProductProvider extends BaseProductProvider
{
    /**
     * @return string
     */
    public function getBaseControllerName()
    {
        // TODO: Implement getBaseControllerName() method.
    }
}

class BaseOrderElementTest_ProductProvider extends BaseOrderElement
{

}

class BaseProductServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return BaseProductServiceTest_ProductProvider
     */
    public function getBaseProvider()
    {
        $serializer = $this->getMock('JMS\Serializer\SerializerInterface');
        $serializer->expects($this->any())->method('serialize')->will($this->returnValue('{}'));

        $provider = new BaseProductServiceTest_ProductProvider($serializer);

        $basketElementManager = $this->getMock('\Sonata\Component\Basket\BasketElementManagerInterface');
        $basketElementManager->expects($this->any())->method('getClass')->will($this->returnValue('\Sonata\Tests\Component\Product\BaseOrderElementTest_ProductProvider'));

        $provider->setBasketElementManager($basketElementManager);

        $provider->setOrderElementClassName(get_class(new OrderElement()));

        return $provider;
    }

    public function testOptions()
    {
        $provider = $this->getBaseProvider();

        $this->assertInternalType('array', $provider->getOptions());
        $this->assertNull($provider->getOption('foo'));
        $provider->setOptions(array('foo' => 'bar'));

        $this->assertEquals('bar', $provider->getOption('foo'));
    }

    public function testOrderElement()
    {
        $product = $this->getMock('Sonata\Component\Product\ProductInterface');
        $product->expects($this->any())->method('getId')->will($this->returnValue(42));
        $product->expects($this->any())->method('getName')->will($this->returnValue('Product name'));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(9.99));
        $product->expects($this->any())->method('getOptions')->will($this->returnValue(array('foo' => 'bar')));
        $product->expects($this->any())->method('getDescription')->will($this->returnValue('product description'));

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $provider = $this->getBaseProvider();

        $orderElement = $provider->createOrderElement($basketElement);

        $this->assertInstanceOf('Sonata\Component\Order\OrderElementInterface', $orderElement);
        $this->assertEquals(OrderInterface::STATUS_PENDING, $orderElement->getStatus());
        $this->assertEquals('Product name', $orderElement->getDesignation());
        $this->assertEquals(1, $orderElement->getQuantity());
    }

    public function testVariation()
    {
        $provider = $this->getBaseProvider();

        $this->assertInternalType('array', $provider->getVariationFields());

        $provider->setVariationFields(array('name', 'price'));

        $this->assertTrue($provider->hasVariationFields(), '::hasVariationFields() return true' );
        $this->assertTrue($provider->isVariateBy('name'), '::isVariateBy() return true for existing field');
        $this->assertFalse($provider->isVariateBy('fake'), '::isVariateBy() return false for non existing field');
        $this->assertInternalType('array', $provider->getVariationFields());
    }

    public function testDuplicate()
    {
        $provider = $this->getBaseProvider();
        $provider->setVariationFields(array('Name', 'Price'));

        $product = new Product;
        $product->id = 2;

        $variation = $provider->createVariation($product);
        $variation->setPrice(11);
        $product->addVariation($variation);

        $this->assertNull($variation->getId());
        $this->assertEquals('fake name (duplicated)', $variation->getName(), '::getName() return the duplicated name');

        $variation = $provider->createVariation($product);
        $variation->setPrice(12);
        $product->addVariation($variation);

        $variation = $provider->createVariation($product);
        $variation->setPrice(13);
        $product->addVariation($variation);

        $this->assertEquals(count($product->getVariations()), 3,  '::getVariations() returns 3 elements');

        $product->setName('test');
        $product->setVat(5.5);
        $product->setPrice(4);

        // copy the information into the variation
        $provider->copyVariation($product, 'all');

        $this->assertEquals(4, $product->getPrice(), '::getPrice() return 4');

        // price should be unchanged
        $variations = $product->getVariations();

        $this->assertEquals(11, $variations[0]->getPrice(), '::getPrice() return 11');
        $this->assertEquals(12, $variations[1]->getPrice(), '::getPrice() return 12');
        $this->assertEquals(13, $variations[2]->getPrice(), '::getPrice() return 13');

        // vat should be updated
        $this->assertEquals(5.5, $variations[0]->getVat(), '::getVat() return 5.5');
        $this->assertEquals(5.5, $variations[1]->getVat(), '::getVat() return 5.5');
        $this->assertEquals(5.5, $variations[2]->getVat(), '::getVat() return 5.5');
    }
}
