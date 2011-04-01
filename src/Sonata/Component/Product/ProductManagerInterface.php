<?php

/*
 * This file is part of the Sonata product.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketElementInterface;

interface ProductManagerInterface
{
    /**
     * Creates an empty medie instance
     *
     * @return Product
     */
    function createProduct();

    /**
     * Deletes a product
     *
     * @param Product $product
     * @return void
     */
    function deleteProduct(ProductInterface $product);

    /**
     * Finds one product by the given criteria
     *
     * @param array $criteria
     * @return ProductInterface
     */
    function findProductBy(array $criteria);

    /**
     * Returns the product's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a product
     *
     * @param Product $product
     * @return void
     */
    function updateProduct(ProductInterface $product);
    
    /**
     * return true if the basket element is still valid
     *
     * @param Basket $basket
     * @param Product $product
     *
     * @return BasketElement
     */
    public function basketAddProduct(BasketInterface $basket, ProductInterface $product, array $values);

    /**
     * Merge a product with another when the product is already present into the basket
     *
     * @param Basket $basket
     * @param Product $product
     *
     * @return BasketElement
     */
    public function basketMergeProduct(BasketInterface $basket, ProductInterface $product, array $values);

    /**
     * @abstract
     * @param BasketElement $basketElement
     *
     * @return boolean true if the basket element is still valid
     */
    public function isValidBasketElement(BasketElementInterface $basketElement);

    /**
     * This method return the return price of basket element, this method
     * allow to update the price of the basket element depend on the presence
     * of another product
     *
     * @abstract
     * @param  $basket
     * @param  $basketElement
     * @return return the unit price of the basketElement
     */
    public function basketCalculatePrice(BasketInterface $basket, BasketElementInterface $basketElement);

    /**
     * Return true if the product can be added to the provided basket
     *
     * @abstract
     * @param Sonata\Component\Basket\Basket $basket
     * @param Sonata\Component\Product\ProductInterface $product
     * @param array $options
     * @return boolean
     */
    public function isAddableToBasket(BasketInterface $basket, ProductInterface $product, array $options = array());
}
