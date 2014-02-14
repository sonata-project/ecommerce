<?php

namespace Sonata\Test\ProductBundle\Seo\Services;

use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Seo\Services\Twitter;
use Sonata\SeoBundle\Seo\SeoPage;
use Sonata\SeoBundle\Twig\Extension\SeoExtension;

class ProductTwitterMock extends BaseProduct
{
    /**
     * Get id.
     *
     * @return integer
     */
    public function getId() { }

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
        ob_start();
        $twitterService->alterPage($seoPage, $product, null);
        $extension->renderMetadatas();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertContains('twitter:label1', $content);
        $this->assertNotContains('twitter:image:src', $content);
    }
}
