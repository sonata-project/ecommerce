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

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\CustomerElementInterface;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AddressController
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
     * Constructor
     *
     * @param AddressManagerInterface  $addressManager
     * @param FormFactoryInterface     $formFactory
     */
    public function __construct(AddressManagerInterface $addressManager, FormFactoryInterface $formFactory)
    {
        $this->addressManager  = $addressManager;
        $this->formFactory     = $formFactory;
    }

    /**
     * Retrieves a specific address
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
     * Updates an address
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
     * @param integer $id      An Address identifier
     * @param Request $request A Symfony request
     *
     * @return Address
     *
     * @throws NotFoundHttpException
     */
    public function putAddressAction($id, Request $request)
    {
        $address = $this->getAddress($id);

        $form = $this->formFactory->createNamed(null, 'sonata_customer_api_form_address', $address, array(
            'csrf_protection' => false
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

    /**
     * Deletes an address
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
     * @param integer $id An Address identifier
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
     * Retrieves address with id $id or throws an exception if it doesn't exist
     *
     * @param integer $id
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
}