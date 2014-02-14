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

class CollectionController extends Controller
{
    /**
     * List the main collections
     *
     * @return Response
     */
    public function indexAction()
    {
        $pager = $this->get('sonata.classification.manager.collection')
            ->getRootCollectionsPager($this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Collection:index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * Display one collection
     *
     * @throws NotFoundHttpException
     *
     * @param $collectionId
     * @param $slug
     *
     * @return Response
     */
    public function viewAction($collectionId, $slug)
    {
        $collection = $this->get('sonata.classification.manager.collection')->findOneBy(array('id' => $collectionId));

        if (!$collection) {
            throw new NotFoundHttpException(sprintf('Unable to find the collection with id=%d', $collectionId));
        }

        return $this->render('SonataProductBundle:Collection:view.html.twig', array(
           'collection' => $collection
        ));
    }

    /**
     * List collections from one collections
     *
     * @param $collectionId
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function listSubCollectionsAction($collectionId)
    {
        $pager = $this->get('sonata.classification.manager.collection')
            ->getSubCollectionsPager($collectionId, $this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Collection:list_sub_collections.html.twig', array(
            'pager' => $pager
        ));
    }

    /**
     * List the product related to one collection
     *
     * @param $collectionId
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function listProductsAction($collectionId)
    {
        $pager = $this->get('sonata.product.set.manager')
            ->getProductsByCollectionIdPager($collectionId, $this->get('request')->get('page'));

        return $this->render('SonataProductBundle:Collection:list_products.html.twig', array(
            'pager' => $pager
        ));
    }

    /**
     * @param null $collection
     * @param int  $depth
     * @param int  $deep
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function listSideMenuCollectionsAction($collection = null, $depth = 1, $deep = 0)
    {
        $collection = $collection ?: $this->get('sonata.classification.manager.collection')->getRootCollection();

        return $this->render('SonataProductBundle:Collection:side_menu_collection.html.twig', array(
          'root_collection' => $collection,
          'depth'         => $depth,
          'deep'          => $deep + 1
        ));
    }
}
