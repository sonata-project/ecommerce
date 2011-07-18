<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\CategoryManagerInterface;
use Sonata\Component\Product\CategoryInterface;

use Sonata\AdminBundle\Datagrid\ORM\ProxyQuery;
use Sonata\AdminBundle\Datagrid\ORM\Pager;

use Doctrine\ORM\EntityManager;

class CategoryManager implements CategoryManagerInterface
{
    protected $em;
    protected $repository;
    protected $class;
    protected $categories = null;

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
    public function findOneCategoryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds one category by the given criteria
     *
     * @param array $criteria
     * @return Category
     */
    public function findCategoryBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
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

    public function getRootCategoriesPager($page = 1, $limit = 25)
    {
        $page = (int)$page == 0 ? 1 : (int)$page;

        $queryBuiler = $this->em->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->andWhere('c.parent IS NULL');

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuiler));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    public function getSubCategoriesPager($categoryId, $page = 1, $limit = 25)
    {

        $queryBuiler = $this->em->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.parent = :categoryId')
            ->setParameter('categoryId', $categoryId);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuiler));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    public function getRootCategory()
    {
        $this->loadCategories();

        return $this->categories[0];
    }

    public function loadCategories()
    {
        if ($this->categories !== null) {
            return;
        }

        $class = $this->getClass();

        $this->categories = $this->em->createQuery(sprintf('SELECT c FROM %s c INDEX BY c.id', $class))
            ->execute();

        $root = new $class;
        $root->setName('root');

        foreach ($this->categories as $category) {

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();

            if (!$parent) {
                $root->addChildren($category);

                continue;
            }

            $parent->addChildren($category);
        }

        $this->categories[0] = $root;
    }
}