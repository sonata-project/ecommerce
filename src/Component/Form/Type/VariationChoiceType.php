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

use Sonata\Component\Product\Pool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class VariationChoiceType extends AbstractType
{
    /**
     * @var Pool
     */
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $choiceType = 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
        } else {
            $choiceType = 'choice';
        }

        $choices = $this->pool->getProvider($options['product'])->getVariationsChoices($options['product'], $options['fields']);

        foreach ($choices as $choiceTitle => $choiceValues) {
            $choiceOptions = array(
                'label' => sprintf('form_%s', $choiceTitle),
                'translation_domain' => 'SonataProductBundle',
            );
            // NEXT_MAJOR: Remove this "if" (when requirement of Symfony is >= 2.7)
            if (method_exists('Symfony\Component\Form\AbstractType', 'configureOptions')) {
                $choiceValues = array_flip($choiceValues);
                // choice_as_value option is not needed in SF 3.0+
                if (method_exists('Symfony\Component\Form\FormTypeInterface', 'setDefaultOptions')) {
                    $choiceOptions['choices_as_values'] = true;
                }
            }
            $choiceOptions['choices'] = $choiceValues;

            $builder->add($choiceTitle, $choiceType, array_merge(
                    $choiceOptions,
                    $options['field_options']
                )
            );
        }
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
        $resolver->setDefaults(array(
            'field_options' => array(),
            'product' => null,
            'fields' => null,
            'csrf_protection' => false,
            'method' => 'GET',
        ));

        $resolver->setRequired(array('product', 'fields'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_product_variation_choices';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
