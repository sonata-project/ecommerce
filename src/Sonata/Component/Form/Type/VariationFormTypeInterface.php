<?php

namespace Sonata\Component\Form\Type;

use Sonata\Component\Product\ProductManagerInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;

interface VariationFormTypeInterface
{
    /**
     * @param ProductManagerInterface  $manager
     * @param DateTimeHelper           $dateTimeHelper
     */
    public function __construct(ProductManagerInterface $manager, DateTimeHelper $dateTimeHelper);

    /**
     * Fetch the possible values for a given field
     *
     * @param string $name
     *
     * @return array
     */
    public function getChoicesForVariation($name);

    /**
     * @return array()
     */
    public function getVariationFields();
}