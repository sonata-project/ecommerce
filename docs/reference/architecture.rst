=============================
Sonata ECommerce Architecture
=============================

.. image:: ../images/dcEntities.svg

Product
=======

A ``Product`` defines the data related to one entry in the persistency layer. An application
can have different types of product. A product is always linked to a ``ProductProvider``.

The link between the ``Product`` and the ``ProductProvider`` is done through the discriminator
column.

A ``ProductProvider`` is responsable of the ``Product`` lifecycle across the application :

  - Compute prices
  - Forms manager : front and backend
  - Add a product into the basket
  - Create a OrderElement upon the ``Product`` information

A ``ProductManager`` is responsable of the ``Product`` lifecycle with the database :

  - Retrieve a product type
  - Save/Delete a product type
  - Find a product type

A ``CollectionProductManager`` is responsable of retrieving a set of different products.

  - Retrieve products

Customer #TODO
==============

Basket #TODO
============

Order #TODO
===========

Invoice #TODO
=============


