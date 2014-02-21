<?php

namespace Sonata\Test\ProductBundle\Seo\Services;

use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Seo\Services\Facebook;
use Sonata\SeoBundle\Seo\SeoPage;
use Sonata\SeoBundle\Twig\Extension\SeoExtension;

class ProductFbMock extends BaseProduct
{
    /**
     * Get id.
     *
     * @return integer
     */
    public function getId() { }

    public function getDescription() { return 'O-some product'; }
}

class FacebookTest extends \PHPUnit_Framework_TestCase
{
    public function testAlterPage()
    {
        $mediaPool = $this->getMockBuilder('Sonata\MediaBundle\Provider\Pool')->disableOriginalConstructor()->getMock();
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\NumberHelper')->disableOriginalConstructor()->getMock();
        $currencyDetector = $this->getMockBuilder('Sonata\Component\Currency\CurrencyDetectorInterface')->disableOriginalConstructor()->getMock();
        $product = new ProductFbMock();
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        // Check if the header data are correctly registered
        $fbService = new Facebook($router, $mediaPool, $numberHelper, $currencyDetector, 'test', 'test', 'reference');
        ob_start();
        $fbService->alterPage($seoPage, $product, null);
        $extension->renderHeadAttributes();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertContains('fb: http://ogp.me/ns/fb#', $content);

        ob_start();
        $extension->renderMetadatas();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertContains('O-some product', $content);
    }
}
