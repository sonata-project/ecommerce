<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

/**
 * Class ProductFinder.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductFinder implements ProductFinderInterface
{
    /**
     * @var ProductManagerInterface
     */
    private $pManager;

    /**
     * Constructor.
     *
     * @param ProductManagerInterface $pManager
     */
    public function __construct(ProductManagerInterface $pManager)
    {
        $this->pManager = $pManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getCrossSellingSimilarProducts(ProductInterface $product)
    {
        return $this->pManager->findInSameCollections($product->getProductCollections());
    }

    /**
     * {@inheritdoc}
     */
    public function getCrossSellingSimilarParentProducts(ProductInterface $product, $limit = null)
    {
        return $this->pManager->findParentsInSameCollections($product->getProductCollections(), $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpSellingSimilarProducts(ProductInterface $product)
    {
        // TODO: Implement getUpSellingSimilarProducts() method.
    }
}
