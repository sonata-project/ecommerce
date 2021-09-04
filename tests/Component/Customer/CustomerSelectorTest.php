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

namespace Sonata\Component\Tests\Customer;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Customer\CustomerSelector;
use Sonata\IntlBundle\Locale\LocaleDetectorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CustomerSelectorTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testUserNotConnected(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('create')->willReturn($customer);

        $session = $this->createMock(SessionInterface::class);

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects(static::once())->method('isGranted')->willReturn(false);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects(static::once())->method('getLocale')->willReturn('en');

        $customerSelector = new CustomerSelector($customerManager, $session, $authorizationChecker, $tokenStorage, $localeDetector);

        $customer = $customerSelector->get();

        static::assertInstanceOf(CustomerInterface::class, $customer);
    }

    public function testInvalidUserType(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User must be an instance of Symfony\\Component\\Security\\Core\\User\\UserInterface');

        $customerManager = $this->createMock(CustomerManagerInterface::class);

        $session = $this->createMock(Session::class);

        $token = $this->createMock(TokenInterface::class);
        $token->expects(static::once())->method('getUser')->willReturn(new User());

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects(static::once())->method('isGranted')->willReturn(true);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects(static::once())->method('getToken')->willReturn($token);

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects(static::once())->method('getLocale')->willReturn('en');

        $customerSelector = new CustomerSelector($customerManager, $session, $authorizationChecker, $tokenStorage, $localeDetector);

        $customerSelector->get();
    }

    public function testExistingCustomer(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('findOneBy')->willReturn($customer);

        $session = $this->createMock(Session::class);

        $user = new ValidUser();

        $token = $this->createMock(TokenInterface::class);
        $token->expects(static::once())->method('getUser')->willReturn($user);

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects(static::once())->method('isGranted')->willReturn(true);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects(static::once())->method('getToken')->willReturn($token);

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects(static::once())->method('getLocale')->willReturn('en');

        $customerSelector = new CustomerSelector($customerManager, $session, $authorizationChecker, $tokenStorage, $localeDetector);

        $customer = $customerSelector->get();

        static::assertInstanceOf(CustomerInterface::class, $customer);
    }

    public function testNonExistingCustomerNonInSession(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('findOneBy')->willReturn(false);
        $customerManager->expects(static::once())->method('create')->willReturn($customer);

        $session = $this->createMock(Session::class);

        $user = new ValidUser();

        $token = $this->createMock(TokenInterface::class);
        $token->expects(static::once())->method('getUser')->willReturn($user);

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects(static::once())->method('isGranted')->willReturn(true);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects(static::once())->method('getToken')->willReturn($token);

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects(static::once())->method('getLocale')->willReturn('en');

        $customerSelector = new CustomerSelector($customerManager, $session, $authorizationChecker, $tokenStorage, $localeDetector);

        $customer = $customerSelector->get();

        static::assertInstanceOf(CustomerInterface::class, $customer);
    }

    public function testNonExistingCustomerInSession(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('findOneBy')->willReturn(false);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::exactly(2))->method('getCustomer')->willReturn($customer);

        $session = new Session(new MockArraySessionStorage());
        $session->set('sonata/basket/factory/customer/new', $basket);

        $user = new ValidUser();

        $token = $this->createMock(TokenInterface::class);
        $token->expects(static::once())->method('getUser')->willReturn($user);

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects(static::once())->method('isGranted')->willReturn(true);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects(static::once())->method('getToken')->willReturn($token);

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects(static::once())->method('getLocale')->willReturn('en');

        $customerSelector = new CustomerSelector($customerManager, $session, $authorizationChecker, $tokenStorage, $localeDetector);

        $customer = $customerSelector->get();

        static::assertInstanceOf(CustomerInterface::class, $customer);
    }
}
