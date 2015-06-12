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

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Sonata\DatagridBundle\Datagrid\Datagrid;
use Sonata\DatagridBundle\Pager\Elastica\Pager;
use Sonata\DatagridBundle\ProxyQuery\Elastica\ProxyQuery;

/**
 * Class ElasticaSearchProvider
 *
 * This is the Elasticsearch search provider (used via FOSElasticaBundle)
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ElasticaSearchProvider extends BaseSearchProvider implements SearchProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        if (!$this->query) {
            $this->query = new \Elastica\Query();
        }

        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters()
    {
        if ($this->filters) {
            return;
        }

        // Master product only filter (no variations)
        $this->filters[] = new \Elastica\Filter\Missing('parent');

        // Category filter
        if ($this->getSearchParameter('category')) {
            $this->filters[] = new \Elastica\Filter\Term(array(
                'product_categories.category_id' => $this->getSearchParameter('category')
            ));
        }

        // Price filter
        if ($this->getSearchParameter('price')) {
            $this->filters[] = new \Elastica\Filter\Range('price', array(
                'to' => $this->getSearchParameter('price')
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildFacets()
    {
        if ($this->facets) {
            return;
        }

        // Categories facet
        $categories = new \Elastica\Facet\Terms('categories');
        $categories->setField('product_categories.category_id');

        $this->facets[] = $categories;
    }

    /**
     * {@inheritdoc}
     */
    public function buildQuery()
    {
        $query = $this->getQuery();
        $query->setQuery(new \Elastica\Query\QueryString($this->getSearchParameter('term')));

        $filters = new \Elastica\Filter\Bool();

        // Filters
        foreach ($this->getFilters() as $filter)
        {
            $filters->addMust($filter);
        }

        $query->setFilter($filters);

        // Facets
        foreach($this->getFacets() as $facet) {
            $facet->setFilter($filters);

            $query->addFacet($facet);
        }
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
}