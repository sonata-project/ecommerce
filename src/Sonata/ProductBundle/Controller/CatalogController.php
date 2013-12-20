<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Controller;

use Application\Sonata\ClassificationBundle\Entity\Category;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\Component\Currency\CurrencyDetector;
use Sonata\ProductBundle\Entity\ProductSetManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatalogController extends Controller
{
    /**
     * Index action for catalog.
     */
    public function indexAction()
    {
        $page        = $this->getRequest()->get('page', 1);
        $displayMax  = $this->getRequest()->get('max', 9);
        $displayMode = $this->getRequest()->get('mode', 'grid');

        if (!in_array($displayMode, array('grid'))) { // "list" mode will be added later
            throw new NotFoundHttpException(sprintf('Given display_mode "%s" doesn\'t exist.', $displayMode));
        }

        $category = $this->retrieveCategoryFromQueryString();

        $pager = $this->getProductSetManager()->getCategoryActiveProductsPager($category, $page, $displayMax);

        return $this->render('SonataProductBundle:Catalog:index.html.twig', array(
            'display_mode' => $displayMode,
            'pager'        => $pager,
            'currency'     => $this->getCurrencyDetector()->getCurrency(),
            'category'     => $category,
        ));
    }

    /**
     * Retrieve Category from its id and slug, if any.
     *
     * @return Category|null
     */
    protected function retrieveCategoryFromQueryString()
    {
        $categoryId   = $this->getRequest()->get('category_id');
        $categorySlug = $this->getRequest()->get('category_slug');

        if (!$categoryId || !$categorySlug) {
            return null;
        }

        return $this->getCategoryManager()->findOneBy(array(
            'id'      => $categoryId,
            'enabled' => true,
        ));
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
