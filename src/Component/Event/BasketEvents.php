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
 * Class BasketEvents
 *
 * @package Sonata\Component\Event
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
final class BasketEvents
{
    const PRE_BASKET_ADD_PRODUCT  = 'sonata.ecommerce.product.pre_basket_add_product';
    const POST_BASKET_ADD_PRODUCT = 'sonata.ecommerce.product.post_basket_add_product';

    const PRE_BASKET_MERGE_PRODUCT  = 'sonata.ecommerce.product.pre_basket_merge_product';
    const POST_BASKET_MERGE_PRODUCT = 'sonata.ecommerce.product.post_basket_merge_product';

    const PRE_BASKET_CALCULATE_PRICE  = 'sonata.ecommerce.product.pre_basket_calculate_price';
    const POST_BASKET_CALCULATE_PRICE = 'sonata.ecommerce.product.post_basket_calculate_price';
}