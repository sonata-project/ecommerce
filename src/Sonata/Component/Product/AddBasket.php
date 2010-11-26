<?php

/*
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;


class AddBasket
{

    /**
     * @validation:NotBlank()
     */
    private $product_id;

    /**
     * @validation:NotBlank()
     * @validation:AssertType("object")
     */
    private $product;

    /**
     * @validation:NotBlank()
     * @validation:Min(1)
     * @validation:Max(64)
     */
    private $quantity;

    /**
     * @return integer the product id
     */
    public function getProductId()
    {
        return $this->product->getId();
    }

    /**
     * The product id is only set if there is not product attached to this object
     *
     * @param  integet $product_id the product id
     */
    public function setProductId($product_id)
    {

        // never erase this value
        if($this->product_id !== null) {
           return;
        }

        $this->product_id = $product_id;
    }

    /**
     * Set the related product
     *
     * @param  $product
     */
    public function setProduct($product)
    {
        $this->product_id = $product->getId();
        $this->product = $product;
    }

    /**
     * Set the quantity
     *
     * @param  integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return the quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}