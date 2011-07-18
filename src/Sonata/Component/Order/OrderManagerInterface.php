<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Order;

interface OrderManagerInterface
{
    /**
     * Creates an empty order instance
     *
     * @return Order
     */
    function create();

    /**
     * Deletes a order
     *
     * @param Order $order
     * @return void
     */
    function delete(OrderInterface $order);

    /**
     * Finds one order by the given criteria
     *
     * @param array $criteria
     * @return OrderInterface
     */
    function findOneBy(array $criteria);

    /**
     * Finds many orders by the given criteria
     *
     * @param array $criteria
     * @return OrderInterface
     */
    function findBy(array $criteria);

    /**
     * Returns the order's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a order
     *
     * @param Order $order
     * @return void
     */
    function save(OrderInterface $order);
}