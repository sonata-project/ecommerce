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

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class ProductExtension extends \Twig_Extension
{
    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $basketElementClass;

    /**
     * Constructor.
     *
     * @param ProductPool          $productPool        Sonata product pool
     * @param FormFactoryInterface $formFactory        Symfony form factory
     * @param string               $basketElementClass Sonata basket element class name
     */
    public function __construct(ProductPool $productPool, FormFactoryInterface $formFactory, $basketElementClass)
    {
        $this->productPool = $productPool;
        $this->formFactory = $formFactory;
        $this->basketElementClass = $basketElementClass;
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
            new \Twig_SimpleFunction('sonata_product_stock',                    array($this, 'getProductStock')),
            new \Twig_SimpleFunction('sonata_product_form_add_basket',          array($this, 'getFormAddBasket')),
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
     * @param boolean           $vat      Returns price including VAT?
     *
     * @return float
     */
    public function getProductPrice(ProductInterface $product, CurrencyInterface $currency, $vat = false)
    {
        return $this->productPool->getProvider($product)->calculatePrice($product, $currency, $vat, 1);
    }

    /**
     * Return the available stock of the product.
     *
     * @param ProductInterface $product A product instance
     *
     * @return int
     */
    public function getProductStock(ProductInterface $product)
    {
        return $this->productPool->getProvider($product)->getStockAvailable($product);
    }

    /**
     * Returns a "add to basket" form for a specified product
     *
     * @param ProductInterface $product A product instance
     *
     * @return FormView
     */
    public function getFormAddBasket(ProductInterface $product)
    {
        $formBuilder = $this->formFactory->createNamedBuilder('add_basket', 'form', null, array(
            'data_class' => $this->basketElementClass,
            'csrf_protection' => false
        ));

        $this->productPool->getProvider($product)->defineAddBasketForm($product, $formBuilder, false);

        $form = $formBuilder->getForm()->createView();

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_product';
    }
}
