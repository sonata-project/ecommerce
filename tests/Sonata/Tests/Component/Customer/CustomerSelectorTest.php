<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Customer;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Sonata\Component\Customer\CustomerSelector;
use Sonata\Component\Basket\Basket;
use FOS\UserBundle\Model\User as BaseUser;

class ValidUser extends BaseUser
{
}

class User
{
    public function getId()
    {
        return 1;
    }
}


class CustomerSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testUserNotConnected()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('create')->will($this->returnValue($customer));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(false));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext);

        $customer = $customerSelector->get();

        $this->assertInstanceOf('Sonata\Component\Customer\CustomerInterface', $customer);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage User must be an instance of FOS\UserBundle\Model\UserInterface
     */
    public function testInvalidUserType()
    {
        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue(new User()));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext);

        $customerSelector->get();
    }

    public function testExistingCustomer()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue($customer));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $user = new ValidUser();

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext);

        $customer = $customerSelector->get();

        $this->assertInstanceOf('Sonata\Component\Customer\CustomerInterface', $customer);
    }

    public function testNonExistingCustomerNonInSession()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue(false));
        $customerManager->expects($this->once())->method('create')->will($this->returnValue($customer));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $user = new ValidUser();

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext);

        $customer = $customerSelector->get();

        $this->assertInstanceOf('Sonata\Component\Customer\CustomerInterface', $customer);
    }

    public function testNonExistingCustomerInSession()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue(false));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->exactly(2))->method('getCustomer')->will($this->returnValue($customer));

        $session = new Session();
        $session->set('sonata/basket/factory/customer/new', $basket);

        $user = new ValidUser();

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->once())->method('isGranted')->will($this->returnValue(true));
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $customerSelector = new CustomerSelector($customerManager, $session, $securityContext);

        $customer = $customerSelector->get();

        $this->assertInstanceOf('Sonata\Component\Customer\CustomerInterface', $customer);
    }
}