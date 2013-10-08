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

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Product\DeliveryInterface;

interface ProductInterface
{
    /**
     * @return integer the product id
     */
    public function getId();

    /**
     * @return float the product price
     */
    public function getPrice();

    /**
     * @param float $price the product price
     */
    public function setPrice($price);

    /**
     * @return float the vat price
     */
    public function getVat();

    /**
     * @param float $vat the product vat
     */
    public function setVat($vat);

    /**
     * @return string the product name
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string the product name
     */
    public function getParent();

    /**
     * @param ProductInterface $parent
     */
    public function setParent(ProductInterface $parent);

    /**
     * @return array the product options
     */
    public function getOptions();

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @return boolean , true is the product is enabled (ready to be sell)
     */
    public function getEnabled();

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Return true if the product is recurrent
     *
     * @return void
     */
    public function isRecurrentPayment();

    /**
     * Return true if the product is a variation, linked to a main product
     *
     * @return boolean
     */
    public function isVariation();

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription();

    /**
     * Set RAW description.
     *
     * @param string $rawDescription
     */
    public function setRawDescription($rawDescription);

    /**
     * Get RAW description.
     *
     * @return string $rawDescription
     */
    public function getRawDescription();

    /**
     * Set description formatter.
     *
     * @param string $descriptionFormatter
     */
    public function setDescriptionFormatter($descriptionFormatter);

    /**
     * Get description formatter.
     *
     * @return string $descriptionFormatter
     */
    public function getDescriptionFormatter();

    /**
     * @param DeliveryInterface $delivery
     */
    public function addDelivery(DeliveryInterface $delivery);

    /**
     * @return array
     */
    public function getDeliveries();

    /**
     * Set the Product variations.
     *
     * @param array $variations
     */
    public function setVariations(array $variations);

    /**
     * Get the variations.
     *
     * @return array
     */
    public function getVariations();

    /**
     * Add a Product variation.
     *
     * @param ProductInterface $product
     */
    public function addVariation(ProductInterface $product);

    /**
     * Returns Product base data as an array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Populate entity from an array.
     *
     * @param array $array
     */
    public function fromArray($array);
}
