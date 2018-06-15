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

namespace Sonata\Component\Form\Type;

use Sonata\Component\Delivery\Pool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DeliveryChoiceType extends AbstractType
{
    protected $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_delivery_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = [];       
        foreach ($this->pool->getMethods() as $name => $instance) {  
            $choices[$instance->getName()] = $instance->getCode();
        }
        
        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    /**
     * {@inheritdoc}
     * @deprecated Remove this function next major
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $choices = [];

        foreach ($this->pool->getMethods() as $name => $instance) {          
            $choices[$name] = $instance->getName();
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }
}
