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

namespace Sonata\PaymentBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Paginator;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Tests\Product\Collection;
use Sonata\ProductBundle\Controller\CollectionController;
use Sonata\ProductBundle\Entity\ProductSetManager;
use Sonata\ProductBundle\Tests\Entity\Product;
use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class CollectionControllerTest extends TestCase
{
    public function testCollectionPublicServicesAction(): void
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        //Mock Currency
        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currencyDetector->expects($this->any())->method('getCurrency')->willReturn($currency);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                ->method('get')
                ->with('sonata.price.currency.detector')
                ->willReturn($currencyDetector);

        $collectionControllerTest = new CollectionController();
        $collectionControllerTest->setContainer($container);

        $classCollectionController = new \ReflectionClass(CollectionController::class);
        $methodGetCurrencyDetector = $classCollectionController->getMethod('getCurrencyDetector');
        $methodGetCurrencyDetector->setAccessible(true);

        $this->assertSame('EUR', $methodGetCurrencyDetector->invoke($collectionControllerTest)->getCurrency()->getLabel());

        // collection manager

        $collectionManagerInterface = $this->createMock(CollectionManagerInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                ->method('get')
                ->with('sonata.classification.manager.collection')
                ->willReturn($collectionManagerInterface);

        $collectionControllerTest = new CollectionController();
        $collectionControllerTest->setContainer($container);

        $classCollectionController = new \ReflectionClass(CollectionController::class);
        $methodGetCurrencyDetector = $classCollectionController->getMethod('getCollectionManagerInterface');
        $methodGetCurrencyDetector->setAccessible(true);

        $this->assertInstanceOf(CollectionManagerInterface::class, $methodGetCurrencyDetector->invoke($collectionControllerTest));

        // product set manager

        $productSetManager = $this->createMock(ProductSetManager::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                ->method('get')
                ->with('sonata.product.set.manager')
                ->willReturn($productSetManager);

        $collectionControllerTest = new CollectionController();
        $collectionControllerTest->setContainer($container);

        $classCollectionController = new \ReflectionClass(CollectionController::class);
        $methodGetCurrencyDetector = $classCollectionController->getMethod('getProductSetManager');
        $methodGetCurrencyDetector->setAccessible(true);

        $this->assertInstanceOf(ProductSetManager::class, $methodGetCurrencyDetector->invoke($collectionControllerTest));

        //seo

        $seoPage = $this->createMock(SeoPage::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                ->method('get')
                ->with('sonata.seo.page')
                ->willReturn($seoPage);

        $collectionControllerTest = new CollectionController();
        $collectionControllerTest->setContainer($container);

        $classCollectionController = new \ReflectionClass(CollectionController::class);
        $methodGetCurrencyDetector = $classCollectionController->getMethod('getSeoManager');
        $methodGetCurrencyDetector->setAccessible(true);

        $this->assertInstanceOf(SeoPage::class, $methodGetCurrencyDetector->invoke($collectionControllerTest));
    }

    public function testCollectionGridAction(): void
    {
        $request = new Request();
        $request->setMethod('POST');
        $request->query->set('mode', 'list');

        $collectionControllerTest = $this->createCollectionController();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Given display_mode "list" doesn\'t exist.');

        $result = $collectionControllerTest->indexAction($request);
    }

    public function testCollectionCurrencyAction(): void
    {
        $collectionControllerTest = $this->createCollectionController();

        $classCollectionController = new \ReflectionClass(CollectionController::class);
        $methodGetCurrencyDetector = $classCollectionController->getMethod('getCurrencyDetector');
        $methodGetCurrencyDetector->setAccessible(true);

        $this->assertSame('EUR', $methodGetCurrencyDetector->invoke($collectionControllerTest)->getCurrency()->getLabel());
    }

    public function testCollectionProductManagerAction(): void
    {
        $collectionControllerTest = $this->createCollectionController();

        $classCollectionController = new \ReflectionClass(CollectionController::class);
        $methodGetSetProductManager = $classCollectionController->getMethod('getProductSetManager');
        $methodGetSetProductManager->setAccessible(true);

        $classProductSetManager = new \ReflectionClass(ProductSetManager::class);
        $methodQueryincollection = $classProductSetManager->getMethod('queryincollection');
        $methodQueryincollection->setAccessible(true);

        $collection1 = new Collection();
        $collection1->setId(1);
        $collection1->setName('Collection-1');

        $productSetManager = $methodGetSetProductManager->invoke($collectionControllerTest);
        $queryincollection = $methodQueryincollection->invokeArgs($productSetManager, [$collection1, 1]);

        $this->assertSame('SELECT DISTINCT FROM '.collection::class.' q LEFT JOIN p.productCollections pc WHERE pc.collection = :collection', $queryincollection->getDql());
    }

    public function testIndexActionCollectionNotFound(): void
    {
        $request = new Request();
        $request->setMethod('POST');
        $request->query->set('collection_id', 1);
        $request->query->set('page', 1);
        $request->query->set('max', 9);
        $request->query->set('mode', 'grid');

        $collectionControllerTest = $this->createCollectionController();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Unable to find the collection with id=1');

        $result = $collectionControllerTest->indexAction($request);
    }

    public function testIndexActionCollectionFound(): void
    {
        $collection = new Collection();
        $collection->setId(1);
        $collection->setName('my-collection');

        //mocking
        $paginator = $this->createMock(Paginator::class);
        $paginator->expects($this->any())->method('paginate')->willReturn([]);

        $container = $this->createMock(ContainerInterface::class);
        $template = $this->createMock(Environment::class);

        $collectionManagerInterface = $this->createMock(CollectionManagerInterface::class);
        $collectionManagerInterface->expects($this->any())->method('findOneBy')->willReturn($collection);

        $container->expects($this->at(0))
        ->method('get')
        ->with('knp_paginator')
        ->willReturn($paginator);

        $container->expects($this->at(2))
        ->method('has')
        ->with('twig')
        ->willReturn(true);

        $container->expects($this->at(3))
        ->method('get')
        ->with('twig')
        ->willReturn($template);

        $request = new Request();
        $request->setMethod('POST');
        $request->query->set('collection_id', 1);
        $request->query->set('page', 1);
        $request->query->set('max', 9);
        $request->query->set('mode', 'grid');

        $collectionControllerTest = $this->createCollectionController(null, null, $collectionManagerInterface, null);

        $collectionControllerTest->setContainer($container);

        $this->assertSame(200, $collectionControllerTest->indexAction($request)->getStatusCode());
    }

    public function createCollectionController(
        ?SeoPage $sonataSeoPage = null,
        ?CurrencyDetectorInterface $currencyDetector = null,
        ?CollectionManagerInterface $collectionManagerInterface = null,
        ?ProductSetManager $productSetManager = null
        ): CollectionController {
        if (!$sonataSeoPage) {
            $sonataSeoPage = new SeoPage('Collection page');
        }

        if (!$currencyDetector) {
            $currency = new Currency();
            $currency->setLabel('EUR');

            //Mock Currency
            $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
            $currencyDetector->expects($this->any())->method('getCurrency')->willReturn($currency);
        }

        //Mock repository
        $repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('findOneBy');
        $repository->expects($this->any())->method('findBy');

        //Mock Entitymanaer
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->willReturn($repository);

        //Mock Manager registry
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        //Prepare Query Builder
        $qb = new QueryBuilder($em);
        $qb->from(Collection::class, 'q');

        //Mock createQueryBuilder result
        $repository->expects($this->any())->method('createQueryBuilder')->willReturn($qb);

        if (!$collectionManagerInterface) {
            $collectionManagerInterface = $this->createMock(CollectionManagerInterface::class);
        }

        if (!$productSetManager) {
            $productSetManager = new ProductSetManager(Product::class, $registry);
        }

        return new CollectionController($sonataSeoPage, $currencyDetector, $collectionManagerInterface, $productSetManager);
    }
}
