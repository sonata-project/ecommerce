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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CustomerSelectorTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testUserNotConnected()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects($this->once())->method('create')->will($this->returnValue($customer));

        $session = $this->createMock(SessionInterface::class);

        $securityContext = $this->createMock(SecurityContextInterface::class);
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects($this->once())->method('getLocale')->will($this->returnValue('en'));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext, $localeDetector);

        $customer = $customerSelector->get();

        $this->assertInstanceOf(CustomerInterface::class, $customer);
    }

    public function testInvalidUserType()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User must be an instance of Symfony\\Component\\Security\\Core\\User\\UserInterface');

        $customerManager = $this->createMock(CustomerManagerInterface::class);

        $session = $this->createMock(Session::class);

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())->method('getUser')->will($this->returnValue(new User()));

        $securityContext = $this->createMock(SecurityContextInterface::class);
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects($this->once())->method('getLocale')->will($this->returnValue('en'));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext, $localeDetector);

        $customerSelector->get();
    }

    public function testExistingCustomer()
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue($customer));

        $session = $this->createMock(Session::class);

        $user = new ValidUser();

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $securityContext = $this->createMock(SecurityContextInterface::class);
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects($this->once())->method('getLocale')->will($this->returnValue('en'));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext, $localeDetector);

        $customer = $customerSelector->get();

        $this->assertInstanceOf(CustomerInterface::class, $customer);
    }

    public function testNonExistingCustomerNonInSession()
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue(false));
        $customerManager->expects($this->once())->method('create')->will($this->returnValue($customer));

        $session = $this->createMock(Session::class);

        $user = new ValidUser();

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $securityContext = $this->createMock(SecurityContextInterface::class);
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects($this->once())->method('getLocale')->will($this->returnValue('en'));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext, $localeDetector);

        $customer = $customerSelector->get();

        $this->assertInstanceOf(CustomerInterface::class, $customer);
    }

    public function testNonExistingCustomerInSession()
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue(false));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->exactly(2))->method('getCustomer')->will($this->returnValue($customer));

        $session = new Session(new MockArraySessionStorage());
        $session->set('sonata/basket/factory/customer/new', $basket);

        $user = new ValidUser();

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $securityContext = $this->createMock(SecurityContextInterface::class);
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $localeDetector = $this->createMock(LocaleDetectorInterface::class);
        $localeDetector->expects($this->once())->method('getLocale')->will($this->returnValue('en'));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext, $localeDetector);

        $customer = $customerSelector->get();

        $this->assertInstanceOf(CustomerInterface::class, $customer);
    }
}
