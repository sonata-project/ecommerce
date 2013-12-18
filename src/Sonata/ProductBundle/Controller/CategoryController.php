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
}
