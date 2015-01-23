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

The SonataProductBundle basically manages the Product-related entities & managers, offers AdminBundle integration and provides a basic controller and basic views to display the products.

It offers a console command to easily generate a new product type in your application:

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


Furthermore, you can create a dump file with products schema information. Even those created with the previous command:

.. code-block:: bash

    Usage:
     sonata:doctrine:utils -f /tmp/dump.json dump-products-meta

    Arguments:
     action                The action to execute [dump-meta | dump-products-meta | dump-products-category-meta | dump-media-meta | dump-category-meta]

    Options:
     --filename (-f)       If filename is specified, result will be dump into this file under json format.
     --help (-h)           Display this help message.
     --quiet (-q)          Do not output any message.
     --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.
     --version (-V)        Display this application version.
     --ansi                Force ANSI output.
     --no-ansi             Disable ANSI output.
     --no-interaction (-n) Do not ask any interactive question.
     --shell (-s)          Launch the shell.
     --process-isolation   Launch commands from shell as a separate process.
     --env (-e)            The Environment name. (default: "dev")
     --no-debug            Switches off debug mode.


This can be used for import/export purposes for example.

Product / SonataSeoBundle integration
=====================================

To enhance your project interactions with third parties web communities, the SonataProductBundle ships with SonataSeoBundle.

You can easily write your own microformats implementation, and, to do so, we recommend to write your own service in which you can inject, at least, a ProductInterface entity and a SeoPageInterface entity.

Concrete examples of implementation as `Facebook Open Graph <http://developers.facebook.com/docs/opengraph/>`_ or `Twitter Cards <https://dev.twitter.com/docs/cards>`_ have been developed as working examples in the Sonata Demo.

Configuration
=============

The bundle allows you to configure the entity classes ; you'll also need to register the doctrine mapping.

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

Import Command
==============

The bundle also implements an command to import products from a csv file. It can also handle product main image.

.. code-block:: bash
    Usage:
        sonata:product:add-multiple --file sample.csv

    Other usage:
        cat sample.csv|php app/console sonata:product:add-multiple -v --strict

    Options:
         --file                The file to parse
         --delimiter           Set the field delimiter (one character only) (default: ",")
         --enclosure           Set the field enclosure character (one character only). (default: "\"")
         --escape              Set the escape character (one character only). Defaults as a backslash (default: "\\")
         --type-column       Set the product family column name (default: "type")
         --sku-column          Set the product sku column name (default: "sku")
         --image-column        Set the product image column name (default: "image")
         --categories-column     Set the product category column name (default: "categories")
         --strict              If strict is true, process will stop on exception. Otherwise, it will try to process the next line
         --help (-h)           Display this help message.
         --quiet (-q)          Do not output any message.
         --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.
         --version (-V)        Display this application version.
         --ansi                Force ANSI output.
         --no-ansi             Disable ANSI output.
         --no-interaction (-n) Do not ask any interactive question.
         --shell (-s)          Launch the shell.
         --process-isolation   Launch commands from shell as a separate process.
         --env (-e)            The Environment name. (default: "dev")
         --no-debug            Switches off debug mode.


The sample.csv file contains the following lines::

    type,sku,name,description,price,image,price_including_vat,categories,enabled
    goodie,goodie_1,"Goodie 1","My awesome goodie",25,"/var/www/sonata-dev/web/uploads/media/import/thumb_11_sonata_product_large.jpeg",1,"shoes,clothes",1
    goodie,goodie_2,"Goodie 2","My awesome goodie",25,"/var/www/sonata-dev/web/uploads/media/import/thumb_12_sonata_product_large.jpeg",1,"plush",1
    travel,travel_1,"Travel 1","My awesome travel",245,"/var/www/sonata-dev/web/uploads/media/import/thumb_13_sonata_product_large.jpeg",0,"mugs",1
    travel,travel_2,"Travel 2","My awesome travel",250,"/var/www/sonata-dev/web/uploads/media/import/thumb_14_sonata_product_large.jpeg",1,"goody,mugs",1

*Values in category column are category's slugs separated by ",".*

**You can configure the following parameters to match your needs.**
 - sonata.product.import.product_code_prefix: Prefix to generate product code. Default is sonata.ecommerce_demo.product
 - sonata.product.import.media_provider_key: Key of the media manager that should handle product main media. Default is sonata.media.provider.image
 - sonata.product.import.media_context: Set the media context value. Default is "sonata_product"
 - sonata.product.import.product_category_manager: Key of the product_category manager. By default it is an alias to sonata.product_category.product service.
 - sonata.product.import.category_manager: Key of the category manager. By default it is an alias to sonata.product_category.product service.
 - sonata.product.import.logger: Key of the logger service that you want to use in this command. By default it is an alias to sonata.classification.manager.category service.