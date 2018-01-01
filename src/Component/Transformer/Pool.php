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

namespace Sonata\Component\Transformer;

/**
 * The pool stored a group of available payment method.
 */
class Pool
{
    /**
     * @var array
     */
    protected $transformer = [];

    /**
     * Add a transformer into into the pool.
     *
     * @param string          $type
     * @param BaseTransformer $instance
     */
    public function addTransformer($type, BaseTransformer $instance): void
    {
        $this->transformer[$type] = $instance;
    }

    /**
     * @return array
     */
    public function getTransformers()
    {
        return $this->transformer;
    }

    /**
     * return a Transformer Object.
     *
     * @param string $type
     *
     * @return BaseTransformer
     */
    public function getTransformer($type)
    {
        return isset($this->transformer[$type]) ? $this->transformer[$type] : null;
    }
}
