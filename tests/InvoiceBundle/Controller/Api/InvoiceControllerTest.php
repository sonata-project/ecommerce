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

use FOS\RestBundle\Request\ParamFetcherInterface;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Invoice\InvoiceElementInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\InvoiceBundle\Controller\Api\InvoiceController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class InvoiceControllerTest extends TestCase
{
    public function testGetInvoicesAction(): void
    {
        $pager = $this->createStub(PagerInterface::class);
        $invoiceManager = $this->createMock(InvoiceManagerInterface::class);
        $invoiceManager->expects(static::once())->method('getPager')->willReturn($pager);

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects(static::exactly(3))->method('get')->willReturn(1, 10, null);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        static::assertSame($pager, $this->createInvoiceController(null, $invoiceManager)->getInvoicesAction($paramFetcher));
    }

    public function testGetInvoiceAction(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        static::assertSame($invoice, $this->createInvoiceController($invoice)->getInvoiceAction(1));
    }

    public function testGetInvoiceActionNotFoundException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Invoice (42) not found');

        $this->createInvoiceController()->getInvoiceAction(42);
    }

    public function testGetInvoiceInvoiceelementsAction(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $invoiceElements = $this->createMock(InvoiceElementInterface::class);
        $invoice->expects(static::once())->method('getInvoiceElements')->willReturn([$invoiceElements]);

        static::assertSame([$invoiceElements], $this->createInvoiceController($invoice)->getInvoiceInvoiceelementsAction(1));
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
            $invoiceManager = $this->createMock(InvoiceManagerInterface::class);
        }
        if (null !== $invoice) {
            $invoiceManager->expects(static::once())->method('findOneBy')->willReturn($invoice);
        }

        return new InvoiceController($invoiceManager);
    }
}
