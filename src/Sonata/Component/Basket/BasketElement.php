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

class BasketElement implements \Serializable
{

    protected $product_id = null;

    protected $product = null;

    protected $price = null;

    protected $quantity = 1;

    protected $options = array();

    protected $name = null;

    protected $pos = null;

    protected $product_repository = null;

    protected $product_code = null;

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
     * Define the related product
     *
     * @param Product $product
     * @return BasketElement
     */
    public function setProduct(ProductInterface $product, $product_repository)
    {

        $this->product      = $product;
        $this->product_id   = $product->getId();
        $this->product_code = $product_repository->getClassMetadata()->discriminatorValue;
        $this->name         = $product->getName();
        $this->price        = $product->getPrice();
        $this->options      = $product->getOptions();

        $this->product_repository = $product_repository;
        
        return $this;
    }

    /**
     * Return the related product
     *
     * @return Product
     */
    public function getProduct()
    {

        if($this->product == null && $this->getProductRepository())
        {
            $this->product      = $this->getProductRepository()->findOneById($this->product_id);
        }
        
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

    public function serialize()
    {

        return serialize(array(
            'product_id' => $this->product_id,
            'pos'        => $this->pos,
            'price'      => $this->price,
            'quantity'   => $this->quantity,
            'options'    => $this->options,
            'name'       => $this->name,
            'product_code' => $this->product_code,
        ));
    }

    public function unserialize($data)
    {

        $data = unserialize($data);

        $this->product_id   = $data['product_id'];
        $this->pos          = $data['pos'];
        $this->price        = $data['price'];
        $this->quantity     = $data['quantity'];
        $this->options      = $data['options'];
        $this->name         = $data['name'];
        $this->product_code = $data['product_code'];
    }

    public function setProductRepository($product_repository)
    {
        $this->product_repository = $product_repository;
    }

    public function getProductRepository()
    {
        return $this->product_repository;
    }

    public function setProductCode($product_code)
    {
        $this->product_code = $product_code;
    }

    public function getProductCode()
    {
        return $this->product_code;
    }
}
