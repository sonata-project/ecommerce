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

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\ClassificationBundle\Entity\BaseCategory;
use Sonata\ClassificationBundle\Entity\BaseCollection;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Product\ProductDefinition;
use Sonata\ProductBundle\Entity\BaseDelivery;
use Sonata\ProductBundle\Entity\BasePackage;
use Sonata\ProductBundle\Entity\BaseProductCategory;
use Sonata\ProductBundle\Entity\BaseProductCollection;
use Sonata\ProductBundle\Entity\ProductCategoryManager;
use Sonata\ProductBundle\Entity\ProductCollectionManager;
use Sonata\ProductBundle\Model\BaseProductProvider;
use Sonata\OrderBundle\Entity\BaseOrderElement;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Order\OrderInterface;
use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\Tests\Component\Basket\ProductProviderTest;

class Product extends BaseProduct
{
    public $enabled = true;
    public $id = 1;
    public $name = 'fake name';
    public $price = 15;
    public $vat = 19.6;

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

class ProductCategory extends BaseProductCategory
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }
}

class Category extends BaseCategory
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }
}

class ProductCollection extends BaseProductCollection
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }
}

class Collection extends BaseCollection
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }
}

class Package extends BasePackage { }
class Delivery extends BaseDelivery { }

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

    public function testProductSalableStatus()
    {
        $product = new Product();

        $product->setEnabled(false);
        $this->assertFalse($product->isSalable());

        $product->setEnabled(true);
        $this->assertTrue($product->isSalable());

        $product->setVariations(new ArrayCollection(array(new Product())));
        $this->assertFalse($product->isSalable());
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

        $productProvider = new ProductProviderTest($this->getMock('JMS\Serializer\SerializerInterface'));
        $productProvider->setCurrencyPriceCalculator(new CurrencyPriceCalculator());
        $productManager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);
        $basketElement->setProductDefinition($productDefinition);

        $provider = $this->getBaseProvider();

        $orderElement = $provider->createOrderElement($basketElement);

        $this->assertInstanceOf('Sonata\Component\Order\OrderElementInterface', $orderElement);
        $this->assertEquals(OrderInterface::STATUS_PENDING, $orderElement->getStatus());
        $this->assertEquals('Product name', $orderElement->getDesignation());
        $this->assertEquals(1, $orderElement->getQuantity());
    }

    public function testVariationFields()
    {
        $provider = $this->getBaseProvider();

        $this->assertEmpty($provider->getVariationFields());

        $provider->setVariationFields(array('name', 'price'));

        $this->assertTrue($provider->hasVariationFields());
        $this->assertTrue($provider->isVariateBy('name'));
        $this->assertFalse($provider->isVariateBy('fake'));
        $this->assertNotEmpty($provider->getVariationFields());
        $this->assertEquals(array('name', 'price'), $provider->getVariationFields());
    }

    public function testVariationCreation()
    {
        $provider = $this->getBaseProvider();
        $provider->setVariationFields(array('name', 'price'));

        $product = new Product();
        $product->id = 2;

        $product->addDelivery(new Delivery());
        $product->addPackage(new Package());
        $product->addProductCategory(new ProductCategory());

        $variation1 = $provider->createVariation($product, false);
        $variation2 = $provider->createVariation($product, true);

        $this->assertNull($variation1->getId());
        $this->assertEquals('fake name (duplicated)', $variation1->getName());
        $this->assertEquals($product->getId(), $variation1->getParent()->getId());
        $this->assertFalse($variation1->isEnabled());
        $this->assertTrue($variation1->isVariation());

        $this->assertEquals(2, count($product->getVariations()));

        $this->assertEquals(0, count($variation1->getVariations()));
        $this->assertEquals(0, count($variation1->getPackages()));
        $this->assertEquals(0, count($variation1->getDeliveries()));
        $this->assertEquals(0, count($variation1->getProductCategories()));

        $this->assertEquals(0, count($variation2->getVariations()));
        $this->assertEquals(1, count($variation2->getPackages()));
        $this->assertEquals(1, count($variation2->getDeliveries()));

        $provider->setVariationFields(array('packages', 'productCollections', 'productCategories', 'deliveries'));

        $variation3 = $provider->createVariation($product, true);

        $this->assertEquals(0, count($variation3->getVariations()));
        $this->assertEquals(0, count($variation3->getPackages()));
        $this->assertEquals(0, count($variation3->getDeliveries()));
        $this->assertEquals(0, count($variation3->getProductCategories()));
    }

    public function testProductDataSynchronization()
    {
        $provider = $this->getBaseProvider();
        $provider->setVariationFields(array('price'));

        $product = new Product();
        $product->id = 2;

        $variation = $provider->createVariation($product);

        $product->setName('Product new name');
        $product->setPrice(50);
        $product->setVatRate(5.5);

        $provider->synchronizeVariationsProduct($product);

        $this->assertEquals($product->getName(), $variation->getName());
        $this->assertEquals(15, $variation->getPrice());
        $this->assertEquals($product->getVatRate(), $variation->getVatRate());
        $this->assertTrue($variation->isEnabled());

        $this->assertEquals(1, count($product->getVariations()));
        $this->assertEquals(0, count($variation->getVariations()));
    }

    public function testProductCategoriesSynchronization()
    {
        $provider = $this->getBaseProvider();

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $productCategoryManager = new ProductCategoryManager('Sonata\Tests\Component\Product\ProductCategory', $registry);
        $provider->setProductCategoryManager($productCategoryManager);

        // create product
        $product = new Product();

        // create category1 (main) and add to product
        $category1 = new Category();
        $category1->setId(1);
        $productCategory1 = new ProductCategory();
        $productCategory1->setId(1);
        $productCategory1->setMain(true);
        $productCategory1->setCategory($category1);
        $product->addProductCategory($productCategory1);

        // create product variation without sync categories
        $variation = $provider->createVariation($product, false);
        $this->assertEquals(0, count($variation->getProductCategories()));

        // synchronise 1 category
        $provider->synchronizeVariationsCategories($product);
        $this->assertEquals(1, count($variation->getProductCategories()));

        // create category2 and add to product
        $category2 = new Category();
        $category2->setId(2);
        $productCategory2 = new ProductCategory();
        $productCategory2->setId(2);
        $productCategory2->setCategory($category2);
        $product->addProductCategory($productCategory2);

        // variation still have 1 category (no sync yet)
        $this->assertEquals(1, count($variation->getProductCategories()));

        // synchronize 2 categories
        $provider->synchronizeVariationsCategories($product);
        $this->assertEquals(2, count($variation->getProductCategories()));

        // remove category1 from product
        $product->removeProductCategory($productCategory1);

        // variation still have 2 categories
        $this->assertEquals(2, count($variation->getProductCategories()));

        $provider->synchronizeVariationsCategories($product);
//        $this->assertEquals(1, count($variation->getProductCategories()));
//        $this->assertFalse($variation->getProductCategories()->contains($productCategory1));
//        $this->assertTrue($variation->getProductCategories()->contains($productCategory2));
    }

    public function testProductCollectionsSynchronization()
    {
        $provider = $this->getBaseProvider();

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $productCollectionManager = new ProductCollectionManager('Sonata\Tests\Component\Product\ProductCollection', $registry);
        $provider->setProductCollectionManager($productCollectionManager);

        $product = new Product();

        $collection1 = new Collection();
        $collection1->setId(1);
        $productCollection1 = new ProductCollection();
        $productCollection1->setId(1);
        $productCollection1->setCollection($collection1);
        $product->addProductCollection($productCollection1);

        $variation = $provider->createVariation($product, false);
        $this->assertEquals(0, count($variation->getProductCollections()));

        $provider->synchronizeVariationsCollections($product);
        $this->assertEquals(1, count($variation->getProductCollections()));

        $collection2 = new Collection();
        $collection2->setId(2);
        $productCollection2 = new ProductCollection();
        $productCollection2->setId(2);
        $productCollection2->setCollection($collection2);
        $product->addProductCollection($productCollection2);

        $this->assertEquals(1, count($variation->getProductCollections()));

        $provider->synchronizeVariationsCollections($product);
        $this->assertEquals(2, count($variation->getProductCollections()));

        $product->removeProductCollection($productCollection1);
        $this->assertEquals(2, count($variation->getProductCollections()));

        $repository->expects($this->any())->method('findOneBy')->will($this->returnValue($productCollection1));

        $provider->synchronizeVariationsCollections($product);
//        $this->assertEquals(1, count($variation->getProductCollections()));
//        $this->assertFalse($variation->getProductCollections()->contains($productCollection1));
//        $this->assertTrue($variation->getProductCollections()->contains($productCollection2));
    }

    public function testProductPackagesSynchronization()
    {
        $provider = $this->getBaseProvider();

        $product = new Product();

        $package1 = new Package();
        $product->addPackage($package1);

        $variation = $provider->createVariation($product, false);

        $this->assertEquals(0, count($variation->getPackages()));

        $provider->synchronizeVariationsPackages($product);
        $this->assertEquals(1, count($variation->getPackages()));

        $package2 = new Package();
        $product->addPackage($package2);

        $this->assertEquals(1, count($variation->getPackages()));

        $provider->synchronizeVariationsPackages($product);
        $this->assertEquals(2, count($variation->getPackages()));

        $product->removePackage($package1);
        $this->assertEquals(2, count($variation->getPackages()));

        $provider->synchronizeVariationsPackages($product);
        $this->assertEquals(1, count($variation->getPackages()));
    }

    public function testProductDeliveriesSynchronization()
    {
        $provider = $this->getBaseProvider();

        $product = new Product();

        $delivery1 = new Delivery();
        $product->addDelivery($delivery1);

        $variation = $provider->createVariation($product, false);

        $this->assertEquals(0, count($variation->getDeliveries()));

        $provider->synchronizeVariationsDeliveries($product);
        $this->assertEquals(1, count($variation->getDeliveries()));

        $delivery2 = new Delivery();
        $product->addDelivery($delivery2);

        $this->assertEquals(1, count($variation->getDeliveries()));

        $provider->synchronizeVariationsDeliveries($product);
        $this->assertEquals(2, count($variation->getDeliveries()));

        $product->removeDelivery($delivery1);
        $this->assertEquals(2, count($variation->getDeliveries()));

        $provider->synchronizeVariationsDeliveries($product);
        $this->assertEquals(1, count($variation->getDeliveries()));
    }

    public function testArrayProduct()
    {
        $product = new Product;

        $arrayProduct = array(
            'sku'                       => 'productSku',
            'slug'                      => 'productslug',
            'name'                      => 'productName',
            'description'               => 'productDescription',
            'rawDescription'            => 'productRawDescription',
            'descriptionFormatter'      => 'productDescriptionFormatter',
            'shortDescription'          => 'productShortDescription',
            'rawShortDescription'       => 'productRawShortDescription',
            'shortDescriptionFormatter' => 'productShortDescriptionFormatter',
            'price'                     => 123.45,
            'vatRate'                   => 678.90,
            'stock'                     => 12345,
            'enabled'                   => 1,
            'options'                   => array('key1' => 'value1', 'key2' => array('value2', 'value3')),
        );

        $product->fromArray($arrayProduct);

        $this->assertEquals($arrayProduct, $product->toArray());

        $this->assertEquals($product->getSku(),                       $arrayProduct['sku']);
        $this->assertEquals($product->getSlug(),                      $arrayProduct['slug']);
        $this->assertEquals($product->getName(),                      $arrayProduct['name']);
        $this->assertEquals($product->getDescription(),               $arrayProduct['description']);
        $this->assertEquals($product->getRawDescription(),            $arrayProduct['rawDescription']);
        $this->assertEquals($product->getDescriptionFormatter(),      $arrayProduct['descriptionFormatter']);
        $this->assertEquals($product->getShortDescription(),          $arrayProduct['shortDescription']);
        $this->assertEquals($product->getRawShortDescription(),       $arrayProduct['rawShortDescription']);
        $this->assertEquals($product->getShortDescriptionFormatter(), $arrayProduct['shortDescriptionFormatter']);
        $this->assertEquals($product->getPrice(),                     $arrayProduct['price']);
        $this->assertEquals($product->getVatRate(),                   $arrayProduct['vatRate']);
        $this->assertEquals($product->getStock(),                     $arrayProduct['stock']);
        $this->assertEquals($product->getEnabled(),                   $arrayProduct['enabled']);
        $this->assertEquals($product->getOptions(),                   $arrayProduct['options']);
    }
}
