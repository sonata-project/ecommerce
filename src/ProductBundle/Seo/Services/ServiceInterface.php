<?php

namespace Sonata\ProductBundle\Seo\Services;

use Sonata\Component\Product\ProductInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;

/**
 * ServiceInterface.
 *
 * @author     Xavier Coureau <xcoureau@ekino.com>
 */
interface ServiceInterface
{
    /**
     * Add the meta information.
     *
     * @param SeoPageInterface $seoPage
     * @param ProductInterface $product
     */
    public function alterPage(SeoPageInterface $seoPage, ProductInterface $product);
}
