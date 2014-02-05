.. index::
    single: Delivery

========
Delivery
========

Presentation
============

The Delivery bundle allows you to handle the various delivery methods for your product. A basic free delivery method is offered to you, but you can add your own as you see fit.

There, you're able to define several services, such as the delivery pool (regrouping all services tagged ``sonata.delivery.method``) and the delivery methods.

The delivery pool regroups the various delivery methods.
The delivery selector selects the appropriate delivery method(s) for a given product.

Configuration
=============

You can override the Delivery selector and pool classes through the following parameters:

* ``sonata.delivery.selector.class``
* ``sonata.delivery.pool.class``

Note that in order to use a different selector, yours should override the default one (``Sonata\Component\Delivery\Selector``). If you just want to implement a brand new class based on ``Sonata\Component\Delivery\ServiceDeliverySelectorInterface``, you will also have register it as a new service and reference it into your application configuration (see ``selector`` node below).

You can also add your own delivery methods to the delivery pool by tagging your services with the following tag name: ``sonata.delivery.method``. Remember to use unique codes for your delivery methods.

Default configuration:

.. code-block:: yaml

    sonata_delivery:
        selector:             sonata.delivery.selector.default
        services:
            free_address_required:
                name:                 free_address_required
                enabled:              false
                code:                 free_address_required
                priority:             10
            free_address_not_required:
                name:                 free_address_not_required
                enabled:              false
                code:                 free_address_not_required
                priority:             10
