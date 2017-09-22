<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Form;

use Sonata\Component\Form\Transformer\SerializeDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketElementType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @param string $class An entity data class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create('options')->addModelTransformer(new SerializeDataTransformer())
        );
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
            'data_class' => $this->class,
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_basket_api_form_basket_element';
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
    public function getParent()
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Sonata\BasketBundle\Form\ApiBasketElementParentType'
            : 'sonata_basket_api_form_basket_element_parent';
    }
}
