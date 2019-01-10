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

namespace Sonata\Component\Event;

use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AddBasketElementEvent extends Event
{
    /**
     * @var BasketInterface
     */
    protected $basket;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var BasketElementInterface
     */
    protected $basketElement;

    /**
     * @var ProductProviderInterface
     */
    protected $productProvider;

    /**
     * @param BasketInterface          $basket
     * @param BasketElementInterface   $basketElement
     * @param ProductInterface         $product
     * @param ProductProviderInterface $productProvider
     */
    public function __construct(BasketInterface $basket, BasketElementInterface $basketElement, ProductInterface $product, ProductProviderInterface $productProvider)
    {
        $this->basket = $basket;
        $this->basketElement = $basketElement;
        $this->product = $product;
        $this->productProvider = $productProvider;
    }

    /**
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    public function getBasketElement()
    {
        return $this->basketElement;
    }

    /**
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Sonata\Component\Product\ProductProviderInterface
     */
    public function getProductProvider()
    {
        return $this->productProvider;
    }
}
