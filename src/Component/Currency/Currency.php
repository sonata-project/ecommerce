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

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class Currency implements CurrencyInterface
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
//     protected $symbol;

    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function equals($currency)
    {
        if (!$currency instanceof CurrencyInterface) {
            return false;
        }

        return $this->getLabel() === $currency->getLabel();
    }

    /*
     * {@inheritdoc}
     */
//     public function getSymbol()
//     {
//         return $this->symbol;
//     }

    /*
     * @param string $symbol
     */
//     public function setSymbol($symbol)
//     {
//         $this->symbol = $symbol;
//         return $this;
//     }
}
