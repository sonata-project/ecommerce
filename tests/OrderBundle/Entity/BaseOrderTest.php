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

namespace Sonata\OrderBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\BasketBundle\Entity\BaseBasketElement;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\OrderBundle\Entity\BaseOrderElement;
use Sonata\ProductBundle\Entity\BaseProduct;

class OrderTest extends BaseOrder
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BaseOrderTest extends TestCase
{
    public function testOrderElementsVatAmounts(): void
    {
        $order = new OrderTest();

        $element1 = $this->getMockBuilder(BaseOrderElement::class)->getMock();
        $element1->expects($this->any())->method('getVatRate')->will($this->returnValue(20));
        $element1->expects($this->any())->method('getVatAmount')->will($this->returnValue(3));

        $element2 = $this->getMockBuilder(BaseOrderElement::class)->getMock();
        $element2->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element2->expects($this->any())->method('getVatAmount')->will($this->returnValue(2));

        $element3 = $this->getMockBuilder(BaseBasketElement::class)->getMock();
        $element3->expects($this->any())->method('getProduct')->will($this->returnValue(
            $this->getMockBuilder(BaseProduct::class)->getMock()
        ));
        $element3->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element3->expects($this->any())->method('getVatAmount')->will($this->returnValue(5));

        $order->setOrderElements([$element1, $element2, $element3]);

        $items = $order->getVatAmounts();

        $this->assertInternalType('array', $items, 'Should return an array');

        foreach ($items as $item) {
            $this->assertArrayHasKey('rate', $item, 'Array items should contains a "rate" key');
            $this->assertArrayHasKey('amount', $item, 'Array items should contains a "amount" key');

            $this->assertContains($item['rate'], [10, 20]);
            $this->assertContains($item['amount'], [7, 3]);
        }
    }
}
