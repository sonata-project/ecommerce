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
final class BasketEvents
{
    public const PRE_ADD_PRODUCT = 'sonata.ecommerce.basket.pre_add_product';
    public const POST_ADD_PRODUCT = 'sonata.ecommerce.basket.post_add_product';

    public const PRE_MERGE_PRODUCT = 'sonata.ecommerce.basket.pre_merge_product';
    public const POST_MERGE_PRODUCT = 'sonata.ecommerce.basket.post_merge_product';

    public const PRE_CALCULATE_PRICE = 'sonata.ecommerce.basket.pre_calculate_price';
    public const POST_CALCULATE_PRICE = 'sonata.ecommerce.basket.post_calculate_price';
}
