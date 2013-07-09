============
Installation
============

Quick Install
=============

If you want to create a quick and running sonata ecommerce project, we strongly advise that you use our sandbox:
See `Sonata Sandbox installation <https://github.com/sonata-project/sandbox>`_.

Manual Install
==============

You already have a project and want to add ecommerce capabilities to it? Follow these instructions:

- Check dependencies are installed:

    - Symfony2
    - PHP 5.3.2

- Add the project to your composer.json

::

    "require" {
        ...
        "sonata-project/ecommerce": "2.3.*",
        ...
    }
    
- Run composer install

- Follow FOSUserBundle README to set it up

- Follow EasyExtendsBundle README

- Follow MediaBundle README

- Add the following bundles in your ``kernel::registerBundles()`` method

::

        new FOS\UserBundle\FOSUserBundle(),
        new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),

        new Sonata\CustomerBundle\SonataCustomerBundle(),
        new Sonata\ProductBundle\SonataProductBundle(),
        new Sonata\BasketBundle\SonataBasketBundle(),
        new Sonata\OrderBundle\SonataOrderBundle(),
        new Sonata\InvoiceBundle\SonataInvoiceBundle(),
        new Sonata\MediaBundle\SonataMediaBundle(),
        new Sonata\DeliveryBundle\SonataDeliveryBundle(),
        new Sonata\PaymentBundle\SonataPaymentBundle(),


- Run the ``easy-extends:generate`` command, this command will generate the Application entities required by the Sonata's Bundles

::

        php app/console sonata:easy-extends:generate SonataCustomerBundle
        php app/console sonata:easy-extends:generate SonataDeliveryBundle
        php app/console sonata:easy-extends:generate SonataBasketBundle
        php app/console sonata:easy-extends:generate SonataInvoiceBundle
        php app/console sonata:easy-extends:generate SonataMediaBundle
        php app/console sonata:easy-extends:generate SonataOrderBundle
        php app/console sonata:easy-extends:generate SonataPaymentBundle
        php app/console sonata:easy-extends:generate SonataProductBundle

- Then add the following bundles in your ``kernel::registerBundles()`` method

::

        new Application\Sonata\CustomerBundle\SonataCustomerBundle(),
        new Application\Sonata\DeliveryBundle\SonataDeliveryBundle(),
        new Application\Sonata\BasketBundle\SonataBasketBundle(),
        new Application\Sonata\InvoiceBundle\SonataInvoiceBundle(),
        new Application\Sonata\MediaBundle\SonataMediaBundle(),
        new Application\Sonata\OrderBundle\SonataOrderBundle(),
        new Application\Sonata\PaymentBundle\SonataPaymentBundle(),
        new Application\Sonata\ProductBundle\SonataProductBundle(),

You can use these bundles to extend entities or template files

- Edit your ``config.yml`` and add the following lines

::

            sonata_delivery:
                services:
                    sonata.delivery.method.free_address_required:
                        name: Free
                        enabled: true
                        priority: 1

                selector: sonata.delivery.method.free_address_required

            sonata_payment:
                services:
                    sonata.payment.method.paypal:
                        name:     Paypal
                        id:       paypal
                        enabled:  true

                        transformers:
                            basket: sonata.payment.transformer.basket
                            order:  sonata.payment.transformer.order

                        options:
                            web_connector_name: curl

                            account:            your_paypal_account@fake.com
                            cert_id:            fake
                            paypal_cert_file:   %kernel.root_dir%/paypal_cert_pem_sandbox.txt
                            url_action:         https://www.sandbox.paypal.com/cgi-bin/webscr

                            debug: true
                            class_order:        Application\Sonata\OrderBundle\Entity\Order
                            url_callback:       sonata_payment_callback
                            url_return_ko:      sonata_payment_error
                            url_return_ok:      sonata_payment_confirmation

                            method:             encryptViaBuffer # encryptViaFile || encryptViaBuffer

                            key_file:           %kernel.root_dir%/my-prvkey.pem
                            cert_file:          %kernel.root_dir%/my-pubcert.pem

                            openssl:            /opt/local/bin/openssl


                # service which find the correct payment methods for a basket
                selector: sonata.payment.selector.simple

                # service which generate the correct order and invoice number
                generator: sonata.payment.generator.mysql

                transformers:
                    order:  sonata.payment.transformer.order
                    basket: sonata.payment.transformer.basket

            services:
                # Register dedicated Product Managers
    #           sonata.product.manager.amazon:
    #                class: Sonata\ProductBundle\Entity\ProductManager
    #                arguments:
    #                    - Application\Sonata\ProductBundle\Entity\Amazon
    #                    - @sonata.product.entity_manager
    #
    #           sonata.product.manager.bottle:
    #                class: Sonata\ProductBundle\Entity\ProductManager
    #                arguments:
    #                    - Application\Sonata\ProductBundle\Entity\Bottle
    #                    - @sonata.product.entity_manager
    #
    #           # Register dedicated Product Providers
    #            sonata.product.type.amazon:
    #                class: Application\Sonata\ProductBundle\Entity\AmazonProductProvider
    #
    #           sonata.product.type.bottle:
    #                class: Application\Sonata\ProductBundle\Entity\BottleProductProvider

- Add the current lines in your ``routing.yml`` files

::

        # sonata front controller
        sonata_user:
            resource: @SonataUserBundle/Resources/config/routing/user.xml
            prefix: /shop/user

        sonata_order:
            resource: @SonataOrderBundle/Resources/config/routing/order.xml
            prefix: /shop/user/invoice

        sonata_product:
            resource: @SonataProductBundle/Resources/config/routing/product.xml
            prefix: /shop/product

        sonata_category:
            resource: @SonataProductBundle/Resources/config/routing/category.xml
            prefix: /shop/category

        sonata_payment:
            resource: @SonataPaymentBundle/Resources/config/routing/payment.xml
            prefix: /shop/payment

        sonata_invoice:
            resource: @SonataInvoiceBundle/Resources/config/routing/invoice.xml
            prefix: /shop/user/invoice

- Add these lines into the admin (AdminBundle)

::

        product:
            label:      Product
            group:      Shop
            class:      Sonata\ProductBundle\Admin\ProductAdmin
            entity:     Application\Sonata\ProductBundle\Entity\Product
            controller: SonataProductBundle:ProductAdmin
            children:
                product_delivery:
                    label:      Product Delivery
                    group:      Shop
                    class:      Sonata\ProductBundle\Admin\ProductDeliveryAdmin
                    entity:     Application\Sonata\ProductBundle\Entity\Delivery
                    controller: SonataProductBundle:ProductDeliveryAdmin

        order:
            label:      Order
            group:      Shop
            class:      Sonata\OrderBundle\Admin\OrderAdmin
            entity:     Application\Sonata\OrderBundle\Entity\Order
            controller: SonataOrderBundle:OrderAdmin
            children:
                order_element:
                    label:      Order Element
                    group:      Shop
                    class:      Sonata\OrderBundle\Admin\OrderElementAdmin
                    entity:     Application\Sonata\OrderBundle\Entity\OrderElement
                    controller: SonataOrderBundle:OrderElementAdmin

        order_element:
            label:      Order Element
            group:      Shop
            class:      Sonata\OrderBundle\Admin\OrderElementAdmin
            entity:     Application\Sonata\OrderBundle\Entity\OrderElement
            controller: SonataOrderBundle:OrderElementAdmin
            options:
                show_in_dashboard: false

        customer:
            label:      Customer
            group:      Shop
            class:      Sonata\CustomerBundle\Admin\CustomerAdmin
            entity:     Application\Sonata\CustomerBundle\Entity\Customer
            controller: SonataCustomerBundle:CustomerAdmin
            children:
                order:
                    label:      Order
                    group:      Shop
                    class:      Sonata\OrderBundle\Admin\OrderAdmin
                    entity:     Application\Sonata\OrderBundle\Entity\Order
                    controller: SonataOrderBundle:OrderAdmin

                address:
                    label:      Address
                    group:      Shop
                    class:      Sonata\CustomerBundle\Admin\AddressAdmin
                    entity:     Application\Sonata\CustomerBundle\Entity\Address
                    controller: SonataCustomerBundle:AddressAdmin