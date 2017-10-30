<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Controller;

use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CustomerController extends Controller
{
    /**
     * Lists customer's addresses.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addressesAction()
    {
        $customer = $this->getCustomer();

        $typeCodes = BaseAddress::getTypesList();

        // This allows to specify the display order
        $addresses = [
            $typeCodes[AddressInterface::TYPE_DELIVERY] => [],
            $typeCodes[AddressInterface::TYPE_BILLING] => [],
            $typeCodes[AddressInterface::TYPE_CONTACT] => [],
        ];

        if (null === $customer) {
            // Customer not yet created, the user didn't order yet
            $customer = $this->getCustomerManager()->create();
            $customer->setUser($this->getUser());
            $this->getCustomerManager()->save($customer);
        } else {
            $custAddresses = $this->getAddressManager()->findBy(['customer' => $customer]);

            foreach ($custAddresses as $address) {
                $addresses[$address->getTypeCode()][] = $address;
            }
        }

        // Set redirection URL to be to the list of addresses
        $this->get('session')->set('sonata_address_redirect', $this->generateUrl('sonata_customer_addresses'));

        return $this->render('SonataCustomerBundle:Addresses:list.html.twig', [
                'addresses' => $addresses,
                'customer' => $customer,
                'breadcrumb_context' => 'customer_address',
            ]);
    }

    /**
     * Adds an address to current customer.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAddressAction()
    {
        return $this->updateAddress();
    }

    /**
     * Controller action to edit address $id.
     *
     * @param $id
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAddressAction($id)
    {
        return $this->updateAddress($id);
    }

    /**
     * Deletes address $id.
     *
     * @param $id The address to delete
     *
     * @return RedirectResponse
     */
    public function deleteAddressAction($id)
    {
        if ('POST' !== $this->getRequest()->getMethod()) {
            throw new MethodNotAllowedHttpException(['POST']);
        }

        $address = $this->getAddressManager()->findOneBy(['id' => $id]);

        $this->checkAddress($address);

        $this->getAddressManager()->delete($address);

        $this->get('session')->getFlashBag()->add('sonata_customer_success', 'customer_address_delete');

        return new RedirectResponse($this->generateUrl('sonata_customer_addresses'));
    }

    /**
     * Sets address $id to current.
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function setCurrentAddressAction($id)
    {
        $address = $this->getAddressManager()->findOneBy(['id' => $id]);
        $this->checkAddress($address);

        $this->getAddressManager()->setCurrent($address);

        return new RedirectResponse($this->generateUrl('sonata_customer_addresses'));
    }

    /**
     * Updates or create an address.
     *
     * @param int $id Address id
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function updateAddress($id = null)
    {
        $customer = $this->getCustomer();

        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $addressFormType = 'Sonata\CustomerBundle\Form\Type\AddressType';
        } else {
            $addressFormType = 'sonata_customer_address';
        }
        // Show address creation/edition form
        if (null === $id) {
            $form = $this->createForm($addressFormType);
        } else {
            $address = $this->getAddressManager()->findOneBy(['id' => $id]);
            $this->checkAddress($address);

            $form = $this->createForm($addressFormType, $address, [
                'context' => $this->getRequest()->query->get('context'),
            ]);
        }

        $template = 'SonataCustomerBundle:Addresses:new.html.twig';

        if ('POST' == $this->get('request')->getMethod()) {
            $form->handleRequest($this->get('request'));

            if ($form->isValid()) {
                $address = $form->getData();

                $customer->addAddress($address);

                $this->getCustomerManager()->save($customer);

                $this->get('session')->getFlashBag()->add('sonata_customer_success', $id ? 'address_edit_success' : 'address_add_success');

                $url = $this->get('session')->get('sonata_address_redirect', $this->generateUrl('sonata_customer_addresses'));

                return new RedirectResponse($url);
            }
        }

        return $this->render($template, [
            'form' => $form->createView(),
            'breadcrumb_context' => 'customer_address',
        ]);
    }

    /**
     * Checks if $address is valid.
     *
     * @param AddressInterface $address
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function checkAddress(AddressInterface $address = null)
    {
        if (null === $address
            || $address->getCustomer()->getId() !== $this->getCustomer()->getId()) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @return \Sonata\Component\Customer\CustomerInterface
     */
    protected function getCustomer()
    {
        $user = $this->getUser();

        return $this->getCustomerManager()->findOneBy(['user' => $user]);
    }

    /**
     * @return AddressManagerInterface
     */
    protected function getAddressManager()
    {
        return $this->get('sonata.address.manager');
    }

    /**
     * @return CustomerManagerInterface
     */
    protected function getCustomerManager()
    {
        return $this->get('sonata.customer.manager');
    }
}
