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

use Symfony\Component\HttpFoundation\Session\Session;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Currency\CurrencyDetectorInterface;

class BasketEntityFactory implements BasketFactoryInterface
{
    /**
     * @var \Sonata\Component\Basket\BasketManagerInterface
     */
    protected $basketManager;

    /**
     * @var \Sonata\Component\Basket\BasketBuilderInterface
     */
    protected $basketBuilder;

    /**
     * @var \Sonata\Component\Currency\CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @param \Sonata\Component\Basket\BasketManagerInterface      $basketManager
     * @param \Sonata\Component\Basket\BasketBuilderInterface      $basketBuilder
     * @param \Sonata\Component\Currency\CurrencyDetectorInterface $currencyDetector
     * @param \Symfony\Component\HttpFoundation\Session\Session    $session
     */
    public function __construct(BasketManagerInterface $basketManager, BasketBuilderInterface $basketBuilder, CurrencyDetectorInterface $currencyDetector, Session $session)
    {
        $this->basketManager    = $basketManager;
        $this->basketBuilder    = $basketBuilder;
        $this->currencyDetector = $currencyDetector;
        $this->session          = $session;
    }

    /**
     * Load the basket
     *
     * @param  \Sonata\Component\Customer\CustomerInterface $customer
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function load(CustomerInterface $customer)
    {
        $basket = null;

        if ($customer->getId()) {
            $basket = $this->basketManager->loadBasketPerCustomer($customer);
        }

        if (!$basket) {
            $basket = $this->basketManager->create();
            $basket->setLocale($customer->getLocale());
            $basket->setCurrency($this->currencyDetector->getCurrency());
        }

        $basket->setCustomer($customer);

        $this->basketBuilder->build($basket);

        return $basket;
    }

    /**
     * Save the basket
     *
     * @param  \Sonata\Component\Basket\BasketInterface $basket
     * @return void
     */
    public function save(BasketInterface $basket)
    {
        if ($basket->getCustomerId()) {
            $this->basketManager->save($basket);
        } else {
            $this->session->set('sonata/basket/factory/customer/new', $basket);
        }
    }
}
