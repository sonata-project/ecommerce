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

namespace Sonata\Component\Customer;

use Sonata\Component\Basket\BasketInterface;
use Sonata\IntlBundle\Locale\LocaleDetectorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerSelector implements CustomerSelectorInterface
{
    /**
     * @var CustomerManagerInterface
     */
    private $customerManager;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param CustomerManagerInterface      $customerManager
     * @param SessionInterface              $session
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param LocaleDetectorInterface       $localeDetector
     */
    public function __construct(
        CustomerManagerInterface $customerManager,
        SessionInterface $session,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        LocaleDetectorInterface $localeDetector
    ) {
        $this->customerManager = $customerManager;
        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->locale = $localeDetector->getLocale();
    }

    /**
     * Get the customer.
     *
     * @throws \RuntimeException
     *
     * @return CustomerInterface
     */
    public function get()
    {
        $customer = null;
        $user = null;

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // user is authenticated
            $user = $this->tokenStorage->getToken()->getUser();

            if (!$user instanceof UserInterface) {
                throw new \RuntimeException('User must be an instance of Symfony\Component\Security\Core\User\UserInterface');
            }

            $customer = $this->customerManager->findOneBy([
                'user' => $user->getId(),
            ]);
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
     * @return BasketInterface
     */
    private function getBasket()
    {
        return $this->session->get('sonata/basket/factory/customer/new');
    }
}
