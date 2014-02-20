<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\InvoiceBundle\Entity;

use Sonata\InvoiceBundle\Entity\BaseInvoice;

class InvoiceTest extends BaseInvoice
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}

/**
 * Class BaseInvoiceTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BaseInvoiceTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoiceElementsVatAmounts()
    {
        $invoice = new InvoiceTest();

        $element1 = $this->getMockBuilder('Sonata\InvoiceBundle\Entity\BaseInvoiceElement')->getMock();
        $element1->expects($this->any())->method('getVatRate')->will($this->returnValue(20));
        $element1->expects($this->any())->method('getVatAmount')->will($this->returnValue(3));

        $element2 = $this->getMockBuilder('Sonata\InvoiceBundle\Entity\BaseInvoiceElement')->getMock();
        $element2->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element2->expects($this->any())->method('getVatAmount')->will($this->returnValue(2));

        $element3 = $this->getMockBuilder('Sonata\BasketBundle\Entity\BaseBasketElement')->getMock();
        $element3->expects($this->any())->method('getProduct')->will($this->returnValue($this->getMockBuilder('Sonata\ProductBundle\Entity\BaseProduct')->getMock()));
        $element3->expects($this->any())->method('getVatRate')->will($this->returnValue(10));
        $element3->expects($this->any())->method('getVatAmount')->will($this->returnValue(5));

        $invoice->setInvoiceElements(array($element1, $element2, $element3));

        $items = $invoice->getVatAmounts();

        $this->assertTrue(is_array($items), 'Should return an array');

        foreach ($items as $item) {
            $this->assertArrayHasKey('rate', $item, 'Array items should contains a "rate" key');
            $this->assertArrayHasKey('amount', $item, 'Array items should contains a "amount" key');

            $this->assertTrue(in_array($item['rate'], array(10, 20)));
            $this->assertTrue(in_array($item['amount'], array(7, 3)));
        }
    }
}
