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

namespace Sonata\ProductBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializerInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\InvalidProductException;
use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Currency\CurrencyPriceCalculatorInterface;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\Component\Event\AddBasketElementEvent;
use Sonata\Component\Event\AfterCalculatePriceEvent;
use Sonata\Component\Event\BasketEvents;
use Sonata\Component\Event\BeforeCalculatePriceEvent;
use Sonata\Component\Form\Transformer\QuantityTransformer;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductCollectionManagerInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\Form\Validator\ErrorElement;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class BaseProductProvider implements ProductProviderInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $variationFields = [];

    /**
     * @var string
     */
    protected $code;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ProductCategoryManagerInterface
     */
    protected $productCategoryManager;

    /**
     * @var ProductCollectionManagerInterface
     */
    protected $productCollectionManager;

    /**
     * @var BasketElementManagerInterface
     */
    protected $basketElementManager;

    /**
     * @var string
     */
    protected $orderElementClassName;

    /**
     * @var CurrencyPriceCalculatorInterface
     */
    protected $currencyPriceCalculator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function setCurrencyPriceCalculator(CurrencyPriceCalculatorInterface $currencyPriceCalculator): void
    {
        $this->currencyPriceCalculator = $currencyPriceCalculator;
    }

    /**
     * @return \Sonata\Component\Currency\CurrencyPriceCalculatorInterface
     */
    public function getCurrencyPriceCalculator()
    {
        return $this->currencyPriceCalculator;
    }

    public function setBasketElementManager(BasketElementManagerInterface $basketElementManager): void
    {
        $this->basketElementManager = $basketElementManager;
    }

    /**
     * @param string $orderElementClassName
     */
    public function setOrderElementClassName($orderElementClassName): void
    {
        $this->orderElementClassName = $orderElementClassName;
    }

    /**
     * @return \Sonata\Component\Basket\BasketElementManagerInterface
     */
    public function getBasketElementManager()
    {
        return $this->basketElementManager;
    }

    public function setProductCategoryManager(ProductCategoryManagerInterface $productCategoryManager): void
    {
        $this->productCategoryManager = $productCategoryManager;
    }

    public function getProductCategoryManager()
    {
        return $this->productCategoryManager;
    }

    public function setProductCollectionManager(ProductCollectionManagerInterface $productCollectionManager): void
    {
        $this->productCollectionManager = $productCollectionManager;
    }

    public function getProductCollectionManager()
    {
        return $this->productCollectionManager;
    }

    public function setOptions(array $options = []): void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return array|null
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    ////////////////////////////////////////////////
    //   ORDER RELATED FUNCTIONS

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement A basket element instance
     * @param string                                          $format        A format to obtain raw product
     *
     * @return \Sonata\Component\Order\OrderElementInterface
     */
    public function createOrderElement(BasketElementInterface $basketElement, $format = 'json')
    {
        /** @var OrderElementInterface $orderElement */
        $orderElement = new $this->orderElementClassName();
        $orderElement->setQuantity($basketElement->getQuantity());
        $orderElement->setUnitPriceExcl($basketElement->getUnitPrice(false));
        $orderElement->setUnitPriceInc($basketElement->getUnitPrice(true));
        $orderElement->setPrice($basketElement->getPrice(true));
        $orderElement->setVatRate($basketElement->getVatRate());
        $orderElement->setDesignation($basketElement->getName());
        $orderElement->setProductType($this->getCode());
        $orderElement->setStatus(OrderInterface::STATUS_PENDING);
        $orderElement->setDeliveryStatus(ServiceDeliveryInterface::STATUS_OPEN);
        $orderElement->setCreatedAt(new \DateTime());
        $orderElement->setOptions($basketElement->getOptions());

        $product = $basketElement->getProduct();
        $orderElement->setDescription($product->getDescription());
        $orderElement->setProductId($product->getId());
        $orderElement->setRawProduct($this->getRawProduct($product, $format));

        return $orderElement;
    }

    /**
     * @param string $format
     *
     * @return array
     */
    public function getRawProduct(ProductInterface $product, $format = 'json')
    {
        return json_decode($this->serializer->serialize($product, $format), true);
    }

    /**
     * @param string $type
     * @param string $format
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function getProductFromRaw(OrderElementInterface $orderElement, $type, $format = 'json')
    {
        return $this->serializer->deserialize(json_encode($orderElement->getRawProduct()), $type, $format);
    }

    ////////////////////////////////////////////////
    //   VARIATION RELATED FUNCTIONS

    public function hasVariations(ProductInterface $product)
    {
        return 0 < \count($product->getVariations());
    }

    public function hasEnabledVariations(ProductInterface $product)
    {
        if (!$this->hasVariations($product)) {
            return false;
        }

        foreach ($product->getVariations() as $variation) {
            /** @var ProductInterface $variation */
            if ($variation->isEnabled()) {
                return true;
            }
        }

        return false;
    }

    public function getEnabledVariations(ProductInterface $product)
    {
        $result = new ArrayCollection();

        if (!$this->hasVariations($product)) {
            return $result;
        }

        foreach ($product->getVariations() as $variation) {
            /** @var ProductInterface $variation */
            if ($variation->isEnabled()) {
                $result->add($variation);
            }
        }

        return $result;
    }

    public function getVariatedProperties(ProductInterface $product, array $fields = [])
    {
        if (null === $product->getParent()) {
            // This is not a variation, hence no properties variated
            return [];
        }

        $fields = $this->getMergedFields($fields);

        $accessor = PropertyAccess::createPropertyAccessor();

        $properties = [];

        foreach ($fields as $field) {
            $properties[$field] = $accessor->getValue($product, $field);
        }

        return $properties;
    }

    public function getVariationsChoices(ProductInterface $product, array $fields = [])
    {
        if (!($this->hasEnabledVariations($product) || $product->getParent())) {
            // Product is neither master nor a variation, not concerned
            return [];
        }

        $fields = $this->getMergedFields($fields);

        // We retrieve the variations fresh from DB so we may find the values
        $variations = $this->getEnabledVariations($product->getParent() ?: $product);

        $accessor = PropertyAccess::createPropertyAccessor();

        $choices = [];
        foreach ($variations as $mVariation) {
            foreach ($fields as $field) {
                $variationValue = $accessor->getValue($mVariation, $field);
                if (!\array_key_exists($field, $choices) || !\in_array($variationValue, $choices[$field], true)) {
                    $choices = array_merge_recursive($choices, [$field => [$variationValue]]);
                }
            }
        }

        // Sort options
        foreach ($fields as $field) {
            natcasesort($choices[$field]);
        }

        return $choices;
    }

    public function getVariation(ProductInterface $product, array $choices = [])
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->getEnabledVariations($product) as $variation) {
            foreach ($choices as $choice => $value) {
                if (!\in_array($choice, $this->getVariationFields(), true)) {
                    throw new \RuntimeException("The field '".$choice."' is not among the variation fields");
                }

                if ($accessor->getValue($variation, $choice) !== $value) {
                    continue 2;
                }
            }

            return $variation;
        }
    }

    /**
     * @param  $name
     *
     * @return bool return true if the field $name is a variation
     */
    public function isVariateBy($name)
    {
        return \in_array($name, $this->variationFields, true);
    }

    /**
     * @return bool return true if the product haas some variation fields
     */
    public function hasVariationFields()
    {
        return \count($this->getVariationFields()) > 0;
    }

    public function setVariationFields(array $fields = []): void
    {
        $this->variationFields = $fields;
    }

    /**
     * @return array
     */
    public function getVariationFields()
    {
        return $this->variationFields;
    }

    public function buildForm(FormBuilderInterface $builder, array $options, $isVariation = false): void
    {
    }

    public function buildEditForm(FormMapper $formMapper, $isVariation = false): void
    {
        $formMapper->with('Product');

        $formMapper->add('enabled');

        $formMapper->add('name');
        $formMapper->add('sku');

        $formMapper
            ->add('price', NumberType::class)
            ->add('priceIncludingVat')
            ->add('vatRate', NumberType::class)
            ->add('stock', IntegerType::class)
        ;

        if (!$isVariation || \in_array('description', $this->variationFields, true)) {
            $formMapper->add('description', FormatterType::class, [
                'source_field' => 'rawDescription',
                'source_field_options' => ['attr' => ['class' => 'span10', 'rows' => 20]],
                'format_field' => 'descriptionFormatter',
                'target_field' => 'description',
                'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
            ]);
        }

        if (!$isVariation || \in_array('short_description', $this->variationFields, true)) {
            $formMapper->add('shortDescription', FormatterType::class, [
                'source_field' => 'rawShortDescription',
                'source_field_options' => ['attr' => ['class' => 'span10', 'rows' => 20]],
                'format_field' => 'shortDescriptionFormatter',
                'target_field' => 'shortDescription',
                'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
            ]);
        }

        $formMapper->end();

        if (!$isVariation || \in_array('image', $this->variationFields, true) || \in_array('gallery', $this->variationFields, true)) {
            $formMapper->with('Media');

            if (!$isVariation || \in_array('image', $this->variationFields, true)) {
                $formMapper->add('image', ModelListType::class, [
                    'required' => false,
                ], [
                    'link_parameters' => [
                        'context' => 'product_catalog',
                        'filter' => ['context' => ['value' => 'product_catalog']],
                        'provider' => '',
                    ],
                ]);
            }

            if (!$isVariation || \in_array('gallery', $this->variationFields, true)) {
                $formMapper->add('gallery', ModelListType::class, [
                    'required' => false,
                ], [
                    'link_parameters' => [
                        'context' => 'product_catalog',
                        'filter' => ['context' => ['value' => 'product_catalog']],
                        'provider' => '',
                    ],
                ]);
            }

            $formMapper->end();
        }
    }

    public function buildCreateForm(FormMapper $formMapper): void
    {
        $this->buildEditForm($formMapper);
    }

    public function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('sku')
            ->add('description')
            ->add('price')
            ->add('number')
            ->add('vatRate')
            ->add('stock')
            ->add('enabled')
        ;
    }

    public function createVariation(ProductInterface $product, $copyDependencies = true)
    {
        if ($product->isVariation()) {
            throw new \RuntimeException('Cannot create a variation from a variation product');
        }

        $variation = clone $product;
        $variation->setParent($product);
        $variation->setId(null);
        $variation->setVariations(new ArrayCollection());
        $variation->setDeliveries(new ArrayCollection());
        $variation->setProductCategories(new ArrayCollection());
        $variation->setProductCollections(new ArrayCollection());
        $variation->setPackages(new ArrayCollection());

        $product->addVariation($variation);

        $variationCollection = new ArrayCollection([$variation]);

        $this->synchronizeVariationsProduct($product, $variationCollection);

        if ($copyDependencies) {
            $this->synchronizeVariationsDeliveries($product, $variationCollection);
            $this->synchronizeVariationsPackages($product, $variationCollection);
            $this->synchronizeVariationsCategories($product, $variationCollection);
            $this->synchronizeVariationsCollections($product, $variationCollection);
        }

        $variation->setEnabled(false);
        $variation->setName(sprintf('%s (duplicated)', $product->getName()));
        $variation->setSku(sprintf('%s_DUPLICATE', $product->getSku()));

        return $variation;
    }

    public function synchronizeVariations(ProductInterface $product, ArrayCollection $variations = null): void
    {
        $this->synchronizeVariationsProduct($product, $variations);
        $this->synchronizeVariationsDeliveries($product, $variations);
        $this->synchronizeVariationsCategories($product, $variations);
        $this->synchronizeVariationsPackages($product, $variations);
        $this->synchronizeVariationsCollections($product, $variations);
    }

    public function synchronizeVariationsProduct(ProductInterface $product, ArrayCollection $variations = null): void
    {
        $variationFields = array_merge(['id', 'parent'], $this->getVariationFields());

        $values = $product->toArray();

        foreach ($variationFields as $field) {
            if (\array_key_exists($field, $values)) {
                unset($values[$field]);
            }
        }

        if (!$variations) {
            $variations = $product->getVariations();
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($variations as $variation) {
            foreach ($values as $name => $value) {
                $accessor->setValue($variation, $name, $value);
            }
        }
    }

    public function synchronizeVariationsDeliveries(ProductInterface $product, ArrayCollection $variations = null): void
    {
        if (\in_array('deliveries', $this->getVariationFields(), true)) {
            return;
        }

        if (!$variations) {
            $variations = $product->getVariations();
        }

        $productDeliveries = $product->getDeliveries();

        /** @var ProductInterface $variation */
        foreach ($variations as $variation) {
            $variationDeliveries = $variation->getDeliveries();

            // browsing variation deliveries and remove excessing deliveries
            foreach ($variationDeliveries as $productDelivery) {
                if ($productDelivery && !$productDeliveries->contains($productDelivery)) {
                    $variation->removeDelivery($productDelivery);
                }
            }

            // browsing Product deliveries and add missing deliveries
            foreach ($productDeliveries as $productDelivery) {
                if ($productDelivery && !$variationDeliveries->contains($productDelivery)) {
                    $delivery = clone $productDelivery;
                    $variation->addDelivery($delivery);
                }
            }
        }
    }

    public function synchronizeVariationsCategories(ProductInterface $product, ArrayCollection $variations = null): void
    {
        if (\in_array('productCategories', $this->getVariationFields(), true)) {
            return;
        }

        if (!$variations) {
            $variations = $product->getVariations();
        }

        $productCategories = $product->getProductCategories();

        /** @var ProductInterface $variation */
        foreach ($variations as $variation) {
            // browsing variation categories and remove excessing categories
            foreach ($variation->getCategories() as $variationCategory) {
                if ($variationCategory && !$productCategories->contains($variationCategory)) {
                    $this->productCategoryManager->removeCategoryFromProduct($variation, $variationCategory);
                }
            }

            // browsing Product categories and add missing categories
            foreach ($productCategories as $productCategory) {
                $category = $productCategory->getCategory();

                if ($category && !$variation->getCategories()->contains($category)) {
                    $this->productCategoryManager->addCategoryToProduct($variation, $category, $productCategory->getMain());
                }
            }
        }
    }

    public function synchronizeVariationsCollections(ProductInterface $product, ArrayCollection $variations = null): void
    {
        if (\in_array('productCollections', $this->getVariationFields(), true)) {
            return;
        }

        if (!$variations) {
            $variations = $product->getVariations();
        }

        $productCollections = $product->getCollections();

        /** @var ProductInterface $variation */
        foreach ($variations as $variation) {
            $variationCollections = $variation->getCollections();

            // browsing variation collections and remove excessing collections
            foreach ($variationCollections as $variationCollection) {
                if ($variationCollection && !$productCollections->contains($variationCollection)) {
                    $this->productCollectionManager->removeCollectionFromProduct($variation, $variationCollection);
                }
            }

            // browsing Product collections and add missing collections
            foreach ($productCollections as $productCollection) {
                if ($productCollection && !$variationCollections->contains($productCollection)) {
                    $this->productCollectionManager->addCollectionToProduct($variation, $productCollection);
                }
            }
        }
    }

    public function synchronizeVariationsPackages(ProductInterface $product, ArrayCollection $variations = null): void
    {
        if (\in_array('packages', $this->getVariationFields(), true)) {
            return;
        }

        if (!$variations) {
            $variations = $product->getVariations();
        }

        $productPackages = $product->getPackages();

        /** @var ProductInterface $variation */
        foreach ($variations as $variation) {
            $variationPackages = $variation->getPackages();

            // browsing variation packages and remove excessing packages
            foreach ($variationPackages as $variationPackage) {
                if ($variationPackage && !$productPackages->contains($variationPackage)) {
                    $variation->removePackage($variationPackage);
                }
            }

            // browsing Product packages and add missing packages
            foreach ($productPackages as $productPackage) {
                if ($productPackage && !$variationPackages->contains($productPackage)) {
                    $package = clone $productPackage;
                    $variation->addPackage($package);
                }
            }
        }
    }

    /////////////////////////////////////////////////////
    // BASKET RELATED FUNCTIONS

    /**
     * @see \Sonata\Component\Product\ProductProviderInterface::createBasketElement()
     *
     * @return object
     */
    public function createBasketElement(ProductInterface $product = null, array $options = [])
    {
        $basketElement = $this->getBasketElementManager()->create();
        $this->buildBasketElement($basketElement, $product, $options);

        return $basketElement;
    }

    public function buildBasketElement(BasketElementInterface $basketElement, ProductInterface $product = null, array $options = []): void
    {
        if ($product) {
            $basketElement->setProduct($this->code, $product);

            if (!$basketElement->getQuantity() && 0 !== $basketElement->getQuantity()) {
                $basketElement->setQuantity(1);
            }
        }

        $basketElement->setOptions($options);
    }

    /**
     * This function return the form used in the product view page.
     *
     * @param \Sonata\Component\Product\ProductInterface $product      A Sonata product instance
     * @param \Symfony\Component\Form\FormBuilder        $formBuilder  Symfony form builder
     * @param bool                                       $showQuantity Specifies if quantity field will be displayed (default true)
     * @param array                                      $options      An options array
     */
    public function defineAddBasketForm(ProductInterface $product, FormBuilder $formBuilder, $showQuantity = true, array $options = []): void
    {
        $basketElement = $this->createBasketElement($product);

        // create the product form
        $formBuilder
            ->setData($basketElement)
            ->add('productId', HiddenType::class);

        if ($showQuantity) {
            $formBuilder->add('quantity', IntegerType::class);
        } else {
            $transformer = new QuantityTransformer();
            $formBuilder->add(
                    $formBuilder->create('quantity', HiddenType::class, ['data' => 1])
                                ->addModelTransformer($transformer)
            );
        }
    }

    public function defineBasketElementForm(BasketElementInterface $basketElement, FormBuilder $formBuilder, array $options = []): void
    {
        $formBuilder
            ->add('delete', CheckboxType::class)
            ->add('quantity', IntegerType::class)
            ->add('productId', HiddenType::class);
    }

    public function validateFormBasketElement(ErrorElement $errorElement, BasketElementInterface $basketElement, BasketInterface $basket): void
    {
        // the item is flagged as deleted, no need to validate the item
        if ($basketElement->getDelete() || 0 === $basketElement->getQuantity()) {
            return;
        }

        // refresh the product from the database
        $product = $basketElement->getProduct();

        // check if the product is still in database
        if (!$product) {
            $errorElement->addViolation('product_not_available');

            return;
        }

        // check if the product is still enabled
        if (!$basketElement->getProduct()->getEnabled()) {
            $errorElement->addViolation('product_not_enabled');

            return;
        }

        // check if the quantity is numeric
        if (!is_numeric($basketElement->getQuantity())) {
            $errorElement
                ->with('quantity')
                    ->addViolation('product_quantity_not_numeric')
                ->end();

            return;
        }

        $errorElement
            ->with('quantity')
                ->assertRange(
                    [
                        'min' => 1,
                        'max' => $this->getStockAvailable($basketElement->getProduct()),
                        'minMessage' => 'basket_quantity_limit_min',
                        'maxMessage' => 'basket_quantity_limit_max',
                    ]
                )
            ->end();
    }

    /**
     * Adds $basketElement related to $product to $basket.
     *
     * @throws \Sonata\Component\Basket\InvalidProductException
     *
     * @return bool|\Sonata\Component\Basket\BasketElementInterface
     */
    public function basketAddProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $basketElement)
    {
        $event = new AddBasketElementEvent($basket, $basketElement, $product, $this);
        $this->getEventDispatcher()->dispatch(BasketEvents::PRE_ADD_PRODUCT, $event);

        if ($basket->hasProduct($product)) {
            return false;
        }

        if ($product->isMaster() && $this->hasVariations($product)) {
            throw new InvalidProductException(sprintf("You can't add '%s' to the basket as it is a master product with variations.", $product->getSku()));
        }

        $basketElementOptions = $product->getOptions();
        // add the default product options to the basket element
        if (\is_array($basketElementOptions) && !empty($basketElementOptions)) {
            foreach ($basketElementOptions as $option => $value) {
                $basketElement->setOption($option, $value);
            }
        }

        $this->updateComputationPricesFields($basket, $basketElement, $product);

        $basket->addBasketElement($basketElement);

        $event = new AddBasketElementEvent($basket, $basketElement, $product, $this);
        $this->getEventDispatcher()->dispatch(BasketEvents::POST_ADD_PRODUCT, $event);

        return $event->getBasketElement();
    }

    /**
     * Merge a product with another when the product is already present into the basket.
     *
     * @throws \RuntimeException
     *
     * @return bool|\Sonata\Component\Basket\BasketElementInterface
     */
    public function basketMergeProduct(BasketInterface $basket, ProductInterface $product, BasketElementInterface $newBasketElement)
    {
        $event = new AddBasketElementEvent($basket, $newBasketElement, $product, $this);
        $this->getEventDispatcher()->dispatch(BasketEvents::PRE_MERGE_PRODUCT, $event);

        if (!$basket->hasProduct($product)) {
            return false;
        }

        $basketElement = $basket->getElement($product);
        if (!$basketElement) {
            throw new \RuntimeException('no basket element related to product.id : %s', $product->getId());
        }

        $basketElement->setQuantity($basketElement->getQuantity() + $newBasketElement->getQuantity());

        $this->updateComputationPricesFields($basket, $basketElement, $product);

        $event = new AddBasketElementEvent($basket, $basketElement, $product, $this);
        $this->getEventDispatcher()->dispatch(BasketEvents::POST_MERGE_PRODUCT, $event);

        return $event->getBasketElement();
    }

    /**
     * @return bool true if the basket element is still valid
     */
    public function isValidBasketElement(BasketElementInterface $basketElement)
    {
        $product = $basketElement->getProduct();

        if (!$product instanceof ProductInterface) {
            return false;
        }

        return true;
    }

    public function updateComputationPricesFields(BasketInterface $basket, BasketElementInterface $basketElement, ProductInterface $product): void
    {
        $unitPrice = $this->calculatePrice($product, $basket->getCurrency(), $product->isPriceIncludingVat(), 1);
        $price = $this->calculatePrice($product, $basket->getCurrency(), $product->isPriceIncludingVat(), $basketElement->getQuantity());

        $basketElement->setUnitPrice($unitPrice);
        $basketElement->setPrice($price);
        $basketElement->setPriceIncludingVat($product->isPriceIncludingVat());
        $basketElement->setVatRate($product->getVatRate());
    }

    public function calculatePrice(ProductInterface $product, CurrencyInterface $currency, $vat = false, $quantity = 1)
    {
        $event = new BeforeCalculatePriceEvent($product, $currency, $vat, $quantity);
        $this->getEventDispatcher()->dispatch(BasketEvents::PRE_CALCULATE_PRICE, $event);

        $vat = $event->getVat();
        $quantity = $event->getQuantity();

        if (!\is_int($quantity) || $quantity < 1) {
            throw new \RuntimeException('Expected integer >= 1 for quantity, '.$quantity.' given.');
        }

        $price = (float) (bcmul((string) $this->currencyPriceCalculator->getPrice($product, $currency, $vat), (string) $quantity));

        $afterEvent = new AfterCalculatePriceEvent($product, $currency, $vat, $quantity, $price);
        $this->getEventDispatcher()->dispatch(BasketEvents::POST_CALCULATE_PRICE, $afterEvent);

        return $afterEvent->getPrice();
    }

    /**
     * Return true if the product can be added to the provided basket.
     *
     * @return bool
     */
    public function isAddableToBasket(BasketInterface $basket, ProductInterface $product, array $options = [])
    {
        return true;
    }

    /**
     * return a fresh product instance (so information are reloaded: enabled and stock ...).
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function reloadProduct(ProductInterface $product)
    {
        return $this->findOneById($product->getId());
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function findOneById($id)
    {
        $results = $this->createQueryBuilder('p')
            ->addSelect('i')
            ->addSelect('g')
            ->leftJoin('p.image', 'i')
            ->leftJoin('p.gallery', 'g')
            ->andWhere('p.id = :id')
            ->getQuery()
            ->setParameters(['id' => $id])
            ->setMaxResults(1)
            ->execute();

        return \count($results) > 0 ? $results[0] : false;
    }

    public function getStockAvailable(ProductInterface $product)
    {
        return $product->getStock();
    }

    /**
     * @param string $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function getCheapestEnabledVariation(ProductInterface $product)
    {
        if (!$this->hasEnabledVariations($product)) {
            return;
        }

        $variations = $this->getEnabledVariations($product);

        $result = null;

        foreach ($variations as $productVariation) {
            if (null === $result || $productVariation->getPrice() < $result->getPrice()) {
                $result = $productVariation;
            }
        }

        return $result;
    }

    public function getFilters()
    {
        return [
            'price' => [
                0,
                10,
                20,
                50,
                100,
                200,
                500,
                1000,
            ],
        ];
    }

    public function updateStock($product, ProductManagerInterface $productManager, $diff): void
    {
        $productManager->updateStock($product, $diff);
    }

    /**
     * Checks $fields if specified, returns variation fields otherwise.
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function getMergedFields(array $fields)
    {
        if (0 === \count($fields)) {
            // If we didn't specify the fields (filtered), we get all variation fields possible values
            $fields = $this->getVariationFields();
        } else {
            foreach ($fields as $field) {
                if (!\in_array($field, $this->getVariationFields(), true)) {
                    throw new \RuntimeException("The field '".$field."' is not among the variation fields");
                }
            }
        }

        return $fields;
    }
}
