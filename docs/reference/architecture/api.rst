.. index::
    single: API
    single: Product
    single: Order
    single: Invoice
    single: Customer
    single: Basket
    pair: API; Architecture

API
===

Sonata e-commerce embeds `Controllers` to provide an `API` through `FOSRestBundle`, with its documentation provided by ``NelmioApiDocBundle``.

Setup
-----

If you wish to use it, you must first follow the installation instructions of both bundles:

* `FOSRestBundle <https://github.com/FriendsOfSymfony/FOSRestBundle>`_
* `NelmioApiDocBundle <https://github.com/nelmio/NelmioApiDocBundle>`_

Here's the configuration we used, you may adapt it to your needs:

.. code-block:: yaml

    fos_rest:
        param_fetcher_listener: true
        body_listener: true
        format_listener: true
        view:
            view_response_listener: force
        body_converter:
            enabled: true
            validate: true

    sensio_framework_extra:
        view: { annotations: false }
        router: { annotations: true }
        request: { converters: true }

    jms_serializer:
        metadata:
            directories:
                - { name: 'sonata_datagrid', path: "%kernel.project_dir%/vendor/sonata-project/datagrid-bundle/src/Resources/config/serializer", namespace_prefix: 'Sonata\DatagridBundle' }
                - { name: 'sonata_basket_component', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/BasketBundle/Resources/config/serializer/Component", namespace_prefix: 'Sonata\Component' }
                - { name: 'sonata_basket', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/BasketBundle/Resources/config/serializer", namespace_prefix: 'Sonata\BasketBundle' }
                - { name: 'sonata_customer_component', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/CustomerBundle/Resources/config/serializer/Component", namespace_prefix: 'Sonata\Component' }
                - { name: 'sonata_customer', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/CustomerBundle/Resources/config/serializer", namespace_prefix: 'Sonata\CustomerBundle' }
                - { name: 'sonata_invoice_component', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/InvoiceBundle/Resources/config/serializer/Component", namespace_prefix: 'Sonata\Component' }
                - { name: 'sonata_invoice', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/InvoiceBundle/Resources/config/serializer", namespace_prefix: 'Sonata\InvoiceBundle' }
                - { name: 'sonata_order_component', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/OrderBundle/Resources/config/serializer/Component", namespace_prefix: 'Sonata\Component' }
                - { name: 'sonata_order', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/OrderBundle/Resources/config/serializer", namespace_prefix: 'Sonata\OrderBundle' }
                - { name: 'sonata_product_component', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/ProductBundle/Resources/config/serializer/Component", namespace_prefix: 'Sonata\Component' }
                - { name: 'sonata_product', path: "%kernel.project_dir%/vendor/sonata-project/ecommerce/src/ProductBundle/Resources/config/serializer", namespace_prefix: 'Sonata\ProductBundle' }

    twig:
        exception_controller: null

    framework:
        error_controller: 'FOS\RestBundle\Controller\ExceptionController::showAction'

In order to activate the API's, you'll also need to add this to your routing:

.. code-block:: yaml

    sonata_api_ecommerce_product:
        type:         rest
        prefix:       /api/ecommerce
        #resource:     "@SonataProductBundle/Resources/config/routing/api_nelmio_v3.xml"

    sonata_api_ecommerce_order:
        type:         rest
        prefix:       /api/ecommerce
        #resource:     "@SonataOrderBundle/Resources/config/routing/api_nelmio_v3.xml"

    sonata_api_ecommerce_invoice:
        type:         rest
        prefix:       /api/ecommerce
        #resource:     "@SonataInvoiceBundle/Resources/config/routing/api_nelmio_v3.xml"

    sonata_api_ecommerce_customer:
        type:         rest
        prefix:       /api/ecommerce
        #resource:     "@SonataCustomerBundle/Resources/config/routing/api_nelmio_v3.xml"

    sonata_api_ecommerce_basket:
        type:         rest
        prefix:       /api/ecommerce
        #resource:     "@SonataBasketBundle/Resources/config/routing/api_nelmio_v3.xml"


Serialization
-------------

We're using ``JMSSerializationBundle's`` serialization groups to customize the inputs and outputs.

The taxonomy is as follows:

* ``sonata_api_read`` is the group used to display entities
* ``sonata_api_write`` is the group used for input entities (when used instead of forms)

If you wish to customize the outputted data, feel free to set up your own serialization options by configuring `JMSSerializer` with those groups.
