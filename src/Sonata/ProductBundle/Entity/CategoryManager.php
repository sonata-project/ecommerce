<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\CategoryBundle\Entity;

use Sonata\Component\Product\CategoryManagerInterface;
use Sonata\Component\Product\CategoryInterface;
use Doctrine\ORM\EntityManager;

class CategoryManager implements CategoryManagerInterface
{
    protected $em;
    protected $repository;
    protected $class;
    
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if(class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty medie instance
     *
     * @return category
     */
    public function createCategory()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a category
     *
     * @param Category $category
     * @return void
     */
    public function updateCategory(CategoryInterface $category)
    {
        $this->em->persist($category);
        $this->em->flush();
    }

    /**
     * Returns the category's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one category by the given criteria
     *
     * @param array $criteria
     * @return Category
     */
    public function findCategoryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes a category
     *
     * @param Category $category
     * @return void
     */
    public function deleteCategory(CategoryInterface $category)
    {
        $this->em->remove($category);
        $this->em->flush();
    }
}