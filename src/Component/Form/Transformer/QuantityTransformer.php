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
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     *
     * @param $quantity
     *
     * @return mixed
     */
    public function transform($quantity)
    {
        return $quantity;
    }

    /**
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     *
     * @param $quantity
     *
     * @return int
     */
    public function reverseTransform($quantity)
    {
        return (int) $quantity;
    }
}
