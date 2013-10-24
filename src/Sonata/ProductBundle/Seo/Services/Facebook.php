<?php

namespace Sonata\ProductBundle\Seo\Services;

use Sonata\SeoBundle\Seo\SeoPageInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\HttpFoundation\Request;

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
     * @var Request
     */
    protected $request;

    /**
     * @param Pool    $mediaPool
     * @param Request $request
     */
    public function __construct(Pool $mediaPool, Request $request)
    {
        $this->mediaPool = $mediaPool;
        $this->request = $request;
    }

    /**
     * Add the meta information
     *
     * @param SeoPageInterface $seoPage
     * @param ProductInterface $product
     * @param string|null      $currency
     *
     * @return void
     */
    public function alterPage(SeoPageInterface $seoPage, ProductInterface $product, $currency = null)
    {
        $this->registerHeaders($seoPage);

        $seoPage->addMeta('property', 'og:type', 'og:product')
            ->addMeta('property', 'og:title', $product->getName())
            ->addMeta('property', 'og:description', $product->getDescription())
            ->addMeta('property', 'og:url', $this->request->getUri());

        // If a media is available, we add the opengraph image data
        if ($image = $product->getImage()) {
            $format = 'reference';
            $provider = $this->mediaPool->getProvider($image->getProviderName());

            $seoPage->addMeta('property', 'og:image:type', $provider->generatePublicUrl($image, $format))
                ->addMeta('property', 'og:image:width', $image->getWidth())
                ->addMeta('property', 'og:image:height', $image->getHeight());
        }

        if (null !== $currency) {
            $seoPage->addMeta('property', 'product:price:amount', $product->getPrice())
                ->addMeta('property', 'product:price:currency', $currency);
        }
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