<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

interface ProductCollectionManagerInterface
{
    /**
     * Creates an empty ProductCollection instance
     *
     * @return ProductCollectionInterface
     */
    public function createProductCollection();

    /**
     * Deletes a ProductCollection
     *
     * @param  ProductCollectionInterface $productCollection
     *
     * @return void
     */
    public function deleteProductCollection(ProductCollectionInterface $productCollection);

    /**
     * Finds one ProductCollection by the given criteria
     *
     * @param  array $criteria
     *
     * @return ProductCollectionInterface
     */
    public function findProductCollectionBy(array $criteria);

    /**
     * Returns the ProductCollection's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a ProductCollection
     *
     * @param  ProductCollectionInterface $productCollection
     *
     * @return void
     */
    public function updateProductCollection(ProductCollectionInterface $productCollection);
}
