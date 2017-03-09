<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\Seo\Services;

use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Seo\Services\Twitter;
use Sonata\SeoBundle\Seo\SeoPage;
use Sonata\SeoBundle\Twig\Extension\SeoExtension;

class ProductTwitterMock extends BaseProduct
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
    }
}

class TwitterTest extends \PHPUnit_Framework_TestCase
{
    public function testAlterPage()
    {
        $mediaPool = $this->getMockBuilder('Sonata\MediaBundle\Provider\Pool')->disableOriginalConstructor()->getMock();
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\NumberHelper')->disableOriginalConstructor()->getMock();
        $currencyDetector = $this->getMockBuilder('Sonata\Component\Currency\CurrencyDetectorInterface')->disableOriginalConstructor()->getMock();
        $product = new ProductTwitterMock();

        $twitterService = new Twitter($mediaPool, $numberHelper, $currencyDetector, 'test', 'test', 'test', 'test', 'reference');
        $twitterService->alterPage($seoPage, $product, null);
        $content = $extension->getMetadatas();

        $this->assertContains('twitter:label1', $content);
        $this->assertNotContains('twitter:image:src', $content);
    }
}
