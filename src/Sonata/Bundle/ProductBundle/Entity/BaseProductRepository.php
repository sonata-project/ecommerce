<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\Entity;

use Sonata\Component\Product\ProductInterface as Product;
use Sonata\Component\Basket\BasketElement;

class BaseProductRepository extends \Doctrine\ORM\EntityRepository
{

    protected
        $options            = array(),
        $variation_fields   = array()
     ;

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {

        return $this->options;
    }

    public function getOption($name, $default = null)
    {

        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function createProductInstance()
    {

    }

    ////////////////////////////////////////////////
    //   VARIATION RELATED FUNCTIONS

    /**
     * @param  $name
     * @return bool return true if the field $name is a variation
     */
    public function isVariateBy($name) {

        return in_array($name, $this->variation_fields);
    }


    /**
     * @return bool return true if the product haas some variation fields
     */
    public function hasVariationFields() {

        return count($this->getVariationFields()) > 0;
    }

    public function setVariationFields($fields) {

        $this->variation_fields = $fields;
    }

    public function getVariationFields() {

        return $this->variation_fields;
    }

    public function createVariation($product) {

        if($product->isVariation()) {

            throw RuntimeException('Cannot create a variation from a variation product');
        }

        $variation = clone $product;
        $variation->setParent($product);
        $variation->setId(null);
        $variation->setEnabled(false);
        $variation->setName(sprintf('%s (duplicated)', $product->getName()));

        return $variation;
    }

    public function copyVariation($product, $name = 'all', $force_copy = false)
    {

        if($product->isVariation()) {

            return;
        }

        switch($name) {
          case 'product':
                $this->copyProductVariation($product, $force_copy);

                return;


          case 'all':
                $this->copyProductVariation($product, $force_copy);

                return;

        }
    }

    public function copyProductVariation(Product $product, $force_copy = false)
    {

        $variation_fields = array_merge(array('id'), $this->getVariationFields());

        // fields to copy
        $values = array(
            'Name'    => $product->getName(),
            'Price'   => $product->getPrice(),
            'Vat'     => $product->getVat(),
            'Enabled' => $product->getEnabled()
        );

        if(!$force_copy) {

            foreach($variation_fields as $field) {

                if(!array_key_exists($field, $values)) {
                   continue;
                }

                unset($values[$field]);
            }
        }

        foreach($product->getVariations() as $variation) {

            foreach($values as $name => $value) {

                call_user_func(array($variation, 'set'.$name), $value );
            }
        }
    }

    /////////////////////////////////////////////////////
    // BASKET RELATED FUNCTIONS

    /**
     * This function return the form used in the product view page
     *
     * @param  $product
     * @param  $validator
     * @param array $options
     * @return Application\ProductBundle\Products\Bottle\BottleAddBasketForm
     */
    public function getAddBasketForm($product, $validator, $options = array())
    {
        // create the product form
        $class = $this->getAddBasketClass();

        $product_basket = new $class;
        $product_basket->setProduct($product);
        $product_basket->setQuantity(1);

        // create the form
        $class = $this->getAddBasketFormClass();

        return new $class('basket', $product_basket, $validator, $options);
    }


    /**
     * return an array of errors if any, you can also manipulate the basket_element if require
     * please not you always work with a clone version of the basket_element.
     *
     * If the basket is valid it will then replace the one in session
     *
     * @param  $basket_element
     * @return array
     */
    public function validateFormBasketElement($basket_element)
    {

        // initialize the errors array
        $errors = array(
            'global' => false,    // global error, ie the basket element is not valid anymore
            'fields' => array(),  // error per field
        );

        // the item is flagged as deleted, no need to validate the item
        if($basket_element->getDelete()) {

            return $errors;
        }

        // refresh the product from the database
        $product = $basket_element->getProduct();

        // check if the product is still enabled
        if(!$product) {
            $errors['global'] = array(
                'The product is not available anymore',
                array(),
                null
            );

            return $errors;
        }

        // check if the product is still enabled
        if(!$basket_element->getProduct()->isEnabled()) {
            $errors['global'] = array(
                'The product is not enabled anymore',
                array(),
                null
            );

            return $errors;
        }

        // check if the quantity is numeric
        if(!is_numeric($basket_element->getQuantity())) {
            $errors['fields']['quantity'] = array(
                'The product quantity is not a numeric value',
                array('{{ quantity }}' => $basket_element->getQuantity()),
                $basket_element->getQuantity() // todo : not sure about the third element
            );

            return $errors;
        }

        // check if the product is still available
        if($this->getStockAvailable($basket_element->getProduct()) < $basket_element->getQuantity()) {
            $errors['fields']['quantity'] = array(
                'The product quantity ({{ quantity }}) is not valid',
                array('{{ quantity }}' => $basket_element->getQuantity()),
                $basket_element->getQuantity() // todo : not sure about the third element
            );
        }

        // add here your own validation check



        return $errors;
    }

    /**
     * return true if the basket element is still valid
     *
     * @param Basket $basket
     * @param Product $product
     *
     * @return BasketElement
     */
    public function basketAddProduct($basket, $product, $values)
    {

        if ($basket->hasProduct($product)) {
            return false;
        }

        $class = $this->getBasketElementClass();
        
        $basket_element = new $class;
        $basket_element->setProduct($product, $this);
        $basket_element->setQuantity($values->getQuantity());

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
    public function basketMergeProduct($basket, $product, $values)
    {

        if (!$basket->hasProduct($product)) {

            return false;
        }

        $basket_element = $basket->getElement($product);
        $basket_element->setQuantity($basket_element->getQuantity() + $values->getQuantity());

        return $basket_element;
    }

    /**
     * @abstract
     * @param BasketElement $basket_element
     *
     * @return boolean true if the basket element is still valid
     */
    public function isValidBasketElement($basket_element)
    {
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
    public function basketCalculatePrice($basket, $basket_element)
    {

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
    public function isAddableToBasket($basket, $product, $options = array())
    {

        return true;
    }

    /**
     * return a fresh product instance (so information are reloaded: enabled and stock ...)
     *
     * @param  $product
     * @return ProductInterface
     */
    public function reloadProduct($product)
    {

        return $this->findOneById($product->getId());
    }

    /**
     * return the stock available for the current product
     *
     * @param  $product
     * @return int the stock available
     */
    public function getStockAvailable($product)
    {

        return $product->getStock();
    }

    /**
     * make the method public ...
     *
     * @return
     */
    public function getClassMetadata()
    {
        return parent::getClassMetadata();
    }
}