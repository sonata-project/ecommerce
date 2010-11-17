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

class Loader
{
    protected $session;

    protected $product_pool;

    protected $basket_class;

    public function __construct($class)
    {
        $this->basket_class = $class;
    }

    public function setSession($session)
    {
        $this->session = $session;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getBasket()
    {

        $basket = $this->getSession()->get('sonata/basket');

        if(!$basket) {
            $basket = new $this->basket_class;
        }
        
        $basket->setProductPool($this->getProductPool());

        $this->getSession()->set('sonata/basket', $basket);

        return $basket;
    }

    public function setProductPool($product_pool)
    {
        $this->product_pool = $product_pool;
    }

    public function getProductPool()
    {
        return $this->product_pool;
    }

}