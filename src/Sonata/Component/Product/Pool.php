<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

/**
 * The pool stored a group of available payment method
 *
 */
class Pool
{
    protected $products = array();


    /**
     * add a delivery method into the pool
     *
     * @param  $instance
     * @return void
     */
    public function addProduct($definition)
    {
        $this->products[$definition['code']] = $definition;
    }

    /**
     *
     * @return array of delivery methods
     */
    public function getProductDefinitions()
    {

        return $this->products;
    }

    /**
     * return a Delivery Object
     *
     * @param  $code
     * @return array
     */
    public function getProductDefinition($code)
    {

        return isset($this->products[$code]) ? $this->products[$code] : null;
    }

    /**
     * @throws RuntimeException
     * @param  $product ProductInterface|product code|product class
     * @return array
     */
    public function getRepository($product)
    {
        // code
        if (is_string($product) && strpos($product, '\\') === false)
        {
            return $this->products[$product]['repository'];
        }

        if($product instanceof ProductInterface) {
            $class = get_class($product);
        } else {
            $class = $product;
        }


        foreach($this->products as $product)
        {
            if($class === $product['class'])
            {
                return $product['repository'];
            }
        }

        throw new RuntimeException(sprintf('No product repository defined for the class %s', $class));
    }
}