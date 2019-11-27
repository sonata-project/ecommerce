===============================
e-commerce Bundles installation
===============================

Prerequisites
-------------

There are some Sonata dependencies that need to be installed and configured beforehand:

    - `SonataAdminBundle <https://sonata-project.org/bundles/admin>`_
    - `SonataEasyExtendsBundle <https://sonata-project.org/bundles/easy-extends>`_
    - `SonataCoreBundle <https://sonata-project.org/bundles/core>`_
    - `SonataBlockBundle <https://sonata-project.org/bundles/block>`_
    - `SonataClassificationBundle <https://sonata-project.org/bundles/classification>`_
    - `SonataMediaBundle <https://sonata-project.org/bundles/media>`_
    - `SonataNotificationBundle <https://sonata-project.org/bundles/notification>`_
    - `SonataDashboardBundle <https://sonata-project.org/bundles/dashboard>`_
    - `SonataSeoBundle <https://sonata-project.org/bundles/seo>`_
    - `SonataFormatterBundle <https://sonata-project.org/bundles/formatter>`_

Follow their configuration step; you will find everything you need in their own
installation chapter.

.. note::
    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

.. code-block:: bash

    $ composer require sonata-project/ecommerce

Next, be sure to enable the bundles in your ``bundles.php`` file if they
are not already enabled:

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
        Sonata\DeliveryBundle\SonataDeliveryBundle::class => ['all' => true],
        Sonata\PaymentBundle\SonataPaymentBundle::class => ['all' => true],
        Sonata\PriceBundle\SonataPriceBundle::class => ['all' => true],
        // ...
        new Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    ];

.. note::
    If you are not using Symfony Flex, you should enable bundles in your
    ``AppKernel.php``.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Sonata\CustomerBundle\SonataCustomerBundle(),
            new Sonata\ProductBundle\SonataProductBundle(),
            new Sonata\BasketBundle\SonataBasketBundle(),
            new Sonata\OrderBundle\SonataOrderBundle(),
            new Sonata\InvoiceBundle\SonataInvoiceBundle(),
            new Sonata\DeliveryBundle\SonataDeliveryBundle(),
            new Sonata\PaymentBundle\SonataPaymentBundle(),
            new Sonata\PriceBundle\SonataPriceBundle(),
            // ...
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        );
    }

Configuration
-------------

.. note::
    If you are not using Symfony Flex, all configuration in this section should
    be added to ``app/config/config.yml``.

.. configuration-block::

    .. code-block:: yaml

            # app/config/sonata_ecommerce.yml

            sonata_media:
                # ...
                contexts:
                    # ...
                    product_catalog:
                        providers:
                            - sonata.media.provider.image

                        formats:
                            preview: { width: 80 , quality: 70}
                            small: { width: 100 , quality: 70}
                            large: { width: 500 , quality: 70}
                            big:   { width: 800 , quality: 70}

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

Extending the Bundle
--------------------
At this point, the bundle is functional, but not quite ready yet. You need
to generate the correct entities for all bundles:

.. code-block:: bash

    bin/console sonata:easy-extends:generate SonataBasketBundle --dest=src --namespace_prefix=App
    bin/console sonata:easy-extends:generate SonataCustomerBundle --dest=src --namespace_prefix=App
    bin/console sonata:easy-extends:generate SonataInvoiceBundle --dest=src --namespace_prefix=App
    bin/console sonata:easy-extends:generate SonataOrderBundle --dest=src --namespace_prefix=App
    bin/console sonata:easy-extends:generate SonataPaymentBundle --dest=src --namespace_prefix=App
    bin/console sonata:easy-extends:generate SonataProductBundle --dest=src --namespace_prefix=App

.. note::
    If you are not using Symfony Flex, use command without ``--namespace_prefix=App``.

With provided parameters, the files are generated in ``src/Sonata``.

.. note::

    The command will generate domain objects in an ``App`` namespace.
    So you can point entities' associations to a global and common namespace.
    This will make Entities sharing easier as your models will allow to
    point to a global namespace. For instance the basket will be
    ``App\Sonata\BasketBundle\Entity\Basket``.

.. note::
    If you are not using Symfony Flex, the namespace will be ``App\Sonata``.

Now, add the new ``App`` Bundle into the ``bundles.php``:

.. code-block:: php

    <?php

    // config/bundles.php

    return [
        //...

        App\Sonata\CustomerBundle\ApplicationSonataCustomerBundle::class => ['all' => true],
        App\Sonata\BasketBundle\ApplicationSonataBasketBundle::class => ['all' => true],
        App\Sonata\InvoiceBundle\ApplicationSonataInvoiceBundle::class => ['all' => true],
        App\Sonata\OrderBundle\ApplicationSonataOrderBundle::class => ['all' => true],
        App\Sonata\PaymentBundle\ApplicationSonataPaymentBundle::class => ['all' => true],
        App\Sonata\ProductBundle\ApplicationSonataProductBundle::class => ['all' => true],
    ];

.. note::
    If you are not using Symfony Flex, add the new ``App`` Bundle into your
    ``AppKernel.php``.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerbundles()
    {
        return [
            // ...
            new App\Sonata\CustomerBundle\ApplicationSonataCustomerBundle(),
            new App\Sonata\BasketBundle\ApplicationSonataBasketBundle(),
            new App\Sonata\InvoiceBundle\ApplicationSonataInvoiceBundle(),
            new App\Sonata\OrderBundle\ApplicationSonataOrderBundle(),
            new App\Sonata\PaymentBundle\ApplicationSonataPaymentBundle(),
            new App\Sonata\ProductBundle\ApplicationSonataProductBundle(),
            // ...
        ];
    }

Next, add the correct routing files:

.. configuration-block::

    .. code-block:: yaml

        # config/routes.yaml

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

.. note::
    If you are not using Symfony Flex, routes should be added to ``app/config/routing.yml``.

If you are not using auto-mapping in doctrine you will have to add it there
too:

.. note::
    If you are not using Symfony Flex, next configuration should be added
    to ``app/config/config.yml``.

.. code-block:: yaml

    # config/packages/doctrine.yaml

    doctrine:
        #...
        orm:
            entity_managers:
                default:
                    mappings:
                        #...
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


The only thing left is to update your schema:

.. code-block:: bash

    php bin/console doctrine:schema:update --force

Create missing contexts:

.. code-block:: bash

    $ bin/console sonata:classification:fix-context
    $ bin/console sonata:media:fix-media-context

After the initial setup, you have to create a product:

    Bundles Product <reference/bundles/product>
