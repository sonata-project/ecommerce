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
    public function createDelivery();

    /**
     * Deletes a delivery
     *
     * @param  DeliveryInterface $delivery
     * @return void
     */
    public function deleteDelivery(DeliveryInterface $delivery);

    /**
     * Finds one delivery by the given criteria
     *
     * @param  array             $criteria
     * @return DeliveryInterface
     */
    public function findDeliveryBy(array $criteria);

    /**
     * Returns the delivery's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a delivery
     *
     * @param  DeliveryInterface $delivery
     */
    public function updateDelivery(DeliveryInterface $delivery);
}
