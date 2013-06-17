<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Generator;

use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Order\OrderInterface;

interface ReferenceInterface
{
    /**
     * @param InvoiceInterface $invoice
     */
    public function invoice(InvoiceInterface $invoice);

    /**
     * @param OrderInterface $order
     */
    public function order(OrderInterface $order);
}
