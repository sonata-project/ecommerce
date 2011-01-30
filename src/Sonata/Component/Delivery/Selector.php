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
    protected $deliveryPool;

    protected $productPool;

    protected $logger;

    public function setDeliveryPool($deliveryPool)
    {
        $this->deliveryPool = $deliveryPool;
    }

    public function getDeliveryPool()
    {
        return $this->deliveryPool;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setProductPool($productPool)
    {
        $this->productPool = $productPool;
    }

    public function getProductPool()
    {
        return $this->product_pool;
    }

    public function getAvailableMethods($basket, $deliveryAddress)
    {
        $instances = array();

        // no address defined !
        if (!$deliveryAddress) {

            return false;
        }

        // STEP 1 : We get product's delivery methods
        foreach ($basket->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            if (!$product) {

                $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d does not exist', $basketElement->getProductId()));
                
                return false;
            }

            $product_deliveries = $product->getDelivery();


            foreach ($product_deliveries as $productDelivery) {

                // delivery method already selected
                if (array_key_exists($productDelivery->getCode(), $instances)) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s already selected', $basketElement->getProductId(), $productDelivery->getCode()));

                    continue;
                }
                
                $deliveryMethod = $this->getDeliveryPool()->getMethod($productDelivery->getCode());
                
                if (!$deliveryMethod) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code: %s does not exist', $basketElement->getProductId(), $productDelivery->getCode()));

                    continue;
                }

                // product delivery not enable
                if (!$deliveryMethod->getEnabled()) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s is not enabled', $basketElement->getProductId(), $productDelivery->getCode()));

                    continue;
                }

                // the product is not deliverable at the $shipping_address
                if ($deliveryAddress->getCountryCode() != $productDelivery->getCountryCode()) {

                    $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s the country code does not match (%s != %s)', $basketElement->getProductId(), $productDelivery->getCode(), $deliveryAddress->getCountryCode(), $productDelivery->getCountryCode()));
                    
                    continue;
                }

                $this->getLogger()->info(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s selected', $basketElement->getProductId(), $productDelivery->getCode()));
                
                $instances[$productDelivery->getCode()] = $deliveryMethod;
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