<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\CustomerBundle\Controller\Api;

use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\CustomerElementInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Class CustomerController
 *
 * @package Sonata\CustomerBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CustomerController
{
    /**
     * @var \Sonata\Component\Customer\CustomerManagerInterface
     */
    protected $customerManager;

    /**
     * @var \Sonata\Component\Order\OrderManagerInterface
     */
    protected $orderManager;

    /**
     * Constructor
     *
     * @param CustomerManagerInterface $customerManager
     * @param OrderManagerInterface    $orderManager
     */
    public function __construct(CustomerManagerInterface $customerManager, OrderManagerInterface $orderManager)
    {
        $this->customerManager = $customerManager;
        $this->orderManager    = $orderManager;
    }

    /**
     * Returns a paginated list of customers.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\Component\Customer\CustomerInterface", "groups"="sonata_api_read"}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for customers list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of customers by page")
     * @QueryParam(name="orderBy", array=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query customers order by clause (key is field, value is direction")
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return CustomerInterface[]
     */
    public function getCustomersAction(ParamFetcherInterface $paramFetcher)
    {
        // No supported filters as of now
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

        return $this->customerManager->findBy($filters, $orderBy, $count, $page);
    }

    /**
     * Retrieves a specific customer
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="customer id"}
     *  },
     *  output={"class"="Sonata\Component\Customer\CustomerInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when customer is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return CustomerInterface
     */
    public function getCustomerAction($id)
    {
        return $this->getCustomer($id);
    }

    /**
     * Retrieves a specific customer's orders
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="customer id"}
     *  },
     *  output={"class"="Sonata\Component\Order\OrderInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when customer is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return OrderInterface
     */
    public function getCustomerOrdersAction($id)
    {
        $customer = $this->getCustomer($id);
        return $this->orderManager->findBy(array('customer' => $customer));
    }

    /**
     * Retrieves a specific customer's addresses
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="customer id"}
     *  },
     *  output={"class"="Sonata\Component\Customer\AddressInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when customer is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return AddressInterface
     */
    public function getCustomerAddressesAction($id)
    {
        return $this->getCustomer($id)->getAddresses();
    }

    /**
     * Retrieves customer with id $id or throws an exception if it doesn't exist
     *
     * @param $id
     *
     * @return CustomerInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getCustomer($id)
    {
        $customer = $this->customerManager->findOneBy(array('id' => $id));

        if (null === $customer) {
            throw new NotFoundHttpException(sprintf('Customer (%d) not found', $id));
        }

        return $customer;
    }
}