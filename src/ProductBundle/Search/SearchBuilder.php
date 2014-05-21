<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Search;

use Sonata\DatagridBundle\Datagrid\Datagrid;
use Sonata\DatagridBundle\Datagrid\DatagridInterface;
use Sonata\DatagridBundle\ProxyQuery\Elastica\ProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\Elastica\QueryBuilder;
use Sonata\ProductBundle\Search\Provider\SearchProviderInterface;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class SearchBuilder
 *
 * This is the search builder
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class SearchBuilder
{
    /**
     * @var SearchProviderInterface
     */
    protected $provider;

    /**
     * @var DatagridInterface
     */
    protected $datagrid;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Constructor
     *
     * @param SearchProviderInterface $provider A Sonata search provider
     */
    public function __construct(SearchProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Returns search provider
     *
     * @return SearchProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Returns product search filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->getProvider()->getFilters();
    }

    /**
     * Returns product search facets
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->getProvider()->getFacets();
    }

    /**
     * Returns datagrid (builds it if not instantiated)
     *
     * @return DatagridInterface
     */
    public function getDatagrid()
    {
        if (!$this->datagrid) {
            $this->buildDatagrid();
        }

        return $this->datagrid;
    }

    /**
     * Handles a search from a Symfony Request instance
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function handleRequest(Request $request)
    {
        $this->getProvider()->setSearchParameters(array(
            'q'          => $request->query->get('q'),
            'term'       => sprintf('*%s*', $request->query->get('q')),
            'page'       => $request->query->get('page', 1),
            'category'   => $request->query->get('category'),
            'price'      => $request->query->get('price'),
            'sort'       => $request->query->get('sort'),
        ));

        $this->getProvider()->build();
    }

    /**
     * Returns search parameters
     *
     * @return array
     */
    public function getSearchParameters()
    {
        return $this->getProvider()->getSearchParameters();
    }

    protected function buildDatagrid()
    {
        $this->datagrid = new Datagrid(new ProxyQuery($this->queryBuilder), $pager, $formBuilder, $values);
    }
}