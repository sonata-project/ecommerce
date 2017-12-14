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

namespace Sonata\Component\Tests\Invoice;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Invoice\InvoiceStatusRenderer;
use Sonata\InvoiceBundle\Entity\BaseInvoice;

/**
 * @author Hugo Briand <briand@ekino.com>
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class InvoiceStatusRendererTest extends TestCase
{
    public function testHandles(): void
    {
        $renderer = new InvoiceStatusRenderer();

        $invoice = new \DateTime();
        $this->assertFalse($renderer->handlesObject($invoice));

        $invoice = $this->createMock(InvoiceInterface::class);
        $this->assertTrue($renderer->handlesObject($invoice));
    }

    public function testGetClass(): void
    {
        $renderer = new InvoiceStatusRenderer();
        $invoice = $this->createMock(InvoiceInterface::class);

        $invoice->expects($this->once())->method('getStatus')->will($this->returnValue(array_rand(BaseInvoice::getStatusList())));
        $this->assertContains($renderer->getStatusClass($invoice, '', 'error'), ['danger', 'warning', 'success']);
    }
}
