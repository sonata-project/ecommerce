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
use Sonata\DatagridBundle\Datagrid\DatagridFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseSearchProvider
 *
 * This is the base search provider
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
abstract class BaseSearchProvider implements SearchProviderInterface
{
    /**
     * @var DatagridFactoryInterface
     */
    protected $datagridFactory;

    /**
     * @var DatagridBuilderInterface
     */
    protected $datagridBuilder;

    /**
     * @var array
     */
    protected $searchParameters;

    /**
     * Constructor
     *
     * @param DatagridFactoryInterface $datagridFactory
     */
    public function __construct(DatagridFactoryInterface $datagridFactory)
    {
        $this->datagridFactory = $datagridFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchParameters(array $searchParameters)
    {
        $this->searchParameters = $searchParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchParameters()
    {
        return $this->searchParameters;
    }

    /**
     * Returns a search parameter by its key
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getSearchParameter($key)
    {
        return isset($this->searchParameters[$key]) ? $this->searchParameters[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagridBuilder()
    {
        if (!$this->datagridBuilder) {
            $this->datagridBuilder = $this->datagridFactory->getDatagridBuilder($this->getDatagridType(), $this->getSearchParameters());
        }

        return $this->datagridBuilder;
    }

    /**
     * @return \Sonata\DatagridBundle\Datagrid\DatagridInterface
     */
    public function getDatagrid()
    {
        return $this->getDatagridBuilder()->getDatagrid();
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $this->buildFilters();
        $this->buildFacets();
        $this->buildSort();
//        $this->buildForm();
    }

    /**
     * {@inheritdoc}
     */
    public function buildSort()
    {
        switch($this->getSearchParameter('sort')) {
            case 'price_asc':
                $this->getDatagrid()->setSort('price', 'asc');
                break;
            case 'price_desc':
                $this->getDatagrid()->setSort('price', 'desc');
                break;
            default:
                break;
        }
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
        $this->setSearchParameters(array(
            'q'          => $request->query->get('q'),
            'term'       => sprintf('*%s*', $request->query->get('q')),
            '_page'      => $request->query->get('page', 1),
            '_per_page'  => $request->query->get('count', 9),
            'category'   => $request->query->get('category'),
            'price'      => $request->query->get('price'),
            'sort'       => $request->query->get('sort'),
        ));

        $this->build();
    }

    protected abstract function getDatagridType();

    /**
     * Adds fields to search form
     */
    protected function buildForm()
    {
        // Add sort options
        $this->getDatagridBuilder()->addFormField('sort', 'choice', array(
            'multiple' => false,
            'choices' => array(
                'score'      => 'Sort by pertinence',
                'price_asc'  => 'Sort by ascending price',
                'price_desc' => 'Sort by descending price',
            )
        ));

        // Add hidden fields corresponding to current URL parameters
        foreach ($this->getSearchParameters() as $parameter => $value) {
            if ($value && !in_array($parameter, array('term', 'sort', 'page'))) {
                $this->getDatagridBuilder()->addFormField($parameter, 'hidden', array('data' => $value));
            }
        }
    }
}