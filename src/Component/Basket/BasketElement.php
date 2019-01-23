<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductInterface;

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
    protected $unitPrice = null;

    /**
     * @var float
     */
    protected $price = null;

    /**
     * @var bool
     */
    protected $priceIncludingVat;

    /**
     * @var float
     */
    protected $vatRate = null;

    /**
     * @var int
     */
    protected $quantity = 1;

    /**
     * @var array
     */
    protected $options = [];

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
        $this->product = $product;
        $this->productId = $product->getId();
        $this->productCode = $productCode;
        $this->name = $product->getName();
        $this->price = $product->getPrice();
        $this->options = $product->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        if (null === $this->product && $this->hasProductDefinition()) {
            $this->product = $this->getProductDefinition()->getManager()->findOneBy(['id' => $this->productId]);
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
        if ($productId === $this->productId) {
            return;
        }

        $product = $this->getProductDefinition()->getManager()->findOneBy(['id' => $productId]);

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
        return bcsub($this->getTotal(true), $this->getTotal(false));
    }

    /**
     * {@inheritdoc}
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice($vat = false)
    {
        $price = (string) $this->unitPrice;

        if (!$vat && $this->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv((string) $this->getVatRate(), '100')));
        }

        if ($vat && !$this->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd('1', bcdiv((string) $this->getVatRate(), '100')));
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal($vat = false)
    {
        return bcmul((string) $this->getUnitPrice($vat), (string) $this->getQuantity(), 100);
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
    public function setOptions(array $options = [])
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
    public function getPrice($vat = false)
    {
        $price = $this->price;

        if (!$vat && true === $this->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv($this->getVatRate(), '100')));
        }

        if ($vat && false === $this->isPriceIncludingVat()) {
            $price = bcmul((string) $price, (string) bcadd('1', bcdiv($this->getVatRate(), '100')));
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceIncludingVat($priceIncludingVat)
    {
        $this->priceIncludingVat = $priceIncludingVat;
    }

    /**
     * {@inheritdoc}
     */
    public function isPriceIncludingVat()
    {
        return $this->priceIncludingVat;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity >= 0 ? $quantity : 1;
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

        if (false === $product->getEnabled()) {
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
        return serialize([
            'productId' => $this->productId,
            'position' => $this->position,
            'unitPrice' => $this->unitPrice,
            'price' => $this->price,
            'priceIncludingVat' => $this->priceIncludingVat,
            'quantity' => $this->quantity,
            'vatRate' => $this->vatRate,
            'options' => $this->options,
            'name' => $this->name,
            'productCode' => $this->productCode,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        $this->productId = $data['productId'];
        $this->position = $data['position'];
        $this->unitPrice = $data['unitPrice'];
        $this->price = $data['price'];
        $this->priceIncludingVat = $data['priceIncludingVat'];
        $this->vatRate = $data['vatRate'];
        $this->quantity = $data['quantity'];
        $this->options = $data['options'];
        $this->name = $data['name'];
        $this->productCode = $data['productCode'];
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
