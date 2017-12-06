<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Basket;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\Loader;

class LoaderTest extends TestCase
{
    public function testLoadBasket()
    {
        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basketFactory = $this->createMock('Sonata\Component\Basket\BasketFactoryInterface');
        $basketFactory->expects($this->once())->method('load')->will($this->returnValue($basket));

        $customerSelector = $this->createMock('Sonata\Component\Customer\CustomerSelectorInterface');
        $customerSelector->expects($this->once())->method('get')->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);

        $this->assertInstanceOf('Sonata\Component\Basket\BasketInterface', $loader->getBasket());
    }

    public function testExceptionLoadBasket()
    {
        $this->expectException(\RuntimeException::class);

        $this->expectException('RuntimeException');

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $basketFactory = $this->createMock('Sonata\Component\Basket\BasketFactoryInterface');
        $basketFactory->expects($this->once())->method('load')->will($this->returnCallback(function () {
            throw new \RuntimeException();
        }));

        $customerSelector = $this->createMock('Sonata\Component\Customer\CustomerSelectorInterface');
        $customerSelector->expects($this->once())->method('get')->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);
        $loader->getBasket();
    }
}
