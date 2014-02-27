.. index::
    pair: Payment; Debug

===================
Debug payment setup
===================

Presentation
============

Debug Payment simulates the bank for your testing purpose. The ``sendbank`` will redirect you to a page where you can act as the bank and choose the action you want (accept or refuse the payment).

Then, ``DebugPayment`` will call itself over HTTP (it needs to be self-reachable through its URL) and process the payment or not.

For security reasons, this method is only available in dev environment.

Configuration
=============

.. code-block:: yaml

    sonata_payment:
        # ...
        services:
            # Which payment methods are enabled?
            # ...
            debug:
                name:    Debug Payment
                enabled: true
                code:    debug
                browser: sonata.payment.browser.curl

                transformers:
                    basket: sonata.payment.transformer.basket
                    order:  sonata.payment.transformer.order

                options:
                    url_callback:  sonata_payment_callback
                    url_return_ko: sonata_payment_error
                    url_return_ok: sonata_payment_confirmation
