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

use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Doctrine\Model\ManagerInterface;

interface ProductCollectionManagerInterface extends ManagerInterface
{
    /**
     * Adds a Category to a Product.
     */
    public function addCollectionToProduct(ProductInterface $product, CollectionInterface $collection);

    /**
     * Removes a Category from a Product.
     */
    public function removeCollectionFromProduct(ProductInterface $product, CollectionInterface $collection);
}
