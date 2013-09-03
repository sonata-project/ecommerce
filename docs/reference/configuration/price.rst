=====
Price
=====

Price bundle configuration is as follows:

::

      sonata_price:
        currency: EUR # Or any value present in array_keys(Intl::getCurrencyBundle()->getCurrencyNames)


.. code-block:: yaml

    doctrine:
        dbal:
            # ...

            types:
                currency: Sonata\Component\Currency\CurrencyType

You can also change the services class (defined as parameters):

* ``sonata.price.currency.detector.class`` for the currency detector
* ``sonata.price.currency.manager.class`` for the currency manager (finding the CurrencyInterface item matching the currency label)
