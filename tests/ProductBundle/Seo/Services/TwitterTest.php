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

class TwitterTest extends TestCase
{
    public function testAlterPage(): void
    {
        $mediaPool = $this->createMock('Sonata\MediaBundle\Provider\Pool');
        $seoPage = new SeoPage('test');
        $extension = new SeoExtension($seoPage, 'UTF-8');
        $numberHelper = $this->createMock('Sonata\IntlBundle\Templating\Helper\NumberHelper');
        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $product = new ProductTwitterMock();

        $twitterService = new Twitter($mediaPool, $numberHelper, $currencyDetector, 'test', 'test', 'test', 'test', 'reference');
        $twitterService->alterPage($seoPage, $product, null);
        $content = $extension->getMetadatas();

        $this->assertContains('twitter:label1', $content);
        $this->assertNotContains('twitter:image:src', $content);
    }
}
