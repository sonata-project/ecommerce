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

use Symfony\Component\Form\FormBuilder;

interface ProductProviderInterface
{

    function getBaseControllerName();

    /**
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param \Symfony\Component\Form\FormBuilder $formBuilder
     * @param array $options
     * @return void
     */
    function defineAddBasketForm(ProductInterface $product, FormBuilder $formBuilder, array $options = array());

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Symfony\Component\Form\FormBuilder $formBuilder
     * @param array $options
     * @return void
     */
    function defineBasketElementForm(BasketElementInterface $basketElement, FormBuilder $formBuilder, array $options = array());

    /**
     * return true if the basket element is still valid
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array $values
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    public function basketAddProduct(BasketInterface $basket, ProductInterface $product, array $values = array());

    /**
     * Merge a product with another when the product is already present into the basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array $values
     * @return BasketElement
     */
    public function basketMergeProduct(BasketInterface $basket, ProductInterface $product, array $values = array());

    /**
     * @abstract
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
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
     * @param  \Sonata\Component\Basket\BasketInterface $basket
     * @param  \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return return the unit price of the basketElement
     */
    public function basketCalculatePrice(BasketInterface $basket, BasketElementInterface $basketElement);

    /**
     * Return true if the product can be added to the provided basket
     *
     * @abstract
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array $options
     * @return boolean
     */
    public function isAddableToBasket(BasketInterface $basket, ProductInterface $product, array $options = array());
}
