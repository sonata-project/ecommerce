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

namespace Sonata\Component\Event;

use Sonata\Component\Order\OrderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderTransformEvent extends Event
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @param OrderInterface $order
     */
    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }
}
