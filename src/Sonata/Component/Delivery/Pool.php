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
 * The pool stored a group of available delivery method
 *
 */
class Pool
{
    protected $methods = array();


    /**
     * add a delivery method into the pool
     *
     * @param  $instance
     * @return void
     */
    public function addMethod($instance)
    {
        $this->methods[$instance->getCode()] = $instance;
    }

    /**
     *
     * @return array of delivery methods
     */
    public function getMethods()
    {
        
        return $this->methods;
    }

    /**
     * return a Delivery Object
     *
     * @param  $code
     * @return Sonata\Component\Delivery\DeliveryInterface
     */
    public function getMethod($code)
    {
        
        return isset($this->methods[$code]) ? $this->methods[$code] : null;
    }
}