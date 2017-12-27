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

use Sonata\ClassificationBundle\Model\CategoryInterface;

interface ProductCategoryInterface
{
    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * Set if product category is the main category.
     *
     * @param bool $main
     */
    public function setMain($main);

    /**
     * Get if product category is the main category.
     *
     * @return bool
     */
    public function getMain();

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @return \Datetime
     */
    public function getCreatedAt();

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param CategoryInterface $category
     */
    public function setCategory(CategoryInterface $category);

    /**
     * @return CategoryInterface
     */
    public function getCategory();
}
