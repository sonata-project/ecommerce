.. index::
    pair: Payment; Ogone

===========
Ogone setup
===========

Presentation
============

Ogone is a CreditCard payment provider. Here's how to configure it in order to use it in your application.

Configuration
=============

Here's how to configure Ogone for Sonata e-commerce:

1. Go to the Ogone configuration portal:
    1. ``Technical Settings`` -> ``Global Security Parameters`` ; set ``Hash Algorithm`` to ``SHA-1``
    2. ``Technical Settings`` -> ``Data and origin verification`` ; add your various domain(s) URL(s) separated by ';' (no spaces)
    3. ``Technical Settings`` -> ``Data and origin verification`` ; fill in your SHA-IN pass phrase (keep it, you'll need it for your app config)
    4. ``Technical Settings`` -> ``Transaction feedback``
        1. ``HTTP redirection in the browser`` : leave all fields blank
        2. ``Direct HTTP server-to-server request`` : ``Timing of the request`` : ``always deferred``
            URL of the merchant's post-payment page
                1. If the payment's status is "accepted", "on hold" or "uncertain" -> ``{your_host}/payment/callback``
                2. If the payment's status is "cancelled by the client" or "too many rejections by the acquirer". -> ``{your_host}/payment/error``
        3. ``SHA-OUT pass phrase`` ; fill in with the same key as the one you've given for the SHA-IN


2. Configure the payment gateway according to the values you'll get with your Ogone account (or you'll fill in the Ogone admin)

.. code-block:: yaml

    sonata_payment:
        services:

            # ...

            ogone:
                name:                   ogone
                enabled:                true
                code:                   ogone

                transformers:
                    basket:             sonata.payment.transformer.basket
                    order:              sonata.payment.transformer.order

                options:
                    url_callback:       sonata_payment_callback
                    url_return_ko:      sonata_payment_error
                    url_return_ok:      sonata_payment_confirmation

                    form_url:           "%ogone.form_url%"
                    pspid:              "%ogone.pspid%"
                    home_url:           "%ogone.home_url%"
                    catalog_url:        "%ogone.catalog_url%"

                    sha_key:            "%ogone.sha_key%"
                    sha-out_key:        "%ogone.sha-out_key%"

            # ...

        parameters:
            # ...

            # If you've overridden the OgonePayment class
            sonata.payment.method.ogone.class:      "Application\\Sonata\\Component\\Payment\\Ogone\\OgonePayment"
            #...