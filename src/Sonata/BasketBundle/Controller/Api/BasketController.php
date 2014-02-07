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

use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketManagerInterface;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * Constructor
     *
     * @param BasketManagerInterface $basketManager
     */
    public function __construct(BasketManagerInterface $basketManager)
    {
        $this->basketManager = $basketManager;
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
     * @QueryParam(name="basketBy", array=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query baskets basket by clause (key is field, value is direction")
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
        $basketBy = $paramFetcher->get('basketBy');
        $filters = array_intersect_key($paramFetcher->all(), $supportedFilters);

        foreach ($filters as $key => $value) {
            if (null === $value) {
                unset($filters[$key]);
            }
        }

        return $this->basketManager->findBy($filters, $basketBy, $count, $page);
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