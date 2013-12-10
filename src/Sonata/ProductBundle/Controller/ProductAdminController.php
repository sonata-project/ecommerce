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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

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

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showVariationsAction()
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        if (!$product = $this->admin->getObject($id)) {
            throw new NotFoundHttpException('Product not found.');
        }

        return $this->render('SonataProductBundle:ProductAdmin:variations.html.twig', array(
            'product' => $product,
        ));
    }

    /**
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function createVariationAction()
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        if (!$product = $this->admin->getObject($id)) {
            throw new NotFoundHttpException('Product not found.');
        }

        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $form = $this->createFormBuilder(null, array())
            ->add('number', 'integer', array(
                'required'    => true,
                'label'       => $this->getTranslator()->trans('variations_number', array(), 'SonataProductBundle'),
                'attr'        => array('min' => 1),
                'constraints' => array(
                    new NotBlank(),
                    new Range(array('min' => 1)),
                ),
            ))
            ->getForm();

        if ($this->getRequest()->isMethod('POST')) {
            $form->submit($this->getRequest());

            if ($form->isValid()) {
                $number = $form->get('number')->getData();

                $manager         = $this->getProductManager();
                $productProvider = $this->getProductPool()->getProvider($product);

                for ($i = 1; $i <= $number; $i++) {
                    try {
                        $variation = $productProvider->createVariation($product);

                        $manager->persist($variation);
                    } catch (\Exception $e) {
                        $this->addFlash('sonata_flash_error', 'flash_create_variation_error');

                        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
                    }
                }

                $manager->flush();

                $this->addFlash('sonata_flash_success', 'flash_create_variation_success');

                return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
            }
        }

        return $this->render('SonataProductBundle:ProductAdmin:create_variation.html.twig', array(
            'object' => $product,
            'form'   => $form->createView(),
            'action' => 'edit',
        ));
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
        return $this->get('sonata.product.entity_manager');
    }
}
