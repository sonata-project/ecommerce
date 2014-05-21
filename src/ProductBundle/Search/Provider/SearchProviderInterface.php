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

use Sonata\DatagridBundle\Datagrid\DatagridBuilderInterface;

/**
 * Class SearchProviderInterface
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
interface SearchProviderInterface
{
    /**
     * Builds search request
     *
     * @return void
     */
    public function build();

    /**
     * Sets search parameters like the query string, sort order, filters, ...
     *
     * @param array $searchParameters
     */
    public function setSearchParameters(array $searchParameters);

    /**
     * Returns search parameters
     *
     * @return array
     */
    public function getSearchParameters();

    /**
     * Returns search provider datagrid
     *
     * @return DatagridBuilderInterface
     */
    public function getDatagridBuilder();

    /**
     * Builds product search filters to apply to query
     *
     * @return void
     */
    public function buildFilters();

    /**
     * Builds product search facets to apply to query
     *
     * @return void
     */
    public function buildFacets();

    /**
     * Builds product search query
     *
     * @return void
     */
    public function buildQuery();

    /**
     * Builds product datagrid
     *
     * @return void
     */
    public function buildDatagrid();

    /**
     * Builds sort options
     */
    public function buildSort();
}