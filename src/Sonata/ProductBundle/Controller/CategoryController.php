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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;

class CategoryController extends Controller
{
    /**
     * List the main categories
     *
     * @return Response
     */
    public function indexAction()
    {
        $pager = $this->get('sonata.classification.manager.category')
            ->getRootCategoriesPager($this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Category:index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * Display one category
     *
     * @throws NotFoundHttpException
     *
     * @param $categoryId
     * @param $slug
     *
     * @return Response
     */
    public function viewAction($categoryId, $slug)
    {
        $category = $this->getActiveCategory($categoryId, $slug);

        if (!$category) {
            throw new NotFoundHttpException(sprintf('Unable to find the category with id=%d', $categoryId));
        }

        return $this->render('SonataProductBundle:Category:view.html.twig', array(
           'category' => $category
        ));
    }

    /**
     * List categories from one categories
     *
     * @param $categoryId
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function listSubCategoriesAction($categoryId)
    {
        $pager = $this->get('sonata.classification.manager.category')
            ->getSubCategoriesPager($categoryId, $this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Category:list_sub_categories.html.twig', array(
            'pager' => $pager
        ));
    }

    /**
     * List the product related to one category
     *
     * @param int $categoryId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function listProductsAction($categoryId)
    {
        $category = $this->getActiveCategory($categoryId);

        if (!$category) {
            throw new NotFoundHttpException(sprintf('Unable to find the category with id=%d', $categoryId));
        }

        $pager = $this->get('sonata.product.set.manager')
            ->getActiveProductsByCategoryIdPager($categoryId, $this->get('request')->get('page'));

        $viewParams = array(
            'pager' => $pager,
            'category' => $category,
        );

        if (count($pager)) {
            $viewParams['provider'] = $this->get('sonata.product.pool')->getProvider($pager->getCurrent());
        }

        return $this->render('SonataProductBundle:Category:list_products.html.twig', $viewParams);
    }

    /**
     * @param int $categoryId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function displayBreadcrumbAction($categoryId)
    {
        $categories = $this->get('sonata.classification.manager.category')->getCategories();

        if (!isset($categories[$categoryId])) {
            throw new NotFoundHttpException(sprintf('Unable to find the category with id=%d', $categoryId));
        }

        $sorted = array(
            $categories[$categoryId]
        );
        $selectedCategoryId = $categoryId;

        while ($category = $categories[$categoryId]->getParent()) {
            $sorted[] = $category;
            $categoryId = $category->getId();
        }

        $sorted = array_reverse($sorted, true);

        return $this->render('SonataProductBundle:Category:display_breadcrumb.html.twig', array(
            'categories' => $sorted,
            'selectedCategoryId' => $selectedCategoryId,
        ));
    }

    /**
     * @param null $category
     * @param int  $depth
     * @param int  $deep
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function listSideMenuCategoriesAction($category = null, $depth = 1, $deep = 0)
    {
        $category = $category ?: $this->get('sonata.classification.manager.category')->getRootCategory();

        return $this->render('SonataProductBundle:Category:side_menu_category.html.twig', array(
          'root_category' => $category,
          'depth'         => $depth,
          'deep'          => $deep + 1
        ));
    }

    /**
     * Wrapper to retrieve an active category from its ID and slug if provided
     *
     * @param int         $categoryId
     * @param null|string $slug
     *
     * @return \Sonata\ClassificationBundle\Model\CategoryInterface|null
     */
    protected function getActiveCategory($categoryId, $slug = null)
    {
        $settings = array(
            'id' => $categoryId,
            'enabled' => true
        );

        if ($slug) {
            $settings['slug'] = $slug;
        }

        return $this->get('sonata.classification.manager.category')->findOneBy($settings);
    }
}
