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

A ``ProductProvider`` is responsible for the ``Product`` lifecycle across the application:

  - Compute prices
  - Forms manager: front and backend
  - Add a product into the basket
  - Create an OrderElement upon the ``Product`` information
  - Create a product variations

A ``ProductManager`` is responsible for the ``Product`` lifecycle with the database:

  - Retrieve a product type
  - Save/Delete a product type
  - Find a product type

A ``ProductSetManager`` is responsible for retrieving a set of different products, or specific products (it overrides the ``ProductManager``).

A ``ProductFinder`` is responsible for finding matching products to a given one. It will noticeably be used for cross-selling & up-selling matches.


Product Variations
==================

A ``Product`` can be duplicated. For instance, you can have a ``Product`` available in some variations. For instance, a ``Product`` like a Mug can be available in many
colors but in the same size. Then, the color can have multiple values and is defined as a variation field.

The ``ProductProvider`` is responsible for the variation creation.

The variations are related to a parent ``Product``. When you edit some data in your parent ``Product``, you can synchronize them with the ``ProductProvider``.

Product Template
================

Here are the blocks you can override in the product template.

The product sheet is based on the main ``product`` block, which is divided in 2 blocks:

  - ``product_left`` block, which includes:

    - ``product_carousel`` block displays the gallery associated with the product or just the main image of the product

  - ``product_right`` block, which includes:

    - ``product_title`` block displays the product title,

    - ``product_properties`` block displays various information about the product (price, ...) in the blocks listed below:

      - ``product_generic_properties`` block displays generic informaiton about the product (non variation fields):

        - ``product_unit_price`` block displays the unit price of the product,
        - ``product_reference`` block displays the reference (aka SKU) of the product,
      - ``product_description_short`` block shows the short description of the product,
      - ``product_errors`` block shows the product page errors (unavailable stock, ...),
    - ``product_variations_list`` and ``product_variations_form_block`` blocks display variations products list.
    - ``product_delivery`` block displays product delivery information,
    - ``product_basket`` block displays quantity information and the "Add to basket" button.


Additionally, you can override those template blocks:

  - ``product_cross`` block that can be overrided in you do not want to displays cross-selling block and the following is encapsulated in:

    - ``product_cross_selling`` block that includes cross-selling block.

Product Helpers
===============

Some Twig helpers are available for your templates:

  - ``sonata_product_provider`` gives you the related ``ProductProvider`` for a given ``Product``.
  - ``sonata_product_has_variations`` returns true or false if the ``Product`` has variations.
  - ``sonata_product_has_enabled_variations`` returns true or false if the ``Product`` has at least one variation enabled.
  - ``sonata_product_cheapest_variation`` returns the cheapest variation, based on its price.
  - ``sonata_product_cheapest_variation_price`` returns the price of the cheapest variation.
  - ``sonata_product_price`` calculates the price of a ``Product``.
  - ``sonata_product_stock`` gets the available stock of a ``Product``.
