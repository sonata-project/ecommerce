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

    /**
     * {@inheritdoc}
     */
    public function setProductPool(Pool $pool)
    {
        $this->productPool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductPool()
    {
        return $this->productPool;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return 0 === \count($this->getBasketElements());
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function setDeliveryMethod(ServiceDeliveryInterface $method = null)
    {
        $this->deliveryMethod = $method;
        $this->deliveryMethodCode = $method ? $method->getCode() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryAddress(AddressInterface $address = null)
    {
        $this->deliveryAddress = $address;
        $this->deliveryAddressId = $address ? $address->getId() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethod(PaymentInterface $method = null)
    {
        $this->paymentMethod = $method;
        $this->paymentMethodCode = $method ? $method->getCode() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $address = null)
    {
        $this->billingAddress = $address;
        $this->billingAddressId = $address ? $address->getId() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function reset($full = true)
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

    /**
     * {@inheritdoc}
     */
    public function getBasketElements()
    {
        return $this->basketElements;
    }

    /**
     * {@inheritdoc}
     */
    public function setBasketElements($basketElements)
    {
        $this->basketElements = [];
        foreach ($basketElements as $basketElement) {
            $this->addBasketElement($basketElement);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function countBasketElements()
    {
        return \count($this->basketElements);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBasketElements()
    {
        return $this->countBasketElements() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getElement(ProductInterface $product)
    {
        if (!$this->hasProduct($product)) {
            throw new \RuntimeException('The product does not exist');
        }

        $pos = $this->positions[$product->getId()];

        return $this->getElementByPos($pos);
    }

    /**
     * {@inheritdoc}
     */
    public function getElementByPos($pos)
    {
        return isset($this->basketElements[$pos]) ? $this->basketElements[$pos] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function removeElements(array $elementsToRemove)
    {
        $this->inBuild = true;
        foreach ($elementsToRemove as $element) {
            $this->removeBasketElement($element);
        }
        $this->buildPrices();
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement(BasketElementInterface $element)
    {
        return $this->removeBasketElement($element);
    }

    /**
     * {@inheritdoc}
     */
    public function addBasketElement(BasketElementInterface $basketElement)
    {
        $basketElement->setPosition($this->cptElement);

        $this->basketElements[$this->cptElement] = $basketElement;
        $this->positions[$basketElement->getProduct()->getId()] = $this->cptElement;

        ++$this->cptElement;

        $this->buildPrices();
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getDeliveryPrice($vat = false)
    {
        $method = $this->getDeliveryMethod();

        if (!$method instanceof ServiceDeliveryInterface) {
            return 0;
        }

        return $method->getTotal($this, $vat);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryVat()
    {
        $method = $this->getDeliveryMethod();

        if (!$method instanceof ServiceDeliveryInterface) {
            return 0;
        }

        return $method->getVatRate();
    }

    /**
     * {@inheritdoc}
     */
    public function hasProduct(ProductInterface $product)
    {
        if (!array_key_exists($product->getId(), $this->positions)) {
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

    /**
     * {@inheritdoc}
     */
    public function buildPrices()
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

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        $elementsToRemove = [];
        foreach ($this->getBasketElements() as $basketElement) {
            if ($basketElement->getDelete() || 0 === $basketElement->getQuantity()) {
                $elementsToRemove[] = $basketElement;
            }
        }
        $this->removeElements($elementsToRemove);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->getSerializationFields());
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        $properties = $this->getUnserializationFields();

        foreach ($properties as $property) {
            $this->$property = $data[$property] ?? $this->$property;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryAddressId($deliveryAddressId)
    {
        $this->deliveryAddressId = $deliveryAddressId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryAddressId()
    {
        return $this->deliveryAddressId;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddressId($billingAddressId)
    {
        $this->billingAddressId = $billingAddressId;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddressId()
    {
        return $this->billingAddressId;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethodCode($paymentMethodCode)
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryMethodCode($deliveryMethodCode)
    {
        $this->deliveryMethodCode = $deliveryMethodCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryMethodCode()
    {
        return $this->deliveryMethodCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(CustomerInterface $customer = null)
    {
        $this->customer = $customer;
        $this->customerId = $customer ? $customer->getId() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->customerId;
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
    public function setOptions($options)
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
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(CurrencyInterface $currency)
    {
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getPositions()
    {
        return $this->positions;
    }
}
