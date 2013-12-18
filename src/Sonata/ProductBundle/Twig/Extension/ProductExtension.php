<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Twig\Extension;

use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Product\ProductProviderInterface;

/**
 * @author Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class ProductExtension extends \Twig_Extension
{
    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * Constructor.
     *
     * @param ProductPool $productPool
     */
    public function __construct(ProductPool $productPool)
    {
        $this->productPool = $productPool;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sonata_product_provider', array($this, 'getProductProvider')),
        );
    }

    /**
     * Return the Provider of the given Product.
     *
     * @param $product
     *
     * @return ProductProviderInterface
     */
    public function getProductProvider($product)
    {
        return $this->productPool->getProvider($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_product';
    }
}
