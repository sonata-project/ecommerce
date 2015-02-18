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

use Sonata\Component\Currency\CurrencyFormType;
use Sonata\Component\Form\Transformer\SerializeDataTransformer;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ApiBasketType
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var CurrencyFormType
     */
    protected $currencyFormType;

    /**
     * Constructor
     *
     * @param string                $class           An entity data class
     * @param CurrencyFormType      $currencyFormType A Sonata ecommerce currency form type
     */
    public function __construct($class, CurrencyFormType $currencyFormType)
    {
        $this->class            = $class;
        $this->currencyFormType = $currencyFormType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create('currency', $this->currencyFormType)
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
            'validation_groups' => array('api'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_basket_api_form_basket';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'sonata_basket_api_form_basket_parent';
    }
}