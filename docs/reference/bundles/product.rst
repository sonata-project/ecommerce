.. index::
    single: Product
    single: Seo

=======
Product
=======

Architecture
============

For more information about our position regarding *product* architecture, you can read: :doc:`../architecture/product`.

Presentation
============

The ``SonataProductBundle`` basically manages the Product-related entities & managers, offers ``AdminBundle`` integration and provides a basic controller and basic views to display the products.
It also offers a console command to easily generate a new product type in your application:

.. code-block:: bash

    Usage:
     sonata:product:generate product service_id

    Arguments:
     product               The product to create
     service_id            The service id to define

    Options:
     --help (-h)           Display this help message.
     --quiet (-q)          Do not output any message.
     --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
     --version (-V)        Display this application version.
     --ansi                Force ANSI output.
     --no-ansi             Disable ANSI output.
     --no-interaction (-n) Do not ask any interactive question.
     --shell (-s)          Launch the shell.
     --process-isolation   Launch commands from shell as a separate process.
     --env (-e)            The Environment name. (default: "dev")
     --no-debug            Switches off debug mode.

Product / SonataSeoBundle integration
=====================================

To enhance your project interactions with third parties web communities, the ``SonataProductBundle`` ships with ``SonataSeoBundle``.

You can easily write your own microdata implementation and, to do so, we recommend to write your own service in which you can inject, at least, a `ProductInterface` entity and a `SeoPageInterface` entity.

Concrete examples of implementation as `Facebook Open Graph <http://developers.facebook.com/docs/opengraph/>`_ or `Twitter Cards <https://dev.twitter.com/docs/cards>`_ have been developed as working examples in the Sonata Demo.

Configuration
=============

The bundle allows you to configure the entity classes; you'll also need to register the doctrine mapping.

.. code-block:: yaml

    sonata_product:
        products:
            # Prototype
            id:
                provider:             ~  # Required
                manager:              ~  # Required
                variations:
                    fields:           [] # Required
        class:
            product:              Application\Sonata\ProductBundle\Entity\Product
            package:              Application\Sonata\ProductBundle\Entity\Package
            product_category:     Application\Sonata\ProductBundle\Entity\ProductCategory
            product_collection:   Application\Sonata\ProductBundle\Entity\ProductCollection
            category:             Application\Sonata\ClassificationBundle\Entity\Category
            collection:           Application\Sonata\ClassificationBundle\Entity\Collection
            delivery:             Application\Sonata\ProductBundle\Entity\Delivery
            gallery:              Application\Sonata\MediaBundle\Entity\Gallery

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataProductBundle: ~
                        SonataProductBundle: ~

Blocks
======

``SonataProductBundle`` comes with some blocks services that you can use anywhere you want to show your products :

* ``SimilarProductsBlockService``: from a given Product id (``base_product_id``), displays the Products in the same Collection (limited to ``number`` ones).
* ``RecentProductsBlockService``: displays the last products added to the database (limited to ``number`` items).
