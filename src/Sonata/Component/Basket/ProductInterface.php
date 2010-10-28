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

interface ProductInterface {

    /**
     * @abstract
     * @return integer the product id
     */
    public function getId();


    /**
     * @abstract
     * @return float the product price
     */
    public function getPrice();

    /**
     * @abstract
     * @return float the vat price
     */
    public function getVat();
        

    /**
     * @abstract
     * @return float the vat price
     */
    public function getName();

    /**
     * @abstract
     * @return array the product options
     */
    public function getOptions();

    /**
     * @abstract
     * @return boolean , true is the product is enabled (ready to be sell)
     */
    public function getEnabled();
}
