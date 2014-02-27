.. index::
    single: Product
    single: Product; ProductVariations
    single: Product; ProductTemplate
    single: Product; ProductHelpers
    pair: Product; Architecture

=======
Product
=======

A ``Product`` defines the data related to one entry in the persistence layer. An application can have different types of product. A `Product` is always linked to a ``ProductProvider``.

The link between the ``Product`` and the ``ProductProvider`` is done through the configuration file (see ``app/config/sonata/sonata_product.yml`` under ``sonata_product`` namespace in the sandbox).

A ``ProductProvider`` is responsible of the ``Product`` lifecycle across the application:

  - Compute prices
  - Forms manager: front and backend
  - Add a `Product` into the `Basket`
  - Create an ``OrderElement`` upon the ``Product`` information
  - Create variations

A ``ProductManager`` is responsible of the ``Product`` lifecycle with the database:

  - Retrieve a `Product` type
  - Save/Delete a `Product` type
  - Find a `Product` type

A ``ProductSetManager`` is responsible of retrieving a set of different products, or specific products (it overrides the ``ProductManager``).

A ``ProductFinder`` is responsible for finding matching products related to a given one. It will noticeably be used for cross-selling & up-selling matches.

Each `Product` prototype has its own type (defined in ``ProductType`` property).
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
The "variation fields" are used to differentiate the variations. Each of these fields can't be modified from the parent product since they are "children specific".

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
    - ``product_variations_form_block`` blocks displays a form allowing to select the variation based variated properties (see :ref:`block.variations_form` for more information).
    - ``product_delivery`` block displays product delivery information (to override by default),
    - ``product_basket`` block displays the "Add to basket" form (rendered by ``SonataBasketBundle:Basket:add_product_form.html.twig`` template).

Also, to allow the display of variations, a javascript block has been implemented :
    - ``product_javascript_init`` block to register javascript functions related to products

Additionally, you can override those template blocks:

  - ``product_cross`` block is used as a container to encapsulate the following :

    - ``product_cross_selling`` block that includes cross-selling block (rendered by ``SonataProductBundle:Product:view_similar.html.twig`` template).

  - ``product_full_description`` block is used to render the full description of the product
  - ``product_comment`` block is used to render comments related to the product

Product Helpers
===============

Some Twig helpers are available for your templates:

  - ``sonata_product_provider`` gives you the related ``ProductProvider`` for a given ``Product``.
  - ``sonata_product_has_variations`` returns true or false if the ``Product`` has variations.
  - ``sonata_product_has_enabled_variations`` returns true or false if the ``Product`` has enabled variations.
  - ``sonata_product_cheapest_variation`` returns cheapest variation, based on its price.
  - ``sonata_product_cheapest_variation_price`` returns the price of the cheapest variation.
  - ``sonata_product_price`` calculates the price of a ``Product``.
  - ``sonata_product_stock`` gets the available stock of a ``Product``.

Product Block Services
======================

Some `SonataBlock` services are available as well:

.. _block.variations_form:

sonata.product.block.variations_form
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Renders the variations_form. A ``Product`` argument is needed.

Options:
  - ``variations_properties`` is an array of properties you wish to display.
  - ``form_route`` and ``form_route_parameters`` are used to generate the URL to submit the variation form.
  - ``form_field_options`` allows you to give an array of options to the form field generated. Note that this parameter will be applied to every form fields (their type is "choice". See `Symfony Choice Form Type <http://symfony.com/doc/current/reference/forms/types/choice.html>`_ for a list of available parameters.

sonata.product.block.recent_products
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Renders the latest added products. By default, the number of displayed products is set to 5, but you may override this setting using the setting key name ``number``.

sonata.product.block.categories_menu
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Displays a KnpMenu rendering the `Product` categories. It is rendered using the template ``SonataBlockBundle:Block:block_side_menu_template.html.twig`` that you might want to override.

sonata.product.block.filters_menu
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Displays a `KnpMenu <https://github.com/KnpLabs/KnpMenuBundle/blob/1.1.x/Resources/doc/index.md>`_ rendering the currently selected product type filters (work in progress).
