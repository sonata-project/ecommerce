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
interface CurrencyInterface
{
    /**
     * Returns currency's label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Currency comparison.
     *
     * @param mixed $currency
     *
     * @return bool
     */
    public function equals($currency);

    /*
     * Returns currency's symbol
     *
     * @return string
     */
//     public function getSymbol();
}
