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
use Sonata\Component\Product\ProductDefinition;

class BasketElement implements \Serializable, BasketElementInterface
{
    /**
     * @var int
     */
    protected $productId = null;

    /**
     * @var ProductInterface
     */
    protected $product = null;

    /**
     * @var float
     */
    protected $price = null;

    /**
     * @var int
     */
    protected $quantity = 1;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var int
     */
    protected $position = null;

    /**
     * @var ProductDefinition
     */
    protected $productDefinition = null;

    /**
     * @var string
     */
    protected $productCode = null;

    /*
     * used by the validation framework
     *
     * @var bool
     */
    protected $delete = false;

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($productCode, ProductInterface $product)
    {
        $this->product      = $product;
        $this->productId    = $product->getId();
        $this->productCode  = $productCode;
        $this->name         = $product->getName();
        $this->price        = $product->getPrice();
        $this->options      = $product->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        if ($this->product == null && $this->hasProductDefinition()) {
            $this->product = $this->getProductDefinition()->getManager()->findOneBy(array('id' => $this->productId));
        }

        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        if ($productId == $this->productId) {
            return;
        }

        $product = $this->getProductDefinition()->getManager()->findOneBy(array('id' => $productId));

        if (!$product) {
            $this->productId = null;

            return;
        }

        $this->getProductDefinition()->getProvider()->buildBasketElement($this, $product);
    }

    /**
     * {@inheritdoc}
     */
    public function getVatAmount()
    {
        $tva = $this->getTotal(true) - $this->getTotal();

        return bcadd($tva, 0, 2);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getTotal($tva = false)
    {
        return $this->getUnitPrice($tva) * $this->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name, $default = null)
    {
        if (!array_key_exists($name, $this->options)) {
            return $default;
        }

        return $this->options[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity > 0 ? $quantity : 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            'productId'   => $this->productId,
            'position'    => $this->position,
            'price'       => $this->price,
            'quantity'    => $this->quantity,
            'options'     => $this->options,
            'name'        => $this->name,
            'productCode' => $this->productCode
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        $this->productId    = $data['productId'];
        $this->position     = $data['position'];
        $this->price        = $data['price'];
        $this->quantity     = $data['quantity'];
        $this->options      = $data['options'];
        $this->name         = $data['name'];
        $this->productCode  = $data['productCode'];
    }

    /**
     * {@inheritdoc}
     */
    public function getProductManager()
    {
        return $this->productDefinition->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductProvider()
    {
        return $this->productDefinition->getProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function setProductDefinition(ProductDefinition $productDefinition)
    {
        $this->productDefinition = $productDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductDefinition()
    {
        return $this->productDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductDefinition()
    {
        return $this->productDefinition instanceof ProductDefinition;
    }
}
