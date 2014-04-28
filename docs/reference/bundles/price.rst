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

Installation
============

This central e-commerce bundle requires you add a `bcscale <http://php.net/manual/en/function.bcscale.php>`_ method in your ``AppKernel.php`` file to ensure that prices are correctly computed.
Please, update your ``AppKernel.php`` file like this:

.. code-block:: php

    <?php

    // ...

    class AppKernel extends Kernel
    {
        public function init()
        {
            bcscale(3); // or any other value greater than 0

            ...


Configuration
=============

Price bundle configuration is as follows:

.. code-block:: yaml

    sonata_price:
        currency: EUR # Or any valid value according ISO 4217 standard
    doctrine:
        dbal:
            # ...

            types:
                currency: Sonata\Component\Currency\CurrencyDoctrineType

You can also change the services class (defined as parameters):

* ``sonata.price.currency.detector.class`` for the currency detector
* ``sonata.price.currency.manager.class`` for the currency manager (finding the ``CurrencyInterface`` item matching the currency label)

As you may see in the sample configuration, we defined a new doctrine field type to store currencies in database.
