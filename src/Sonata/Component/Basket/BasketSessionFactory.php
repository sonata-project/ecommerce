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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BasketSessionFactory extends BaseBasketFactory
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
     * @param BasketManagerInterface    $basketManager
     * @param BasketBuilderInterface    $basketBuilder
     * @param CurrencyDetectorInterface $currencyDetector
     * @param SessionInterface          $session
     */
    public function __construct(BasketManagerInterface $basketManager, BasketBuilderInterface $basketBuilder, CurrencyDetectorInterface $currencyDetector, SessionInterface $session)
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
        $basket = $this->session->get($this->getSessionVarName($customer));

        if (!$basket) {
            $basket = $this->basketManager->create();
            $basket->setLocale($customer->getLocale());
            $basket->setCurrency($this->currencyDetector->getCurrency());
        }

        $basket->setCustomer($customer);

        $this->basketBuilder->build($basket);

        // always clone the basket so it can be only savec by calling
        // the save method
        return clone $basket;
    }

    /**
     * Save the basket
     *
     * @param  \Sonata\Component\Basket\BasketInterface $basket
     * @return void
     */
    public function save(BasketInterface $basket)
    {
        $this->session->set($this->getSessionVarName($basket->getCustomer()), $basket);
    }

    /**
     * Get the name of the session variable
     *
     * @param  \Sonata\Component\Customer\CustomerInterface $customer
     * @return string
     */
    protected function getSessionVarName(CustomerInterface $customer)
    {
        return sprintf('sonata/basket/factory/customer/%s', $customer->getId() ?: 'new');
    }
}
