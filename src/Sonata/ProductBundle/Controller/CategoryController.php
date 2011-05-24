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

use Sonata\AdminBundle\Datagrid\ORM\Pager;
use Doctrine\ORM\Query\Expr;

class CategoryController extends Controller
{

    /**
     * List the main categories
     *
     * @return Response
     */
    public function indexAction()
    {
        $pager = $this->get('sonata.category.manager')
            ->getRootCategoriesPager($this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Category:index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * Display one category
     *
     * @throws NotFoundHttpException
     * @param  $categoryId
     * @param  $slug
     * @return Response
     */
    public function viewAction($categoryId, $slug)
    {
        $em             = $this->get('doctrine.orm.default_entity_manager');
        $repository     = $em->getRepository('Application\Sonata\ProductBundle\Entity\Category');

        $category = $repository->findOneById($categoryId);

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
     * @param  $categoryId
     * @return void
     */
    public function listSubCategoriesAction($categoryId)
    {
        $pager = $this->get('sonata.category.manager')
            ->getSubCategoriesPager($categoryId, $this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Category:list_sub_categories.html.twig', array(
            'pager' => $pager
        ));
    }

    /**
     * List the product related to one category
     *
     * @param  $categoryId
     * @return
     */
    public function listProductsAction($categoryId)
    {
        $pager = $this->get('sonata.product.collection.manager')
            ->getProductsByCategoryIdPager($categoryId, $this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Category:list_products.html.twig', array(
            'pager' => $pager
        ));
    }


    public function listSideMenuCategoriesAction($category = null, $depth = 1, $deep = 0)
    {
        $em             = $this->get('doctrine.orm.default_entity_manager');
        $repository     = $em->getRepository('Application\Sonata\ProductBundle\Entity\Category');

        $category = $category ?: $repository->getRootCategory();

        return $this->render('SonataProductBundle:Category:side_menu_category.html.twig', array(
          'root_category' => $category,
          'depth'         => $depth,
          'deep'          => $deep + 1
        ));
    }
}