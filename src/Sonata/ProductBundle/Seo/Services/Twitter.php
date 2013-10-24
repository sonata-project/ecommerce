<?php

namespace Sonata\ProductBundle\Seo\Services;

use Sonata\SeoBundle\Seo\SeoPageInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\HttpFoundation\Request;

/**
 * TwitterService.
 *
 * @author     Xavier Coureau <xcoureau@ekino.com>
 */
class Twitter implements ServiceInterface
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
        $seoPage->addMeta('name', 'twitter:card', 'product')
            ->addMeta('name', 'twitter:site', $product->getName())
            ->addMeta('name', 'twitter:title', $product->getName())
            ->addMeta('name', 'twitter:description', substr($product->getDescription(), 0, 200));

        if (null !== $currency) {
            $seoPage->addMeta('name', 'twitter:label1', 'Price')
                ->addMeta('name', 'twitter:data1', sprintf('%.2f%s', $product->getPrice(), $currency))
                // TODO provide useful information
                ->addMeta('name', 'twitter:label2', 'Price')
                ->addMeta('name', 'twitter:data2', sprintf('%.2f%s', $product->getPrice(), $currency));
        }

        $seoPage->addMeta('name', 'twitter:domain', $this->request->getHost());

        // If a media is available, we add the image data
        if ($image = $product->getImage()) {
            $format = 'reference';
            $provider = $this->mediaPool->getProvider($image->getProviderName());

            $seoPage->addMeta('property', 'twitter:image:src', $provider->generatePublicUrl($image, $format));
        }
    }
}