<?php

namespace Sonata\Component\Basket;

use Symfony\Component\HttpFoundation\Session;
use Sonata\Component\Customer\CustomerInterface;

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
     * @var \Symfony\Component\HttpFoundation\Session
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
     * @param \Sonata\Component\Customer\CustomerInterface
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function load(CustomerInterface $customer)
    {
        $basket = null;

        if ($customer->getId()) {
            $basket = $this->basketManager->findOneBy(array(
                'customer' => $customer->getId()
            ));
        }

        if (!$basket) {
            $basket = $this->basketManager->create();
        }

        $basket->setCustomer($customer);

        $this->basketBuilder->build($basket);

        return $basket;
    }

    /**
     * Save the basket
     *
     * @param \Sonata\Component\Basket\BasketInterface
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
