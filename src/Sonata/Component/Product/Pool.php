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
    protected $classes = array();

    protected $em = null;


    /**
     * add a delivery method into the pool
     *
     * @param  $instance
     * @return void
     */
    public function addProduct($definition)
    {
        $this->products[$definition['id']] = $definition;
        $this->classes[$definition['class']] = $definition['id'];
    }

    /**
     * define the entity manager
     * 
     * @return void
     */
    public function setEntityManager($em) {
        $this->em = $em;
    }

    /**
     * define the entity manager
     *
     * @return void
     */
    public function getEntityManager() {
        return $this->em;
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

    public function getProductCode($product)
    {
        $class = get_class($product);
        
        return isset($this->classes[$class]) ? $this->classes[$class] : null;
    }

    /**
     * @throws RuntimeException
     * @param  $product ProductInterface|product code|product class
     * @return array
     */
    public function getRepository($product)
    {
        // code
        if (is_string($product) && strpos($product, '\\') === false) {
            
            $class =  $this->products[$product]['class'];
        }
        else if($product instanceof ProductInterface) {

            $class = get_class($product);
        } else {
            
            $class = $product;
        }

        return $this->getEntityManager()->getRepository($class);
    }

    public function setClasses($classes)
    {
        $this->classes = $classes;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function setProducts($products)
    {
        $this->products = $products;
    }

    public function getProducts()
    {
        return $this->products;
    }
}