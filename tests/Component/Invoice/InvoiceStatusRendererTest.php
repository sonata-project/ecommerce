<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Invoice;

use Sonata\Component\Invoice\InvoiceStatusRenderer;
use Sonata\InvoiceBundle\Entity\BaseInvoice;

/**
 * Class InvoiceStatusRendererTest
 *
 * @package Sonata\Tests\Component\Invoice
 *
 * @author Hugo Briand <briand@ekino.com>
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class InvoiceStatusRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testHandles()
    {
        $renderer = new InvoiceStatusRenderer();

        $invoice = new \DateTime;
        $this->assertFalse($renderer->handlesObject($invoice));

        $invoice = $this->getMock('Sonata\Component\Invoice\InvoiceInterface');
        $this->assertTrue($renderer->handlesObject($invoice));
    }

    public function testGetClass()
    {
        $renderer = new InvoiceStatusRenderer();
        $invoice  = $this->getMock('Sonata\Component\Invoice\InvoiceInterface');

        $invoice->expects($this->once())->method('getStatus')->will($this->returnValue(array_rand(BaseInvoice::getStatusList())));
        $this->assertContains($renderer->getStatusClass($invoice, '', 'error'), array('danger', 'warning', 'success'));
    }
}
