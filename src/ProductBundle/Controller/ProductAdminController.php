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

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductAdminController extends Controller
{
    /**
     * @param Request|null $request
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $parameters = $this->admin->getPersistentParameters();

        if (!$parameters['provider']) {
            return $this->render('SonataProductBundle:ProductAdmin:select_provider.html.twig', [
                'providers' => $this->get('sonata.product.pool')->getProducts(),
                'base_template' => $this->getBaseTemplate(),
                'admin' => $this->admin,
                'action' => 'create',
            ]);
        }

        return parent::createAction($request);
    }

    /**
     * @param Request|null $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showVariationsAction(Request $request = null)
    {
        $id = $request->get($this->admin->getIdParameter());

        if (!$product = $this->admin->getObject($id)) {
            throw new NotFoundHttpException('Product not found.');
        }

        return $this->render('SonataProductBundle:ProductAdmin:variations.html.twig', [
            'product' => $product,
        ]);
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
     * Return the Product Pool.
     *
     * @return \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected function getTranslator()
    {
        return $this->get('translator');
    }

    /**
     * Return the Product manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getProductManager()
    {
        return $this->get('doctrine')->getManagerForClass($this->admin->getClass());
    }
}
