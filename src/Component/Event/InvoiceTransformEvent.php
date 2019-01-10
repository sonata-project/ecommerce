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

namespace Sonata\Component\Event;

use Sonata\Component\Invoice\InvoiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class InvoiceTransformEvent extends Event
{
    /**
     * @var InvoiceInterface
     */
    protected $invoice;

    /**
     * @param InvoiceInterface $invoice
     */
    public function __construct(InvoiceInterface $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return InvoiceInterface
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}
