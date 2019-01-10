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

namespace Sonata\Component\Transformer;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Event\BasketTransformEvent;
use Sonata\Component\Event\OrderTransformEvent;
use Sonata\Component\Event\TransformerEvents;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderTransformer extends BaseTransformer
{
    /**
     * @var ProductPool the product pool
     */
    protected $productPool;

    /**
     * @var array the transformer option
     */
    protected $options;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param ProductPool              $productPool
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ProductPool $productPool, EventDispatcherInterface $eventDispatcher)
    {
        $this->productPool = $productPool;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param OrderInterface  $order
     * @param BasketInterface $basket
     *
     * @return BasketInterface
     */
    public function transformIntoBasket(OrderInterface $order, BasketInterface $basket)
    {
        $event = new OrderTransformEvent($order);
        $this->eventDispatcher->dispatch(TransformerEvents::PRE_ORDER_TO_BASKET_TRANSFORM, $event);

        // we reset the current basket
        $basket->reset(true);

        $basket->setCurrency($order->getCurrency());
        $basket->setLocale($order->getLocale());

        // We are free to convert !
        foreach ($order->getOrderElements() as $orderElement) {
            /*
             * @var $orderElement OrderElementInterface
             */
            $provider = $this->productPool->getProvider($orderElement->getProductType());
            $manager = $this->productPool->getManager($orderElement->getProductType());

            $product = $manager->findOneBy(['id' => $orderElement->getProductId()]);

            if (!$product) {
                continue;
            }

            $basketElement = $provider->createBasketElement($product, $orderElement->getOptions());
            $basketElement->setQuantity($orderElement->getQuantity());

            $provider->basketAddProduct($basket, $product, $basketElement);
        }

        $basket->setCustomer($order->getCustomer());

        $basket->buildPrices();

        $event = new BasketTransformEvent($basket);
        $this->eventDispatcher->dispatch(TransformerEvents::POST_ORDER_TO_BASKET_TRANSFORM, $event);

        return $basket;
    }
}
