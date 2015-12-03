<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Currency;

use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CurrencyFormType.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyFormType extends CurrencyType
{
    /**
     * @var CurrencyDataTransformer
     */
    private $currencyTransformer;

    /**
     * Constructor.
     *
     * @param CurrencyDataTransformer $currencyTransformer
     */
    public function __construct(CurrencyDataTransformer $currencyTransformer)
    {
        $this->currencyTransformer = $currencyTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer($this->currencyTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_currency';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
