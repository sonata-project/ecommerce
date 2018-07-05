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

namespace Sonata\Component\Tests\Basket;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BaseBasketFactory;
use Sonata\Component\Basket\BasketBuilderInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketManagerInterface;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Customer\CustomerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    public function load(CustomerInterface $customer): void
    {
    }

    public function save(BasketInterface $basket): void
    {
    }

    public function reset(BasketInterface $basket, $full = true): void
    {
    }
}

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BaseBasketFactoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $session = $this->createMock(SessionInterface::class);

        $basketFactory = new BasketFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $this->assertEquals($basketManager, $basketFactory->getBasketManager());
        $this->assertEquals($basketBuilder, $basketFactory->getBasketBuilder());
        $this->assertEquals($currencyDetector, $basketFactory->getCurrencyDetector());
        $this->assertEquals($session, $basketFactory->getSession());
    }
}
