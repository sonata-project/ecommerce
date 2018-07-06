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
class CurrencyDetector implements CurrencyDetectorInterface
{
    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * Constructs the currency detector service by finding the default currency.
     *
     * @param string                   $currencyLabel
     * @param CurrencyManagerInterface $currencyManager
     */
    public function __construct($currencyLabel, CurrencyManagerInterface $currencyManager)
    {
        $this->currency = $currencyManager->findOneByLabel($currencyLabel);
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}
