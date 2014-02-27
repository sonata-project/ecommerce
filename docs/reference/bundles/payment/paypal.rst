.. index::
    pair: Payment; Paypal

============
Paypal setup
============

Presentation
============

Paypal is a CreditCard and web payment provider. Here's how to configure it in order to use it in your application.

Configuration
=============

Here's how to configure paypal for Sonata e-commerce:

1. Activate IPN notification service

URL: http://youwebsite.com/shop/payment/callback

2. Retrieve paypal public certificate (paypal_cert_pem.txt)

  - Go to https://www.paypal.com/fr/cgi-bin/webscr?cmd=_profile-website-cert
  - Click *Download*
  - Put this file somewhere in your project

3. Generate your private and public keys [f1]_

.. code-block:: bash

    $ cd yoursite/keys
    $ sudo openssl genrsa -out my-prvkey.pem 1024
    $ openssl req -new -key my-prvkey.pem -x509 -days 365 -out my-pubcert.pem

Then:
    - Go to https://www.paypal.com/fr/cgi-bin/webscr?cmd=_profile-website-cert
    - Press *Upload*
    - Select the ``my-pubcert.pem`` file

4. Configure the payment gateway

.. code-block:: yaml

    sonata_payment.config:
        methods:
            paypal:
                name: Paypal
                id: paypal
                enabled: true
                class: Sonata\Component\Payment\Paypal
                options:
                    cert_id:            CERTIFICATE ID # related to the cert_file file
                    account:            PAYPAL ACCOUNT NUMBER
                    debug:              false
                    class_order:        Application\Sonata\OrderBundle\Entity\Order
                    url_callback:       sonata_payment_callback
                    url_return_ko:      sonata_payment_error
                    url_return_ok:      sonata_payment_confirmation
                    url_action:         https://www.paypal.com/cgi-bin/webscr

                    method:             encryptViaBuffer # encryptViaFile || encryptViaBuffer

                    key_file:           %kernel.root_dir%/my-prvkey.pem
                    cert_file:          %kernel.root_dir%/my-pubcert.pem
                    paypal_cert_file:   %kernel.root_dir%/paypal_cert_pem.txt
                    openssl:            /opt/local/bin/openssl

                transformers:
                    basket: sonata.transformer.basket
                    order: sonata.transformer.order

.. rubric:: Footnotes

.. [f1] A good way to do it is explained here: http://help.ubuntu.com/community/SSH/OpenSSH/Keys
