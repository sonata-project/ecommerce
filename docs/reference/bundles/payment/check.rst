.. index::
    pair: Payment; Check

===========
Check setup
===========

Presentation
============

Check payment allows you to handle customers paying their orders by check (that is to say, once the order has been delivered). This payment mean can also be used if the payment is handled in another way than through the website (CRM integration for instance).

Configuration
=============

.. code-block:: yaml

    sonata_payment:
        # ...
        services:
            # Which payment methods are enabled?
            # ...
            check:
                name:                 Check
                enabled:              ~ # Required
                code:                 check
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                browser:              sonata.payment.browser.curl
                options:
                    shop_secret_key:      ~
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
