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

namespace Sonata\ProductBundle\Tests\Seo\Services;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\IntlBundle\Templating\Helper\NumberHelper;
use Sonata\MediaBundle\Provider\Pool;
use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Seo\Services\Facebook;
use Sonata\SeoBundle\Seo\SeoPage;
use Sonata\SeoBundle\Twig\Extension\SeoExtension;
use Symfony\Component\Routing\RouterInterface;

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
    public function testAlterPage(): void
    {
        $mediaPool = $this->createMock(Pool::class);
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->createMock(NumberHelper::class);
        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $product = new ProductFbMock();
        $router = $this->createMock(RouterInterface::class);

        // Check if the header data are correctly registered
        $fbService = new Facebook($router, $mediaPool, $numberHelper, $currencyDetector, 'test', 'test', 'reference');
        $fbService->alterPage($seoPage, $product, null);
        $content = $extension->getHeadAttributes();

        $this->assertContains('fb: http://ogp.me/ns/fb#', $content);

        $content = $extension->getMetadatas();

        $this->assertContains('O-some product', $content);
    }
}
