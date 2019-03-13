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

    public function setPosition($position): void
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setProduct($productCode, ProductInterface $product): void
    {
        $this->product = $product;
        $this->productId = $product->getId();
        $this->productCode = $productCode;
        $this->name = $product->getName();
        $this->price = $product->getPrice();
        $this->options = $product->getOptions();
    }

    public function getProduct()
    {
        if (null === $this->product && $this->hasProductDefinition()) {
            $this->product = $this->getProductDefinition()->getManager()->findOneBy(['id' => $this->productId]);
        }

        return $this->product;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId): void
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

    public function getVatAmount()
    {
        return bcsub($this->getTotal(true), $this->getTotal(false));
    }

    public function setVatRate($vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    public function getVatRate()
    {
        return $this->vatRate;
    }

    public function setUnitPrice($unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

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

    public function getTotal($vat = false)
    {
        return bcmul((string) $this->getUnitPrice($vat), (string) $this->getQuantity(), 100);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options = []): void
    {
        $this->options = $options;
    }

    public function getOption($name, $default = null)
    {
        if (!\array_key_exists($name, $this->options)) {
            return $default;
        }

        return $this->options[$name];
    }

    public function hasOption($name)
    {
        return \array_key_exists($name, $this->options);
    }

    public function setOption($name, $value): void
    {
        $this->options[$name] = $value;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function getPrice($vat = false)
    {
        $price = (string) $this->price;

        if (!$vat && true === $this->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv((string) $this->getVatRate(), '100')));
        }

        if ($vat && false === $this->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd('1', bcdiv((string) $this->getVatRate(), '100')));
        }

        return $price;
    }

    public function setPriceIncludingVat($priceIncludingVat): void
    {
        $this->priceIncludingVat = $priceIncludingVat;
    }

    public function isPriceIncludingVat()
    {
        return $this->priceIncludingVat;
    }

    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity >= 0 ? $quantity : 1;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

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

    public function setDelete($delete): void
    {
        $this->delete = $delete;
    }

    public function getDelete()
    {
        return $this->delete;
    }

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

    public function unserialize($data): void
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

    public function getProductManager()
    {
        return $this->productDefinition->getManager();
    }

    public function getProductProvider()
    {
        return $this->productDefinition->getProvider();
    }

    public function setProductDefinition(ProductDefinition $productDefinition): void
    {
        $this->productDefinition = $productDefinition;
    }

    public function getProductDefinition()
    {
        return $this->productDefinition;
    }

    public function getProductCode()
    {
        return $this->productCode;
    }

    public function hasProductDefinition()
    {
        return $this->productDefinition instanceof ProductDefinition;
    }
}
