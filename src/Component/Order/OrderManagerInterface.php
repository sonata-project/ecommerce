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

namespace Sonata\Component\Order;

use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\Doctrine\Model\PageableManagerInterface;
use Sonata\UserBundle\Model\UserInterface;

interface OrderManagerInterface extends ManagerInterface, PageableManagerInterface
{
    /**
     * Finds orders belonging to given user.
     *
     * @param UserInterface $user
     * @param array         $orderBy
     * @param int|null      $limit
     * @param int|null      $offset
     *
     * @return OrderInterface[]
     */
    public function findForUser(UserInterface $user, array $orderBy = [], $limit = null, $offset = null);

    /**
     * Return an Order from its id with its related OrderElements.
     *
     * @param int $orderId
     *
     * @return OrderInterface
     */
    public function getOrder($orderId);
}
