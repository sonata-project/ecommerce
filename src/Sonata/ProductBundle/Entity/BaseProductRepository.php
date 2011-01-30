<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\ProductInterface as Product;
use Sonata\Component\Basket\BasketElement;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Delivery\DeliveryInterface;


class BaseProductRepository extends \Doctrine\ORM\EntityRepository
{

    protected $options            = array();

    protected $variationFields   = array();


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

    public function getProductType()
    {
        return $this->getClassMetadata()->discriminatorValue;
    }

    ////////////////////////////////////////////////
    //   ORDER RELATED FUNCTIONS

    public function createOrderElement($basketElement)
    {
        $product = $basketElement->getProduct();

        $orderElement = new \Application\Sonata\OrderBundle\Entity\OrderElement;
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
//        foreach($product->toArray(false) as $name => $value)
//        {
//          if(is_null($value) || strlen(trim($value)) == 0)
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
//        foreach($basketElement->getOptions() as $name => $value)
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

    public function setVariationFields($fields)
    {

        $this->variationFields = $fields;
    }

    public function getVariationFields()
    {

        return $this->variationFields;
    }

    public function createVariation($product)
    {

        if($product->isVariation()) {

            throw \RuntimeException('Cannot create a variation from a variation product');
        }

        $variation = clone $product;
        $variation->setParent($product);
        $variation->id = null;
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

        $variationFields = array_merge(array('id'), $this->getVariationFields());

        // fields to copy
        $values = array(
            'Name'    => $product->getName(),
            'Price'   => $product->getPrice(),
            'Vat'     => $product->getVat(),
            'Enabled' => $product->getEnabled()
        );

        if(!$force_copy) {

            foreach($variationFields as $field) {

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
     * @return Application\Sonata\ProductBundle\Products\Bottle\BottleAddBasketForm
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
     * return an array of errors if any, you can also manipulate the basketElement if require
     * please not you always work with a clone version of the basketElement.
     *
     * If the basket is valid it will then replace the one in session
     *
     * @param  $basketElement
     * @return array
     */
    public function validateFormBasketElement($basketElement)
    {

        // initialize the errors array
        $errors = array(
            'global' => false,    // global error, ie the basket element is not valid anymore
            'fields' => array(),  // error per field
        );

        // the item is flagged as deleted, no need to validate the item
        if($basketElement->getDelete()) {

            return $errors;
        }

        // refresh the product from the database
        $product = $basketElement->getProduct();

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
        if(!$basketElement->getProduct()->isEnabled()) {
            $errors['global'] = array(
                'The product is not enabled anymore',
                array(),
                null
            );

            return $errors;
        }

        // check if the quantity is numeric
        if(!is_numeric($basketElement->getQuantity())) {
            $errors['fields']['quantity'] = array(
                'The product quantity is not a numeric value',
                array('{{ quantity }}' => $basketElement->getQuantity()),
                $basketElement->getQuantity() // todo : not sure about the third element
            );

            return $errors;
        }

        // check if the product is still available
        if($this->getStockAvailable($basketElement->getProduct()) < $basketElement->getQuantity()) {
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
        
        $basketElement = new $class;
        $basketElement->setProduct($product, $this);
        $basketElement->setQuantity($values->getQuantity());

        if($values instanceof \Application\Sonata\OrderBundle\Entity\OrderElement) {
            // restore the basketElement from an order element
            // ie: an error occur during the payment process

            // tweak the code here
        } else {
            // create a new basket element from the product

            // tweak the code here
        }

        $basketElement_options = $product->getOptions();
        // add the default product options to the basket element
        if (is_array($basketElement_options) && !empty($basketElement_options)) {

            foreach ($basketElement_options as $option => $value) {
                $basketElement->setOption($option, $value);
            }

        }

        $basket->addBasketElement($basketElement);

        return $basketElement;
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

        $basketElement = $basket->getElement($product);
        $basketElement->setQuantity($basketElement->getQuantity() + $values->getQuantity());

        return $basketElement;
    }

    /**
     * @abstract
     * @param BasketElement $basketElement
     *
     * @return boolean true if the basket element is still valid
     */
    public function isValidBasketElement($basketElement)
    {
        $product = $basketElement->getProduct();

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
     * @param BasketElement $basketElement
     *
     * @return float price of the basket element
     */
    public function basketCalculatePrice($basket, $basketElement)
    {

        return $basketElement->getProduct()->getPrice();
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