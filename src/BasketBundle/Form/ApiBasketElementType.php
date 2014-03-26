<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\BasketBundle\Form;

use Metadata\MetadataFactoryInterface;

use Sonata\Component\Form\Transformer\SerializeDataTransformer;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ApiBasketElementType
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketElementType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor
     *
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

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => $this->class,
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_basket_api_form_basket_element';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'sonata_basket_api_form_basket_element_parent';
    }
}