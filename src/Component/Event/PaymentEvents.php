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

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class PaymentEvents
{
    public const PRE_ERROR = 'sonata.ecommerce.payment.pre_error';

    // Sent just before adding the order to the message queue
    public const POST_ERROR = 'sonata.ecommerce.payment.post_error';

    public const CONFIRMATION = 'sonata.ecommerce.payment.confirmation';

    public const PRE_CALLBACK = 'sonata.ecommerce.payment.pre_callback';

    // Sent just before adding the order to the message queue
    public const POST_CALLBACK = 'sonata.ecommerce.payment.post_callback';

    public const PRE_SENDBANK = 'sonata.ecommerce.payment.pre_sendbank';
    public const POST_SENDBANK = 'sonata.ecommerce.payment.post_sendbank';
}
