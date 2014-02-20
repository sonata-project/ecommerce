<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\BasketBundle\Entity;

use Sonata\BasketBundle\Entity\BaseBasket;

class BasketTest extends BaseBasket
{

}

/**
 * Class BaseBasketTest
 *
 * @package Sonata\Tests\BasketBundle\Entity
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class BaseBasketTest extends \PHPUnit_Framework_TestCase
{
    public function testSetBasketElements()
    {
        $basket = new BasketTest();

        $pool = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $pool->expects($this->any())->method('getProvider')->will($this->returnValue($this->getMock('Sonata\Component\Product\ProductProviderInterface')));

        $basket->setProductPool($pool);

        $element = $this->getMockBuilder('Sonata\BasketBundle\Entity\BaseBasketElement')->getMock();
        $element->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder('Sonata\ProductBundle\Entity\BaseProduct')->getMock()));

        $elements = array(
            "notBasketElementInterface",
            $element
        );

        $basket->setBasketElements($elements);

        $this->assertEquals(1, count($basket->getBasketElements()));
    }

    public function testReset()
    {
        $basket = new BasketTest();

        $pool = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $pool->expects($this->any())->method('getProvider')->will($this->returnValue($this->getMock('Sonata\Component\Product\ProductProviderInterface')));

        $basket->setProductPool($pool);

        $element = $this->getMockBuilder('Sonata\BasketBundle\Entity\BaseBasketElement')->getMock();
        $element->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder('Sonata\ProductBundle\Entity\BaseProduct')->getMock()));

        $elements = array(
            "notBasketElementInterface",
            $element
        );

        $basket->setBasketElements($elements);

        $basket->reset(false);

        $this->assertEquals(1, count($basket->getBasketElements()));

        $basket->reset();

        $this->assertEquals(0, count($basket->getBasketElements()));
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $basket->getBasketElements());
    }

    public function testBasketElementsVatAmounts()
    {
        $basket = new BasketTest();

        $pool = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $pool->expects($this->any())->method('getProvider')->will($this->returnValue($this->getMock('Sonata\Component\Product\ProductProviderInterface')));

        $basket->setProductPool($pool);

        $element1 = $this->getMockBuilder('Sonata\BasketBundle\Entity\BaseBasketElement')->getMock();
        $element1->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder('Sonata\ProductBundle\Entity\BaseProduct')->getMock()));
        $element1->expects($this->any())->method('getVatRate')->will($this->returnValue(20));
        $element1->expects($this->any())->method('getVatAmount')->will($this->returnValue(3));

        $element2 = $this->getMockBuilder('Sonata\BasketBundle\Entity\BaseBasketElement')->getMock();
        $element2->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder('Sonata\ProductBundle\Entity\BaseProduct')->getMock()));
        $element2->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element2->expects($this->any())->method('getVatAmount')->will($this->returnValue(2));

        $element3 = $this->getMockBuilder('Sonata\BasketBundle\Entity\BaseBasketElement')->getMock();
        $element3->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder('Sonata\ProductBundle\Entity\BaseProduct')->getMock()));
        $element3->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element3->expects($this->any())->method('getVatAmount')->will($this->returnValue(5));

        $basket->setBasketElements(array($element1, $element2, $element3));

        $items = $basket->getVatAmounts();

        $this->assertTrue(is_array($items), 'Should return an array');

        foreach ($items as $item) {
            $this->assertArrayHasKey('rate', $item, 'Array items should contains a "rate" key');
            $this->assertArrayHasKey('amount', $item, 'Array items should contains a "amount" key');

            $this->assertTrue(in_array($item['rate'], array(10, 20)));
            $this->assertTrue(in_array($item['amount'], array(7, 3)));
        }
    }
}
