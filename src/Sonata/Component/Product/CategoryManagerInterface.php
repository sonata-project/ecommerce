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

interface CategoryManagerInterface
{

    /**
     * Creates an empty medie instance
     *
     * @return Category
     */
    public function createCategory();

    /**
     * Deletes a category
     *
     * @param  \Sonata\Component\Product\CategoryInterface $category
     * @return void
     */
    public function deleteCategory(CategoryInterface $category);

    /**
     * Finds one category by the given criteria
     *
     * @param  array             $criteria
     * @return CategoryInterface
     */
    public function findCategoryBy(array $criteria);

    /**
     * Returns the category's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a category
     *
     * @param  \Sonata\Component\Product\CategoryInterface $category
     * @return void
     */
    public function updateCategory(CategoryInterface $category);

    /**
     * Returns the root categories
     *
     * @abstract
     * @param  array $criteria
     * @return void
     */
    public function getRootCategoriesPager($page = 1, $limit = 25);
}
