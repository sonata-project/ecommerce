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

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sonata\ClassificationBundle\Model\CollectionInterface;

interface ProductCollectionInterface
{
    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled();

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt.
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt.
     *
     * @return \Datetime $createdAt
     */
    public function getCreatedAt();

    /**
     * Set Product.
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * Get Product.
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Set Collection.
     *
     * @param CollectionInterface $collection
     */
    public function setCollection(CollectionInterface $collection);

    /**
     * Get Collection.
     *
     * @return CollectionInterface $collection
     */
    public function getCollection();
}
