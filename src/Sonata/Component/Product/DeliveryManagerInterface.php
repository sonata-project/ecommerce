<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;


interface DeliveryManagerInterface
{

    /**
     * Creates an empty delivery instance
     *
     * @return Delivery
     */
    function createDelivery();

    /**
     * Deletes a delivery
     *
     * @param Delivery $delivery
     * @return void
     */
    function deleteDelivery(DeliveryInterface $delivery);

    /**
     * Finds one delivery by the given criteria
     *
     * @param array $criteria
     * @return DeliveryInterface
     */
    function findDeliveryBy(array $criteria);

    /**
     * Returns the delivery's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a delivery
     *
     * @param Delivery $delivery
     * @return void
     */
    function updateDelivery(DeliveryInterface $delivery);
}