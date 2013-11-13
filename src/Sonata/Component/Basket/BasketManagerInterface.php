<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\CustomerInterface;

interface BasketManagerInterface
{
    /**
     * Creates an empty basket instance
     *
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function create();

    /**
     * Updates a basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     */
    public function save(BasketInterface $basket);

    /**
     * Finds one basket by the given criteria
     *
     * @param array $criteria
     *
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Returns the basket's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Finds many baskets by the given criteria
     *
     * @param array $criteria
     *
     * @return \Sonata\Component\Basket\BasketInterface[]
     */
    public function findBy(array $criteria);

    /**
     * Deletes a basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     */
    public function delete(BasketInterface $basket);

    /**
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     */
    public function loadBasketPerCustomer(CustomerInterface $customer);
}
