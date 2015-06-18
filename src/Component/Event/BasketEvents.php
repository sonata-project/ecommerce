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
 * Class BasketEvents.
 *
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
final class BasketEvents
{
    const PRE_ADD_PRODUCT  = 'sonata.ecommerce.basket.pre_add_product';
    const POST_ADD_PRODUCT = 'sonata.ecommerce.basket.post_add_product';

    const PRE_MERGE_PRODUCT  = 'sonata.ecommerce.basket.pre_merge_product';
    const POST_MERGE_PRODUCT = 'sonata.ecommerce.basket.post_merge_product';

    const PRE_CALCULATE_PRICE  = 'sonata.ecommerce.basket.pre_calculate_price';
    const POST_CALCULATE_PRICE = 'sonata.ecommerce.basket.post_calculate_price';
}
