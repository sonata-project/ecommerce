.. index::
    single: Delivery

========
Delivery
========

Presentation
============

The ``SonataDeliveryBundle`` allows you to handle the various `Delivery` methods for your product. A basic free delivery method is offered to you, but you can add your own as you see fit.

There, you're able to define several services, such as the `Delivery` pool (regrouping all services tagged ``sonata.delivery.method``) and the `Delivery` methods:

* The delivery pool regroups the various delivery methods.
* The delivery selector selects the appropriate delivery method(s) for a given product.

Configuration
=============

You can override the `Delivery` selector and pool classes through the following parameters:

* ``sonata.delivery.selector.class``
* ``sonata.delivery.pool.class``

.. note::

    In order to use a different selector, yours should override the default one (``Sonata\Component\Delivery\Selector``). If you just want to implement a brand new class based on ``Sonata\Component\Delivery\ServiceDeliverySelectorInterface``, you will also have register it as a new service and reference it into your application configuration (see ``selector`` node below).

Default configuration:

.. code-block:: yaml

    sonata_delivery:
        selector:             sonata.delivery.selector.default

        # This allows you to configure provided delivery methods
        services:
            free_address_required:
                name:                 free_address_required
                code:                 free_address_required
                priority:             10
            free_address_not_required:
                name:                 free_address_not_required
                code:                 free_address_not_required
                priority:             10

        # Here you will enable the delivery methods you wish to provide
        # and add your custom ones
        methods:
            free_address_required: ~     # This is a provided method, we don't need to specify its service id
            take_away: application.sonata.delivery.take_away    # Custom delivery method, we specify the service id


Add a custom delivery method
============================

In order to add a custom delivery methods, here are the steps to follow:

1. Create your own delivery method class:

.. code-block:: php

    <?php

    namespace Application\AcmeBundle\Delivery;

    use Sonata\Component\Delivery\BaseServiceDelivery;


    /**
     * Class TakeAwayDelivery
     */
    class TakeAwayDelivery extends BaseServiceDelivery
    {
        /**
         * {@inheritdoc}
         */
        public function isAddressRequired()
        {
            return false;
        }

        /**
         * {@inheritdoc}
         */
        public function getCode()
        {
            return 'take_away';
        }
    }

2. Declare the service associated (don't forget the tag):

.. code-block:: xml

        <service id="application.acme.delivery.take_away" class="Application\AcmeBundle\Delivery\TakeAwayDelivery">
            <tag name="sonata.delivery.method" />
        </service>

3. Add it to your configuration:

.. code-block:: yaml

    sonata_delivery:
        # ...

        methods:
            # ...
            take_away: application.acme.delivery.take_away

4. That's it! The new method is configured (you'll have to add it to your products though).
