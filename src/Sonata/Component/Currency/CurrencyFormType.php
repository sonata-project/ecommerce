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
 * Class CurrencyFormType
 *
 * @package Sonata\Component\Currency
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
     * Constructor
     *
     * @param CurrencyDataTransformer $currencyTransformer
     */
    function __construct(CurrencyDataTransformer $currencyTransformer)
    {
        $this->currencyTransformer = $currencyTransformer;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer($this->currencyTransformer);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'currency';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'sonata_currency';
    }
}