<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\ProductInterface;

class ProductCollectionManager extends ProductManager
{
    /**
     * Deletes a product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function delete(ProductInterface $product)
    {
        throw new \RuntimeException('A ProductCollectionManager cannot delete a product');
    }

    /**
     * Creates an empty medie instance
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function create()
    {
        throw new \RuntimeException('A ProductCollectionManager cannot create a product');
    }

    /**
     * Creates an empty medie instance
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function save(ProductInterface $product)
    {
        throw new \RuntimeException('A ProductCollectionManager cannot save a product');
    }
}
