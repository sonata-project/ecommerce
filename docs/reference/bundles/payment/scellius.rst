.. index::
    pair: Payment; Scellius

==============
Scellius setup
==============

Presentation
============

Scellius is a CreditCard payment provider. Here's how to configure it in order to use it in your application.

Configuration
=============

.. code-block:: yaml

    sonata_payment:
        # ...
        services:
            # Which payment methods are enabled?
            # ...
            scellius:
                name:                 Scellius
                enabled:              ~ # Required
                code:                 scellius
                generator:            sonata.payment.provider.scellius.none_generator
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                options:
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
                    template:             SonataPaymentBundle:Payment:scellius.html.twig
                    shop_secret_key:      ~
                    request_command:      ~
                    response_command:     ~
                    merchant_id:          ~
                    merchant_country:     ~
                    pathfile:             ~
                    language:             ~
                    payment_means:        ~
                    base_folder:          ~
                    data:
                    header_flag:          no
                    capture_day:
                    capture_mode:
                    bgcolor:
                    block_align:
                    block_order:
                    textcolor:
                    normal_return_logo:
                    cancel_return_logo:
                    submit_logo:
                    logo_id:
                    logo_id2:
                    advert:
                    background_id:
                    templatefile:
