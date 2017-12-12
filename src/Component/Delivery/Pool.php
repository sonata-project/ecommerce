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

namespace Sonata\Component\Delivery;

/**
 * The pool stored a group of available delivery method.
 */
class Pool
{
    /**
     * @var array
     */
    protected $methods = [];

    /**
     * add a delivery method into the pool.
     *
     * @param ServiceDeliveryInterface $instance
     */
    public function addMethod(ServiceDeliveryInterface $instance): void
    {
        $this->methods[$instance->getCode()] = $instance;
    }

    /**
     * @return array of delivery methods
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * return a ServiceDeliveryInterface Object.
     *
     * @param string $code
     *
     * @return ServiceDeliveryInterface
     */
    public function getMethod($code)
    {
        return isset($this->methods[$code]) ? $this->methods[$code] : null;
    }
}
