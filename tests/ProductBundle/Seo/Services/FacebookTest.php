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
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\IntlBundle\Templating\Helper\NumberHelper;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\ImageProvider;
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

    public function getName()
    {
        return 'Product1';
    }

    public function getSku()
    {
        return 'Sku1';
    }

    public function getPrice($vat = false)
    {
        return 123.56;
    }

    public function getSlug()
    {
        return 'product-1';
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
        $router->expects(static::any())
            ->method('generate')->willReturn('/product/link');

        //Prepare currency
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
                ->method('getCurrency')
                ->willReturn($currency);

        $numberHelper->expects(static::any())
                ->method('formatDecimal')
                ->willReturn($product->getPrice());

        // Check if the header data are correctly registered
        $fbService = new Facebook($router, $mediaPool, $numberHelper, $currencyDetector, 'test', 'test', 'reference');
        $fbService->alterPage($seoPage, $product);
        $content = $extension->getHeadAttributes();

        static::assertStringContainsString('fb: http://ogp.me/ns/fb#', $content);

        $content = $extension->getMetadatas();

        static::assertStringContainsString('property="og:description" content="O-some product"', $content);
        static::assertStringContainsString('property="og:title" content="Product1"', $content);
        static::assertStringContainsString('property="product:price:amount" content="123.56"', $content);
    }

    public function testAlterPageImage(): void
    {
        $mediaPool = $this->createMock(Pool::class);
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->createMock(NumberHelper::class);
        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $product = new ProductFbMock();
        $router = $this->createMock(RouterInterface::class);
        $router->expects(static::any())
            ->method('generate')->willReturn('/product/link');

        //Prepare currency
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
                ->method('getCurrency')
                ->willReturn($currency);

        // Test getImage
        $image = $this->createMock(MediaInterface::class);

        $imageProvider = $this->createMock(ImageProvider::class);
        $imageProvider->expects(static::any())
            ->method('generatePublicUrl')->willReturn('/upload/dummy.png');

        $image->expects(static::any())
            ->method('getName')->willReturn('correctMedia');
        $image->expects(static::any())
            ->method('getWidth')->willReturn(1111);
        $image->expects(static::any())
            ->method('getHeight')->willReturn(2222);
        $image->expects(static::any())
            ->method('getProviderName')->willReturn($imageProvider);
        $image->expects(static::any())
            ->method('getContentType')->willReturn('image/png');

        $mediaPool->expects(static::any())
            ->method('getProvider')->willReturn($imageProvider);

        $product->setImage($image);

        // Check if the header data are correctly registered
        $fbService = new Facebook($router, $mediaPool, $numberHelper, $currencyDetector, 'http://my-domain.ltd', 'test', 'reference');
        $fbService->alterPage($seoPage, $product);
        $content = $extension->getHeadAttributes();

        static::assertStringContainsString('fb: http://ogp.me/ns/fb#', $content);

        $content = $extension->getMetadatas();

        // image link
        static::assertStringContainsString('http://my-domain.ltd/upload/dummy.png', $content);
        //image width
        static::assertStringContainsString('meta property="og:image:width" content="1111"', $content);
        //image height
        static::assertStringContainsString('meta property="og:image:height" content="2222"', $content);
        //image content type
        static::assertStringContainsString('meta property="og:image:type" content="image/png"', $content);
    }
}
