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

/**
 * Class TransformerEvents
 *
 * @package Sonata\Component\Event
 *
 * @author Hugo Briand <briand@ekino.com>
 */
final class TransformerEvents
{
    const PRE_BASKET_ORDER_TRANSFORM   = "sonata.ecommerce.pre_basket_order_transform";
    const POST_BASKET_ORDER_TRANSFORM  = "sonata.ecommerce.pre_basket_order_transform";
    const PRE_ORDER_BASKET_TRANSFORM   = "sonata.ecommerce.pre_order_basket_transform";
    const POST_ORDER_BASKET_TRANSFORM  = "sonata.ecommerce.pre_order_basket_transform";
    const PRE_ORDER_INVOICE_TRANSFORM  = "sonata.ecommerce.pre_order_invoice_transform";
    const POST_ORDER_INVOICE_TRANSFORM = "sonata.ecommerce.pre_order_invoice_transform";
}