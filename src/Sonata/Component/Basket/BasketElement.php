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

    protected $productId = null;

    protected $product = null;

    protected $price = null;

    protected $quantity = 1;

    protected $options = array();

    protected $name = null;

    protected $pos = null;

    protected $productRepository = null;

    protected $productCode = null;

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
    public function setProduct(ProductInterface $product, $productRepository)
    {

        $this->product      = $product;
        $this->productId    = $product->getId();
        $this->productCode  = $productRepository->getClassMetadata()->discriminatorValue;
        $this->name         = $product->getName();
        $this->price        = $product->getPrice();
        $this->options      = $product->getOptions();

        $this->productRepository = $productRepository;
        
        return $this;
    }

    /**
     * Return the related product
     *
     * @return Product
     */
    public function getProduct()
    {

        if ($this->product == null && $this->getProductRepository())
        {
            $this->product      = $this->getProductRepository()->findOneById($this->productId);
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
        return $this->productId;
    }

    /**
     * Never call this method, use the setProduct instead. This method is only used
     * by the form framework
     * 
     * @param  $productId
     * @return void
     */
    public function setProductId($productId)
    {
        if (!$this->getProduct()) {
            $this->productId = $productId;
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
            'productId'   => $this->productId,
            'pos'         => $this->pos,
            'price'       => $this->price,
            'quantity'    => $this->quantity,
            'options'     => $this->options,
            'name'        => $this->name,
            'productCode' => $this->productCode,
        ));
    }

    public function unserialize($data)
    {

        $data = unserialize($data);

        $this->productId    = $data['productId'];
        $this->pos          = $data['pos'];
        $this->price        = $data['price'];
        $this->quantity     = $data['quantity'];
        $this->options      = $data['options'];
        $this->name         = $data['name'];
        $this->productCode  = $data['productCode'];
    }

    public function setProductRepository($productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProductRepository()
    {
        return $this->productRepository;
    }

    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
    }

    public function getProductCode()
    {
        return $this->productCode;
    }
}
