<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Model;

use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Delivery\DeliveryInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketElement;

use Symfony\Component\Form\FormBuilder;

use Application\Sonata\OrderBundle\Entity\OrderElement;

abstract class BaseProductProvider implements ProductProviderInterface
{
    protected $options           = array();

    protected $variationFields   = array();

    protected $code;

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return array|null
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    ////////////////////////////////////////////////
    //   ORDER RELATED FUNCTIONS

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return \Application\Sonata\OrderBundle\Entity\OrderElement
     */
    public function createOrderElement(BasketElementInterface $basketElement)
    {
        $product = $basketElement->getProduct();

        $orderElement = new OrderElement;
        $orderElement->setQuantity($basketElement->getQuantity());
        $orderElement->setPrice($basketElement->getTotal(false));
        $orderElement->setVat($basketElement->getVat());
        $orderElement->setDesignation($basketElement->getName());
        $orderElement->setDescription($product->getDescription());
        $orderElement->setSerialize(null);
        $orderElement->setProductId($product->getId());
        $orderElement->setProductType($this->getProductType());
        $orderElement->setStatus(OrderInterface::STATUS_PENDING);
        $orderElement->setDeliveryStatus(DeliveryInterface::STATUS_OPEN);
        $orderElement->setCreatedAt(new \DateTime);

        // todo : create a serialized version of the product element
        $orderElement->setSerialize(array('todo'));

        // we save product information
//        foreach ($product->toArray(false) as $name => $value)
//        {
//          if (is_null($value) || strlen(trim($value)) == 0)
//          {
//            continue;
//          }
//
//          $orderElement_option = new OrderElementOption;
//          $orderElement_option->setName('product_'.$name);
//          $orderElement_option->setValue($value);
//
//          $orderElement->addOption($orderElement_option);
//        }

//        $orderElement_option = new OrderElementOption;
//        $orderElement_option->setName('product_is_recurrent');
//        $orderElement_option->setValue($product->isRecurrentPayment() ? '1' : '0');

//        // we save basketElement options
//        foreach ($basketElement->getOptions() as $name => $value)
//        {
//          $orderElement_option = new OrderElementOption;
//          $orderElement_option->setName($name);
//          $orderElement_option->setValue($value);
//
//          $orderElement->addOption($orderElement_option);
//        }

        return $orderElement;
    }


    ////////////////////////////////////////////////
    //   VARIATION RELATED FUNCTIONS

    /**
     * @param  $name
     * @return bool return true if the field $name is a variation
     */
    public function isVariateBy($name)
    {
        return in_array($name, $this->variationFields);
    }

    /**
     * @return bool return true if the product haas some variation fields
     */
    public function hasVariationFields()
    {
        return count($this->getVariationFields()) > 0;
    }

    /**
     * @param array $fields
     * @return void
     */
    public function setVariationFields(array $fields = array())
    {
        $this->variationFields = $fields;
    }

    /**
     * @return array
     */
    public function getVariationFields()
    {
        return $this->variationFields;
    }

    /**
     * @throws \RuntimeException
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function createVariation(ProductInterface $product)
    {
        if ($product->isVariation()) {
            throw \RuntimeException('Cannot create a variation from a variation product');
        }

        $variation = clone $product;
        $variation->setParent($product);
        $variation->setId(null);
        $variation->setEnabled(false);
        $variation->setName(sprintf('%s (duplicated)', $product->getName()));

        return $variation;
    }

    /**
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param string $name
     * @param bool $forceCopy
     * @return
     */
    public function copyVariation(ProductInterface $product, $name = 'all', $forceCopy = false)
    {
        if ($product->isVariation()) {
            return;
        }

        switch ($name) {
            case 'product':
                $this->copyProductVariation($product, $forceCopy);
                return;

            case 'all':
                $this->copyProductVariation($product, $forceCopy);
                return;
        }
    }

    /**
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param bool $forceCopy
     * @return void
     */
    public function copyProductVariation(ProductInterface $product, $forceCopy = false)
    {
        $variationFields = array_merge(array('id'), $this->getVariationFields());

        // fields to copy
        $values = array(
            'Name'    => $product->getName(),
            'Price'   => $product->getPrice(),
            'Vat'     => $product->getVat(),
            'Enabled' => $product->getEnabled()
        );

        if (!$forceCopy) {
            foreach ($variationFields as $field) {

                if (!array_key_exists($field, $values)) {
                   continue;
                }

                unset($values[$field]);
            }
        }

        foreach ($product->getVariations() as $variation) {
            foreach ($values as $name => $value) {
                call_user_func(array($variation, 'set'.$name), $value );
            }
        }
    }

    /////////////////////////////////////////////////////
    // BASKET RELATED FUNCTIONS

    /**
     * This function return the form used in the product view page
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param \Symfony\Component\Form\FormBuilder $formBuilder
     * @param array $options
     * @return void
     */
    public function defineAddBasketForm(ProductInterface $product, FormBuilder $formBuilder, array $options = array())
    {
        // create the product form
        $formBuilder
            ->setData(array(
                'quantity'   => 1,
                'productId'  => $product->getId()
            ))
            ->add('quantity', 'text')
            ->add('productId', 'hidden');
    }


    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $product
     * @param \Symfony\Component\Form\FormBuilder $formBuilder
     * @param array $options
     * @return void
     */
    public function defineBasketElementForm(BasketElementInterface $basketElement, FormBuilder $formBuilder, array $options = array())
    {
        $formBuilder
            ->setData(array(
                'quantity' => $basketElement->getQuantity(),
                'id'       => $basketElement->getProductId()
            ))
            ->add('delete', 'checkbox')
            ->add('quantity', 'text')
            ->add('id', 'hidden');
    }

    /**
     * return an array of errors if any, you can also manipulate the basketElement if require
     * please not you always work with a clone version of the basketElement.
     *
     * If the basket is valid it will then replace the one in session
     *
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return array
     */
    public function validateFormBasketElement(BasketElementInterface $basketElement)
    {
        // initialize the errors array
        $errors = array(
            'global' => false,    // global error, ie the basket element is not valid anymore
            'fields' => array(),  // error per field
        );

        // the item is flagged as deleted, no need to validate the item
        if ($basketElement->getDelete()) {

            return $errors;
        }

        // refresh the product from the database
        $product = $basketElement->getProduct();

        // check if the product is still enabled
        if (!$product) {
            $errors['global'] = array(
                'The product is not available anymore',
                array(),
                null
            );

            return $errors;
        }

        // check if the product is still enabled
        if (!$basketElement->getProduct()->isEnabled()) {
            $errors['global'] = array(
                'The product is not enabled anymore',
                array(),
                null
            );

            return $errors;
        }

        // check if the quantity is numeric
        if (!is_numeric($basketElement->getQuantity())) {
            $errors['fields']['quantity'] = array(
                'The product quantity is not a numeric value',
                array('{{ quantity }}' => $basketElement->getQuantity()),
                $basketElement->getQuantity() // todo : not sure about the third element
            );

            return $errors;
        }

        // check if the product is still available
        if ($this->getStockAvailable($basketElement->getProduct()) < $basketElement->getQuantity()) {
            $errors['fields']['quantity'] = array(
                'The product quantity ({{ quantity }}) is not valid',
                array('{{ quantity }}' => $basketElement->getQuantity()),
                $basketElement->getQuantity() // todo : not sure about the third element
            );
        }

        // add here your own validation check

        return $errors;
    }

    /**
     * Returns true if the basket element is still valid
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array $values
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    public function basketAddProduct(BasketInterface $basket, ProductInterface $product, array $values = array())
    {
        if ($basket->hasProduct($product)) {
            return false;
        }

        $basketElement = new BasketElement;

        if ($values instanceof OrderElementInterface) {
            // restore the basketElement from an order element
            // ie: an error occur during the payment process
            throw new \RuntimeException('not implemented');
        } else if(is_array($values)) {
            $basketElement->setProduct($this->getCode(), $product);
            $basketElement->setQuantity($values['quantity']);
        } else {
            throw new \RuntimeException('invalid data');
        }

        $basketElementOptions = $product->getOptions();
        // add the default product options to the basket element
        if (is_array($basketElementOptions) && !empty($basketElementOptions)) {
            foreach ($basketElementOptions as $option => $value) {
                $basketElement->setOption($option, $value);
            }
        }

        $basket->addBasketElement($basketElement);

        return $basketElement;
    }


    /**
     * Merge a product with another when the product is already present into the basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array $values
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    public function basketMergeProduct(BasketInterface $basket, ProductInterface $product, array $values = array())
    {
        if (!$basket->hasProduct($product)) {
            return false;
        }

        $basketElement = $basket->getElement($product);
        if (!$basketElement) {
            throw new \RuntimeExeption('no basket element related to product.id : %s', $product->getId());
        }

        $basketElement->setQuantity($basketElement->getQuantity() + $values['quantity']);

        return $basketElement;
    }

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     *
     * @return boolean true if the basket element is still valid
     */
    public function isValidBasketElement(BasketElementInterface $basketElement)
    {
        $product = $basketElement->getProduct();

        if (!$product instanceof ProductInterface) {
            return false;
        }

        if (!$product->isValid()) {
            return false;
        }

        return true;
    }

    /**
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     *
     * @return float price of the basket element
     */
    public function basketCalculatePrice(BasketInterface $basket, BasketElementInterface $basketElement)
    {
        return $basketElement->getProduct()->getPrice();
    }

    /**
     * Return true if the product can be added to the provided basket
     *
     * @abstract
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array $options
     * @return boolean
     */
    public function isAddableToBasket(BasketInterface $basket, ProductInterface $product, array $options = array())
    {
        return true;
    }

    /**
     * return a fresh product instance (so information are reloaded: enabled and stock ...)
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function reloadProduct(ProductInterface $product)
    {
        return $this->findOneById($product->getId());
    }

    /**
     * @param integer $id
     * @return bool
     */
    public function findOneById($id)
    {
        $results = $this->createQueryBuilder('p')
            ->addSelect('i')
            ->leftJoin('p.image', 'i')
            ->andWhere('p.id = :id')
            ->getQuery()
            ->setParameters(array('id' => $id))
            ->setMaxResults(1)
            ->execute();

        return count($results) > 0 ? $results[0] : false;
    }

    /**
     * return the stock available for the current product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return int the stock available
     */
    public function getStockAvailable(ProductInterface $product)
    {
        return $product->getStock();
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }
}