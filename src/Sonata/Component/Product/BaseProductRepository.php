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

use Sonata\Component\Product\ProductInterface as Product;
use Sonata\Component\Basket\BasketElement;

class BaseProductRepository implements ProductRepositoryInterface {

    /**
     * return true if the basket element is still valid
     *
     * @param Basket $basket
     * @param Product $product
     *
     * @return BasketElement
     */
    public function basketAddProduct($basket, $product, array $values) {

        if ($basket->hasProduct($product)) {
            return false;
        }

        $quantity = isset($values['quantity']) ? $values['quantity'] : 1;

        $basket_element = new BasketElement;
        $basket_element->setProduct($product);
        $basket_element->setQuantity($quantity);

        $basket_element_options = $product->getOptions();
        // add the default product options to the basket element
        if (is_array($basket_element_options) && !empty($basket_element_options)) {

            foreach ($basket_element_options as $option => $value) {
                $basket_element->setOption($option, $value);
            }
            
        }

        $basket->addBasketElement($basket_element);

        return $basket_element;
    }

    /**
     * Merge a product with another when the product is already present into the basket
     *
     * @param Basket $basket
     * @param Product $product
     *
     * @return BasketElement
     */
    public function basketMergeProduct($basket, $product, array $values) {

        $quantity = isset($values['quantity']) ? $values['quantity'] : 1;

        if (!$basket->hasProduct($product)) {

            return false;
        }

        $basket_element = $basket->getElement($product);
        $basket_element->setQuantity($basket_element->getQuantity() + $quantity);

        return $basket_element;
    }

    /**
     * @abstract
     * @param BasketElement $basket_element
     *
     * @return boolean true if the basket element is still valid
     */
    public function isValidBasketElement($basket_element) {
        $product = $basket_element->getProduct();

        if (!$product instanceof Product) {

            return false;
        }

        if (!$product->isValid()) {

            return false;
        }

        return true;
    }

    /**
     * @param Basket $basket
     * @param BasketElement $basket_element
     * 
     * @return float price of the basket element
     */
    public function basketCalculatePrice($basket, $basket_element) {

        return $basket_element->getProduct()->getPrice();
    }

    /**
     * Return true if the product can be added to the provided basket
     *
     * @abstract
     * @param Sonata\Component\Basket\Basket $basket
     * @param Sonata\Component\Product\ProductInterface $product
     * @param array $options
     * @return boolean
     */
    public function isAddableToBasket($basket, $product, $options = array()) {
        
        return true;
    }
}