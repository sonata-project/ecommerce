<?php

/*
 * This file is part of the Sonata package.
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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class VariationChoiceType.
 *
 *
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
        $choices = $this->pool->getProvider($options['product'])->getVariationsChoices($options['product'], $options['fields']);

        foreach ($choices as $choiceTitle => $choiceValues) {
            $builder->add($choiceTitle, 'choice', array_merge(
                    array('translation_domain' => 'SonataProductBundle'),
                    $options['field_options'],
                    array(
                        'label'   => sprintf('form_%s', $choiceTitle),
                        'choices' => $choiceValues,
                    )
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'field_options'   => array(),
            'product'         => null,
            'fields'          => null,
            'csrf_protection' => false,
            'method'          => 'GET',
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
