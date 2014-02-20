<?php

namespace Sonata\ProductBundle\Seo\Services;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\MediaBundle\Provider\Pool;
use Sonata\IntlBundle\Templating\Helper\NumberHelper;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * FacebookService.
 *
 * @author     Xavier Coureau <xcoureau@ekino.com>
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
     * @param RouterInterface           $router
     * @param Pool                      $mediaPool
     * @param NumberHelper              $numberHelper
     * @param CurrencyDetectorInterface $currencyDetector
     * @param string                    $domain
     * @param                           $mediaFormat
     */
    public function __construct(RouterInterface $router, Pool $mediaPool, NumberHelper $numberHelper, CurrencyDetectorInterface $currencyDetector, $domain, $mediaFormat)
    {
        $this->router    = $router;
        $this->mediaPool = $mediaPool;
        $this->numberHelper = $numberHelper;
        $this->currencyDetector = $currencyDetector;
        $this->domain = $domain;
        $this->mediaFormat = $mediaFormat;
    }

    /**
     * @param SeoPageInterface $seoPage
     * @param ProductInterface $product
     */
    public function alterPage(SeoPageInterface $seoPage, ProductInterface $product)
    {
        $this->registerHeaders($seoPage);

        $seoPage->addMeta('property', 'og:type', 'og:product')
            ->addMeta('property', 'og:title', $product->getName())
            ->addMeta('property', 'og:description', $product->getDescription())
            ->addMeta('property', 'og:url', $this->router->generate('sonata_product_view', array(
                'slug'      => $product->getSlug(),
                'productId' => $product->getId()
            ), true))
            ->addMeta('property', 'product:price:amount', $this->numberHelper->formatDecimal($product->getPrice()))
            ->addMeta('property', 'product:price:currency', $this->currencyDetector->getCurrency());

        // If a media is available, we add the opengraph image data
        if ($image = $product->getImage()) {
            $this->addImageInfo($image, $seoPage);
        }
    }

    /**
     * @param MediaInterface   $image
     * @param SeoPageInterface $seoPage
     */
    protected function addImageInfo(MediaInterface $image, SeoPageInterface $seoPage)
    {
        $provider = $this->mediaPool->getProvider($image->getProviderName());

        $seoPage->addMeta('property', 'og:image', $provider->generatePublicUrl($image, $this->mediaFormat))
            ->addMeta('property', 'og:image:width', $image->getWidth())
            ->addMeta('property', 'og:image:height', $image->getHeight())
            ->addMeta('property', 'og:image:type', $image->getContentType());
    }

    /**
     * @param SeoPageInterface $seoPage
     *
     * @return void
     */
    protected function registerHeaders(SeoPageInterface $seoPage)
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
