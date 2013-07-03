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

use Sonata\Component\Product\DeliveryInterface;

interface ProductInterface
{
    /**
     * @abstract
     * @return integer the product id
     */
    public function getId();

    /**
     * @abstract
     * @return float the product price
     */
    public function getPrice();

    /**
     * @abstract
     * @param  $price the product price
     * @return void
     */
    public function setPrice($price);

    /**
     * @abstract
     * @return float the vat price
     */
    public function getVat();

    /**
     * @abstract
     * @param $vat the product vat
     * @return void
     */
    public function setVat($vat);

    /**
     * @abstract
     * @return string the product name
     */
    public function getName();

    /**
     * @abstract
     * @return
     */
    public function setName($name);

    /**
     * @abstract
     * @return string the product name
     */
    public function getParent();

    /**
     * @abstract
     * @return
     */
    public function setParent(ProductInterface $parent);

    /**
     * @abstract
     * @return array the product options
     */
    public function getOptions();

    /**
     * @abstract
     * @param  $option array
     * @return void
     */
    public function setOptions(array $options);

    /**
     * @abstract
     * @return boolean , true is the product is enabled (ready to be sell)
     */
    public function getEnabled();

    /**
     * @abstract
     */
    public function setEnabled($enabled);

    /**
     * Return true if the product is recurrent
     * @abstract
     * @return void
     */
    public function isRecurrentPayment();

    /**
     * Return true if the product is a variation, linked to a main product
     *
     * @abstract
     * @return void
     */
    public function isVariation();

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return text $description
     */
    public function getDescription();

    /**
     * Set RAW description.
     *
     * @param text $rawDescription
     */
    public function setRawDescription($rawDescription);

    /**
     * Get RAW description.
     *
     * @return text $rawDescription
     */
    public function getRawDescription();

    /**
     * Set description formatter.
     *
     * @param text $descriptionFormatter
     */
    public function setDescriptionFormatter($descriptionFormatter);

    /**
     * Get description formatter.
     *
     * @return text $descriptionFormatter
     */
    public function getDescriptionFormatter();

    /**
     * @abstract
     * @param \Sonata\Component\Product\DeliveryInterface $delivery
     */
    public function addDelivery(DeliveryInterface $delivery);

    /**
     * @abstract
     * @return array
     */
    public function getDeliveries();
}
