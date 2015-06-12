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

use Sonata\DatagridBundle\Datagrid\DatagridInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
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
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var mixed
     */
    protected $manager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Elastica\Query
     */
    protected $query;

    /**
     * @var array
     */
    protected $searchParameters;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $facets;

    /**
     * @var DatagridInterface
     */
    protected $datagrid;

    /**
     * Constructor
     *
     * @param FormFactoryInterface $formFactory Symfony Form factory
     * @param mixed                $manager     An elastica entity repository manager
     * @param string               $class       An entity class name
     */
    public function __construct(FormFactoryInterface $formFactory, $manager, $class)
    {
        $this->formFactory = $formFactory;
        $this->manager     = $manager;
        $this->class       = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        $class = $this->getClass();

        return $this->manager->getRepository($class);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagrid()
    {
        return $this->datagrid;
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
    public function build()
    {
        $this->buildFilters();
        $this->buildFacets();
        $this->buildQuery();

        $this->buildDatagrid();
    }

    /**
     * Returns search form builder
     *
     * @return FormBuilderInterface
     */
    protected function buildForm()
    {
        $formBuilder = $this->formFactory->createNamedBuilder('', 'form', array(), array(
            'csrf_protection' => false
        ));

        // Add sort options
        $formBuilder->add('sort', 'choice', array(
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
                $formBuilder->add($parameter, 'hidden', array('data' => $value));
            }
        }

        return $formBuilder;
    }

    /**
     * Returns sort value
     *
     * @param $option
     *
     * @return string|null
     */
    protected function getSortValue($option)
    {
        $sort = array('by' => null, 'order' => null);

        switch ($this->getSearchParameter('sort')) {
            case 'price_asc':
                $sort = array('by' => 'price', 'order' => 'asc');
                break;

            case 'price_desc':
                $sort = array('by' => 'price', 'order' => 'desc');
                break;
        }

        return isset($sort[$option]) ? $sort[$option] : null;
    }
}