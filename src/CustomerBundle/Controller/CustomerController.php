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

namespace Sonata\CustomerBundle\Controller;

use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\CustomerBundle\Form\Type\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
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
     * @return Response
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
     * @return RedirectResponse|Response
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
    public function deleteAddressAction(Request $request, $id)
    {
        if ('POST' !== $request->getMethod()) {
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
     * @return RedirectResponse|Response
     */
    protected function updateAddress($id = null)
    {
        $request = $this->getCurrentRequest();
        $customer = $this->getCustomer();

        // Show address creation/edition form
        if (null === $id) {
            $form = $this->createForm(AddressType::class);
        } else {
            $address = $this->getAddressManager()->findOneBy(['id' => $id]);
            $this->checkAddress($address);

            $form = $this->createForm(AddressType::class, $address, [
                'context' => $request->query->get('context'),
            ]);
        }

        $template = 'SonataCustomerBundle:Addresses:new.html.twig';

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

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
     * @throws NotFoundHttpException
     */
    protected function checkAddress(AddressInterface $address = null): void
    {
        if (null === $address
            || $address->getCustomer()->getId() !== $this->getCustomer()->getId()) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @return CustomerInterface
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
