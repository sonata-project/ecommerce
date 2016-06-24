<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AddressController.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AddressController
{
    /**
     * @var AddressManagerInterface
     */
    protected $addressManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor.
     *
     * @param AddressManagerInterface $addressManager
     * @param FormFactoryInterface    $formFactory
     */
    public function __construct(AddressManagerInterface $addressManager, FormFactoryInterface $formFactory)
    {
        $this->addressManager = $addressManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a paginated list of addresses.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"="sonata_api_read"}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for addresses list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of addresses by page")
     * @QueryParam(name="orderBy", array=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query orders addresses by clause (key is field, value is direction")
     * @QueryParam(name="customer", requirements="\d+", nullable=true, strict=true, description="Filter on customer id")
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Sonata\DatagridBundle\Pager\PagerInterface
     */
    public function getAddressesAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = array(
            'customer' => '',
        );

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
            $sort = array();
        } elseif (!is_array($sort)) {
            $sort = array($sort => 'asc');
        }

        return $this->addressManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific address.
     *
     * @ApiDoc(
     *  resource=true,
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="address id"}
     *  },
     *  output={"class"="Sonata\Component\Customer\AddressInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when address is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return AddressInterface
     */
    public function getAddressAction($id)
    {
        return $this->getAddress($id);
    }

    /**
     * Adds an address.
     *
     * @ApiDoc(
     *  input={"class"="sonata_customer_api_form_address", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\CustomerBundle\Model\Address", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while address creation",
     *  }
     * )
     *
     * @param Request $request A Symfony request
     *
     * @return AddressInterface
     */
    public function postAddressAction(Request $request)
    {
        return $this->handleWriteAddress($request);
    }

    /**
     * Updates an address.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="address id"}
     *  },
     *  input={"class"="sonata_customer_api_form_address", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\CustomerBundle\Model\Address", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while address creation",
     *  }
     * )
     *
     * @param int     $id      An Address identifier
     * @param Request $request A Symfony request
     *
     * @return Address
     *
     * @throws NotFoundHttpException
     */
    public function putAddressAction($id, Request $request)
    {
        return $this->handleWriteAddress($request, $id);
    }

    /**
     * Deletes an address.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="address identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when customer is successfully deleted",
     *      400="Returned when an error has occurred while address deletion",
     *      404="Returned when unable to find address"
     *  }
     * )
     *
     * @param int $id An Address identifier
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws NotFoundHttpException
     */
    public function deleteAddressAction($id)
    {
        $address = $this->getAddress($id);

        try {
            $this->addressManager->delete($address);
        } catch (\Exception $e) {
            return \FOS\RestBundle\View\View::create(array('error' => $e->getMessage()), 400);
        }

        return array('deleted' => true);
    }

    /**
     * Retrieves address with id $id or throws an exception if it doesn't exist.
     *
     * @param int $id
     *
     * @return AddressInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getAddress($id)
    {
        $address = $this->addressManager->findOneBy(array('id' => $id));

        if (null === $address) {
            throw new NotFoundHttpException(sprintf('Address (%d) not found', $id));
        }

        return $address;
    }

    /**
     * Write an address, this method is used by both POST and PUT action methods.
     *
     * @param Request  $request Symfony request
     * @param int|null $id      An Address identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
     */
    protected function handleWriteAddress($request, $id = null)
    {
        $address = $id ? $this->getAddress($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_customer_api_form_address', $address, array(
            'csrf_protection' => false,
        ));

        $form->bind($request);

        if ($form->isValid()) {
            $address = $form->getData();
            $this->addressManager->save($address);

            $view = \FOS\RestBundle\View\View::create($address);
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(array('sonata_api_read'));
            $serializationContext->enableMaxDepthChecks();
            $view->setSerializationContext($serializationContext);

            return $view;
        }

        return $form;
    }
}
