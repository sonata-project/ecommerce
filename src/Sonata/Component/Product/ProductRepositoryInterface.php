<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

interface ProductRepositoryInterface {

    /**
     * return true if the basket element is still valid
     *
     * @param Basket $basket
     * @param Product $product
     *
     * @return BasketElement
     */
    public function basketAddProduct($basket, $product, array $values);

    /**
     * Merge a product with another when the product is already present into the basket
     *
     * @param Basket $tr_shop_basket
     * @param Product $tr_shop_product
     *
     * @return BasketElement
     */
    public function basketMergeProduct($basket, $product, array $values);

    /**
     * @abstract
     * @param trShopBasketElement $tr_shop_basket_element
     *
     * @return boolean true if the basket element is still valid
     */
    public function isValidBasketElement($basket_element);

    /**
     * This method return the return price of basket element, this method
     * allow to update the price of the basket element depend on the presence
     * of another product
     *
     * @abstract
     * @param  $basket
     * @param  $basket_element
     * @return return the unit price of the basket_element
     */
    public function basketCalculatePrice($basket, $basket_element);

    /**
     * Return true if the product can be added to the provided basket
     *
     * @abstract
     * @param Sonata\Component\Basket\Basket $basket
     * @param Sonata\Component\Product\ProductInterface $product
     * @param array $options
     * @return boolean
     */
    public function isAddableToBasket($basket, $product, $options = array());
}