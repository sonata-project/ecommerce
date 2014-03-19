<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketManagerInterface;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BasketController
 *
 * @package Sonata\BasketBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketController
{
    /**
     * @var \Sonata\Component\Basket\BasketManagerInterface
     */
    protected $basketManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor
     *
     * @param BasketManagerInterface $basketManager A Sonata ecommerce basket manager
     * @param FormFactoryInterface   $formFactory   A Symfony form factory
     */
    public function __construct(BasketManagerInterface $basketManager, FormFactoryInterface $formFactory)
    {
        $this->basketManager = $basketManager;
        $this->formFactory   = $formFactory;
    }

    /**
     * Returns a paginated list of baskets.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"="sonata_api_read"}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for baskets list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of baskets by page")
     * @QueryParam(name="orderBy", array=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query baskets basket by clause (key is field, value is direction")
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return BasketInterface[]
     */
    public function getBasketsAction(ParamFetcherInterface $paramFetcher)
    {
        // No filters implemented as of right now
        $supportedFilters = array(
        );

        $page    = $paramFetcher->get('page') - 1;
        $count   = $paramFetcher->get('count');
        $orderBy = $paramFetcher->get('orderBy');
        $filters = array_intersect_key($paramFetcher->all(), $supportedFilters);

        foreach ($filters as $key => $value) {
            if (null === $value) {
                unset($filters[$key]);
            }
        }

        return $this->basketManager->findBy($filters, $orderBy, $count, $page);
    }

    /**
     * Retrieves a specific basket
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket id"}
     *  },
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when basket is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return BasketInterface
     */
    public function getBasketAction($id)
    {
        return $this->getBasket($id);
    }

    /**
     * Retrieves a specific basket's elements
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket id"}
     *  },
     *  output={"class"="Sonata\Component\Basket\BasketElementInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when basket is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return BasketElementInterface
     */
    public function getBasketBasketelementsAction($id)
    {
        return $this->getBasket($id)->getBasketElements();
    }

    /**
     * Adds a basket
     *
     * @ApiDoc(
     *  input={"class"="sonata_basket_api_form_basket", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\Component\Basket\BasketElementInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while basket creation",
     *      404="Returned when unable to find basket"
     *  }
     * )
     *
     * @param Request $request A Symfony request
     *
     * @return BasketInterface
     *
     * @throws NotFoundHttpException
     */
    public function postBasketAction(Request $request)
    {
        return $this->handleWriteBasket($request);
    }

    /**
     * Updates a basket
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket identifier"}
     *  },
     *  input={"class"="sonata_basket_api_form_basket", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\Component\Basket\BasketElementInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while basket update",
     *      404="Returned when unable to find basket"
     *  }
     * )
     *
     * @param integer $id      A Basket identifier
     * @param Request $request A Symfony request
     *
     * @return BasketInterface
     *
     * @throws NotFoundHttpException
     */
    public function putBasketAction($id, Request $request)
    {
        return $this->handleWriteBasket($request, $id);
    }

    /**
     * Deletes a basket
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when basket is successfully deleted",
     *      400="Returned when an error has occurred while basket deletion",
     *      404="Returned when unable to find basket"
     *  }
     * )
     *
     * @param integer $id A Basket identifier
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws NotFoundHttpException
     */
    public function deleteBasketAction($id)
    {
        $basket = $this->getBasket($id);

        try {
            $this->basketManager->delete($basket);
        } catch (\Exception $e) {
            return \FOS\RestBundle\View\View::create(array('error' => $e->getMessage()), 400);
        }

        return array('deleted' => true);
    }

    /**
     * Write a basket, this method is used by both POST and PUT action methods
     *
     * @param Request      $request Symfony request
     * @param integer|null $id      A basket identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
     */
    protected function handleWriteBasket($request, $id = null)
    {
        $basket = $id ? $this->getBasket($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_basket_api_form_basket', $basket, array(
            'csrf_protection' => false
        ));

        $form->bind($request);

        if ($form->isValid()) {
            $basket = $form->getData();
            $this->basketManager->save($basket);

            $view = \FOS\RestBundle\View\View::create($basket);
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(array('sonata_api_read'));
            $serializationContext->enableMaxDepthChecks();
            $view->setSerializationContext($serializationContext);

            return $view;
        }

        return $form;
    }

    /**
     * Retrieves basket with id $id or throws an exception if it doesn't exist
     *
     * @param $id
     *
     * @return BasketInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getBasket($id)
    {
        $basket = $this->basketManager->findOneBy(array('id' => $id));

        if (null === $basket) {
            throw new NotFoundHttpException(sprintf('Basket (%d) not found', $id));
        }

        return $basket;
    }
}