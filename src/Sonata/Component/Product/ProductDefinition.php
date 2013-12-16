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

use Sonata\Component\Form\Type\VariationFormTypeInterface;

class ProductDefinition
{
    /**
     * @var ProductManagerInterface
     */
    protected $manager;

    /**
     * @var ProductProviderInterface
     */
    protected $provider;

    /**
     * @var VariationFormTypeInterface
     */
    protected $variationFormType;

    /**
     * @param ProductProviderInterface      $provider
     * @param ProductManagerInterface       $manager
     * @param VariationFormTypeInterface   $variationFormType
     */
    public function __construct(ProductProviderInterface $provider, ProductManagerInterface $manager, VariationFormTypeInterface $variationFormType)
    {
        $this->provider          = $provider;
        $this->manager           = $manager;
        $this->variationFormType = $variationFormType;
    }

    /**
     * @return ProductManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return ProductProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return VariationFormTypeInterface
     */
    public function getVariationFormType()
    {
        return $this->variationFormType;
    }
}
