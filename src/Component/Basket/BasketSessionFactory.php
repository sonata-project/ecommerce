<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\CustomerInterface;

class BasketSessionFactory extends BaseBasketFactory
{
    /**
     * {@inheritdoc}
     */
    public function load(CustomerInterface $customer)
    {
        // always clone the basket so it can be only saved by calling
        // the save method
        return clone parent::load($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function save(BasketInterface $basket)
    {
        $this->storeInSession($basket);
    }

    /**
     * {@inheritdoc}
     */
    public function reset(BasketInterface $basket, $full = true)
    {
        if ($full) {
            $this->clearSession($basket->getCustomer());
        } else {
            $basket->reset($full);
            $this->save($basket);
        }
    }
}
