<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Basket;

use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Product\Pool;
use Sonata\Tests\Component\Basket\ProductRepository;
use Sonata\Tests\Component\Basket\Delivery;
use Sonata\Tests\Component\Basket\Payment;

use Sonata\Component\Basket\Loader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{

    public function testSession()
    {

        $session = $this->getMock('Session', array('set', 'get'));
        $session->expects($this->once())
            ->method('get')
            ->will($this->returnValue(null))
        ;

        $session->expects($this->once())
            ->method('set')
        ;

        $loader = new Loader('Sonata\\Component\\Basket\\Basket');
        $loader->setSession($session);

        $basket = $loader->getBasket();

        $this->assertInstanceOf('Sonata\\Component\\Basket\\Basket', $basket);
    }
}