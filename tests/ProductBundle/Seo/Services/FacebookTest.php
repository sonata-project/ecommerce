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

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Seo\Services\Facebook;
use Sonata\SeoBundle\Seo\SeoPage;
use Sonata\SeoBundle\Twig\Extension\SeoExtension;

class ProductFbMock extends BaseProduct
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
    }

    public function getDescription()
    {
        return 'O-some product';
    }
}

class FacebookTest extends TestCase
{
    public function testAlterPage()
    {
        $mediaPool = $this->createMock('Sonata\MediaBundle\Provider\Pool');
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->createMock('Sonata\IntlBundle\Templating\Helper\NumberHelper');
        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $product = new ProductFbMock();
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');

        // Check if the header data are correctly registered
        $fbService = new Facebook($router, $mediaPool, $numberHelper, $currencyDetector, 'test', 'test', 'reference');
        $fbService->alterPage($seoPage, $product, null);
        $content = $extension->getHeadAttributes();

        $this->assertContains('fb: http://ogp.me/ns/fb#', $content);

        $content = $extension->getMetadatas();

        $this->assertContains('O-some product', $content);
    }
}
