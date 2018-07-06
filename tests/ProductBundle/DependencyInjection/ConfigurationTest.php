<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaults(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), []);

        $this->validateClass($config);
        $this->validateSeo($config);

        $this->assertArrayHasKey('products', $config);
        $this->assertEmpty($config['products']);
    }

    public function validateClass($config): void
    {
        $this->assertEquals([
            'product' => 'App\\Sonata\\ProductBundle\\Entity\\Product',
            'package' => 'App\\Sonata\\ProductBundle\\Entity\\Package',
            'product_category' => 'App\\Sonata\\ProductBundle\\Entity\\ProductCategory',
            'product_collection' => 'App\\Sonata\\ProductBundle\\Entity\\ProductCollection',
            'category' => 'App\\Sonata\\ClassificationBundle\\Entity\\Category',
            'collection' => 'App\\Sonata\\ClassificationBundle\\Entity\\Collection',
            'delivery' => 'App\\Sonata\\ProductBundle\\Entity\\Delivery',
            'media' => 'App\\Sonata\\MediaBundle\\Entity\\Media',
            'gallery' => 'App\\Sonata\\MediaBundle\\Entity\\Gallery',
        ], $config['class']);
    }

    public function validateSeo($config): void
    {
        $this->assertEquals([
            'product' => [
                'site' => '@sonataproject',
                'creator' => '@th0masr',
                'domain' => 'http://demo.sonata-project.org',
                'media_prefix' => 'http://demo.sonata-project.org',
                'media_format' => 'reference',
            ],
        ], $config['seo']);
    }
}
