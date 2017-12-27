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
     * @var BasketFactoryInterface
     */
    protected $basketFactory;

    /**
     * @var CustomerSelectorInterface
     */
    protected $customerSelector;

    /**
     * @var BasketInterface
     */
    protected $basket;

    /**
     * @param BasketFactoryInterface    $basketFactory
     * @param CustomerSelectorInterface $customerSelector
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
     * @return BasketInterface
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
