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

namespace Sonata\InvoiceBundle\Tests\Controller\Api;

use PHPUnit\Framework\TestCase;
use Sonata\InvoiceBundle\Controller\Api\InvoiceController;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class InvoiceControllerTest extends TestCase
{
    public function testGetInvoicesAction(): void
    {
        $invoiceManager = $this->createMock('Sonata\Component\Invoice\InvoiceManagerInterface');
        $invoiceManager->expects($this->once())->method('getPager')->will($this->returnValue([]));

        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $this->assertEquals([], $this->createInvoiceController(null, $invoiceManager)->getInvoicesAction($paramFetcher));
    }

    public function testGetInvoiceAction(): void
    {
        $invoice = $this->createMock('Sonata\Component\Invoice\InvoiceInterface');
        $this->assertEquals($invoice, $this->createInvoiceController($invoice)->getInvoiceAction(1));
    }

    public function testGetInvoiceActionNotFoundException(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Invoice (42) not found');

        $this->createInvoiceController()->getInvoiceAction(42);
    }

    public function testGetInvoiceInvoiceelementsAction(): void
    {
        $invoice = $this->createMock('Sonata\Component\Invoice\InvoiceInterface');
        $invoiceElements = $this->createMock('Sonata\Component\Invoice\InvoiceElementInterface');
        $invoice->expects($this->once())->method('getInvoiceElements')->will($this->returnValue([$invoiceElements]));

        $this->assertEquals([$invoiceElements], $this->createInvoiceController($invoice)->getInvoiceInvoiceelementsAction(1));
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
            $invoiceManager = $this->createMock('Sonata\Component\Invoice\InvoiceManagerInterface');
        }
        if (null !== $invoice) {
            $invoiceManager->expects($this->once())->method('findOneBy')->will($this->returnValue($invoice));
        }

        return new InvoiceController($invoiceManager);
    }
}
