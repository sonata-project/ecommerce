<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Search\Provider;

use Doctrine\ORM\EntityManagerInterface;

use Sonata\DatagridBundle\Datagrid\Datagrid;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class DoctrineSearchProvider
 *
 * This is the Doctrine search provider
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class DoctrineSearchProvider extends BaseSearchProvider implements SearchProviderInterface
{
    /**
     * @var string
     */
    protected $productCategoriesClass;

    /**
     * Constructor
     *
     * @param FormFactoryInterface $formFactory            Symfony Form factory
     * @param ManagerRegistry      $manager                An elastica entity repository manager
     * @param string               $class                  An entity class name
     * @param string               $productCategoriesClass A product categories entity class name
     */
    public function __construct(FormFactoryInterface $formFactory, ManagerRegistry $manager, $class, $productCategoriesClass)
    {
        parent::__construct($formFactory, $manager, $class);

        $this->manager = $this->manager->getManagerForClass($class);
        $this->productCategoriesClass = $productCategoriesClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        if (!$this->query) {
            $this->query = $this->getRepository()->createQueryBuilder('product');
        }

        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters()
    {
        $qb = $this->getQuery();

        // Master product only filter (no variations)
        $field = sprintf('%s.parent', $qb->getRootAlias());
        $this->filters[] = $qb->expr()->isNull($field);

        // Category filter
        $category = $this->getSearchParameter('category');

        if ($category) {
            $this->filters[] = $qb->expr()->orX(
                $qb->expr()->eq('category.id', $category),
                $qb->expr()->eq('category.parent', $category)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildFacets()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function buildQuery()
    {
        $query = $this->getQuery();
        $query->select('DISTINCT product')
              ->leftJoin(sprintf('%s.productCategories', $query->getRootAlias()), 'pc')
              ->leftJoin('pc.category', 'category')
              ->andWhere('product.name LIKE :term OR product.description LIKE :term')
              ->setParameter('term', '%' . $this->getSearchParameter('q') . '%');

        $filters = $query->expr()->andX();

        foreach ($this->getFilters() as $filter) {
            $filters->add($filter);
        }

        $query->andWhere($filters);
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatagrid()
    {
        if ($this->datagrid) {
            return;
        }

        $form = $this->buildForm();

        $this->datagrid = new Datagrid(new ProxyQuery($this), new Pager(), $form, array(
            '_page'       => $this->getSearchParameter('page'),
            '_per_page'   => 6,
            '_sort_by'    => $this->getSortValue('by'),
            '_sort_order' => $this->getSortValue('order'),
            'sort'        => $this->getSearchParameter('sort'),
            'q'           => $this->getSearchParameter('q'),
            'category'    => $this->getSearchParameter('category'),
            'price'       => $this->getSearchParameter('price'),
        ));
    }

    /**
     * Returns Doctrine categories facets query
     *
     * @return mixed
     */
    public function getFacetsQuery()
    {
        $query = $this->getManager()->createQueryBuilder();
        $query->select('category.id AS term, count(pc.product) AS products_nb')
            ->from($this->productCategoriesClass, 'pc')
            ->innerJoin('pc.category', 'category')
            ->innerJoin('pc.product', 'product')
            ->groupBy('category.id');

        $query->andWhere('product.name LIKE :term OR product.description LIKE :term')
            ->setParameter('term', '%' . $this->getSearchParameter('q') . '%');

        $filters = $query->expr()->andX();

        foreach ($this->getFilters() as $filter) {
            $filters->add($filter);
        }

        $query->andWhere($filters);

        return $query;
    }
}