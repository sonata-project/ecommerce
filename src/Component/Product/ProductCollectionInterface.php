<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Model\TimestampableInterface;

interface ProductCollectionInterface extends TimestampableInterface
{
    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled();

    /**
     * Set Product
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);
    /**
     * Get Product
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Set Collection
     *
     * @param CollectionInterface $collection
     */
    public function setCollection(CollectionInterface $collection);

    /**
     * Get Collection
     *
     * @return CollectionInterface $collection
     */
    public function getCollection();
}
