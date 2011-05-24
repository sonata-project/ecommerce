<?php

/*
 * This file is part of the Sonata product.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

interface ProductManagerInterface
{
    /**
     * Creates an empty medie instance
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    function create();

    /**
     * Deletes a product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return void
     */
    function delete(ProductInterface $product);

    /**
     * Saves a product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return void
     */
    function save(ProductInterface $product);

    /**
     * Finds one product by the given criteria
     *
     * @param array $criteria
     * @return array
     */
    function findBy(array $criteria = array());

    /**
     * Finds one product by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Product\ProductInterface
     */
    function findOneBy(array $criteria = array());

    /**
     * Returns the product's fully qualified class name
     *
     * @return string
     */
    function getClass();
}
