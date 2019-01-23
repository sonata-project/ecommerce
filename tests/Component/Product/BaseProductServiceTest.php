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

namespace Sonata\Component\Tests\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Entity\BaseCategory;
use Sonata\ClassificationBundle\Entity\BaseCollection;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Tests\Basket\ProductProviderTest;
use Sonata\OrderBundle\Entity\BaseOrderElement;
use Sonata\ProductBundle\Entity\BaseDelivery;
use Sonata\ProductBundle\Entity\BasePackage;
use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Entity\BaseProductCategory;
use Sonata\ProductBundle\Entity\BaseProductCollection;
use Sonata\ProductBundle\Entity\ProductCategoryManager;
use Sonata\ProductBundle\Entity\ProductCollectionManager;
use Sonata\ProductBundle\Model\BaseProductProvider;

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
        return [];
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

class Package extends BasePackage
{
}
class Delivery extends BaseDelivery
{
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

class BaseProductServiceTest extends TestCase
{
    /**
     * @return BaseProductServiceTest_ProductProvider
     */
    public function getBaseProvider()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->any())->method('serialize')->will($this->returnValue('{}'));

        $provider = new BaseProductServiceTest_ProductProvider($serializer);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->any())->method('getClass')->will(
            $this->returnValue(BaseOrderElementTest_ProductProvider::class)
        );
        $provider->setBasketElementManager($basketElementManager);

        $provider->setOrderElementClassName(\get_class(new OrderElement()));

        return $provider;
    }

    public function testProductSalableStatus()
    {
        $product = new Product();

        $product->setEnabled(false);
        $this->assertFalse($product->isSalable());

        $product->setEnabled(true);
        $this->assertTrue($product->isSalable());

        $product->setVariations(new ArrayCollection([new Product()]));
        $this->assertFalse($product->isSalable());
    }

    public function testOptions()
    {
        $provider = $this->getBaseProvider();

        $this->assertInternalType('array', $provider->getOptions());
        $this->assertNull($provider->getOption('foo'));
        $provider->setOptions(['foo' => 'bar']);

        $this->assertSame('bar', $provider->getOption('foo'));
    }

    public function testOrderElement()
    {
        $product = $this->createMock(ProductInterface::class);
        $product->expects($this->any())->method('getId')->will($this->returnValue(42));
        $product->expects($this->any())->method('getName')->will($this->returnValue('Product name'));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(9.99));
        $product->expects($this->any())->method('getOptions')->will($this->returnValue(['foo' => 'bar']));
        $product->expects($this->any())->method('getDescription')->will($this->returnValue('product description'));

        $productProvider = new ProductProviderTest($this->createMock(SerializerInterface::class));
        $productProvider->setCurrencyPriceCalculator(new CurrencyPriceCalculator());
        $productManager = $this->createMock(ProductManagerInterface::class);

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);
        $basketElement->setProductDefinition($productDefinition);

        $provider = $this->getBaseProvider();

        $orderElement = $provider->createOrderElement($basketElement);

        $this->assertInstanceOf(OrderElementInterface::class, $orderElement);
        $this->assertSame(OrderInterface::STATUS_PENDING, $orderElement->getStatus());
        $this->assertSame('Product name', $orderElement->getDesignation());
        $this->assertSame(1, $orderElement->getQuantity());
    }

    public function testVariationFields()
    {
        $provider = $this->getBaseProvider();

        $this->assertEmpty($provider->getVariationFields());

        $provider->setVariationFields(['name', 'price']);

        $this->assertTrue($provider->hasVariationFields());
        $this->assertTrue($provider->isVariateBy('name'));
        $this->assertFalse($provider->isVariateBy('fake'));
        $this->assertNotEmpty($provider->getVariationFields());
        $this->assertSame(['name', 'price'], $provider->getVariationFields());
    }

    public function testVariationCreation()
    {
        $provider = $this->getBaseProvider();
        $provider->setVariationFields(['name', 'price']);

        $product = new Product();
        $product->id = 2;

        $product->addDelivery(new Delivery());
        $product->addPackage(new Package());
        $product->addProductCategory(new ProductCategory());

        $variation1 = $provider->createVariation($product, false);
        $variation2 = $provider->createVariation($product, true);

        $this->assertNull($variation1->getId());
        $this->assertSame('fake name (duplicated)', $variation1->getName());
        $this->assertSame($product->getId(), $variation1->getParent()->getId());
        $this->assertFalse($variation1->isEnabled());
        $this->assertTrue($variation1->isVariation());

        $this->assertCount(2, $product->getVariations());

        $this->assertCount(0, $variation1->getVariations());
        $this->assertCount(0, $variation1->getPackages());
        $this->assertCount(0, $variation1->getDeliveries());
        $this->assertCount(0, $variation1->getProductCategories());

        $this->assertCount(0, $variation2->getVariations());
        $this->assertCount(1, $variation2->getPackages());
        $this->assertCount(1, $variation2->getDeliveries());

        $provider->setVariationFields(['packages', 'productCollections', 'productCategories', 'deliveries']);

        $variation3 = $provider->createVariation($product, true);

        $this->assertCount(0, $variation3->getVariations());
        $this->assertCount(0, $variation3->getPackages());
        $this->assertCount(0, $variation3->getDeliveries());
        $this->assertCount(0, $variation3->getProductCategories());
    }

    public function testProductDataSynchronization()
    {
        $provider = $this->getBaseProvider();
        $provider->setVariationFields(['price']);

        $product = new Product();
        $product->id = 2;

        $variation = $provider->createVariation($product);

        $product->setName('Product new name');
        $product->setPrice(50);
        $product->setVatRate(5.5);

        $provider->synchronizeVariationsProduct($product);

        $this->assertSame($product->getName(), $variation->getName());
        $this->assertSame(15, $variation->getPrice());
        $this->assertSame($product->getVatRate(), $variation->getVatRate());
        $this->assertTrue($variation->isEnabled());

        $this->assertCount(1, $product->getVariations());
        $this->assertCount(0, $variation->getVariations());
    }

    public function testProductCategoriesSynchronization()
    {
        $provider = $this->getBaseProvider();

        $repository = $this->createMock(EntityRepository::class);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $productCategoryManager = new ProductCategoryManager(ProductCategory::class, $registry);
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
        $this->assertCount(0, $variation->getProductCategories());

        // synchronise 1 category
        $provider->synchronizeVariationsCategories($product);
        $this->assertCount(1, $variation->getProductCategories());

        // create category2 and add to product
        $category2 = new Category();
        $category2->setId(2);
        $productCategory2 = new ProductCategory();
        $productCategory2->setId(2);
        $productCategory2->setCategory($category2);
        $product->addProductCategory($productCategory2);

        // variation still have 1 category (no sync yet)
        $this->assertCount(1, $variation->getProductCategories());

        // synchronize 2 categories
        $provider->synchronizeVariationsCategories($product);
        $this->assertCount(2, $variation->getProductCategories());

        // remove category1 from product
        $product->removeProductCategory($productCategory1);

        // variation still have 2 categories
        $this->assertCount(2, $variation->getProductCategories());

        $provider->synchronizeVariationsCategories($product);
        //        $this->assertEquals(1, count($variation->getProductCategories()));
//        $this->assertFalse($variation->getProductCategories()->contains($productCategory1));
//        $this->assertTrue($variation->getProductCategories()->contains($productCategory2));
    }

    public function testProductCollectionsSynchronization()
    {
        $provider = $this->getBaseProvider();

        $repository = $this->createMock(EntityRepository::class);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $productCollectionManager = new ProductCollectionManager(ProductCollection::class, $registry);
        $provider->setProductCollectionManager($productCollectionManager);

        $product = new Product();

        $collection1 = new Collection();
        $collection1->setId(1);
        $productCollection1 = new ProductCollection();
        $productCollection1->setId(1);
        $productCollection1->setCollection($collection1);
        $product->addProductCollection($productCollection1);

        $variation = $provider->createVariation($product, false);
        $this->assertCount(0, $variation->getProductCollections());

        $provider->synchronizeVariationsCollections($product);
        $this->assertCount(1, $variation->getProductCollections());

        $collection2 = new Collection();
        $collection2->setId(2);
        $productCollection2 = new ProductCollection();
        $productCollection2->setId(2);
        $productCollection2->setCollection($collection2);
        $product->addProductCollection($productCollection2);

        $this->assertCount(1, $variation->getProductCollections());

        $provider->synchronizeVariationsCollections($product);
        $this->assertCount(2, $variation->getProductCollections());

        $product->removeProductCollection($productCollection1);
        $this->assertCount(2, $variation->getProductCollections());

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

        $this->assertCount(0, $variation->getPackages());

        $provider->synchronizeVariationsPackages($product);
        $this->assertCount(1, $variation->getPackages());

        $package2 = new Package();
        $product->addPackage($package2);

        $this->assertCount(1, $variation->getPackages());

        $provider->synchronizeVariationsPackages($product);
        $this->assertCount(2, $variation->getPackages());

        $product->removePackage($package1);
        $this->assertCount(2, $variation->getPackages());

        $provider->synchronizeVariationsPackages($product);
        $this->assertCount(1, $variation->getPackages());
    }

    public function testProductDeliveriesSynchronization()
    {
        $provider = $this->getBaseProvider();

        $product = new Product();

        $delivery1 = new Delivery();
        $product->addDelivery($delivery1);

        $variation = $provider->createVariation($product, false);

        $this->assertCount(0, $variation->getDeliveries());

        $provider->synchronizeVariationsDeliveries($product);
        $this->assertCount(1, $variation->getDeliveries());

        $delivery2 = new Delivery();
        $product->addDelivery($delivery2);

        $this->assertCount(1, $variation->getDeliveries());

        $provider->synchronizeVariationsDeliveries($product);
        $this->assertCount(2, $variation->getDeliveries());

        $product->removeDelivery($delivery1);
        $this->assertCount(2, $variation->getDeliveries());

        $provider->synchronizeVariationsDeliveries($product);
        $this->assertCount(1, $variation->getDeliveries());
    }

    public function testArrayProduct()
    {
        $product = new Product();

        $arrayProduct = [
            'sku' => 'productSku',
            'slug' => 'productslug',
            'name' => 'productName',
            'description' => 'productDescription',
            'rawDescription' => 'productRawDescription',
            'descriptionFormatter' => 'productDescriptionFormatter',
            'shortDescription' => 'productShortDescription',
            'rawShortDescription' => 'productRawShortDescription',
            'shortDescriptionFormatter' => 'productShortDescriptionFormatter',
            'price' => 123.45,
            'vatRate' => 678.90,
            'stock' => 12345,
            'enabled' => 1,
            'options' => ['key1' => 'value1', 'key2' => ['value2', 'value3']],
        ];

        $product->fromArray($arrayProduct);

        $this->assertSame($arrayProduct, $product->toArray());

        $this->assertSame($product->getSku(), $arrayProduct['sku']);
        $this->assertSame($product->getSlug(), $arrayProduct['slug']);
        $this->assertSame($product->getName(), $arrayProduct['name']);
        $this->assertSame($product->getDescription(), $arrayProduct['description']);
        $this->assertSame($product->getRawDescription(), $arrayProduct['rawDescription']);
        $this->assertSame($product->getDescriptionFormatter(), $arrayProduct['descriptionFormatter']);
        $this->assertSame($product->getShortDescription(), $arrayProduct['shortDescription']);
        $this->assertSame($product->getRawShortDescription(), $arrayProduct['rawShortDescription']);
        $this->assertSame($product->getShortDescriptionFormatter(), $arrayProduct['shortDescriptionFormatter']);
        $this->assertSame($product->getPrice(), $arrayProduct['price']);
        $this->assertSame($product->getVatRate(), $arrayProduct['vatRate']);
        $this->assertSame($product->getStock(), $arrayProduct['stock']);
        $this->assertSame($product->getEnabled(), $arrayProduct['enabled']);
        $this->assertSame($product->getOptions(), $arrayProduct['options']);
    }
}
