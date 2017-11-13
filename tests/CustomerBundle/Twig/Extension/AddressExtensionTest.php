<?php

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
use Sonata\CustomerBundle\Twig\Extension\AddressExtension;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressExtensionTest extends TestCase
{
    public function testRenderAddress()
    {
        $environment = $this->createMock('Twig_Environment');
        $deliverySelector = $this->createMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');

        $environment->expects($this->exactly(4))->method('render');

        $address = $this->createMock('Sonata\Component\Customer\AddressInterface');
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

    /**
     * @expectedException \Sonata\CoreBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage sonata_address_render needs an AddressInterface instance or an array with keys (firstname, lastname, address1, postcode, city, country_code)
     */
    public function testRenderAddressInvalidParameter()
    {
        $environment = $this->createMock('Twig_Environment');
        $deliverySelector = $this->createMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');

        $address = [];

        $extension = new AddressExtension($deliverySelector);
        $extension->renderAddress($environment, $address);
    }

    /**
     * @expectedException \Sonata\CoreBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage sonata_address_render needs 'id' key to be set to render the edit button
     */
    public function testRenderAddressMissingId()
    {
        $environment = $this->createMock('Twig_Environment');
        $deliverySelector = $this->createMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');

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

    public function testIsAddressDeliverable()
    {
        $address = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        // Test false
        $deliverySelector = $this->createMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');
        $deliverySelector->expects($this->once())->method('getAvailableMethods')->will($this->returnValue([]));

        $extension = new AddressExtension($deliverySelector);
        $deliverable = $extension->isAddressDeliverable($address, $basket);

        $this->assertFalse($deliverable);

        // Test true
        $deliverySelector = $this->createMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');
        $deliverySelector->expects($this->once())->method('getAvailableMethods')->will($this->returnValue(['paypal']));

        $extension = new AddressExtension($deliverySelector);
        $deliverable = $extension->isAddressDeliverable($address, $basket);

        $this->assertTrue($deliverable);
    }
}
