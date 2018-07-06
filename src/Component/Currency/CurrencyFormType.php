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

namespace Sonata\Component\Currency;

use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyFormType extends CurrencyType
{
    /**
     * @var CurrencyDataTransformer
     */
    private $currencyTransformer;

    /**
     * @param CurrencyDataTransformer $currencyTransformer
     */
    public function __construct(CurrencyDataTransformer $currencyTransformer)
    {
        $this->currencyTransformer = $currencyTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer($this->currencyTransformer);
    }

    public function getParent()
    {
        return CurrencyType::class;
    }

    public function getBlockPrefix()
    {
        return 'sonata_currency';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
