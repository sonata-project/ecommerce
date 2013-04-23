<?php

namespace Sonata\Component\Basket;

use Symfony\Component\HttpFoundation\Session\Session;
use Sonata\Component\Customer\CustomerInterface;

class BasketSessionFactory implements BasketFactoryInterface
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
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @param \Sonata\Component\Basket\BasketManagerInterface $basketManager
     * @param \Sonata\Component\Basket\BasketBuilderInterface $basketBuilder
     * @param \Symfony\Component\HttpFoundation\Session $session
     */
    public function __construct(BasketManagerInterface $basketManager, BasketBuilderInterface $basketBuilder, Session $session)
    {
        $this->basketManager = $basketManager;
        $this->basketBuilder = $basketBuilder;
        $this->session = $session;
    }

    /**
     * Load the basket
     *
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function load(CustomerInterface $customer)
    {
        $basket = $this->session->get($this->getSessionVarName($customer));

        if (!$basket) {
            $basket = $this->basketManager->create();
            $basket->setLocale($customer->getLocale());
            $basket->setCurrency('EUR'); // TODO : handle multiple concurrencies
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
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @return void
     */
    public function save(BasketInterface $basket)
    {
        $this->session->set($this->getSessionVarName($basket->getCustomer()), $basket);
    }

    /**
     * Get the name of the session variable
     *
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     * @return string
     */
    protected function getSessionVarName(CustomerInterface $customer)
    {
        return sprintf('sonata/basket/factory/customer/%s', $customer->getId() ?: 'new');
    }
}
