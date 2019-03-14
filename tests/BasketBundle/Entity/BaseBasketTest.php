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

namespace Sonata\BasketBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sonata\BasketBundle\Entity\BaseBasketElement;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\ProductBundle\Entity\BaseProduct;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BaseBasketTest extends TestCase
{
    public function testSetBasketElements(): void
    {
        $basket = new BasketTest();

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->any())->method('getProvider')->will($this->returnValue($this->createMock(ProductProviderInterface::class)));

        $basket->setProductPool($pool);

        $element = $this->getMockBuilder(BaseBasketElement::class)->getMock();
        $element->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder(BaseProduct::class)->getMock()));

        $elements = [
            'notBasketElementInterface',
            $element,
        ];

        $basket->setBasketElements($elements);

        $this->assertCount(1, $basket->getBasketElements());
    }

    public function testReset(): void
    {
        $basket = new BasketTest();

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->any())->method('getProvider')->will($this->returnValue($this->createMock(ProductProviderInterface::class)));

        $basket->setProductPool($pool);

        $element = $this->getMockBuilder(BaseBasketElement::class)->getMock();
        $element->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder(BaseProduct::class)->getMock()));

        $elements = [
            'notBasketElementInterface',
            $element,
        ];

        $basket->setBasketElements($elements);

        $basket->reset(false);

        $this->assertCount(1, $basket->getBasketElements());

        $basket->reset();

        $this->assertCount(0, $basket->getBasketElements());
        $this->assertInstanceOf(ArrayCollection::class, $basket->getBasketElements());
    }

    public function testBasketElementsVatAmounts(): void
    {
        $basket = new BasketTest();

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->any())->method('getProvider')->will($this->returnValue($this->createMock(ProductProviderInterface::class)));

        $basket->setProductPool($pool);

        $element1 = $this->getMockBuilder(BaseBasketElement::class)->getMock();
        $element1->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder(BaseProduct::class)->getMock()));
        $element1->expects($this->any())->method('getVatRate')->will($this->returnValue(20));
        $element1->expects($this->any())->method('getVatAmount')->will($this->returnValue(3));

        $element2 = $this->getMockBuilder(BaseBasketElement::class)->getMock();
        $element2->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder(BaseProduct::class)->getMock()));
        $element2->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element2->expects($this->any())->method('getVatAmount')->will($this->returnValue(2));

        $element3 = $this->getMockBuilder(BaseBasketElement::class)->getMock();
        $element3->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder(BaseProduct::class)->getMock()));
        $element3->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element3->expects($this->any())->method('getVatAmount')->will($this->returnValue(5));

        $basket->setBasketElements([$element1, $element2, $element3]);

        $items = $basket->getVatAmounts();

        $this->assertInternalType('array', $items, 'Should return an array');

        foreach ($items as $item) {
            $this->assertArrayHasKey('rate', $item, 'Array items should contains a "rate" key');
            $this->assertArrayHasKey('amount', $item, 'Array items should contains a "amount" key');

            $this->assertContains($item['rate'], [10, 20]);
            $this->assertContains($item['amount'], [7, 3]);
        }
    }
}
