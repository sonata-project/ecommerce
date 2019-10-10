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

namespace Sonata\ProductBundle\Seo\Services;

use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\IntlBundle\Templating\Helper\NumberHelper;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\Pool;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * FacebookService.
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class Facebook implements ServiceInterface
{
    /**
     * @var Pool
     */
    protected $mediaPool;

    /**
     * @var NumberHelper
     */
    protected $numberHelper;

    /**
     * @var CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @var string|null
     */
    protected $domain;

    /**
     * @var string|null
     */
    protected $mediaFormat;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param string $domain
     * @param        $mediaFormat
     */
    public function __construct(RouterInterface $router, Pool $mediaPool, NumberHelper $numberHelper, CurrencyDetectorInterface $currencyDetector, $domain, $mediaFormat)
    {
        $this->router = $router;
        $this->mediaPool = $mediaPool;
        $this->numberHelper = $numberHelper;
        $this->currencyDetector = $currencyDetector;
        $this->domain = $domain;
        $this->mediaFormat = $mediaFormat;
    }

    public function alterPage(SeoPageInterface $seoPage, ProductInterface $product): void
    {
        $this->registerHeaders($seoPage);

        $seoPage->addMeta('property', 'og:type', 'og:product')
            ->addMeta('property', 'og:title', $product->getName())
            ->addMeta('property', 'og:description', $product->getDescription())
            ->addMeta('property', 'og:url', $this->router->generate('sonata_product_view', [
                'slug' => $product->getSlug(),
                'productId' => $product->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'product:price:amount', (string) $this->numberHelper->formatDecimal($product->getPrice()))
            ->addMeta('property', 'product:price:currency', $this->currencyDetector->getCurrency()->getLabel());

        // If a media is available, we add the opengraph image data
        if ($image = $product->getImage()) {
            $this->addImageInfo($image, $seoPage);
        }
    }

    protected function addImageInfo(MediaInterface $image, SeoPageInterface $seoPage): void
    {
        $provider = $this->mediaPool->getProvider($image->getProviderName());

        $seoPage->addMeta('property', 'og:image', $this->domain.$provider->generatePublicUrl($image, $this->mediaFormat))
            ->addMeta('property', 'og:image:width', (string) $image->getWidth())
            ->addMeta('property', 'og:image:height', (string) $image->getHeight())
            ->addMeta('property', 'og:image:type', $image->getContentType());
    }

    protected function registerHeaders(SeoPageInterface $seoPage): void
    {
        $attributeName = 'prefix';
        $headAttributes = $seoPage->getHeadAttributes();

        if (!isset($headAttributes[$attributeName])) {
            $headAttributes[$attributeName] = '';
        }

        $headAttributes[$attributeName] .= 'og: http://ogp.me/ns#
fb: http://ogp.me/ns/fb#
product: http://ogp.me/ns/product#';

        $seoPage->setHeadAttributes($headAttributes);
    }
}
