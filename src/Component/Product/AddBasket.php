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
    public function setProductId(int $productId): void
    {
        // never erase this value
        if (null !== $this->productId) {
            return;
        }

        $this->productId = $productId;
    }

    /**
     * Set the related product.
     */
    public function setProduct(ProductInterface $product): void
    {
        $this->productId = $product->getId();
        $this->product = $product;
    }

    /**
     * Set the quantity.
     *
     * @param int $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
