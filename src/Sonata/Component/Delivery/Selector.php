<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Delivery;

/**
 * The selector selects available delivery methods depends on the provided basket
 *
 */
class Selector
{
    protected $delivery_pool;

    protected $product_pool;

    protected $logger;

    public function setDeliveryPool($delivery_pool)
    {
        $this->delivery_pool = $delivery_pool;
    }

    public function getDeliveryPool()
    {
        return $this->delivery_pool;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setProductPool($product_pool)
    {
        $this->product_pool = $product_pool;
    }

    public function getProductPool()
    {
        return $this->product_pool;
    }

    public function getAvailableMethods($basket, $delivery_address)
    {
        $instances = array();

        // no address defined !
        if (!$delivery_address) {

            return false;
        }

        // STEP 1 : We get product's delivery methods
        foreach ($basket->getElements() as $basket_element) {
            $product = $basket_element->getProduct();

            if (!$product) {

                $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d does not exist', $basket_element->getProductId()));
                
                return false;
            }

            $product_deliveries = $product->getDelivery();

            foreach ($product_deliveries as $product_delivery) {

                // delivery method already selected
                if (array_key_exists($product_delivery->getCode(), $instances)) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s already selected', $basket_element->getProductId(), $product_delivery->getCode()));

                    continue;
                }
                
                $delivery_method = $this->getDeliveryPool()->getMethod($product_delivery->getCode());
                
                if (!$delivery_method) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code: %s does not exist', $basket_element->getProductId(), $product_delivery->getCode()));

                    continue;
                }

                // product delivery not enable
                if (!$delivery_method->getEnabled()) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s is not enabled', $basket_element->getProductId(), $product_delivery->getCode()));

                    continue;
                }

                // the product is not deliverable at the $shipping_address
                if ($delivery_address->getCountryCode() != $product_delivery->getCountryCode()) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s the country code does not match (%s != %s)', $basket_element->getProductId(), $product_delivery->getCode(), $delivery_address->getCountryCode(), $product_delivery->getCountryCode()));
                    
                    continue;
                }

                $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s selected', $basket_element->getProductId(), $product_delivery->getCode()));
                
                $instances[$product_delivery->getCode()] = $delivery_method;
            }
        }

        // STEP 2 : We select the delivery methods with the highest priority
        $priority = 0;
        $final_instances = array();
        foreach ($instances as $instance) {
            if ($instance->getPriority() > $priority) {
                $final_instances = array();
                $priority = $instance->getPriority();
            }

            if ($priority == $instance->getPriority()) {
                $final_instances[] = $instance;
            }
        }

        return $final_instances;
    }
}