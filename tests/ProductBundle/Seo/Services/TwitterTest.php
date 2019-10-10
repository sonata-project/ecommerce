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

    public function getDescription()
    {
        return 'O-some product';
    }
}

class TwitterTest extends TestCase
{
    public function testAlterPage(): void
    {
        $mediaPool = $this->createMock(Pool::class);
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->createMock(NumberHelper::class);
        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $product = new ProductTwitterMock();

        $numberHelper->expects($this->any())
                ->method('formatCurrency')
                ->willReturn($product->getPrice());

        //Prepare currency
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
                ->method('getCurrency')
                ->willReturn($currency);

        $twitterService = new Twitter($mediaPool, $numberHelper, $currencyDetector, 'mySiteName', 'Sonata', 'test', 'test', 'reference');
        $twitterService->alterPage($seoPage, $product);
        $content = $extension->getMetadatas();

        $this->assertContains('name="twitter:label1" content="Price"', $content);
        $this->assertNotContains('twitter:image:src', $content);
        $this->assertContains('name="twitter:site" content="mySiteName"', $content);
        $this->assertContains('name="twitter:creator" content="Sonata"', $content);
        $this->assertContains('name="twitter:data2" content="Sku1"', $content);
        $this->assertContains('name="twitter:title" content="Product1"', $content);
        $this->assertContains('name="twitter:description" content="O-some product"', $content);
        $this->assertContains('name="twitter:data1" content="123.56"', $content);
    }

    public function testAlterPageImage(): void
    {
        $mediaPool = $this->createMock(Pool::class);
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->createMock(NumberHelper::class);
        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $product = new ProductTwitterMock();

        //Prepare currency
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
                ->method('getCurrency')
                ->willReturn($currency);

        // Test getImage
        $image = $this->createMock(MediaInterface::class);

        $imageProvider = $this->createMock(ImageProvider::class);
        $imageProvider->expects($this->any())
            ->method('generatePublicUrl')->willReturn('/upload/dummy.png');

        $mediaPool->expects($this->any())
            ->method('getProvider')->willReturn($imageProvider);

        $product->setImage($image);

        $twitterService = new Twitter($mediaPool, $numberHelper, $currencyDetector, 'mySiteName', 'Sonata', 'http://my-domain.ltd', 'reference');
        $twitterService->alterPage($seoPage, $product);
        $content = $extension->getMetadatas();

        $this->assertContains('property="twitter:image:src" content="http://my-domain.ltd/upload/dummy.png"', $content);
    }
}
