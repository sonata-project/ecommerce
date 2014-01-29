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

Each product prototype has its own type (defined in ``ProductType`` property).
You can use ``Sonata\Component\Product\Pool`` to retrieve related ``ProductProvider`` and ``ProductManager`` instances.

Product Variations
==================

A ``Product`` can be duplicated. For instance, you can have a ``Product`` variated in many
colors but all the others parameters are the same.

The ``ProductProvider`` is responsible of the variation creation.

The variations are related to a parent ``Product``. When you edit some data in your parent
``Product``, you can synchronize them with the ``ProductProvider``.

A product and its variations can be synchronized : each field of the master product (except ``id``, ``parent`` and the variation fields) is copied
from the master product to its variations.
The "variation fields" are used to differentiate the variations. You can set a ``color`` property as a variation field and that property won't be
synchronized when you update the master product.

Product Template
================

Here are the blocks you can override in the product template.

The product sheet is based on the main ``product`` block, which is divided in 2 blocks:

  - ``product_left`` block, which includes:

    - ``product_carousel`` block displays the gallery associated with the product or just the main image of the product.

  - ``product_right`` block, which includes:

    - ``product_title`` block displays the product title,

    - ``product_properties`` block displays various information about the product (price, ...) in the blocks listed below:

      - ``product_generic_properties`` block displays generic information about the product (non variation fields):

        - ``product_unit_price`` block displays the unit price of the product,
        - ``product_reference`` block displays the reference (aka SKU) of the product,
      - ``product_description_short`` block shows the short description of the product,
      - ``product_errors`` block shows the product page errors (unavailable stock, ...),
    - ``product_variations_form_block`` blocks displays a form allowing to select the variation based variated properties (see :ref:`block.variations_form` for more informations).
    - ``product_delivery`` block displays product delivery information (to override by default),
    - ``product_basket`` block displays the "Add to basket" form (rendered by ``SonataBasketBundle:Basket:add_product_form.html.twig`` template).


Additionally, you can override those template blocks:

  - ``product_cross`` block that can be overrided in you do not want to displays cross-selling block and the following is encapsulated in:

    - ``product_cross_selling`` block that includes cross-selling block (rendered by ``SonataProductBundle:Product:view_similar.html.twig`` template).

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

Product Block Services
======================

Some SonataBlock services are available as well:

.. _block.variations_form:

sonata.product.block.variations_form
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Renders the variations_form. A ``product`` argument is needed.

Options:
  - ``variations_properties``: an array of properties you wish to display
  - ``form_route`` and ``form_route_parameters`` which are used to generate the URL for the submit of the post (and the AJAX submit as well)
  - ``form_field_options`` which allows you to give an array of options to the form field generated.

sonata.product.block.recent_products
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Renders the latest added products. By default, the number of displayed products is set to 5, but you may override this setting.

sonata.product.block.categories_menu
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Displays a KnpMenu rendering the product categories.

sonata.product.block.filters_menu
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Displays a KnpMenu rendering the currently selected product type filters (WIP).
