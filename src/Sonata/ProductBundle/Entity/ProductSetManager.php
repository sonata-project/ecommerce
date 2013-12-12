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

class ProductSetManager extends ProductManager
{
    /**
     * Deletes a product
     *
     * @param ProductInterface $productSet
     * @param bool             $andFlush
     *
     * @throws \RuntimeException
     */
    public function delete($productSet, $andFlush = true)
    {
        throw new \RuntimeException('A ProductSetManager cannot delete a product');
    }

    /**
     * Creates an empty ProductSet instance
     *
     * @throws \RuntimeException
     */
    public function create()
    {
        throw new \RuntimeException('A ProductSetManager cannot create a product');
    }

    /**
     * Creates an empty ProductSet instance
     *
     * @param ProductInterface $productSet
     * @param bool             $andFlush
     *
     * @throws \RuntimeException
     */
    public function save($productSet, $andFlush = true)
    {
        throw new \RuntimeException('A ProductSetManager cannot save a product');
    }
}
