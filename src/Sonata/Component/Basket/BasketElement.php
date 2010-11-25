<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

use Sonata\Component\Product\ProductInterface;

class BasketElement
{

    protected $product_id = null;

    protected $product = null;

    protected $price = null;

    protected $quantity = 1;

    protected $options = array();

    protected $name = null;

    protected $errors = array();

    protected $pos = null;

    /*
     * use by the validation framework
     */
    protected $delete = false;

    /**
     * the position in the basket stack
     *
     * @param unknown_type $pos
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    /**
     * return the pos of the current basket element
     *
     * @return int
     */
    public function getPos()
    {

        return $this->pos;
    }

    /**
     * return the name of the basket element
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * add an error to the basket element
     *
     * @param string $name
     * @param string $value
     */
    public function addError($name, $value)
    {
        $this->errors[$name] = $value;
    }

    /**
     * return the errors
     *
     * @return array
     */
    public function getErrors()
    {

        return $this->errors;
    }

    /**
     * Define the related product
     *
     * @param trShopProduct $product
     * @return BasketElement
     */
    public function setProduct(ProductInterface $product)
    {

        $this->product     = $product;
        $this->product_id  = $product->getId();
        $this->name        = $product->getName();
        $this->price       = $product->getPrice();
        $this->options     = $this->product->getOptions();

        return $this;
    }

    /**
     * Return the related product
     *
     * @return Product
     */
    public function getProduct()
    {

        return $this->product;
    }

    /**
     * return the product id
     * 
     * @return null
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Never call this method, use the setProduct instead. This method is only used
     * by the form framework
     * 
     * @param  $product_id
     * @return void
     */
    public function setProductId($product_id)
    {
        if(!$this->getProduct()) {
            $this->product_id = $product_id;
        }
    }

    /**
     * Return the VAT amount
     *
     *
     * @return $float
     */
    public function getVatAmount()
    {

        $tva = $this->getTotal(true) - $this->getTotal();

        return bcadd($tva, 0, 2);
    }

    /**
     * Return the VAT
     *
     *
     * @return $float
     */
    public function getVat()
    {

        $product = $this->getProduct();
        if (!$product instanceof ProductInterface) {
            return 0;
        }

        return $product->getVat();
    }

    /**
     * Return the price
     *
     * if $tva = true, return the price with vat
     *
     * @param boolean $tva
     * @return float
     */
    public function getUnitPrice($tva = false)
    {
        $price = $this->price;

        $product = $this->getProduct();
        if (!$product instanceof ProductInterface) {
            return 0;
        }

        if ($tva) {
            $price = $price * (1 + $product->getVat() / 100);
        }

        return bcadd($price, 0, 2);
    }

    /**
     * Return the total (price * quantity)
     *
     * if $tva = true, return the price with vat
     *
     * @param boolean $tva
     * @return float
     */
    public function getTotal($tva = false)
    {

        return $this->getUnitPrice($tva) * $this->getQuantity();
    }

    /**
     * return the basket element options array
     *
     * @return array
     */
    public function getOptions()
    {

        return $this->options;
    }

    /**
     * return a option value depends on the $name
     *
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {

        if (!array_key_exists($name, $this->options)) {

            return null;
        }

        return $this->options[$name];
    }

    /**
     * Define the option value
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Define the price
     *
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Define the quantity
     *
     * @param float $price
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * return the quantity
     * 
     * @return int
     */
    public function getQuantity()
    {

        return $this->quantity;
    }

    /**
     * Check if the basket element is still valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $product = $this->getProduct();
        if (!$product instanceof ProductInterface) {

            return false;
        }

        if ($product->getEnabled() == false) {

            return false;
        }

        return true;
    }

    public function setDelete($delete)
    {
        $this->delete = $delete;
    }

    public function getDelete()
    {
        return $this->delete;
    }
}
