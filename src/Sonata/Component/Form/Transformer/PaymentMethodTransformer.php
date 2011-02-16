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

use Symfony\Component\Form\ValueTransformer\ValueTransformerInterface;
use Symfony\Component\Form\ValueTransformer\TransformationFailedException;
use Symfony\Component\Form\Configurable;
    
/**
 * Transform a method code into a method instance
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PaymentMethodTransformer extends Configurable implements ValueTransformerInterface
{
    protected function configure()
    {
        $this->addRequiredOption('payment_pool');

        parent::configure();
    }

    /**
     * @param array $ids
     * @param Collection $collection
     */
    public function reverseTransform($value)
    {

       return $this->getOption('payment_pool')->getMethod($value);
    }

    /**
     * @param Collection $value
     */
    public function transform($value)
    {

        return $value ? $value->getCode() : null;
    }
}