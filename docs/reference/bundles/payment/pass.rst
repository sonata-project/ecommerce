.. index::
    pair: Payment; Pass

==========
Pass setup
==========

Presentation
============

Pass payment simulates a payment provider inside the application. If the application uses it, it will call itself over HTTP (it needs to be self-reachable through its URL) and systematically validate the payment. This is the method used by default by the Sonata demo.

You may want to use this for free products for instance, this will allow you to keep a track of the "payment" transactions even though they didn't occur.

Configuration
=============

.. code-block:: yaml

    sonata_payment:
        # ...
        services:
            # Which payment methods are enabled?
            # ...
            pass:
                name:                 Pass
                enabled:              ~ # Required
                code:                 pass
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                browser:              sonata.payment.browser.curl
                options:
                    shop_secret_key:      ~
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
