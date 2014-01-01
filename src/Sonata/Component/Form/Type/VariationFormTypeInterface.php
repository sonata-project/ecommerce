<?php

namespace Sonata\Component\Form\Type;

use Sonata\Component\Product\ProductManagerInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;

interface VariationFormTypeInterface
{
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