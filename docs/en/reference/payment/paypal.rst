============
Paypal setup
============

1. Activate IPN notification service

url : http://youwebsite.com/shop/payment/callback

2. Retrieve paypal public certificate (paypal_cert_pem.txt)

  - Go to https://www.paypal.com/fr/cgi-bin/webscr?cmd=_profile-website-cert 
  - Click download
  - Put this file somewhere in your project.
  
3. Generate your private and public keys

::

    $ cd yoursite/keys
    $ sudo openssl genrsa -out my-prvkey.pem 1024
    $ openssl req -new -key my-prvkey.pem -x509 -days 365 -out my-pubcert.pem

Then:
    - Go to https://www.paypal.com/fr/cgi-bin/webscr?cmd=_profile-website-cert 
    - Press upload
    - Select the my-pubcert.pem file

4. Configure the payment gateway

::

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
                    
