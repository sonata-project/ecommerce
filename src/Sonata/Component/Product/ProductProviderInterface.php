<?php

/*
 * This file is part of the Sonata product.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;
use Sonata\Component\Currency\CurrencyInterface;

interface ProductProviderInterface
{
    /**
     * @param \Sonata\Component\Basket\BasketElementManagerInterface $basketElementManager
     */
    public function setBasketElementManager(BasketElementManagerInterface $basketElementManager);

    /**
     * @return \Sonata\Component\Basket\BasketElementManagerInterface
     */
    public function getBasketElementManager();

    /**
     * @param ProductCategoryManagerInterface $productCategoryManager
     */
    public function setProductCategoryManager(ProductCategoryManagerInterface $productCategoryManager);

    /**
     * @return ProductCategoryManagerInterface
     */
    public function getProductCategoryManager();

    /**
     * @param ProductCollectionManagerInterface $productCollectionManager
     */
    public function setProductCollectionManager(ProductCollectionManagerInterface $productCollectionManager);

    /**
     * @return ProductCollectionManagerInterface
     */
    public function getProductCollectionManager();

    /**
     * @return string
     */
    public function getBaseControllerName();

    /**
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param \Symfony\Component\Form\FormBuilder        $formBuilder
     * @param array                                      $options
     */
    public function defineAddBasketForm(ProductInterface $product, FormBuilder $formBuilder, array $options = array());

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Symfony\Component\Form\FormBuilder             $formBuilder
     * @param array                                           $options
     */
    public function defineBasketElementForm(BasketElementInterface $basketElement, FormBuilder $formBuilder, array $options = array());

    /**
     * return true if the basket element is still valid
     *
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     * @param ProductInterface                                $product
     * @param \Sonata\Component\Basket\BasketElementInterface $newBasketElement
     */
    public function basketAddProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $newBasketElement);

    /**
     * Merge a product with another when the product is already present into the basket
     *
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     * @param ProductInterface                                $product
     * @param \Sonata\Component\Basket\BasketElementInterface $newBasketElement
     */
    public function basketMergeProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $newBasketElement);

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     *
     * @return boolean true if the basket element is still valid
     */
    public function isValidBasketElement(BasketElementInterface $basketElement);

    /**
     * This method return the return price of basket element, this method
     * allow to update the price of the basket element depend on the presence
     * of another product
     *
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     *
     * @return float the unit price of the basketElement
     */
    public function basketElementCalculateUnitPrice(BasketInterface $basket, BasketElementInterface $basketElement);

    /**
     * Calculate the product price depending on the currency
     *
     * @param ProductInterface          $product
     * @param CurrencyInterface|null    $currency
     * @param int                       $quantity  Defaults to one
     * @param int                       $precision The precision of the arbitrary number for the multiplication (see http://www.php.net/manual/en/function.bcscale.php)
     *
     * @return float
     */
    public function calculatePrice(ProductInterface $product, CurrencyInterface $currency, $quantity = 1, $precision = 3);

    /**
     * Return true if the product can be added to the provided basket
     *
     * @param \Sonata\Component\Basket\BasketInterface   $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array                                      $options
     *
     * @return boolean
     */
    public function isAddableToBasket(BasketInterface $basket, ProductInterface $product, array $options = array());

    /**
     * @param null|ProductInterface $product
     * @param array                 $options
     *
     * @return BasketElementInterface
     */
    public function createBasketElement(ProductInterface $product = null, array $options = array());

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     */
    public function configureShowFields(ShowMapper $showMapper);

    /**
     * @param FormMapper $formMapper
     * @param boolean    $isVariation
     */
    public function buildEditForm(FormMapper $formMapper, $isVariation = false);

    /**
     * @param FormMapper $formMapper
     */
    public function buildCreateForm(FormMapper $formMapper);

    /**
     * @param  \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param  null|\Sonata\Component\Product\ProductInterface $product
     * @param  array                                           $options
     * @return void
     */
    public function buildBasketElement(BasketElementInterface $basketElement, ProductInterface $product = null, array $options = array());

    /**
     * return an array of errors if any, you can also manipulate the basketElement if require
     * please not you always work with a clone version of the basketElement.
     *
     * If the basket is valid it will then replace the one in session
     *
     * @param  \Sonata\AdminBundle\Validator\ErrorElement      $errorElement
     * @param  \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param  \Sonata\Component\Basket\BasketInterface        $basket
     * @return void
     */
    public function validateFormBasketElement(ErrorElement $errorElement, BasketElementInterface $basketElement, BasketInterface $basket);

    /**
     * Creates a variation from a given Product and its dependencies.
     *
     * @param ProductInterface $product          Product to duplicate
     * @param boolean          $copyDependencies If false, duplicates only Product (without dependencies)
     *
     * @throws \RuntimeException
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function createVariation(ProductInterface $product, $copyDependencies = true);

    /**
     * Synchronizes all parent Product data to its variations (or a single one if $targetVariation is specified).
     *
     * @param ProductInterface $product    Parent Product
     * @param ArrayCollection  $variations Optional target variations to synchronize
     */
    public function synchronizeVariations(ProductInterface $product, ArrayCollection $variations = null);

    /**
     * Synchronizes parent Product data to its variations (or a single one if $targetVariation is specified).
     *
     * @param ProductInterface $product    Parent Product
     * @param ArrayCollection  $variations Optional target variations to synchronize
     */
    public function synchronizeVariationsProduct(ProductInterface $product, ArrayCollection $variations = null);

    /**
     * Synchronizes parent Product deliveries to its variations (or a single one if $targetVariation is specified).
     *
     * @param ProductInterface $product    Parent Product
     * @param ArrayCollection  $variations Optional target variations to synchronize
     */
    public function synchronizeVariationsDeliveries(ProductInterface $product, ArrayCollection $variations = null);

    /**
     * Synchronizes parent Product categories to its variations (or a single one if $targetVariation is specified).
     *
     * @param ProductInterface $product    Parent Product
     * @param ArrayCollection  $variations Optional target variations to synchronize
     */
    public function synchronizeVariationsCategories(ProductInterface $product, ArrayCollection $variations = null);

    /**
     * Synchronizes parent Product collections to its variations (or a single one if $targetVariation is specified).
     *
     * @param ProductInterface $product    Parent Product
     * @param ArrayCollection  $variations Optional target variations to synchronize
     */
    public function synchronizeVariationsCollections(ProductInterface $product, ArrayCollection $variations = null);

    /**
     * Synchronizes parent Product packages to its variations (or a single one if $targetVariation is specified).
     *
     * @param ProductInterface $product    Parent Product
     * @param ArrayCollection  $variations Optional target variations to synchronize
     */
    public function synchronizeVariationsPackages(ProductInterface $product, ArrayCollection $variations = null);

    /**
     * Check if the product has variations
     *
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasVariations(ProductInterface $product);

    /**
     * Return true if Product has enabled variation(s).
     *
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasEnabledVariations(ProductInterface $product);

    /**
     * Return the list of enabled product variations
     *
     * @param ProductInterface $product
     *
     * @return ArrayCollection
     */
    public function getEnabledVariations(ProductInterface $product);

    /**
     * Fetch the cheapest variation if provided/existing
     *
     * @param ProductInterface $product
     *
     * @return null|ProductInterface
     */
    public function getCheapestEnabledVariation(ProductInterface $product);

    /**
     * return the stock available for the current product
     *
     * @param  \Sonata\Component\Product\ProductInterface $product
     * @return int                                        the stock available
     */
    public function getStockAvailable(ProductInterface $product);

    /**
     * Gets the fields on which the product might be filtered in the catalog view
     *
     * @return mixed
     */
    public function getFilters();

    /**
     * Gets the possible values for $fields (or variation fields if not set)
     *
     * @param ProductInterface $product
     * @param array            $fields
     *
     * @return array
     */
    public function getVariationsChoices(ProductInterface $product, array $fields = array());

    /**
     * Gets the properties values of $product amongst variation fields or $fields if set
     *
     * @param ProductInterface $product
     * @param array            $fields
     *
     * @return array
     */
    public function getVariatedProperties(ProductInterface $product, array $fields = array());

    /**
     * Gets the variation matching $choices from master product $product
     *
     * @param ProductInterface $product
     * @param array            $choices
     */
    public function getVariation(ProductInterface $product, array $choices = array());

    /**
     * Update the stock value of a given Product id.
     *
     * @param ProductInterface|int    $product
     * @param ProductManagerInterface $productManager
     * @param int                     $diff
     */
    public function updateStock($product, ProductManagerInterface $productManager, $diff);
}
