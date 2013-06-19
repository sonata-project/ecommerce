<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Transformer;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\Pool as ProductPool;

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
     * @param ProductPool $productPool
     */
    public function __construct(ProductPool $productPool)
    {
        $this->productPool = $productPool;
    }

    /**
     * @param  OrderInterface  $order
     * @param  BasketInterface $basket
     * @return BasketInterface
     */
    public function transformIntoBasket(OrderInterface $order, BasketInterface $basket)
    {
        // we reset the current basket
        $basket->reset(true);

        $basket->setCurrency($order->getCurrency());
        $basket->setLocale($order->getLocale());

        // We are free to convert !
        foreach ($order->getOrderElements() as $orderElement) {
            $provider   = $this->productPool->getProvider($orderElement->getProductType());
            $manager    = $this->productPool->getManager($orderElement->getProductType());

            $product    = $manager->findOneBy(array('id' => $orderElement->getProductId()));

            if (!$product) {
                continue;
            }

            $basketElement = $provider->createBasketElement($product, $orderElement->getOptions());

            $provider->basketAddProduct($basket, $product, $basketElement);
        }

        $basket->setCustomer($order->getCustomer());

        $basket->buildPrices();

        return $basket;
    }
}
