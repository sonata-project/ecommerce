.. index::
    single: Product
    single: Product; ProductVariations
    single: Product; ProductTemplate
    single: Product; ProductHelpers

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

Product Template
================

Here are the blocks you can override in the product template.

First of all, these main product blocks are encapsulated by the ``product`` block.

  - ``product_errors`` block shows the product page errors (unavailable stock, ...),
  - ``product_title`` block displays product title,

  - ``product_details`` block defines product image, description text and product gallery in the following blocks:
    - ``product_image`` block displays current product image,
    - ``product_description`` block displays product description text,
    - ``product_gallery`` block display product gallery if defined.

On the right side, there is the following blocks encapsulated in the ``product_right`` block:

  - ``product_properties`` block displays various information on the product (price, variations, ...) in the blocks listed below:
    - ``product_properties_before_price`` block can help you to display additional information before all of them,
    - ``product_price_label`` block displays product price label,
    - ``product_price_price`` block displays product price value,
    - ``product_properties_after_price`` block can help you to display additional information after the blocks listed above.

  - ``product_variations`` block displays product variations information and the following blocks are encapsulated in:
    - ``variations_label`` block displays the product variation title,
    - ``product_variations_list`` block displays variations products list,

  - ``product_delivery`` block displays product delivery information,
  - ``product_basket`` block displays quantity information and "add to basket" button.

Additionally, you can override those template blocks:

  - ``product_cross`` block that can be overrided in you do not want to displays cross-selling block and the following is encapsulated in:
    - ``product_cross_selling`` block that includes cross selling block.

Product Helpers
===============

Some Twig helpers are available for your templates.

  - ``sonata_product_provider`` gives you the related ``ProductProvider`` for a given ``Product``.
  - ``sonata_product_has_variations`` returns true or false if the ``Product`` has variations.
  - ``sonata_product_has_enabled_variations`` returns true or false if the ``Product`` has enabled variations.
  - ``sonata_product_cheapest_variation`` returns cheapest variation, based on its price.
  - ``sonata_product_cheapest_variation_price`` returns the price of the cheapest variation.
  - ``sonata_product_price`` calculates the price of a ``Product``.
  - ``sonata_product_stock`` gets the available stock of a ``Product``.
