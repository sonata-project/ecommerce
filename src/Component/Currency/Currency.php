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
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
    }

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Currency
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

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
