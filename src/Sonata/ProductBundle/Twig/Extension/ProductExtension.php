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

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Symfony\Component\Routing\RouterInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var DateTimeHelper
     */
    protected $dateTimeHelper;

    /**
     * Constructor.
     *
     * @param ProductPool $productPool
     * @param RouterInterface $router
     * @param DateTimeHelper $dateTimeHelper
     */
    public function __construct(ProductPool $productPool, RouterInterface $router, DateTimeHelper $dateTimeHelper)
    {
        $this->productPool      = $productPool;
        $this->router           = $router;
        $this->dateTimeHelper   = $dateTimeHelper;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sonata_product_provider',                 array($this, 'getProductProvider')),
            new \Twig_SimpleFunction('sonata_product_has_variations',           array($this, 'hasVariations')),
            new \Twig_SimpleFunction('sonata_product_has_enabled_variations',   array($this, 'hasEnabledVariations')),
            new \Twig_SimpleFunction('sonata_product_cheapest_variation',       array($this, 'getCheapestEnabledVariation')),
            new \Twig_SimpleFunction('sonata_product_cheapest_variation_price', array($this, 'getCheapestEnabledVariationPrice')),
            new \Twig_SimpleFunction('sonata_product_price',                    array($this, 'getProductPrice')),
            new \Twig_SimpleFunction('sonata_product_jstree',                   array($this, 'jsonTreeBuilder')),
        );
    }

    /**
     * Return the Provider of the given Product.
     *
     * @param $product
     *
     * @return ProductProviderInterface
     */
    public function getProductProvider(ProductInterface $product)
    {
        return $this->productPool->getProvider($product);
    }

    /**
     * Check if the product has variations.
     *
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasVariations(ProductInterface $product)
    {
        return $this->productPool->getProvider($product)->hasVariations($product);
    }

    /**
     * Check if the product has enabled variations.
     *
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasEnabledVariations(ProductInterface $product)
    {
        return $this->productPool->getProvider($product)->hasEnabledVariations($product);
    }

    /**
     * Return the cheapest variation of the product (or itself if none).
     *
     * @param ProductInterface $product
     *
     * @return ProductInterface
     */
    public function getCheapestEnabledVariation(ProductInterface $product)
    {
        if (!$this->productPool->getProvider($product)->hasVariations($product)) {
            return $product;
        }

        return $this->productPool->getProvider($product)->getCheapestEnabledVariation($product);
    }

    /**
     * Return the cheapest variation price of the product (or itself if none).
     *
     * @param ProductInterface $product
     *
     * @return ProductInterface
     */
    public function getCheapestEnabledVariationPrice(ProductInterface $product)
    {
        return $this->productPool->getProvider($product)->getCheapestEnabledVariation($product)->getPrice();
    }

    /**
     * Return the calculated price of the product.
     *
     * @param ProductInterface  $product  A product instance
     * @param CurrencyInterface $currency A currency instance
     *
     * @return float
     */
    public function getProductPrice(ProductInterface $product, CurrencyInterface $currency)
    {
        return $this->productPool->getProvider($product)->calculatePrice($product, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_product';
    }

    /**
     * @param ProductInterface         $product
     *
     * @return string
     */
    public function jsonTreeBuilder(ProductInterface $product)
    {
        $provider = $this->getProductProvider($product);

        if (!$provider->hasEnabledVariations($product)) {
            return array();
        }

        $return = array();
        $variations = $product->getVariations();
        $nbItems = count($variations);
        $previousPropertyName = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($provider->getVariationFields($product) as $field) {
            for ($j = 0; $j < $nbItems; ++$j) {
                $variation = $variations[$j];

                $value = $accessor->getValue($variation, $field);
                $value = $this->formatTimestampProperty($value);

                if (!isset($return[$field][$value])) {
                    $return[$field][$value]['uri'] = array();
                }

                $productUri = $this->router->generate('sonata_product_view', array(
                    'productId' => $variation->getId(),
                    'slug'      => $variation->getSlug(),
                ));
                $return[$field][$value]['uri'][] = $productUri;

                if ($previousPropertyName) {
                    $previousValue = $accessor->getValue($variation, $previousPropertyName);
                    $previousValue = $this->formatTimestampProperty($previousValue);
                    $return[$previousPropertyName][$previousValue][$field] = &$return[$field][$value];
                }
            }

            $previousPropertyName = $field;
        }

        return $return;
    }

    /**
     * @param mixed $input
     *
     * @return string
     */
    protected function formatTimestampProperty($input)
    {
        if ($input instanceof \DateTime) {
            $input = $this->dateTimeHelper->formatDateTime($input);
        }

        return $input;
    }
}
