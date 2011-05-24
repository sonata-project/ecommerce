<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Product\Pool;

use Sonata\AdminBundle\Datagrid\ORM\ProxyQuery;
use Sonata\AdminBundle\Datagrid\ORM\Pager;

use Doctrine\ORM\EntityManager;

class ProductManager implements ProductManagerInterface
{

    protected $pool;
    protected $em;
    protected $class;

    public function __construct($class, EntityManager $em)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * Finds one product by the given criteria
     *
     * @param array $criteria
     * @return ProductInterface
     */
    public function findOneBy(array $criteria = array())
    {
       return $this->em->getRepository($this->class)->findOneBy($criteria);
    }

    /**
     * Finds products by the given criteria
     *
     * @param array $criteria
     * @return ProductInterface
     */
    public function findBy(array $criteria = array())
    {
       return $this->em->getRepository($this->class)->findBy($criteria);
    }

    /**
     * Saves a product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return void
     */
    public function save(ProductInterface $product)
    {
        $this->em->persist($product);
        $this->em->flush();
    }

    /**
     * Returns the product's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Deletes a product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return void
     */
    public function delete(ProductInterface $product)
    {
        $this->em->remove($product);
        $this->em->flush();
    }

    /**
     * Creates an empty medie instance
     *
     * @return Product
     */
    public function create()
    {
        $class = $this->getClass();

        return new $class;
    }

    /**
     * @param int $categoryId
     * @param int $page
     * @param int $limit
     * @return \Sonata\AdminBundle\Datagrid\ORM\Pager
     */
    public function getProductsByCategoryIdPager($categoryId, $page = 1, $limit = 25)
    {
        $queryBuilder = $this->em
            ->createQueryBuilder('p')
            ->from($this->getClass(), 'p')
            ->select('p')
            ->leftJoin('p.productCategories', 'pc')
            ->leftJoin('p.image', 'i')
            ->where('pc.category = :categoryId')
            ->setParameter('categoryId', $categoryId);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}