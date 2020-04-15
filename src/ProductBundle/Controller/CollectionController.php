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

namespace Sonata\ProductBundle\Controller;

use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\ProductBundle\Entity\ProductSetManager;
use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CollectionController extends Controller
{
    /**
     * @var SeoPage
     */
    private $sonataSeoPage;

    /**
     * @var CurrencyDetectorInterface
     */
    private $currencyDetector;

    /**
     * @var CollectionManagerInterface
     */
    private $collectionManagerInterface;

    /**
     * @var ProductSetManager
     */
    private $productSetManager;

    public function __construct(
        ?SeoPage $sonataSeoPage = null,
        ?CurrencyDetectorInterface $currencyDetector = null,
        ?CollectionManagerInterface $collectionManagerInterface = null,
        ?ProductSetManager $productSetManager = null
    ) {
        $this->sonataSeoPage = $sonataSeoPage;
        $this->currencyDetector = $currencyDetector;
        $this->collectionManagerInterface = $collectionManagerInterface;
        $this->productSetManager = $productSetManager;
    }

    /**
     * Display one collection.
     * NEXT_MAJOR: remove this property.
     *
     * @deprecated since 3.1, to be removed in 4.0.
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function viewAction($collectionId, $slug)
    {
        $request = new Request();
        $request->query->set('collection_id', $collectionId);
        $request->query->set('collection_slug', $slug);

        return $this->indexAction($request);
    }

    /**
     * List the product related to one collection.
     */
    final public function indexAction(Request $request): Response
    {
        $page = $request->get('page', 1);
        $displayMax = $request->get('max', 9);
        $displayMode = $request->get('mode', 'grid');
        $option = $request->get('option');

        if (!\in_array($displayMode, ['grid'], true)) { // "list" mode will be added later
            throw new NotFoundHttpException(sprintf('Given display_mode "%s" doesn\'t exist.', $displayMode));
        }

        $collectionId = $request->get('collection_id');
        $collectionSlug = $request->get('collection_slug');

        $collection = $this->getCollectionManagerInterface()->findOneBy(['id' => $collectionId, 'slug' => $collectionSlug, 'enabled' => true]);

        if (!$collection) {
            throw new NotFoundHttpException(sprintf('Unable to find the collection with id=%d', $collectionId));
        }

        $this->getSeoManager()->setTitle($collection ? $collection->getName() : $this->get('translator')->trans('catalog_index_title'));

        $pager = $this->get('knp_paginator');

        $pagination = $pager->paginate($this->getProductSetManager()->queryInCollection($collection, $option), $page, $displayMax);

        return $this->render('@SonataProduct/Collection/list_products.twig', [
                    'display_mode' => $displayMode,
                    'collection' => $collection,
                    'pager' => $pagination,
                    'currency' => $this->getCurrencyDetector()->getCurrency(),
        ]);
    }

    /**
     * List collections from one collections.
     *
     * NEXT_MAJOR: remove this property
     *
     * @deprecated since 3.1, to be removed in 4.0.
     *
     * @param $collectionId
     *
     * @return Response
     */
    final public function listSubCollectionsAction(Request $request, $collectionId)
    {
        $pager = $this->get('sonata.classification.manager.collection')
            ->getSubCollectionsPager($collectionId, $request->get('page'));

        return $this->render('@SonataProduct/Collection/list_sub_collections.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * List the product related to one collection.
     *
     * NEXT_MAJOR: remove this property
     *
     * @deprecated since 3.1, to be removed in 4.0.
     *
     * @param $collectionId
     *
     * @return Response
     */
    final public function listProductsAction(Request $request, $collectionId)
    {
        $pager = $this->get('sonata.product.set.manager')
            ->getProductsByCollectionIdPager($collectionId, $request->get('page'));

        return $this->render('@SonataProduct/Collection/list_products.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * NEXT_MAJOR: remove this property.
     *
     * @deprecated since 3.1, to be removed in 4.0.
     *
     * @param null $collection
     * @param int  $depth
     * @param int  $deep
     *
     * @return Response
     */
    final public function listSideMenuCollectionsAction($collection = null, $depth = 1, $deep = 0)
    {
        $collection = $collection ?: $this->get('sonata.classification.manager.collection')->getRootCollection();

        return $this->render('@SonataProduct/Collection/side_menu_collection.html.twig', [
          'root_collection' => $collection,
          'depth' => $depth,
          'deep' => $deep + 1,
        ]);
    }

    private function getCollectionManagerInterface(): CollectionManagerInterface
    {
        if (!$this->collectionManagerInterface) {
            return $this->get('sonata.classification.manager.collection');
        }

        return $this->collectionManagerInterface;
    }

    private function getProductSetManager(): ProductSetManager
    {
        if (!$this->productSetManager) {
            return $this->get('sonata.product.set.manager');
        }

        return $this->productSetManager;
    }

    private function getSeoManager(): SeoPage
    {
        if (!$this->sonataSeoPage) {
            return $this->get('sonata.seo.page');
        }

        return $this->sonataSeoPage;
    }

    private function getCurrencyDetector(): CurrencyDetectorInterface
    {
        if (!$this->currencyDetector) {
            return $this->get('sonata.price.currency.detector');
        }

        return $this->currencyDetector;
    }
}
