<?php

/*
 * This file is part of the Sonata package.
*
* (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sonata\Component\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Cast the POST value to integer instead a string.
 *
 * @author Jérôme FIX <jerome.fix@zapoyok.info>
 */
class QuantityTransformer implements DataTransformerInterface
{
    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($quantity)
    {
        return $quantity;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($quantity)
    {
        return intval($quantity);
    }
}
