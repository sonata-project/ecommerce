<?php

namespace Sonata\Component\Form\Type;

use Symfony\Component\Form\AbstractType;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class BaseVariationFormType extends AbstractType implements VariationFormTypeInterface
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var ProductManagerInterface
     */
    protected $manager;

    /**
     * @var DateTimeHelper
     */
    protected $dateTimeHelper;

    /**
     * @param ProductManagerInterface  $manager
     * @param DateTimeHelper           $dateTimeHelper
     */
    public function __construct(ProductManagerInterface $manager, DateTimeHelper $dateTimeHelper)
    {
        $this->manager = $manager;
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = $this->getVariationFields();

        if (!count($fields)) {
            return;
        }

        foreach ($fields as $fieldName) {
            $choices = $this->getChoicesForVariation($fieldName);

            if (!count($choices)) {
                continue;
            }

            $this->cleanData($choices);

            $builder->add($fieldName, 'choice', array(
                'choices'            => $choices,
                'translation_domain' => 'SonataProductBundle',
            ));
        }
    }

    /**
     * @param array $options
     */
    protected function cleanData(array &$options)
    {
        $return = array();

        foreach ($options as $option) {
            $value = $option;

            if ($option instanceof \DateTime) {
                $value = $this->dateTimeHelper->formatDateTime($option);
            }

            $return[$value] = $value;
        }

        $options = $return;
        natcasesort($options);
    }
}