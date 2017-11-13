<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Basket;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BaseBasketFactory;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\CustomerInterface;

class BasketFactory extends BaseBasketFactory
{
    /**
     * @return \Sonata\Component\Basket\BasketBuilderInterface
     */
    public function getBasketBuilder()
    {
        return $this->basketBuilder;
    }

    /**
     * @return \Sonata\Component\Basket\BasketManagerInterface
     */
    public function getBasketManager()
    {
        return $this->basketManager;
    }

    /**
     * @return \Sonata\Component\Currency\CurrencyDetectorInterface
     */
    public function getCurrencyDetector()
    {
        return $this->currencyDetector;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * {@inheritdoc}
     */
    public function load(CustomerInterface $customer)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function save(BasketInterface $basket)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function reset(BasketInterface $basket, $full = true)
    {
    }
}

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BaseBasketFactoryTest extends TestCase
{
    public function testConstruct()
    {
        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');

        $basketFactory = new BasketFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $this->assertEquals($basketManager, $basketFactory->getBasketManager());
        $this->assertEquals($basketBuilder, $basketFactory->getBasketBuilder());
        $this->assertEquals($currencyDetector, $basketFactory->getCurrencyDetector());
        $this->assertEquals($session, $basketFactory->getSession());
    }
}
