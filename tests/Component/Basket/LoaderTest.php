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

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketFactoryInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\Loader;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Customer\CustomerSelectorInterface;

class LoaderTest extends TestCase
{
    public function testLoadBasket(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $basket = $this->createMock(BasketInterface::class);
        $basketFactory = $this->createMock(BasketFactoryInterface::class);
        $basketFactory->expects($this->once())->method('load')->will($this->returnValue($basket));

        $customerSelector = $this->createMock(CustomerSelectorInterface::class);
        $customerSelector->expects($this->once())->method('get')->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);

        $this->assertInstanceOf(BasketInterface::class, $loader->getBasket());
    }

    public function testExceptionLoadBasket(): void
    {
        $this->expectException(\RuntimeException::class);

        $customer = $this->createMock(CustomerInterface::class);
        $basketFactory = $this->createMock(BasketFactoryInterface::class);
        $basketFactory->expects($this->once())->method('load')->will($this->returnCallback(function (): void {
            throw new \RuntimeException();
        }));

        $customerSelector = $this->createMock(CustomerSelectorInterface::class);
        $customerSelector->expects($this->once())->method('get')->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);
        $loader->getBasket();
    }
}
