<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\ProductBundle\Search;

use Sonata\ProductBundle\Search\SearchBuilder;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class SearchBuilderTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class SearchBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        // Given
        $provider = $this->getMock('Sonata\ProductBundle\Search\Provider\SearchProviderInterface');
        $provider->expects($this->any())->method('getFilters')->will($this->returnValue(array('myfilter')));
        $provider->expects($this->any())->method('getFacets')->will($this->returnValue(array('myfacet')));
        $provider->expects($this->any())->method('getSearchParameters')->will($this->returnValue(array('parameters')));

        $datagrid = $this->getMock('Sonata\DatagridBundle\Datagrid\DatagridInterface');
        $provider->expects($this->any())->method('getDatagrid')->will($this->returnValue($datagrid));

        $provider->expects($this->once())->method('setSearchParameters');
        $provider->expects($this->once())->method('build');

        $request = new Request(array(
            'q'        => 'my search term',
            'page'     => 2,
            'category' => 3,
            'price'    => 20,
            'sort'     => 'price_asc'
        ));

        // When
        $builder = new SearchBuilder($provider);
        $builder->handleRequest($request);

        // Then
        $this->assertEquals($provider, $builder->getProvider());
        $this->assertEquals($datagrid, $builder->getDatagrid());
        $this->assertEquals($provider->getSearchParameters(), $builder->getSearchParameters());
        $this->assertEquals($provider->getFilters(), $builder->getFilters());
        $this->assertEquals($provider->getFacets(), $builder->getFacets());
    }
}