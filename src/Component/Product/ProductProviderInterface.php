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

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

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

    public function getTemplatesPath(): string;

    /**
     * @param \Sonata\Component\Product\ProductInterface $product      A Sonata product instance
     * @param \Symfony\Component\Form\FormBuilder        $formBuilder  Symfony form builder
     * @param bool                                       $showQuantity Specifies if quantity field will be displayed (default true)
     * @param array                                      $options      An options array
     */
    public function defineAddBasketForm(ProductInterface $product, FormBuilder $formBuilder, $showQuantity = true, array $options = []);

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Symfony\Component\Form\FormBuilder             $formBuilder
     * @param array                                           $options
     */
    public function defineBasketElementForm(BasketElementInterface $basketElement, FormBuilder $formBuilder, array $options = []);

    /**
     * return true if the basket element is still valid.
     *
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     * @param ProductInterface                                $product
     * @param \Sonata\Component\Basket\BasketElementInterface $newBasketElement
     */
    public function basketAddProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $newBasketElement);

    /**
     * Merge a product with another when the product is already present into the basket.
     *
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     * @param ProductInterface                                $product
     * @param \Sonata\Component\Basket\BasketElementInterface $newBasketElement
     */
    public function basketMergeProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $newBasketElement);

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     *
     * @return bool true if the basket element is still valid
     */
    public function isValidBasketElement(BasketElementInterface $basketElement);

    /**
     * Updates basket element different prices computation fields values.
     *
     * @param BasketInterface        $basket        A basket instance
     * @param BasketElementInterface $basketElement A basket element instance
     * @param ProductInterface       $product       A product instance
     */
    public function updateComputationPricesFields(BasketInterface $basket, BasketElementInterface $basketElement, ProductInterface $product);

    /**
     * Calculate the product price depending on the currency.
     *
     * @param ProductInterface       $product  A product instance
     * @param CurrencyInterface|null $currency A currency instance
     * @param bool                   $vat      Returns price including VAT?
     * @param int                    $quantity Defaults to one
     *
     * @return float
     */
    public function calculatePrice(ProductInterface $product, CurrencyInterface $currency, $vat = false, $quantity = 1);

    /**
     * Return true if the product can be added to the provided basket.
     *
     * @param \Sonata\Component\Basket\BasketInterface   $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @param array                                      $options
     *
     * @return bool
     */
    public function isAddableToBasket(BasketInterface $basket, ProductInterface $product, array $options = []);

    /**
     * @param ProductInterface|null $product
     * @param array                 $options
     *
     * @return BasketElementInterface
     */
    public function createBasketElement(ProductInterface $product = null, array $options = []);

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     */
    public function configureShowFields(ShowMapper $showMapper);

    /**
     * Build form by adding provider fields.
     *
     * @param FormBuilderInterface $builder     Symfony form builder
     * @param array                $options     An options array
     * @param bool                 $isVariation Is the product a variation of a master product?
     */
    public function buildForm(FormBuilderInterface $builder, array $options, $isVariation = false);

    /**
     * @param FormMapper $formMapper
     * @param bool       $isVariation
     */
    public function buildEditForm(FormMapper $formMapper, $isVariation = false);

    /**
     * @param FormMapper $formMapper
     */
    public function buildCreateForm(FormMapper $formMapper);

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Sonata\Component\Product\ProductInterface|null $product
     * @param array                                           $options
     */
    public function buildBasketElement(BasketElementInterface $basketElement, ProductInterface $product = null, array $options = []);

    /**
     * return an array of errors if any, you can also manipulate the basketElement if require
     * please not you always work with a clone version of the basketElement.
     *
     * If the basket is valid it will then replace the one in session
     *
     * @param \Sonata\CoreBundle\Validator\ErrorElement       $errorElement
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     */
    public function validateFormBasketElement(ErrorElement $errorElement, BasketElementInterface $basketElement, BasketInterface $basket);

    /**
     * Creates a variation from a given Product and its dependencies.
     *
     * @param ProductInterface $product          Product to duplicate
     * @param bool             $copyDependencies If false, duplicates only Product (without dependencies)
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
     * Check if the product has variations.
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
     * Return the list of enabled product variations.
     *
     * @param ProductInterface $product
     *
     * @return ArrayCollection
     */
    public function getEnabledVariations(ProductInterface $product);

    /**
     * Fetch the cheapest variation if provided/existing.
     *
     * @param ProductInterface $product
     *
     * @return ProductInterface|null
     */
    public function getCheapestEnabledVariation(ProductInterface $product);

    /**
     * return the stock available for the current product.
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @return int the stock available
     */
    public function getStockAvailable(ProductInterface $product);

    /**
     * Gets the fields on which the product might be filtered in the catalog view.
     *
     * @return mixed
     */
    public function getFilters();

    /**
     * Gets the possible values for $fields (or variation fields if not set).
     *
     * @param ProductInterface $product
     * @param array            $fields
     *
     * @return array
     */
    public function getVariationsChoices(ProductInterface $product, array $fields = []);

    /**
     * Gets the properties values of $product amongst variation fields or $fields if set.
     *
     * @param ProductInterface $product
     * @param array            $fields
     *
     * @return array
     */
    public function getVariatedProperties(ProductInterface $product, array $fields = []);

    /**
     * Gets the variation matching $choices from master product $product.
     *
     * @param ProductInterface $product
     * @param array            $choices
     */
    public function getVariation(ProductInterface $product, array $choices = []);

    /**
     * Update the stock value of a given Product id.
     *
     * @param ProductInterface|int    $product
     * @param ProductManagerInterface $productManager
     * @param int                     $diff
     */
    public function updateStock($product, ProductManagerInterface $productManager, $diff);
}
