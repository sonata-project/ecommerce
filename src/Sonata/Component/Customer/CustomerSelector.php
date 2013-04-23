<?php

namespace Sonata\Component\Customer;

use Sonata\Component\Customer\CustomerManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContextInterface;
use FOS\UserBundle\Model\UserInterface;

class CustomerSelector implements CustomerSelectorInterface
{
    /**
     * @var \Sonata\Component\Customer\CustomerManagerInterface
     */
    protected $customerManager;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param \Sonata\Component\Customer\CustomerManagerInterface $customerManager
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct(CustomerManagerInterface $customerManager, Session $session, SecurityContextInterface $securityContext)
    {
        $this->customerManager = $customerManager;
        $this->session = $session;
        $this->securityContext = $securityContext;
    }

    /**
     * Get the customer
     *
     * @return \Sonata\Component\Customer\CustomerInterface
     */
    public function get()
    {
        if (true !== $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            // user is not authenticated
            return $this->customerManager->create();
        }

        $user = $this->securityContext->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('User must be an instance of FOS\UserBundle\Model\UserInterface');
        }

        $customer = $this->customerManager->findOneBy(array(
            'user' => $user->getId()
        ));

        if (!$customer) {
            $basket = $this->getBasket();

            if ($basket && $basket->getCustomer()) {
                $customer = $basket->getCustomer();
            }
        }

        if (!$customer) {
            $customer = $this->customerManager->create();
        }

        if (!$customer->getLocale()) {
            $customer->setLocale($this->session->get('_locale'));
        }

        return $customer;
    }

    /**
     * @return \Sonata\Component\Basket\BasketInterface
     */
    private function getBasket()
    {
        return $this->session->get('sonata/basket/factory/customer/new');
    }
}
