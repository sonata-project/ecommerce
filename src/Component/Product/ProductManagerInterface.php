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

use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\CoreBundle\Model\PageableManagerInterface;

interface ProductManagerInterface extends ManagerInterface, PageableManagerInterface
{
    /**
     * Returns the products in the same collections as those specified in $productCollections.
     *
     * @param mixed    $productCollections
     * @param int|null $limit
     *
     * @return array
     */
    public function findInSameCollections($productCollections, $limit = null);

    /**
     * Returns the parent products in the same collections as those specified in $productCollections.
     *
     * @param mixed    $productCollections
     * @param int|null $limit
     *
     * @return array
     */
    public function findParentsInSameCollections($productCollections, $limit = null);

    /**
     * Retrieve an active product from its id and its slug.
     *
     * @param int    $id
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findEnabledFromIdAndSlug($id, $slug);

    /**
     * @param ProductInterface $product
     *
     * @return array
     */
    public function findVariations(ProductInterface $product);

    /**
     * Updated stock value for a given Product.
     *
     * @param ProductInterface|int $product
     * @param int                  $diff
     */
    public function updateStock($product, $diff);
}
