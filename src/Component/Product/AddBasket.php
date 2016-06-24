<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Symfony\Component\Validator\Constraints as Validation;

class AddBasket
{
    /**
     * @Validation\NotBlank()
     *
     * @var int
     */
    private $productId;

    /**
     * @Validation\NotBlank()
     * @Validation\Type(type="object")
     *
     * @var ProductInterface
     */
    private $product;

    /**
     * @Validation\NotBlank()
     * @Validation\Range(min=1, max=64)
     *
     * @var int
     */
    private $quantity;

    /**
     * @return int the product id
     */
    public function getProductId()
    {
        return $this->product->getId();
    }

    /**
     * The product id is only set if there is not product attached to this object.
     *
     * @param int $productId the product id
     */
    public function setProductId($productId)
    {
        // never erase this value
        if ($this->productId !== null) {
            return;
        }

        $this->productId = $productId;
    }

    /**
     * Set the related product.
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product)
    {
        $this->productId = $product->getId();
        $this->product = $product;
    }

    /**
     * Set the quantity.
     *
     * @param int $quantity
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
