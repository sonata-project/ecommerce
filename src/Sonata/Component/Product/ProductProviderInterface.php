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
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\AdminBundle\Form\FormMapper;

use Symfony\Component\Form\FormBuilder;

interface ProductProviderInterface
{
    /**
     * @abstract
     * @param  \Sonata\Component\Basket\BasketElementManagerInterface $basketElementManager
     * @return void
     */
    public function setBasketElementManager(BasketElementManagerInterface $basketElementManager);

    /**
     * @abstract
     * @return \Sonata\Component\Basket\BasketElementManagerInterface
     */
    public function getBasketElementManager();

    /**
     * @abstract
     * @return string
     */
    public function getBaseControllerName();

    /**
     * @param  \Sonata\Component\Product\ProductInterface $product
     * @param  \Symfony\Component\Form\FormBuilder        $formBuilder
     * @param  array                                      $options
     * @return void
     */
    public function defineAddBasketForm(ProductInterface $product, FormBuilder $formBuilder, array $options = array());

    /**
     * @param  \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param  \Symfony\Component\Form\FormBuilder             $formBuilder
     * @param  array                                           $options
     * @return void
     */
    public function defineBasketElementForm(BasketElementInterface $basketElement, FormBuilder $formBuilder, array $options = array());

    /**
     * return true if the basket element is still valid
     *
     * @abstract
     * @param  \Sonata\Component\Basket\BasketInterface        $basket
     * @param  ProductInterface                                $product
     * @param  \Sonata\Component\Basket\BasketElementInterface $newBasketElement
     * @return void
     */
    public function basketAddProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $newBasketElement);

    /**
     * Merge a product with another when the product is already present into the basket
     *
     * @abstract
     * @param  \Sonata\Component\Basket\BasketInterface        $basket
     * @param  ProductInterface                                $product
     * @param  \Sonata\Component\Basket\BasketElementInterface $newBasketElement
     * @return void
     */
    public function basketMergeProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $newBasketElement);

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
     * @param  \Sonata\Component\Basket\BasketInterface        $basket
     * @param  \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return return                                          the unit price of the basketElement
     */
    public function basketCalculatePrice(BasketInterface $basket, BasketElementInterface $basketElement);

    /**
     * Return true if the product can be added to the provided basket
     *
     * @abstract
     * @param  \Sonata\Component\Basket\BasketInterface   $basket
     * @param  \Sonata\Component\Product\ProductInterface $product
     * @param  array                                      $options
     * @return boolean
     */
    public function isAddableToBasket(BasketInterface $basket, ProductInterface $product, array $options = array());

    /**
     * @abstract
     * @param  null|ProductInterface $product
     * @param  array                 $options
     * @return void
     */
    public function createBasketElement(ProductInterface $product = null, array $options = array());

    /**
     * @abstract
     * @param  \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    public function buildEditForm(FormMapper $formMapper);

    /**
     * @abstract
     * @param  \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    public function buildCreateForm(FormMapper $formMapper);
}
