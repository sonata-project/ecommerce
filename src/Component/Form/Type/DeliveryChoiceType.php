<?php

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
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Symfony\Component\Form\Extension\Core\Type\ChoiceType'
            : 'choice';
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = array();
        $choiceOptions = array();

        foreach ($this->pool->getMethods() as $code => $instance) {
            $choices[$instance->getName()] = $code ;
        }

        // NEXT_MAJOR: Remove (when requirement of Symfony is >= 2.7)
        if (!method_exists('Symfony\Component\Form\AbstractType', 'configureOptions')) {
            $choices = array_flip($choices);
        } else {
            // NEXT_MAJOR: Remove (when requirement of Symfony is >= 3.0)
            if (method_exists('Symfony\Component\Form\FormTypeInterface', 'setDefaultOptions')) {
                $choiceOptions['choices_as_values'] = true;
            }
        }

        $choiceOptions['choices'] = $choices;

        $resolver->setDefaults($choiceOptions);
    }
}
