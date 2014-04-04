<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Basket;

use Sonata\Component\Basket\Basket;

use Sonata\Component\Basket\Loader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{

    public function testLoadBasket()
    {
        $customer         = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $basket           = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basketFactory    = $this->getMock('Sonata\Component\Basket\BasketFactoryInterface');
        $basketFactory->expects($this->once())->method('load')->will($this->returnValue($basket));

        $customerSelector = $this->getMock('Sonata\Component\Customer\CustomerSelectorInterface');
        $customerSelector->expects($this->once())->method('get')->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);

        $this->assertInstanceOf('Sonata\Component\Basket\BasketInterface', $loader->getBasket());
    }

    /**
     * @expectedException        RuntimeException
     */
    public function testExceptionLoadBasket()
    {
        $this->setExpectedException('RuntimeException');

        $customer         = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $basketFactory    = $this->getMock('Sonata\Component\Basket\BasketFactoryInterface');
        $basketFactory->expects($this->once())->method('load')->will($this->returnCallback(function () {
            throw new \RuntimeException();
        }));

        $customerSelector = $this->getMock('Sonata\Component\Customer\CustomerSelectorInterface');
        $customerSelector->expects($this->once())->method('get')->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);
        $loader->getBasket();
    }
}
