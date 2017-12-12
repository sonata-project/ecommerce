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

use Sonata\Component\Product\ProductInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;

/**
 * ServiceInterface.
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
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
