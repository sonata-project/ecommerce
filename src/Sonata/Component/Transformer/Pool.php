<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Transformer;

/**
 * The pool stored a group of available payment method
 *
 */
class Pool
{
    /**
     * @var array
     */
    protected $transformer = array();

    /**
     * add a delivery method into the pool
     *
     * @param  $instance
     * @return void
     */
    public function addTransformer($type, $instance)
    {
        $this->methods[$type] = $instance;
    }

    /**
     *
     * @return array of transformer methods
     */
    public function getTransformers()
    {
        return $this->methods;
    }

    /**
     * return a Transformer Object
     *
     * @param  string                                       $type
     * @return Sonata\Component\Transformer\BaseTransformer
     */
    public function getTransformer($type)
    {
        return isset($this->methods[$type]) ? $this->methods[$type] : null;
    }
}
