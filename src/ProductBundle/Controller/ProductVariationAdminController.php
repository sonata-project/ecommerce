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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\Component\Product\Pool;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ProductVariationAdminController extends Controller
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return Response
     */
    public function createAction(Request $request = null)
    {
        if (!$this->admin->getParent()) {
            throw new \RuntimeException('The admin cannot be call directly, it must be embedded');
        }

        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $form = $this->createFormBuilder(null, [])
            ->add('number', IntegerType::class, [
                'required' => true,
                'label' => $this->getTranslator()->trans('variations_number', [], 'SonataProductBundle'),
                'attr' => ['min' => 1, 'max' => 10],
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1, 'max' => 10]),
                ],
            ])
            ->getForm();

        // product is the main product object, used to create a set of variation
        $product = $this->admin->getParent()->getSubject();

        if ($request->isMethod('POST')) {
            $form->submit($request);

            if ($form->isValid()) {
                $number = $form->get('number')->getData();

                $manager = $this->getProductManager();
                $productProvider = $this->getProductPool()->getProvider($product);

                for ($i = 1; $i <= $number; ++$i) {
                    try {
                        $variation = $productProvider->createVariation($product);

                        $manager->persist($variation);
                    } catch (\Exception $e) {
                        $this->addFlash('sonata_flash_error', 'flash_create_variation_error');

                        return new RedirectResponse($this->admin->generateUrl('create'));
                    }
                }

                $manager->flush();

                $this->addFlash('sonata_flash_success', $this->getTranslator()->trans('flash_create_variation_success', [], 'SonataProductBundle'));

                return new RedirectResponse($this->admin->generateUrl('list'));
            }
        }

        return $this->render('SonataProductBundle:ProductAdmin:create_variation.html.twig', [
            'object' => $product,
            'form' => $form->createView(),
            'action' => 'edit',
        ]);
    }

    /**
     * Return the Product Pool.
     *
     * @return Pool
     */
    protected function getProductPool()
    {
        return $this->get('sonata.product.pool');
    }

    /**
     * Return the Product Pool.
     *
     * @return Translator
     */
    protected function getTranslator()
    {
        return $this->get('translator');
    }

    /**
     * Return the Product manager.
     *
     * @return EntityManager
     */
    protected function getProductManager()
    {
        return $this->get('doctrine')->getManagerForClass($this->admin->getClass());
    }
}
