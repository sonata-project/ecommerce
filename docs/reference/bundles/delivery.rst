========
Delivery
========

Presentation
============

The Delivery bundle allows you to handle the various delivery methods for your product. A basic free delivery method is offered to you, but you can add your own as you seem fit.

There, you're able to define several services, such as the delivery pool (regrouping all services tagged ``sonata.delivery.method``) and the delivery methods.

The delivery pool regroups the various delivery method.
The delivery selector selects the appropriate delivery method for a given product.

Configuration
=============

You can change override the Delivery selector and pool classes through the following parameters:

* ``sonata.delivery.selector.class``
* ``sonata.delivery.pool.class``

You can also add your own delivery methods to the delivery pool by tagging your services with the following tag name: ``sonata.delivery.method``

