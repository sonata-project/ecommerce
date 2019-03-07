===============================
e-commerce Bundles installation
===============================

2 installation process are available:

* :ref:`quick-install-ref`
* :ref:`manual-install-ref`

.. _quick-install-ref:

Quick install
=============

In case you need to work with Sonata e-commerce for a new project or just want to test it for a simple overview, we strongly advise that you use our demo.
This demo is available `here <http://demo.sonata-project.org>`_ and the code on `Github <https://github.com/sonata-project/sandbox>`_.


See the `README.md <https://github.com/sonata-project/sandbox/blob/2.4-develop/README.md>`_ for the dedicated *but very easy* installation process and `CONTRIBUTING.MD <https://github.com/sonata-project/sandbox/blob/2.4-develop/CONTRIBUTING.md>`_  if you want to contribute.

.. note::
    We recommend the 2.4-develop branch for testing purposes only.

.. _manual-install-ref:

Manual installation
===================

Retrieve the bundles with composer:

.. code-block:: bash

    $ composer require sonata-project/ecommerce

Follow the installation procedure available in docs for every of these bundles:

  * SonataCoreBundle
  * SonataBlockBundle
  * SonataEasyExtendsBundle
  * SonataAdminBundle
  * SonataClassificationBundle
  * SonataMediaBundle
  * SonataUserBundle
  * SonataNotificationBundle
  * SonataDashboardBundle
  * SonataSeoBundle
  * SonataFormatterBundle

Then register these bundles in your AppKernel:

.. code-block:: php

    <?php
    // config/bundles.php

    return [
        // ...
        Sonata\CustomerBundle\SonataCustomerBundle::class => ['all' => true],
        Sonata\ProductBundle\SonataProductBundle::class => ['all' => true],
        Sonata\BasketBundle\SonataBasketBundle::class => ['all' => true],
        Sonata\OrderBundle\SonataOrderBundle::class => ['all' => true],
        Sonata\InvoiceBundle\SonataInvoiceBundle::class => ['all' => true],
        Sonata\MediaBundle\SonataMediaBundle::class => ['all' => true],
        Sonata\DeliveryBundle\SonataDeliveryBundle::class => ['all' => true],
        Sonata\PaymentBundle\SonataPaymentBundle::class => ['all' => true],
        Sonata\PriceBundle\SonataPriceBundle::class => ['all' => true],
        // ...
        Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
        FOS\RestBundle\FOSRestBundle::class => ['all' => true],
    ];

Next config each of the bundles:

.. configuration-block::

    .. code-block:: yaml

            # app/config/config.yml

            sonata_user:
                # ...
                profile:
                    menu:
                        - { route: 'sonata_user_profile_edit', label: 'link_edit_profile', domain: 'SonataUserBundle'}
                        - { route: 'sonata_user_profile_edit_authentication', label: 'link_edit_authentication', domain: 'SonataUserBundle'}
                        - { route: 'sonata_order_index', label: 'order_list', domain: 'SonataOrderBundle'}

            sonata_media:
                # ...
                contexts:
                    # ...
                    product_catalog:
                        providers:
                            - sonata.media.provider.image

                        formats:
                            small: { width: 100 , quality: 70}
                            big:   { width: 500 , quality: 70}

                    sonata_category:
                        providers:
                            - sonata.media.provider.image

                        formats:
                            small: { width: 100 , quality: 70}
                            big:   { width: 500 , quality: 70}

            sonata_delivery:
                services:
                    free_address_required:
                        name: Free
                        priority: 1
                        code: free

                selector: sonata.delivery.selector.default

            sonata_payment:
                services:
                    pass:
                        name:    Pass
                        code:    pass
                        browser: sonata.payment.browser.curl

                        transformers:
                            basket: sonata.payment.transformer.basket
                            order:  sonata.payment.transformer.order

                        options:
                            shop_secret_key: some-secret-key
                            url_callback:    sonata_payment_callback
                            url_return_ko:   sonata_payment_error
                            url_return_ok:   sonata_payment_confirmation

                # service which find the correct payment methods for a basket
                selector: sonata.payment.selector.simple

                # service which generate the correct order and invoice number
                generator: sonata.payment.generator.mysql # or sonata.payment.generator.postgres

                transformers:
                    order:  sonata.payment.transformer.order
                    basket: sonata.payment.transformer.basket

            sonata_price:
                currency: EUR

            # Doctrine Configuration
            doctrine:
                # ...
                dbal:
                    types:
                        # ...
                        currency: Sonata\Component\Currency\CurrencyDoctrineType

            services:
                # Register dedicated Product Managers
        #       sonata.product.manager.amazon:
        #           class: Sonata\ProductBundle\Entity\ProductManager
        #           arguments:
        #               - Application\Sonata\ProductBundle\Entity\Amazon
        #               - "@sonata.product.entity_manager"

        #       sonata.product.manager.bottle:
        #           class: Sonata\ProductBundle\Entity\ProductManager
        #           arguments:
        #               - Application\Sonata\ProductBundle\Entity\Bottle
        #               - "@sonata.product.entity_manager"

                # Register dedicated Product Providers
        #       sonata.product.type.amazon:
        #           class: Application\Sonata\ProductBundle\Entity\AmazonProductProvider

        #       sonata.product.type.bottle:
        #           class: Application\Sonata\ProductBundle\Entity\BottleProductProvider


In order to generate the `Application entities` required by the Sonata's bundles, run these `easy-extends:generate` commands:

.. code-block:: bash

    php bin/console sonata:easy-extends:generate SonataBasketBundle --dest=src --namespace_prefix=App
    php bin/console sonata:easy-extends:generate SonataCustomerBundle --dest=src --namespace_prefix=App
    php bin/console sonata:easy-extends:generate SonataDeliveryBundle --dest=src --namespace_prefix=App
    php bin/console sonata:easy-extends:generate SonataInvoiceBundle --dest=src --namespace_prefix=App
    php bin/console sonata:easy-extends:generate SonataMediaBundle --dest=src --namespace_prefix=App
    php bin/console sonata:easy-extends:generate SonataOrderBundle --dest=src --namespace_prefix=App
    php bin/console sonata:easy-extends:generate SonataPaymentBundle --dest=src --namespace_prefix=App
    php bin/console sonata:easy-extends:generate SonataProductBundle --dest=src --namespace_prefix=App

Then add the following bundles in your `kernel::registerBundles()` method (after the previously added bundles):

.. code-block:: php

    // config/bundles.php

    return [
        // ...
        App\Application\Sonata\CustomerBundle\ApplicationSonataCustomerBundle::class => ['all' => true],
        App\Application\Sonata\DeliveryBundle\ApplicationSonataDeliveryBundle::class => ['all' => true],
        App\Application\Sonata\BasketBundle\ApplicationSonataBasketBundle::class => ['all' => true],
        App\Application\Sonata\InvoiceBundle\ApplicationSonataInvoiceBundle::class => ['all' => true],
        App\Application\Sonata\MediaBundle\ApplicationSonataMediaBundle::class => ['all' => true],
        App\Application\Sonata\OrderBundle\ApplicationSonataOrderBundle::class => ['all' => true],
        App\Application\Sonata\PaymentBundle\ApplicationSonataPaymentBundle::class => ['all' => true],
        App\Application\Sonata\ProductBundle\ApplicationSonataProductBundle::class => ['all' => true],
    ]

.. configuration-block::

And these in the config mapping definition (or enable auto_mapping):

    .. code-block:: yaml

        # app/config/config.yml

        doctrine:
            orm:
                entity_managers:
                    default:
                        mappings:
                            # ...
                            SonataProductBundle: ~
                            ApplicationSonataProductBundle: ~
                            SonataCustomerBundle: ~
                            ApplicationSonataCustomerBundle: ~
                            SonataBasketBundle: ~
                            ApplicationSonataBasketBundle: ~
                            SonataOrderBundle: ~
                            ApplicationSonataOrderBundle: ~
                            SonataInvoiceBundle: ~
                            ApplicationSonataInvoiceBundle: ~

Configure the bundles to use the newly generated classes

    .. code-block:: yaml

        # app/config/config.yml

        sonata_customer:
            class:
                customer: App\Application\Sonata\CustomerBundle\Entity\Customer
                address: App\Application\Sonata\CustomerBundle\Entity\Address
                order: App\Application\Sonata\OrderBundle\Entity\Order
                user: App\Application\Sonata\UserBundle\Entity\User


Now, you can build up your database:

.. code-block:: bash

    $ php bin/console doctrine:schema:update --force

Create missing contexts:

.. code-block:: bash

    $ php bin/console sonata:classification:fix-context
    $ php bin/console sonata:media:fix-media-context

Add the current lines in your `routing.yml` files:

.. configuration-block::

    .. code-block:: yaml

        # app/config/routing.yml

        # sonata front controller
        sonata_customer:
            resource: "@SonataCustomerBundle/Resources/config/routing/customer.xml"
            prefix: /shop/user

        sonata_basket:
            resource: "@SonataBasketBundle/Resources/config/routing/basket.xml"
            prefix: /shop/basket

        sonata_order:
            resource: "@SonataOrderBundle/Resources/config/routing/order.xml"
            prefix: /shop/user/invoice

        sonata_product_catalog:
            resource: "@SonataProductBundle/Resources/config/routing/catalog.xml"
            prefix: /shop/catalog

        sonata_product:
            resource: "@SonataProductBundle/Resources/config/routing/product.xml"
            prefix: /shop/product

        sonata_payment:
            resource: "@SonataPaymentBundle/Resources/config/routing/payment.xml"
            prefix: /shop/payment

        sonata_invoice:
            resource: "@SonataInvoiceBundle/Resources/config/routing/invoice.xml"
            prefix: /shop/user/invoice

And voilà! Your application boosted with Sonata e-commerce is now ready to rumble! ;-)
