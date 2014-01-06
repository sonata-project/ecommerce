<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\ProductBundle\DependencyInjection;

use Sonata\ProductBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), array());

        $this->validateClass($config);
        $this->validateSeo($config);

        $this->assertArrayHasKey('products', $config);
        $this->assertEmpty($config['products']);
    }

    public function validateClass($config)
    {
        $this->assertEquals(array(
            'product'            => 'Application\\Sonata\\ProductBundle\\Entity\\Product',
            'package'            => 'Application\\Sonata\\ProductBundle\\Entity\\Package',
            'product_category'   => 'Application\\Sonata\\ProductBundle\\Entity\\ProductCategory',
            'product_collection' => 'Application\\Sonata\\ProductBundle\\Entity\\ProductCollection',
            'category'           => 'Application\\Sonata\\ClassificationBundle\\Entity\\Category',
            'collection'         => 'Application\\Sonata\\ClassificationBundle\\Entity\\Collection',
            'delivery'           => 'Application\\Sonata\\ProductBundle\\Entity\\Delivery',
            'media'              => 'Application\\Sonata\\MediaBundle\\Entity\\Media',
            'gallery'            => 'Application\\Sonata\\MediaBundle\\Entity\\Gallery',
        ), $config['class']);
    }

    public function validateSeo($config)
    {
        $this->assertEquals(array(
            'product' => array(
                'site'         => '@sonataproject',
                'creator'      => '@th0masr',
                'domain'       => 'http://demo.sonata-project.org',
                'media_prefix' => 'http://demo.sonata-project.org',
                'media_format' => 'reference',
            ),

        ), $config['seo']);
    }
}
