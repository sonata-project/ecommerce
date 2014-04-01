.. index::
    single: Event
    single: Product
    single: Order
    single: Invoice
    single: Basket
    pair: Event; Architecture

======
Events
======

Sonata e-commerce provides events through Symfony2's EventDispatcher to allow you to further customize your business logic.

Basket
------

.. code-block:: php

    <?php

    final class BasketEvents
    {
        const PRE_ADD_PRODUCT  = 'sonata.ecommerce.basket.pre_add_product'; // AddBasketElementEvent
        const POST_ADD_PRODUCT = 'sonata.ecommerce.basket.post_add_product'; // AddBasketElementEvent

        const PRE_MERGE_PRODUCT  = 'sonata.ecommerce.basket.pre_merge_product'; // AddBasketElementEvent
        const POST_MERGE_PRODUCT = 'sonata.ecommerce.basket.post_merge_product'; // AddBasketElementEvent

        const PRE_CALCULATE_PRICE  = 'sonata.ecommerce.basket.pre_calculate_price'; // BeforeCalculatePriceEvent
        const POST_CALCULATE_PRICE = 'sonata.ecommerce.basket.post_calculate_price'; // AfterCalculatePriceEvent
    }

Payment
-------

.. code-block:: php

    <?php

    final class PaymentEvents
    {
        // Fires only PaymentEvent instances

        const PRE_ERROR  = "sonata.ecommerce.payment.pre_error";
        // Sent just before adding the order to the message queue
        const POST_ERROR = "sonata.ecommerce.payment.post_error";

        const CONFIRMATION = "sonata.ecommerce.payment.confirmation";

        const PRE_CALLBACK  = "sonata.ecommerce.payment.pre_callback";
        // Sent just before adding the order to the message queue
        const POST_CALLBACK = "sonata.ecommerce.payment.post_callback";

        const PRE_SENDBANK  = "sonata.ecommerce.payment.pre_sendbank";
        const POST_SENDBANK = "sonata.ecommerce.payment.post_sendbank";
    }


Transformers
------------

.. code-block:: php

    <?php

    final class TransformerEvents
    {
        // Basket to order transformation
        const PRE_BASKET_TO_ORDER_TRANSFORM   = "sonata.ecommerce.pre_basket_to_order_transform"; // BasketTransformEvent
        const POST_BASKET_TO_ORDER_TRANSFORM  = "sonata.ecommerce.pre_basket_to_order_transform"; // OrderTransformEvent

        // Order to basket transformation
        const PRE_ORDER_TO_BASKET_TRANSFORM   = "sonata.ecommerce.pre_order_to_basket_transform"; // OrderTransformEvent
        const POST_ORDER_TO_BASKET_TRANSFORM  = "sonata.ecommerce.pre_order_to_basket_transform"; // BasketTransformEvent

        // Order to invoice transformation
        const PRE_ORDER_TO_INVOICE_TRANSFORM  = "sonata.ecommerce.pre_order_to_invoice_transform"; // OrderTransformEvent
        const POST_ORDER_TO_INVOICE_TRANSFORM = "sonata.ecommerce.pre_order_to_invoice_transform"; // InvoiceTransformEvent
    }

Order
-----

We were planning on adding events on order creation/alteration/deletion and status change; however the most effective way is to listen to events fired by the doctrine ORM layer. See `How to Register Event Listeners and Subscribers <http://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html>`_ on the Symfony documentation to do so.