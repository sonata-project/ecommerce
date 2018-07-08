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
class ProductFinder implements ProductFinderInterface
{
    /**
     * @var ProductManagerInterface
     */
    private $pManager;

    /**
     * @param ProductManagerInterface $pManager
     */
    public function __construct(ProductManagerInterface $pManager)
    {
        $this->pManager = $pManager;
    }

    public function getCrossSellingSimilarProducts(ProductInterface $product)
    {
        return $this->pManager->findInSameCollections($product->getProductCollections());
    }

    public function getCrossSellingSimilarParentProducts(ProductInterface $product, $limit = null)
    {
        return $this->pManager->findParentsInSameCollections($product->getProductCollections(), $limit);
    }

    public function getUpSellingSimilarProducts(ProductInterface $product): void
    {
        // TODO: Implement getUpSellingSimilarProducts() method.
    }
}
