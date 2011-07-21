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

interface ProductCategoryInterface
{
    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    function getEnabled();

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return datetime $updatedAt
     */
    function getUpdatedAt();

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return datetime $createdAt
     */
    function getCreatedAt();

    /**
     * Set Product
     *
     * @param ProductInterfacet $product
     */
    function setProduct(ProductInterface $product);
    /**
     * Get Product
     *
     * @return Application\Sonata\ProductBundle\Entity\Product $product
     */
    function getProduct();

    /**
     * Set Category
     *
     * @param CategoryInterface $category
     */
    function setCategory(CategoryInterface $category);

    /**
     * Get Category
     *
     * @return Application\Sonata\ProductBundle\Entity\Category $category
     */
    function getCategory();
}