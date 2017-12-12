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

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\CustomerSelectorInterface;

class Loader
{
    /**
     * @var \Sonata\Component\Basket\BasketFactoryInterface
     */
    protected $basketFactory;

    /**
     * @var \Sonata\Component\Customer\CustomerSelectorInterface
     */
    protected $customerSelector;

    /**
     * @var \Sonata\Component\Basket\BasketInterface
     */
    protected $basket;

    /**
     * @param \Sonata\Component\Basket\BasketFactoryInterface      $basketFactory
     * @param \Sonata\Component\Customer\CustomerSelectorInterface $customerSelector
     */
    public function __construct(BasketFactoryInterface $basketFactory, CustomerSelectorInterface $customerSelector)
    {
        $this->basketFactory = $basketFactory;
        $this->customerSelector = $customerSelector;
    }

    /**
     * Get the basket.
     *
     * @throws \Exception|\RuntimeException
     *
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function getBasket()
    {
        if (!$this->basket) {
            try {
                $this->basket = $this->basketFactory->load($this->customerSelector->get());
            } catch (\Exception $e) {
                // something went wrong while loading the basket
                throw $e;
            }
        }

        return $this->basket;
    }
}
