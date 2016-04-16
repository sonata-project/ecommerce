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

use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Customer\CustomerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @todo Refacto (add an abstract class with the properties & constructor)
 */
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
     * {@inheritdoc}
     */
    public function load(CustomerInterface $customer)
    {
        $basket = null;

        if ($customer->getId()) {
            $basket = $this->basketManager->loadBasketPerCustomer($customer);
        }

        if (!$basket) {
            $basket = $this->loadFromSession();

            if (!$basket) {
                $basket = $this->basketManager->create();
                $basket->setLocale($customer->getLocale());
                $basket->setCurrency($this->currencyDetector->getCurrency());
            }
        }

        $basket->setCustomer($customer);

        $this->basketBuilder->build($basket);

        return $basket;
    }

    /**
     * {@inheritdoc}
     */
    public function save(BasketInterface $basket)
    {
        if ($basket->getCustomerId()) {
            $this->basketManager->save($basket);
        } else {
            $this->session->set(BasketSessionFactory::SESSION_BASE_NAME.'new', $basket);
        }
    }

    /**
     * Loads the basket in session (for authenticating users).
     *
     * @return BasketInterface
     */
    protected function loadFromSession()
    {
        return $this->session->get(BasketSessionFactory::SESSION_BASE_NAME.'new');
    }
}
