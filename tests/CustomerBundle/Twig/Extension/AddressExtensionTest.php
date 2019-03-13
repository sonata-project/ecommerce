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

namespace Sonata\CustomerBundle\Tests\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\ServiceDeliverySelectorInterface;
use Sonata\CustomerBundle\Twig\Extension\AddressExtension;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressExtensionTest extends TestCase
{
    public function testRenderAddress(): void
    {
        $environment = $this->createMock(\Twig_Environment::class);
        $deliverySelector = $this->createMock(ServiceDeliverySelectorInterface::class);

        $environment->expects($this->exactly(4))->method('render');

        $address = $this->createMock(AddressInterface::class);
        $address->expects($this->exactly(3))->method('getFullAddressHtml');

        $extension = new AddressExtension($deliverySelector);

        $extension->renderAddress($environment, $address, false);
        $address->expects($this->exactly(2))->method('getName');
        $extension->renderAddress($environment, $address);
        $address->expects($this->once())->method('getId');
        $extension->renderAddress($environment, $address, true, true);

        $address = [
            'firstname' => '',
            'lastname' => '',
            'address1' => '',
            'postcode' => '',
            'city' => '',
            'country_code' => '',
        ];

        $extension->renderAddress($environment, $address);
    }

    public function testRenderAddressInvalidParameter(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sonata_address_render needs an AddressInterface instance or an array with keys (firstname, lastname, address1, postcode, city, country_code)');

        $environment = $this->createMock('Twig_Environment');
        $deliverySelector = $this->createMock(ServiceDeliverySelectorInterface::class);

        $address = [];

        $extension = new AddressExtension($deliverySelector);
        $extension->renderAddress($environment, $address);
    }

    public function testRenderAddressMissingId(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sonata_address_render needs \'id\' key to be set to render the edit button');

        $environment = $this->createMock('Twig_Environment');
        $deliverySelector = $this->createMock(ServiceDeliverySelectorInterface::class);

        $address = [
            'firstname' => '',
            'lastname' => '',
            'address1' => '',
            'postcode' => '',
            'city' => '',
            'country_code' => '',
        ];

        $extension = new AddressExtension($deliverySelector);
        $extension->renderAddress($environment, $address, true, true);
    }

    public function testIsAddressDeliverable(): void
    {
        $address = $this->createMock(AddressInterface::class);
        $basket = $this->createMock(BasketInterface::class);

        // Test false
        $deliverySelector = $this->createMock(ServiceDeliverySelectorInterface::class);
        $deliverySelector->expects($this->once())->method('getAvailableMethods')->will($this->returnValue([]));

        $extension = new AddressExtension($deliverySelector);
        $deliverable = $extension->isAddressDeliverable($address, $basket);

        $this->assertFalse($deliverable);

        // Test true
        $deliverySelector = $this->createMock(ServiceDeliverySelectorInterface::class);
        $deliverySelector->expects($this->once())->method('getAvailableMethods')->will($this->returnValue(['paypal']));

        $extension = new AddressExtension($deliverySelector);
        $deliverable = $extension->isAddressDeliverable($address, $basket);

        $this->assertTrue($deliverable);
    }
}
