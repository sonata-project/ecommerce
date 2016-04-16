<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\ProductBundle;

use Cocur\Slugify\Slugify;
use Sonata\ProductBundle\SonataProductBundle;

class Product extends \Sonata\ProductBundle\Entity\BaseProduct
{
    /**
     * Returns the id.
     *
     * @return mixed
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

class SonataProductBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSlug
     */
    public function testBoot($text, $expected)
    {
        $bundle = new SonataProductBundle();
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->exactly(2))->method('getParameter')->will($this->returnCallback(function ($value) {
            if ($value == 'sonata.product.product.class') {
                return 'Sonata\Test\ProductBundle\Product';
            }

            if ($value == 'sonata.product.slugify_service') {
                return 'slug_service';
            }
        }));
        $container->expects($this->once())->method('get')->will($this->returnValue(Slugify::create()));

        $bundle->setContainer($container);
        $bundle->boot();

        $product = new Product();
        $product->setSlug($text);
        $this->assertSame($product->getSlug(), $expected);
    }

    public function getSlug()
    {
        return array(
            array('La Coopérative des Tilleuls !', 'la-cooperative-des-tilleuls'),
            array('кооператив липа', 'kooperativ-lipa'),
        );
    }
}
