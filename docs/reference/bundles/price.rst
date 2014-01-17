.. index::
    single: Price

=====
Price
=====

Architecture
============

For more information about our position regarding the *price* architecture, you can read: :doc:`../architecture/currency`.

Presentation
============

Price bundle handles everything related to prices, (right now, only currencies, but it will soon be extended to add taxes, ...)

Configuration
=============

Price bundle configuration is as follows:

.. code-block:: yaml

    sonata_price:
        currency: EUR # Or any value present in array_keys(Intl::getCurrencyBundle()->getCurrencyNames)
    doctrine:
        dbal:
            # ...

            types:
                currency: Sonata\Component\Currency\CurrencyType

You can also change the services class (defined as parameters):

* ``sonata.price.currency.detector.class`` for the currency detector
* ``sonata.price.currency.manager.class`` for the currency manager (finding the CurrencyInterface item matching the currency label)

As you may see in the sample configuration, we defined a new doctrine field type to store currencies in database.