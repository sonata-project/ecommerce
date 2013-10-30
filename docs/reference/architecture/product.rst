.. index::
    single: Product
    single: Product; ProductVariations

=======
Product
=======

A ``Product`` defines the data related to one entry in the persistence layer. An application
can have different types of product. A product is always linked to a ``ProductProvider``.

The link between the ``Product`` and the ``ProductProvider`` is done through the discriminator
column.

A ``ProductProvider`` is responsible of the ``Product`` lifecycle across the application:

  - Compute prices
  - Forms manager: front and backend
  - Add a product into the basket
  - Create an OrderElement upon the ``Product`` information
  - Create variations

A ``ProductManager`` is responsible of the ``Product`` lifecycle with the database:

  - Retrieve a product type
  - Save/Delete a product type
  - Find a product type

A ``ProductSetManager`` is responsible of retrieving a set of different products, or specific products (it overrides the ``ProductManager``).

A ``ProductFinder`` is responsible for finding matching products to a given one. It will noticeably be used for cross-selling & up-selling matches.


Product Variations
==================

A ``Product`` can be duplicated. For instance, you can have a ``Product`` variated in many
colors but all the others parameters are the same.

The ``ProductProvider`` is responsible of the variation creation.

The variations are related to a parent ``Product``. When you edit some data in your parent
``Product``, you can synchronize them with the ``ProductProvider``.