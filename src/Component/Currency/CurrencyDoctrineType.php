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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Intl\Intl;

/**
 * Handles Currency as doctrine type.
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyDoctrineType extends StringType
{
    const CURRENCY = 'currency'; // modify to match your type name

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!array_key_exists($value, Intl::getCurrencyBundle()->getCurrencyNames())) {
            throw new \RuntimeException(sprintf("'%d' is not a supported currency.", $value));
        }

        $currency = new Currency();
        $currency->setLabel($value);

        return $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof CurrencyInterface) {
            throw new \RuntimeException(sprintf("'currency' type only handles values of type Sonata\Component\Currency\CurrencyInterface ; value of type %s given", is_object($value) ? get_class($value) : gettype($value)));
        }

        return $value->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $field['length'] = $this->getDefaultLength($platform);
        $field['fixed'] = true;

        // As currency representation is a norm (always 3 chars), we fix it
        return $platform->getVarcharTypeDeclarationSQL($field);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::CURRENCY;
    }
}
