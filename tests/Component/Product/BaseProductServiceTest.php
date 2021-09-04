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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Tests\Basket\ProductProviderTest;
use Sonata\ProductBundle\Entity\ProductCategoryManager;
use Sonata\ProductBundle\Entity\ProductCollectionManager;

class BaseProductServiceTest extends TestCase
{
    /**
     * @return ProductProvider
     */
    public function getBaseProvider()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects(static::any())->method('serialize')->willReturn('{}');

        $provider = new ProductProvider($serializer);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects(static::any())->method('getClass')->willReturn(
            ProductProvider::class
        );
        $provider->setBasketElementManager($basketElementManager);

        $provider->setOrderElementClassName(\get_class(new OrderElement()));

        return $provider;
    }

    public function testProductSalableStatus(): void
    {
        $product = new Product();

        $product->setEnabled(false);
        static::assertFalse($product->isSalable());

        $product->setEnabled(true);
        static::assertTrue($product->isSalable());

        $product->setVariations(new ArrayCollection([new Product()]));
        static::assertFalse($product->isSalable());
    }

    public function testOptions(): void
    {
        $provider = $this->getBaseProvider();

        static::assertIsArray($provider->getOptions());
        static::assertNull($provider->getOption('foo'));
        $provider->setOptions(['foo' => 'bar']);

        static::assertSame('bar', $provider->getOption('foo'));
    }

    public function testOrderElement(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->expects(static::any())->method('getId')->willReturn(42);
        $product->expects(static::any())->method('getName')->willReturn('Product name');
        $product->expects(static::any())->method('getPrice')->willReturn(9.99);
        $product->expects(static::any())->method('getOptions')->willReturn(['foo' => 'bar']);
        $product->expects(static::any())->method('getDescription')->willReturn('product description');

        $productProvider = new ProductProviderTest($this->createMock(SerializerInterface::class));
        $productProvider->setCurrencyPriceCalculator(new CurrencyPriceCalculator());
        $productManager = $this->createMock(ProductManagerInterface::class);

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);
        $basketElement->setProductDefinition($productDefinition);

        $provider = $this->getBaseProvider();

        $orderElement = $provider->createOrderElement($basketElement);

        static::assertInstanceOf(OrderElementInterface::class, $orderElement);
        static::assertSame(OrderInterface::STATUS_PENDING, $orderElement->getStatus());
        static::assertSame('Product name', $orderElement->getDesignation());
        static::assertSame(1, $orderElement->getQuantity());
    }

    public function testVariationFields(): void
    {
        $provider = $this->getBaseProvider();

        static::assertEmpty($provider->getVariationFields());

        $provider->setVariationFields(['name', 'price']);

        static::assertTrue($provider->hasVariationFields());
        static::assertTrue($provider->isVariateBy('name'));
        static::assertFalse($provider->isVariateBy('fake'));
        static::assertNotEmpty($provider->getVariationFields());
        static::assertSame(['name', 'price'], $provider->getVariationFields());
    }

    public function testVariationCreation(): void
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

        static::assertNull($variation1->getId());
        static::assertSame('fake name (duplicated)', $variation1->getName());
        static::assertSame($product->getId(), $variation1->getParent()->getId());
        static::assertFalse($variation1->isEnabled());
        static::assertTrue($variation1->isVariation());

        static::assertCount(2, $product->getVariations());

        static::assertCount(0, $variation1->getVariations());
        static::assertCount(0, $variation1->getPackages());
        static::assertCount(0, $variation1->getDeliveries());
        static::assertCount(0, $variation1->getProductCategories());

        static::assertCount(0, $variation2->getVariations());
        static::assertCount(1, $variation2->getPackages());
        static::assertCount(1, $variation2->getDeliveries());

        $provider->setVariationFields(['packages', 'productCollections', 'productCategories', 'deliveries']);

        $variation3 = $provider->createVariation($product, true);

        static::assertCount(0, $variation3->getVariations());
        static::assertCount(0, $variation3->getPackages());
        static::assertCount(0, $variation3->getDeliveries());
        static::assertCount(0, $variation3->getProductCategories());
    }

    public function testProductDataSynchronization(): void
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

        static::assertSame($product->getName(), $variation->getName());
        static::assertSame(15, $variation->getPrice());
        static::assertSame($product->getVatRate(), $variation->getVatRate());
        static::assertTrue($variation->isEnabled());

        static::assertCount(1, $product->getVariations());
        static::assertCount(0, $variation->getVariations());
    }

    public function testProductCategoriesSynchronization(): void
    {
        $provider = $this->getBaseProvider();

        $repository = $this->createMock(EntityRepository::class);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects(static::any())->method('getRepository')->willReturn($repository);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::any())->method('getManagerForClass')->willReturn($em);

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
        static::assertCount(0, $variation->getProductCategories());

        // synchronise 1 category
        $provider->synchronizeVariationsCategories($product);
        static::assertCount(1, $variation->getProductCategories());

        // create category2 and add to product
        $category2 = new Category();
        $category2->setId(2);
        $productCategory2 = new ProductCategory();
        $productCategory2->setId(2);
        $productCategory2->setCategory($category2);
        $product->addProductCategory($productCategory2);

        // variation still have 1 category (no sync yet)
        static::assertCount(1, $variation->getProductCategories());

        // synchronize 2 categories
        $provider->synchronizeVariationsCategories($product);
        static::assertCount(2, $variation->getProductCategories());

        // remove category1 from product
        $product->removeProductCategory($productCategory1);

        // variation still have 2 categories
        static::assertCount(2, $variation->getProductCategories());

        $provider->synchronizeVariationsCategories($product);
        //        $this->assertEquals(1, count($variation->getProductCategories()));
//        $this->assertFalse($variation->getProductCategories()->contains($productCategory1));
//        $this->assertTrue($variation->getProductCategories()->contains($productCategory2));
    }

    public function testProductCollectionsSynchronization(): void
    {
        $provider = $this->getBaseProvider();

        $repository = $this->createMock(EntityRepository::class);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects(static::any())->method('getRepository')->willReturn($repository);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::any())->method('getManagerForClass')->willReturn($em);

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
        static::assertCount(0, $variation->getProductCollections());

        $provider->synchronizeVariationsCollections($product);
        static::assertCount(1, $variation->getProductCollections());

        $collection2 = new Collection();
        $collection2->setId(2);
        $productCollection2 = new ProductCollection();
        $productCollection2->setId(2);
        $productCollection2->setCollection($collection2);
        $product->addProductCollection($productCollection2);

        static::assertCount(1, $variation->getProductCollections());

        $provider->synchronizeVariationsCollections($product);
        static::assertCount(2, $variation->getProductCollections());

        $product->removeProductCollection($productCollection1);
        static::assertCount(2, $variation->getProductCollections());

        $repository->expects(static::any())->method('findOneBy')->willReturn($productCollection1);

        $provider->synchronizeVariationsCollections($product);
        //        $this->assertEquals(1, count($variation->getProductCollections()));
//        $this->assertFalse($variation->getProductCollections()->contains($productCollection1));
//        $this->assertTrue($variation->getProductCollections()->contains($productCollection2));
    }

    public function testProductPackagesSynchronization(): void
    {
        $provider = $this->getBaseProvider();

        $product = new Product();

        $package1 = new Package();
        $product->addPackage($package1);

        $variation = $provider->createVariation($product, false);

        static::assertCount(0, $variation->getPackages());

        $provider->synchronizeVariationsPackages($product);
        static::assertCount(1, $variation->getPackages());

        $package2 = new Package();
        $product->addPackage($package2);

        static::assertCount(1, $variation->getPackages());

        $provider->synchronizeVariationsPackages($product);
        static::assertCount(2, $variation->getPackages());

        $product->removePackage($package1);
        static::assertCount(2, $variation->getPackages());

        $provider->synchronizeVariationsPackages($product);
        static::assertCount(1, $variation->getPackages());
    }

    public function testProductDeliveriesSynchronization(): void
    {
        $provider = $this->getBaseProvider();

        $product = new Product();

        $delivery1 = new Delivery();
        $product->addDelivery($delivery1);

        $variation = $provider->createVariation($product, false);

        static::assertCount(0, $variation->getDeliveries());

        $provider->synchronizeVariationsDeliveries($product);
        static::assertCount(1, $variation->getDeliveries());

        $delivery2 = new Delivery();
        $product->addDelivery($delivery2);

        static::assertCount(1, $variation->getDeliveries());

        $provider->synchronizeVariationsDeliveries($product);
        static::assertCount(2, $variation->getDeliveries());

        $product->removeDelivery($delivery1);
        static::assertCount(2, $variation->getDeliveries());

        $provider->synchronizeVariationsDeliveries($product);
        static::assertCount(1, $variation->getDeliveries());
    }

    public function testArrayProduct(): void
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

        static::assertSame($arrayProduct, $product->toArray());

        static::assertSame($product->getSku(), $arrayProduct['sku']);
        static::assertSame($product->getSlug(), $arrayProduct['slug']);
        static::assertSame($product->getName(), $arrayProduct['name']);
        static::assertSame($product->getDescription(), $arrayProduct['description']);
        static::assertSame($product->getRawDescription(), $arrayProduct['rawDescription']);
        static::assertSame($product->getDescriptionFormatter(), $arrayProduct['descriptionFormatter']);
        static::assertSame($product->getShortDescription(), $arrayProduct['shortDescription']);
        static::assertSame($product->getRawShortDescription(), $arrayProduct['rawShortDescription']);
        static::assertSame($product->getShortDescriptionFormatter(), $arrayProduct['shortDescriptionFormatter']);
        static::assertSame($product->getPrice(), $arrayProduct['price']);
        static::assertSame($product->getVatRate(), $arrayProduct['vatRate']);
        static::assertSame($product->getStock(), $arrayProduct['stock']);
        static::assertSame($product->getEnabled(), $arrayProduct['enabled']);
        static::assertSame($product->getOptions(), $arrayProduct['options']);
    }
}
