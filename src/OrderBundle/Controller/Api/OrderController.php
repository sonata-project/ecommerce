<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\OrderBundle\Controller\Api;

use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Class OrderController
 *
 * @package Sonata\OrderBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderController
{
    /**
     * @var \Sonata\Component\Order\OrderManagerInterface
     */
    protected $orderManager;

    /**
     * Constructor
     *
     * @param OrderManagerInterface $orderManager
     */
    public function __construct(OrderManagerInterface $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    /**
     * Returns a paginated list of orders.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"="sonata_api_read"}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for orders list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of orders by page")
     * @QueryParam(name="orderBy", array=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query orders order by clause (key is field, value is direction")
     * @QueryParam(name="status", requirements="\d+", nullable=true, strict=true, description="Filter on order statuses")
     * @QueryParam(name="customer", requirements="\d+", nullable=true, strict=true, description="Filter on customer id")
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Sonata\DatagridBundle\Pager\PagerInterface
     */
    public function getOrdersAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = array(
            'status' => "",
            'customer' => "",
        );

        $page     = $paramFetcher->get('page');
        $limit    = $paramFetcher->get('count');
        $sort     = $paramFetcher->get('orderBy');
        $criteria = array_intersect_key($paramFetcher->all(), $supportedCriteria);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (!$sort) {
            $sort = array();
        } elseif (!is_array($sort)) {
            $sort = array($sort => 'asc');
        }

        return $this->orderManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific order
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="order id"}
     *  },
     *  output={"class"="Sonata\Component\Order\OrderInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when order is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return OrderInterface
     */
    public function getOrderAction($id)
    {
        return $this->getOrder($id);
    }

    /**
     * Retrieves a specific order's elements
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="order id"}
     *  },
     *  output={"class"="Sonata\Component\Order\OrderElementInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when order is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return OrderElementInterface
     */
    public function getOrderOrderelementsAction($id)
    {
        return $this->getOrder($id)->getOrderElements();
    }

    /**
     * Retrieves order with id $id or throws an exception if it doesn't exist
     *
     * @param $id
     *
     * @return OrderInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getOrder($id)
    {
        $order = $this->orderManager->findOneBy(array('id' => $id));

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order (%d) not found', $id));
        }

        return $order;
    }
}
