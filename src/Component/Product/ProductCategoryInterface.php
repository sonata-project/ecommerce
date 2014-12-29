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

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Model\TimestampableInterface;

interface ProductCategoryInterface extends TimestampableInterface
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
     * Set if product category is the main category
     *
     * @param boolean $main
     */
    public function setMain($main);

    /**
     * Get if product category is the main category
     *
     * @return boolean $main
     */
    public function getMain();

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
     * Set Category
     *
     * @param CategoryInterface $category
     */
    public function setCategory(CategoryInterface $category);

    /**
     * Get Category
     *
     * @return CategoryInterface $category
     */
    public function getCategory();
}
