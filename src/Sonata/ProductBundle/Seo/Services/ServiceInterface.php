<?php

namespace Sonata\ProductBundle\Seo\Services;

use Sonata\SeoBundle\Seo\SeoPageInterface;
use Sonata\Component\Product\ProductInterface;

/**
 * ServiceInterface.
 *
 * @author     Xavier Coureau <xcoureau@ekino.com>
 */
interface ServiceInterface
{
    /**
     * Add the meta information
     *
     * @param SeoPageInterface $seoPage
     * @param ProductInterface $product
     * @param string|null      $currency
     *
     * @return void
     */
    public function alterPage(SeoPageInterface $seoPage, ProductInterface $product, $currency = null);
}