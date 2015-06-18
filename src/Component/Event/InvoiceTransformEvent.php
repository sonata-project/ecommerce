<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Event;

use Sonata\Component\Invoice\InvoiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class InvoiceTransformEvent.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class InvoiceTransformEvent extends Event
{
    /**
     * @var InvoiceInterface
     */
    protected $invoice;

    /**
     * Constructor.
     *
     * @param InvoiceInterface $invoice
     */
    public function __construct(InvoiceInterface $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return \Sonata\Component\Invoice\InvoiceInterface
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}
