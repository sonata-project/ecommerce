.. index::
   single: Price
   single: Currency
   pair: Currency; Architecture

========
Currency
========

Default behavior for currency handling is as follows:

* The user defines the currency he wants to use in its application config file (see ``app/config/sonata/sonata_price.yml``). The possible values are defined according to `ISO 4217 standard <http://en.wikipedia.org/wiki/ISO_4217>`_.
* Each component/bundle in the e-commerce solution then relays on the ``CurrencyDetector::getCurrency()`` method to select the correct currency (overridable, see :doc:`../bundles/price`)
* The detector is based on the ``CurrencyManager::findOneByLabel()`` method (overridable, see :doc:`../bundles/price`)

Which means that you can:

* Select any supported currency in the configuration.
* Override the ``CurrencyManager`` to change the currency list handling (get it from a webservice or database for instance).
* Override the ``CurrencyDetector`` to change the way the currency is selected.
