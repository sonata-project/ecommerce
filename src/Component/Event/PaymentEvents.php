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
 * Class PaymentEvents.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
final class PaymentEvents
{
    const PRE_ERROR  = 'sonata.ecommerce.payment.pre_error';

    // Sent just before adding the order to the message queue
    const POST_ERROR = 'sonata.ecommerce.payment.post_error';

    const CONFIRMATION = 'sonata.ecommerce.payment.confirmation';

    const PRE_CALLBACK  = 'sonata.ecommerce.payment.pre_callback';

    // Sent just before adding the order to the message queue
    const POST_CALLBACK = 'sonata.ecommerce.payment.post_callback';

    const PRE_SENDBANK  = 'sonata.ecommerce.payment.pre_sendbank';
    const POST_SENDBANK = 'sonata.ecommerce.payment.post_sendbank';
}
