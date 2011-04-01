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


interface OrderElementManagerInterface
{

    /**
     * Creates an empty orderElement instance
     *
     * @return OrderElement
     */
    function createOrderElement();

    /**
     * Deletes a orderElement
     *
     * @param OrderElement $orderElement
     * @return void
     */
    function deleteOrderElement(OrderElementInterface $orderElement);

    /**
     * Finds one orderElement by the given criteria
     *
     * @param array $criteria
     * @return OrderElementInterface
     */
    function findOrderElementBy(array $criteria);

    /**
     * Returns the orderElement's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates an orderElement
     *
     * @param OrderElement $orderElement
     * @return void
     */
    function updateOrderElement(OrderElementInterface $orderElement);
}