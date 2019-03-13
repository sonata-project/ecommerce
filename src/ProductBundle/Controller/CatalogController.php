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

use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Currency\CurrencyDetector;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\ProductBundle\Entity\ProductSetManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatalogController extends Controller
{
    /**
     * Index action for catalog.
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->get('page', 1);
        $displayMax = $request->get('max', 9);
        $displayMode = $request->get('mode', 'grid');
        $filter = $request->get('filter');
        $option = $request->get('option');

        if (!\in_array($displayMode, ['grid'], true)) { // "list" mode will be added later
            throw new NotFoundHttpException(sprintf('Given display_mode "%s" doesn\'t exist.', $displayMode));
        }

        $category = $this->retrieveCategoryFromQueryString($request);

        $this->get('sonata.seo.page')->setTitle($category ? $category->getName() : $this->get('translator')->trans('catalog_index_title'));

        $pager = $this->get('knp_paginator');
        $pagination = $pager->paginate($this->getProductSetManager()->getCategoryActiveProductsQueryBuilder($category, $filter, $option), $page, $displayMax);

        return $this->render('@SonataProduct/Catalog/index.html.twig', [
            'display_mode' => $displayMode,
            'pager' => $pagination,
            'currency' => $this->getCurrencyDetector()->getCurrency(),
            'category' => $category,
            'provider' => $this->getProviderFromCategory($category),
        ]);
    }

    /**
     * Retrieve Category from its id and slug, if any.
     *
     * @return CategoryInterface|null
     */
    protected function retrieveCategoryFromQueryString(Request $request)
    {
        $categoryId = $request->get('category_id');
        $categorySlug = $request->get('category_slug');

        if (!$categoryId || !$categorySlug) {
            return null;
        }

        return $this->getCategoryManager()->findOneBy([
            'id' => $categoryId,
            'enabled' => true,
        ]);
    }

    /**
     * Gets the product provider associated with $category if any.
     *
     * @param CategoryInterface $category
     *
     * @return ProductProviderInterface|null
     */
    protected function getProviderFromCategory(CategoryInterface $category = null)
    {
        if (null === $category) {
            return null;
        }

        $product = $this->getProductSetManager()->findProductForCategory($category);

        return $product ? $this->getProductPool()->getProvider($product) : null;
    }

    /**
     * @return Pool
     */
    protected function getProductPool()
    {
        return $this->get('sonata.product.pool');
    }

    /**
     * @return ProductSetManager
     */
    protected function getProductSetManager()
    {
        return $this->get('sonata.product.set.manager');
    }

    /**
     * @return CurrencyDetector
     */
    protected function getCurrencyDetector()
    {
        return $this->get('sonata.price.currency.detector');
    }

    /**
     * @return CategoryManager
     */
    protected function getCategoryManager()
    {
        return $this->get('sonata.classification.manager.category');
    }
}
