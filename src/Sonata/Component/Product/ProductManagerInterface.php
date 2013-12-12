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

use Sonata\CoreBundle\Entity\ManagerInterface;

interface ProductManagerInterface extends ManagerInterface
{
    /**
     * Returns the products in the same collections as those specified in $productCollections
     *
     * @param mixed $productCollections
     *
     * @return array
     */
    public function findInSameCollections($productCollections);

    /**
     * Returns the parent products in the same collections as those specified in $productCollections
     *
     * @param mixed $productCollections
     *
     * @return array
     */
    public function findParentsInSameCollections($productCollections);

    /**
     * Retrieve an active product from its id and its slug
     *
     * @param int    $id
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findEnabledFromIdAndSlug($id, $slug);
}
