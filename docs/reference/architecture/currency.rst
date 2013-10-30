.. index::
   single: Price
   single: Currency

========
Currency
========

Default behavior for currency handling is as follows:

* The user defines the currency he wants to use in its application config (related to ``Intl`` currency list)
* Each component/bundle in the ecommerce solution then depends on the ``CurrencyDetector::getCurrency()`` method to select the correct currency (overridable, see :doc:`../bundles/price`)
* The detector is based on the ``CurrencyManager::findOneByLabel()`` method (overridable, see :doc:`../bundles/price`)

Which means that you can:

* Select any handled currency in the configuration.
* Override the ``CurrencyManager`` to change the currency list handling (get it from a DB for instance).
* Override the ``CurrencyDetector`` to change the way the currency is selected.
