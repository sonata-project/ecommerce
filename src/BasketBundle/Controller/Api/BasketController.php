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

namespace Sonata\BasketBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View as FOSRestView;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\Component\Basket\BasketBuilderInterface;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketManagerInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketController
{
    /**
     * @var BasketManagerInterface
     */
    protected $basketManager;

    /**
     * @var BasketElementManagerInterface
     */
    protected $basketElementManager;

    /**
     * @var ProductManagerInterface
     */
    protected $productManager;

    /**
     * @var BasketBuilderInterface
     */
    protected $basketBuilder;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param BasketManagerInterface        $basketManager        A Sonata ecommerce basket manager
     * @param BasketElementManagerInterface $basketElementManager A Sonata ecommerce basket element manager
     * @param ProductManagerInterface       $productManager       A Sonata ecommerce product manager
     * @param BasketBuilderInterface        $basketBuilder        A Sonata ecommerce basket builder
     * @param FormFactoryInterface          $formFactory          A Symfony form factory
     */
    public function __construct(BasketManagerInterface $basketManager, BasketElementManagerInterface $basketElementManager, ProductManagerInterface $productManager, BasketBuilderInterface $basketBuilder, FormFactoryInterface $formFactory)
    {
        $this->basketManager = $basketManager;
        $this->basketElementManager = $basketElementManager;
        $this->productManager = $productManager;
        $this->basketBuilder = $basketBuilder;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a paginated list of baskets.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for baskets list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of baskets by page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Sort specification for the resultset (key is field, value is direction")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return \Sonata\DatagridBundle\Pager\PagerInterface
     */
    public function getBasketsAction(ParamFetcherInterface $paramFetcher)
    {
        // No filters implemented as of right now
        $supportedCriteria = [
        ];

        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('count');
        $sort = $paramFetcher->get('orderBy');
        $criteria = array_intersect_key($paramFetcher->all(), $supportedCriteria);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (!$sort) {
            $sort = [];
        } elseif (!\is_array($sort)) {
            $sort = [$sort => 'asc'];
        }

        return $this->basketManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific basket.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket id"}
     *  },
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when basket is not found"
     *  }
     * )
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
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
     * Retrieves a specific basket's elements.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket id"}
     *  },
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when basket is not found"
     *  }
     * )
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return BasketElementInterface[]
     */
    public function getBasketBasketelementsAction($id)
    {
        return $this->getBasket($id)->getBasketElements();
    }

    /**
     * Adds a basket.
     *
     * @ApiDoc(
     *  input={"class"="sonata_basket_api_form_basket", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while basket creation",
     *      404="Returned when unable to find basket"
     *  }
     * )
     *
     * @param Request $request A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function postBasketAction(Request $request)
    {
        return $this->handleWriteBasket($request);
    }

    /**
     * Updates a basket.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket identifier"}
     *  },
     *  input={"class"="sonata_basket_api_form_basket", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while basket update",
     *      404="Returned when unable to find basket"
     *  }
     * )
     *
     * @param int     $id      A Basket identifier
     * @param Request $request A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function putBasketAction($id, Request $request)
    {
        return $this->handleWriteBasket($request, $id);
    }

    /**
     * Deletes a basket.
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
     * @param int $id A Basket identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteBasketAction($id)
    {
        $basket = $this->getBasket($id);

        try {
            $this->basketManager->delete($basket);
        } catch (\Exception $e) {
            return FOSRestView::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Adds a basket element to a basket.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="basket identifier"},
     *  },
     *  input={"class"="sonata_basket_api_form_basket_element", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while basket/element attachment",
     *      404="Returned when basket or product not found",
     *  }
     * )
     *
     * @param int     $id      A basket identifier
     * @param Request $request A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function postBasketBasketelementsAction($id, Request $request)
    {
        return $this->handleWriteBasketElement($id, $request);
    }

    /**
     * Updates a basket element of a basket.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="basketId", "dataType"="integer", "requirement"="\d+", "description"="basket identifier"},
     *      {"name"="elementId", "dataType"="integer", "requirement"="\d+", "description"="element identifier"},
     *  },
     *  input={"class"="sonata_basket_api_form_basket_element", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\Component\Basket\BasketInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while basket/element attachment",
     *      404="Returned when basket or basket element not found",
     *  }
     * )
     *
     * @param int     $basketId  A basket identifier
     * @param int     $elementId A basket element identifier
     * @param Request $request   A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function putBasketBasketelementsAction($basketId, $elementId, Request $request)
    {
        return $this->handleWriteBasketElement($basketId, $request, $elementId);
    }

    /**
     * Deletes a basket element from a basket.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="basketId", "dataType"="integer", "requirement"="\d+", "description"="basket identifier"},
     *      {"name"="elementId", "dataType"="integer", "requirement"="\d+", "description"="element identifier"},
     *  },
     *  statusCodes={
     *      200="Returned when basket is successfully deleted",
     *      400="Returned when an error has occurred while basket element deletion",
     *      404="Returned when unable to find basket or basket element"
     *  }
     * )
     *
     * @param int $basketId  A basket identifier
     * @param int $elementId A basket element identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteBasketBasketelementsAction($basketId, $elementId)
    {
        $basket = $this->getBasket($basketId);
        $this->basketBuilder->build($basket);

        $elements = $basket->getBasketElements();

        foreach ($elements as $key => $basketElement) {
            if ($basketElement->getId() === $elementId) {
                unset($elements[$key]);
                $this->basketBuilder->build($basket);
            }
        }

        try {
            $basket->setBasketElements($elements);
            $this->basketManager->save($basket);
        } catch (\Exception $e) {
            return FOSRestView::create(['error' => $e->getMessage()], 400);
        }

        $context = new Context();
        $context->setGroups(['sonata_api_read']);

        // simplify when dropping FOSRest < 2.1
        if (method_exists($context, 'enableMaxDepth')) {
            $context->enableMaxDepth();
        } else {
            $context->setMaxDepth(10);
        }

        $view = FOSRestView::create($basket);
        $view->setContext($context);

        return $view;
    }

    /**
     * Write a basket, this method is used by both POST and PUT action methods.
     *
     * @param Request  $request Symfony request
     * @param int|null $id      A basket identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
     */
    protected function handleWriteBasket($request, $id = null)
    {
        $basket = $id ? $this->getBasket($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_basket_api_form_basket', $basket, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basket = $form->getData();
            if ($basket->getCustomerId()) {
                $this->checkExistingCustomerBasket($basket->getCustomerId());
            }
            $this->basketManager->save($basket);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            // simplify when dropping FOSRest < 2.1
            if (method_exists($context, 'enableMaxDepth')) {
                $context->enableMaxDepth();
            } else {
                $context->setMaxDepth(10);
            }

            $view = FOSRestView::create($basket);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }

    /**
     * Write a basket element, this method is used by both POST and PUT action methods.
     *
     * @param int     $basketId  A Sonata ecommerce basket identifier
     * @param Request $request   A Symfony Request service
     * @param int     $elementId A Sonata ecommerce basket element identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
     */
    protected function handleWriteBasketElement($basketId, $request, $elementId = null)
    {
        $basket = $this->getBasket($basketId);
        $this->basketBuilder->build($basket);

        $productId = $request->request->get('productId');
        $product = $this->getProduct($productId);

        $productPool = $basket->getProductPool();

        $code = $productPool->getProductCode($product);
        $productDefinition = $productPool->getProduct($code);

        $basketElement = $elementId ? $this->getBasketElement($elementId) : $this->basketElementManager->create();
        $basketElement->setProductDefinition($productDefinition);

        $form = $this->formFactory->createNamed(null, 'sonata_basket_api_form_basket_element', $basketElement, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basketElement = $form->getData();

            if ($elementId) {
                $this->basketElementManager->save($basketElement);
            } else {
                $basket->addBasketElement($basketElement);
                $this->basketManager->save($basket);
            }

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            // simplify when dropping FOSRest < 2.1
            if (method_exists($context, 'enableMaxDepth')) {
                $context->enableMaxDepth();
            } else {
                $context->setMaxDepth(10);
            }

            $view = FOSRestView::create($basket);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }

    /**
     * Retrieves basket with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return BasketInterface
     */
    protected function getBasket($id)
    {
        $basket = $this->basketManager->findOneBy(['id' => $id]);

        if (null === $basket) {
            throw new NotFoundHttpException(sprintf('Basket (%d) not found', $id));
        }

        return $basket;
    }

    /**
     * Retrieves basket element with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return BasketElementInterface
     */
    protected function getBasketElement($id)
    {
        $basketElement = $this->basketElementManager->findOneBy(['id' => $id]);

        if (null === $basketElement) {
            throw new NotFoundHttpException(sprintf('Basket element (%d) not found', $id));
        }

        return $basketElement;
    }

    /**
     * Retrieves product with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return ProductInterface
     */
    protected function getProduct($id)
    {
        $product = $this->productManager->findOneBy(['id' => $id]);

        if (null === $product) {
            throw new NotFoundHttpException(sprintf('Product (%d) not found', $id));
        }

        return $product;
    }

    /**
     * Throws an exception if it already exists a basket with a specific customer id.
     *
     * @param $customerId
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function checkExistingCustomerBasket($customerId): void
    {
        $basket = $this->basketManager->findOneBy(['customer' => $customerId]);
        if ($basket instanceof BasketInterface) {
            throw new HttpException(400, sprintf('Customer (%d) already has a basket', $customerId));
        }
    }
}
