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

namespace Sonata\Component\Tests\Basket;

use Sonata\Component\Customer\AddressInterface;

class Address implements AddressInterface
{
    public function getPhone()
    {
        return '+33472123123';
    }

    public function getCountryCode()
    {
        return 'FRA';
    }

    public function getCity()
    {
        return 'PARIS';
    }

    public function getPostcode()
    {
        return '75002';
    }

    public function getAddress3()
    {
        return 'Av des champs elysées';
    }

    public function getAddress2()
    {
        return '';
    }

    public function getAddress1()
    {
        return '';
    }

    public function getName()
    {
        return 'home';
    }

    public function getId(): void
    {
        // TODO: Implement getId() method.
    }
}
