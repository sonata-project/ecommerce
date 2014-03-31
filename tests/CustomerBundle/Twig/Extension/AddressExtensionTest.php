<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Tests\CustomerBundle\Twig\Extension;

use Sonata\CustomerBundle\Twig\Extension\AddressExtension;


/**
 * Class AddressExtensionTest
 *
 * @package Sonata\Tests\CustomerBundle\Twig\Extension
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class AddressExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderAddress()
    {
        $environment = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $deliverySelector = $this->getMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');

        $environment->expects($this->exactly(4))->method('render');

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->exactly(3))->method('getFullAddressHtml');

        $extension = new AddressExtension($deliverySelector);

        $extension->renderAddress($environment, $address, false);
        $address->expects($this->exactly(2))->method('getName');
        $extension->renderAddress($environment, $address);
        $address->expects($this->once())->method('getId');
        $extension->renderAddress($environment, $address, true, true);

        $address = array(
            "firstname"    => "",
            "lastname"     => "",
            "address1"     => "",
            "postcode"     => "",
            "city"         => "",
            "country_code" => ""
        );

        $extension->renderAddress($environment, $address);
    }

    /**
     * @expectedException Sonata\CoreBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage sonata_address_render needs an AddressInterface instance or an array with keys (firstname, lastname, address1, postcode, city, country_code)
     */
    public function testRenderAddressInvalidParameter()
    {
        $environment = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $deliverySelector = $this->getMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');

        $address = array();

        $extension = new AddressExtension($deliverySelector);
        $extension->renderAddress($environment, $address);
    }

    /**
     * @expectedException Sonata\CoreBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage sonata_address_render needs 'id' key to be set to render the edit button
     */
    public function testRenderAddressMissingId()
    {
        $environment = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $deliverySelector = $this->getMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');

        $address = array(
            "firstname"    => "",
            "lastname"     => "",
            "address1"     => "",
            "postcode"     => "",
            "city"         => "",
            "country_code" => ""
        );

        $extension = new AddressExtension($deliverySelector);
        $extension->renderAddress($environment, $address, true, true);
    }

    public function testIsAddressDeliverable()
    {
        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        // Test false
        $deliverySelector = $this->getMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');
        $deliverySelector->expects($this->once())->method('getAvailableMethods')->will($this->returnValue(array()));

        $extension = new AddressExtension($deliverySelector);
        $deliverable = $extension->isAddressDeliverable($address, $basket);

        $this->assertFalse($deliverable);

        // Test true
        $deliverySelector = $this->getMock('Sonata\Component\Delivery\ServiceDeliverySelectorInterface');
        $deliverySelector->expects($this->once())->method('getAvailableMethods')->will($this->returnValue(array('paypal')));

        $extension = new AddressExtension($deliverySelector);
        $deliverable = $extension->isAddressDeliverable($address, $basket);

        $this->assertTrue($deliverable);
    }
}
