<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\ProductBundle\Search\Provider;

use Sonata\ProductBundle\Search\Provider\BaseSearchProvider;

class TestSearchProvider extends BaseSearchProvider {
    public function buildFilters()
    {
        $this->filters[] = 'myfilter';
    }

    public function buildFacets()
    {
        $this->facets[] = 'myfacet';
    }

    public function buildDatagrid()
    {
        $this->datagrid = 'datagrid';
    }

    public function buildQuery()
    {

    }

    public function getQuery()
    {

    }
}

class ManagerTest {
    public function getRepository($class)
    {
        return $class;
    }
}

/**
 * Class BaseSearchProviderTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BaseSearchProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        // Given
        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $class = 'Sonata\Test\MyClass';

        $manager = $this->getMock('Sonata\Tests\ProductBundle\Search\Provider\ManagerTest');
        $manager->expects($this->any())->method('getRepository')->will($this->returnValue(null));

        // When
        $provider = new TestSearchProvider($formFactory, $manager, $class);
        $provider->setSearchParameters(array(
            'parameter' => 'value'
        ));

        $provider->build();

        // Then
        $this->assertEquals(array('myfilter'), $provider->getFilters());
        $this->assertEquals(array('myfacet'), $provider->getFacets());
        $this->assertEquals('datagrid', $provider->getDatagrid());
        $this->assertEquals('Sonata\Test\MyClass', $provider->getClass());
        $this->assertEquals($manager, $provider->getManager());
        $this->assertNull($provider->getRepository());
        $this->assertEquals(array('parameter' => 'value'), $provider->getSearchParameters());
        $this->assertEquals('value', $provider->getSearchParameter('parameter'));
    }
}