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

use Sonata\Component\Product\ProductInterface;

class Product implements ProductInterface
{
    public $enabled = true;

    public function getId() {
        return 1;
    }

    public function getPrice() {
        return 15;
    }

    public function getVat() {
        return 19.6;
    }

    public function getName() {
        return "fake name";
    }

    public function getOptions() {
        return array(
            'option1' => 'toto',
        );
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function isRecurrentPayment() {
        return false;
    }
}
