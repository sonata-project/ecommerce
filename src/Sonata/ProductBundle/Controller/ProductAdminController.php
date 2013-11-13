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

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductAdminController extends Controller
{
    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $parameters = $this->admin->getPersistentParameters();

        if (!$parameters['provider']) {
            return $this->render('SonataProductBundle:ProductAdmin:select_provider.html.twig', array(
                'providers'     => $this->get('sonata.product.pool')->getProducts(),
                'base_template' => $this->getBaseTemplate(),
                'admin'         => $this->admin,
                'action'        => 'create'
            ));
        }

        return parent::createAction();
    }

    public function batchActionCreateVariation(ProxyQueryInterface $productQuery)
    {
        $manager = $this->getProductManager();

        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $products = $productQuery->execute();

        try {
            foreach ($products as $product) {
                $productProvider = $this->getProductPool()->getProvider($product);

                $productProvider->setProductCategoryManager($this->getProductCategoryManager());
                $productProvider->setProductCollectionManager($this->getProductCollectionManager());

                $variation = $productProvider->createVariation($product);

                $manager->persist($variation);
            }

            $manager->flush();
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_create_variation_error');

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $this->addFlash('sonata_flash_success', 'flash_batch_create_variation_success');

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    /**
     * Return the Product Pool.
     *
     * @return \Sonata\Component\Product\Pool
     */
    protected function getProductPool()
    {
        return $this->get('sonata.product.pool');
    }

    /**
     * Return the Product manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getProductManager()
    {
        return $this->get('sonata.product.entity_manager');
    }

    /**
     * Return the ProductCategory manager.
     *
     * @return \Sonata\Component\Product\ProductCategoryManagerInterface
     */
    protected function getProductCategoryManager()
    {
        return $this->get('sonata.product_category.product');
    }

    /**
     * Return the ProductCollection manager.
     *
     * @return \Sonata\Component\Product\ProductCollectionManagerInterface
     */
    protected function getProductCollectionManager()
    {
        return $this->get('sonata.product_collection.product');
    }
}
