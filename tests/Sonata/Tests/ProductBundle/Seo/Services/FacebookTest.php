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
}

class FacebookTest extends \PHPUnit_Framework_TestCase
{
    public function testAlterPage()
    {
        $mediaPool = $this->getMockBuilder('Sonata\MediaBundle\Provider\Pool')->disableOriginalConstructor()->getMock();
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $product = new ProductFbMock();

        // Check if the header data are correctly registered
        $fbService = new Facebook($mediaPool, $request);
        ob_start();
        $fbService->alterPage($seoPage, $product, null);
        $extension->renderHeadAttributes();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertContains('fb: http://ogp.me/ns/fb#', $content);

        // Check if the meta are correctly returned
        ob_start();
        $extension->renderMetadatas();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertContains('og:product', $content);

        // Check if the currency is correctly returned
        $fbService->alterPage($seoPage, $product, 'TestCurrency');
        ob_start();
        $extension->renderMetadatas();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertContains('TestCurrency', $content);
    }
}