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

use Sonata\UserBundle\Model\UserInterface;

interface OrderManagerInterface
{
    /**
     * Creates an empty order instance
     *
     * @return OrderInterface
     */
    public function create();

    /**
     * Deletes a order
     *
     * @param  OrderInterface $order
     * @return void
     */
    public function delete(OrderInterface $order);

    /**
     * Finds one order by the given criteria
     *
     * @param  array          $criteria
     * @return OrderInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds orders belonging to given user
     *
     * @param UserInterface $user
     *
     * @return OrderInterface[]
     */
    public function findForUser(UserInterface $user);

    /**
     * Finds many orders by the given criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null  $limit
     * @param null  $offset
     *
     * @return mixed
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Returns the order's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a order
     *
     * @param  Order $order
     * @return void
     */
    public function save(OrderInterface $order);
}
