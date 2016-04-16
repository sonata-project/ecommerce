<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\InvoiceBundle\Controller\Api;

use Sonata\InvoiceBundle\Controller\Api\InvoiceController;

/**
 * Class InvoiceControllerTest.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class InvoiceControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInvoicesAction()
    {
        $invoice        = $this->getMock('Sonata\Component\Invoice\InvoiceInterface');
        $invoiceManager = $this->getMock('Sonata\Component\Invoice\InvoiceManagerInterface');
        $invoiceManager->expects($this->once())->method('findBy')->will($this->returnValue(array($invoice)));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertSame(array($invoice), $this->createInvoiceController(null, $invoiceManager)->getInvoicesAction($paramFetcher));
    }

    public function testGetInvoiceAction()
    {
        $invoice = $this->getMock('Sonata\Component\Invoice\InvoiceInterface');
        $this->assertSame($invoice, $this->createInvoiceController($invoice)->getInvoiceAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Invoice (42) not found
     */
    public function testGetInvoiceActionNotFoundException()
    {
        $this->createInvoiceController()->getInvoiceAction(42);
    }

    public function testGetInvoiceInvoiceelementsAction()
    {
        $invoice         = $this->getMock('Sonata\Component\Invoice\InvoiceInterface');
        $invoiceElements = $this->getMock('Sonata\Component\Invoice\InvoiceElementsInterface');
        $invoice->expects($this->once())->method('getInvoiceElements')->will($this->returnValue(array($invoiceElements)));

        $this->assertSame(array($invoiceElements), $this->createInvoiceController($invoice)->getInvoiceInvoiceelementsAction(1));
    }

    /**
     * @param $invoice
     * @param $invoiceManager
     *
     * @return InvoiceController
     */
    public function createInvoiceController($invoice = null, $invoiceManager = null)
    {
        if (null === $invoiceManager) {
            $invoiceManager = $this->getMock('Sonata\Component\Invoice\InvoiceManagerInterface');
        }
        if (null !== $invoice) {
            $invoiceManager->expects($this->once())->method('findOneBy')->will($this->returnValue($invoice));
        }

        return new InvoiceController($invoiceManager);
    }
}
