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

namespace Sonata\Component\Product;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
interface ProductFinderInterface
{
    /**
     * Gets similar product as $product in a cross selling fashion.
     *
     * @return ProductInterface[]
     */
    public function getCrossSellingSimilarProducts(ProductInterface $product);

    /**
     * Gets similar parent products as $product in a cross selling fashion.
     *
     * @return ProductInterface[]
     */
    public function getCrossSellingSimilarParentProducts(ProductInterface $product);

    /**
     * Gets similar product as $product in an up selling fashion.
     *
     * @return ProductInterface[]
     */
    public function getUpSellingSimilarProducts(ProductInterface $product);
}
