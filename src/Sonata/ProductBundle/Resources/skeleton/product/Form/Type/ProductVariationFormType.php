<?php
/*
 * This file is part of the sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\ProductBundle\Form\Type;

use Sonata\Component\Form\Type\BaseVariationFormType;


class {{ product }}VariationFormType extends BaseVariationFormType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'sonata_product_{{ product_lowercased }}';
    }

    /**
     * @return array
     */
    public function getVariationFields()
    {
        return array();
    }

    /**
     * Fetch the possible values for a given field
     *
     * @param string $name
     *
     * @return array
     */
    public function getChoicesForVariation($name)
    {
        return array();
    }
}
