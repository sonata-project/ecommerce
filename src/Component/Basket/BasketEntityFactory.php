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

/**
 * Class BasketEntityFactory.
 *
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class BasketEntityFactory extends BaseBasketFactory
{
    /**
     * {@inheritdoc}
     */
    public function load(CustomerInterface $customer)
    {
        $sessionBasket = parent::load($customer);

        if ($customer->getId()) {
            $basket = $this->basketManager->loadBasketPerCustomer($customer);

            if (!$basket) {
                return $sessionBasket;
            }

            $this->basketBuilder->build($basket);

            if ($sessionBasket && !$sessionBasket->isEmpty()) {
                // Retrieve elements put in session before user logged in and replace db elements with it
                $basket->setBasketElements($sessionBasket->getBasketElements());
            }

            // Clear session to avoid retaking elements from session afterwards
            $this->clearSession($customer);

            // We need to ensure that both customer & customer id are set
            $basket->setCustomer($customer);

            return $basket;
        }

        return $sessionBasket;
    }

    /**
     * {@inheritdoc}
     */
    public function save(BasketInterface $basket)
    {
        if ($basket->getCustomerId()) {
            $this->basketManager->save($basket);
        } else {
            $this->storeInSession($basket);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset(BasketInterface $basket, $full = true)
    {
        if ($full && $basket->getCustomerId()) {
            $this->basketManager->delete($basket);
        } else {
            $basket->reset($full);
            $this->save($basket);
        }
    }
}
