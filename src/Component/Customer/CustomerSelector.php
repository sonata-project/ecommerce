<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Customer;

use FOS\UserBundle\Model\UserInterface;
use Sonata\IntlBundle\Locale\LocaleDetectorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param CustomerManagerInterface $customerManager
     * @param SessionInterface         $session
     * @param SecurityContextInterface $securityContext
     * @param LocaleDetectorInterface  $localeDetector
     */
    public function __construct(CustomerManagerInterface $customerManager, SessionInterface $session, TokenStorageInterface $tokenStorage, LocaleDetectorInterface $localeDetector)
    {
        $this->customerManager = $customerManager;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->locale = $localeDetector->getLocale();
    }

    /**
     * Get the customer.
     *
     * @throws \RuntimeException
     *
     * @return \Sonata\Component\Customer\CustomerInterface
     */
    public function get()
    {
        $customer = null;
        $user = null;

        if (true === $this->tokenStorage->getToken()->getUser()->isGranted('IS_AUTHENTICATED_FULLY')) {
            // user is authenticated
            $user = $this->tokenStorage->getToken()->getUser();

            if (!$user instanceof UserInterface) {
                throw new \RuntimeException('User must be an instance of FOS\UserBundle\Model\UserInterface');
            }

            $customer = $this->customerManager->findOneBy(array(
                'user' => $user->getId(),
            ));
        }

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
            $customer->setLocale($this->locale);
        }

        if ($user && $customer) {
            $customer->setUser($user);
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
