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

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class Basket implements \Serializable, BasketInterface
{
    /**
     * @var array
     */
    protected $basketElements;

    /**
     * @var array
     */
    protected $positions = [];

    /**
     * @var int
     */
    protected $cptElement = 0;

    /**
     * @var bool
     */
    protected $inBuild = false;

    /**
     * @var Pool
     */
    protected $productPool;

    /**
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * @var PaymentInterface
     */
    protected $paymentMethod;

    /**
     * @var string
     */
    protected $paymentMethodCode;

    /**
     * @var int
     */
    protected $billingAddressId;

    /**
     * @var AddressInterface
     */
    protected $deliveryAddress;

    /**
     * @var ServiceDeliveryInterface
     */
    protected $deliveryMethod;

    /**
     * @var int
     */
    protected $deliveryAddressId;

    /**
     * @var string
     */
    protected $deliveryMethodCode;

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var int
     */
    protected $customerId;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    public function __construct()
    {
        $this->basketElements = [];
    }

    public function setProductPool(Pool $pool): void
    {
        $this->productPool = $pool;
    }

    public function getProductPool()
    {
        return $this->productPool;
    }

    public function isEmpty()
    {
        return 0 === \count($this->getBasketElements());
    }

    public function isValid($elementsOnly = false)
    {
        if ($this->isEmpty()) {
            return false;
        }

        foreach ($this->getBasketElements() as $element) {
            if (false === $element->isValid()) {
                return false;
            }
        }

        if ($elementsOnly) {
            return true;
        }

        if (!$this->getBillingAddress() instanceof AddressInterface) {
            return false;
        }

        if (!$this->getPaymentMethod() instanceof PaymentInterface) {
            return false;
        }

        if (!$this->getDeliveryMethod() instanceof ServiceDeliveryInterface) {
            return false;
        }

        if (!$this->getDeliveryAddress() instanceof AddressInterface) {
            if ($this->getDeliveryMethod()->isAddressRequired()) {
                return false;
            }
        }

        return true;
    }

    public function setDeliveryMethod(ServiceDeliveryInterface $method = null): void
    {
        $this->deliveryMethod = $method;
        $this->deliveryMethodCode = $method ? $method->getCode() : null;
    }

    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    public function setDeliveryAddress(AddressInterface $address = null): void
    {
        $this->deliveryAddress = $address;
        $this->deliveryAddressId = $address ? $address->getId() : null;
    }

    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    public function setPaymentMethod(PaymentInterface $method = null): void
    {
        $this->paymentMethod = $method;
        $this->paymentMethodCode = $method ? $method->getCode() : null;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function setBillingAddress(AddressInterface $address = null): void
    {
        $this->billingAddress = $address;
        $this->billingAddressId = $address ? $address->getId() : null;
    }

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    public function isAddable(ProductInterface $product)
    {
        /*
        * We ask the product repository if it can be added to the basket
        */
        $isAddableBehavior = \call_user_func_array(
            [$this->getProductPool()->getProvider($product), 'isAddableToBasket'],
            array_merge([$this], \func_get_args())
        );

        return $isAddableBehavior;
    }

    public function reset($full = true): void
    {
        $this->deliveryAddressId = null;
        $this->deliveryAddress = null;
        $this->deliveryMethod = null;
        $this->deliveryMethodCode = null;

        $this->billingAddressId = null;
        $this->billingAddress = null;
        $this->paymentMethod = null;
        $this->paymentMethodCode = null;

        if ($full) {
            $this->basketElements = [];
            $this->positions = [];
            $this->cptElement = 0;
            $this->customerId = null;
            $this->customer = null;
            $this->options = [];
        }
    }

    public function getBasketElements()
    {
        return $this->basketElements;
    }

    public function setBasketElements($basketElements): void
    {
        $this->basketElements = [];
        foreach ($basketElements as $basketElement) {
            $this->addBasketElement($basketElement);
        }
    }

    public function countBasketElements()
    {
        return \count($this->basketElements);
    }

    public function hasBasketElements()
    {
        return $this->countBasketElements() > 0;
    }

    public function getElement(ProductInterface $product)
    {
        if (!$this->hasProduct($product)) {
            throw new \RuntimeException('The product does not exist');
        }

        $pos = $this->positions[$product->getId()];

        return $this->getElementByPos($pos);
    }

    public function getElementByPos($pos)
    {
        return isset($this->basketElements[$pos]) ? $this->basketElements[$pos] : null;
    }

    public function removeElements(array $elementsToRemove): void
    {
        $this->inBuild = true;
        foreach ($elementsToRemove as $element) {
            $this->removeBasketElement($element);
        }
        $this->buildPrices();
    }

    public function removeElement(BasketElementInterface $element)
    {
        return $this->removeBasketElement($element);
    }

    public function addBasketElement(BasketElementInterface $basketElement): void
    {
        $basketElement->setPosition($this->cptElement);

        $this->basketElements[$this->cptElement] = $basketElement;
        $this->positions[$basketElement->getProduct()->getId()] = $this->cptElement;

        ++$this->cptElement;

        $this->buildPrices();
    }

    public function removeBasketElement(BasketElementInterface $element)
    {
        $pos = $element->getPosition();

        --$this->cptElement;

        if ($element->getProduct()) {
            unset($this->positions[$element->getProduct()->getId()]);
        }

        unset($this->basketElements[$pos]);

        if (!$this->inBuild) {
            $this->buildPrices();
        }

        return $element;
    }

    public function hasRecurrentPayment()
    {
        foreach ($this->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            if ($product instanceof ProductInterface) {
                if (true === $product->isRecurrentPayment()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getTotal($vat = false, $recurrentOnly = null)
    {
        $total = '0';

        foreach ($this->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            if (true === $recurrentOnly && false === $product->isRecurrentPayment()) {
                continue;
            }

            if (false === $recurrentOnly && true === $product->isRecurrentPayment()) {
                continue;
            }

            $total = bcadd($total, (string) $basketElement->getTotal($vat));
        }

        $total = bcadd($total, (string) $this->getDeliveryPrice($vat));

        return $total;
    }

    public function getVatAmount()
    {
        $vat = '0';

        foreach ($this->getBasketElements() as $basketElement) {
            $vat = bcadd($vat, (string) $basketElement->getVatAmount());
        }

        $deliveryMethod = $this->getDeliveryMethod();

        if ($deliveryMethod instanceof ServiceDeliveryInterface) {
            $vat = bcadd($vat, (string) $deliveryMethod->getVatAmount($this));
        }

        return $vat;
    }

    public function getVatAmounts()
    {
        $amounts = [];

        foreach ($this->getBasketElements() as $basketElement) {
            $rate = $basketElement->getVatRate();
            $amount = (string) $basketElement->getVatAmount();

            if (isset($amounts[$rate])) {
                $amounts[$rate]['amount'] = bcadd($amounts[$rate]['amount'], $amount);
            } else {
                $amounts[$rate] = [
                    'rate' => $rate,
                    'amount' => $amount,
                ];
            }
        }

        return $amounts;
    }

    public function getDeliveryPrice($vat = false)
    {
        $method = $this->getDeliveryMethod();

        if (!$method instanceof ServiceDeliveryInterface) {
            return 0;
        }

        return $method->getTotal($this, $vat);
    }

    public function getDeliveryVat()
    {
        $method = $this->getDeliveryMethod();

        if (!$method instanceof ServiceDeliveryInterface) {
            return 0;
        }

        return $method->getVatRate();
    }

    public function hasProduct(ProductInterface $product)
    {
        if (!\array_key_exists($product->getId(), $this->positions)) {
            return false;
        }

        $pos = $this->positions[$product->getId()];

        foreach ($this->getBasketElements() as $basketElement) {
            if ($pos === $basketElement->getPosition()) {
                return true;
            }
        }

        return false;
    }

    public function buildPrices(): void
    {
        $this->inBuild = true;

        foreach ($this->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            if (!$product instanceof ProductInterface) {
                $this->removeElement($basketElement);

                continue;
            }

            if (!$this->getProductPool()) {
                throw new \RuntimeException('No product pool for basket');
            }

            $provider = $this->getProductPool()->getProvider($product);

            // BasketElement prices might depends on other basket elements
            $provider->updateComputationPricesFields($this, $basketElement, $product);
        }

        $this->inBuild = false;
    }

    public function clean(): void
    {
        $elementsToRemove = [];
        foreach ($this->getBasketElements() as $basketElement) {
            if ($basketElement->getDelete() || 0 === $basketElement->getQuantity()) {
                $elementsToRemove[] = $basketElement;
            }
        }
        $this->removeElements($elementsToRemove);
    }

    public function getSerializationFields()
    {
        $arrayRep = [
            'basketElements' => $this->getBasketElements(),
            'positions' => $this->positions,
            'paymentMethodCode' => $this->paymentMethodCode,
            'cptElement' => $this->cptElement,
            'deliveryMethodCode' => $this->deliveryMethodCode,
            'options' => $this->options,
            'locale' => $this->locale,
            'currency' => $this->currency,
        ];

        if (null !== $this->deliveryAddressId) {
            $arrayRep['deliveryAddressId'] = $this->deliveryAddressId;
        } elseif (null !== $this->deliveryAddress) {
            $arrayRep['deliveryAddress'] = $this->deliveryAddress;
        }

        if (null !== $this->billingAddressId) {
            $arrayRep['billingAddressId'] = $this->billingAddressId;
        } elseif (null !== $this->billingAddress) {
            $arrayRep['billingAddress'] = $this->billingAddress;
        }

        if (null !== $this->customerId) {
            $arrayRep['customerId'] = $this->customerId;
        } elseif (null !== $this->customer) {
            $arrayRep['customer'] = $this->customer;
        }

        return $arrayRep;
    }

    public function getUnserializationFields()
    {
        return [
            'basketElements',
            'positions',
            'deliveryAddress',
            'deliveryAddressId',
            'deliveryMethodCode',
            'billingAddress',
            'billingAddressId',
            'paymentMethodCode',
            'cptElement',
            'customer',
            'customerId',
            'options',
            'locale',
            'currency',
        ];
    }

    public function serialize()
    {
        return serialize($this->getSerializationFields());
    }

    public function unserialize($data): void
    {
        $data = unserialize($data);

        $properties = $this->getUnserializationFields();

        foreach ($properties as $property) {
            $this->$property = $data[$property] ?? $this->$property;
        }
    }

    public function setDeliveryAddressId($deliveryAddressId): void
    {
        $this->deliveryAddressId = $deliveryAddressId;
    }

    public function getDeliveryAddressId()
    {
        return $this->deliveryAddressId;
    }

    public function setBillingAddressId($billingAddressId): void
    {
        $this->billingAddressId = $billingAddressId;
    }

    public function getBillingAddressId()
    {
        return $this->billingAddressId;
    }

    public function setPaymentMethodCode($paymentMethodCode): void
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    public function setDeliveryMethodCode($deliveryMethodCode): void
    {
        $this->deliveryMethodCode = $deliveryMethodCode;
    }

    public function getDeliveryMethodCode()
    {
        return $this->deliveryMethodCode;
    }

    public function setCustomer(CustomerInterface $customer = null): void
    {
        $this->customer = $customer;
        $this->customerId = $customer ? $customer->getId() : null;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomerId($customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options): void
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

    public function setOption($name, $value): void
    {
        $this->options[$name] = $value;
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getPositions()
    {
        return $this->positions;
    }
}
