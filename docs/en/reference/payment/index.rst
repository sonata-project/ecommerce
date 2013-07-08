=======
Payment
=======

Methods
-------
.. toctree::
    :maxdepth: 2

    Paypal <paypal>

Process
-------

Regardless of the payment method, the payment process is as follows:

1. ``PaymentController::callbankAction``

Redirects the user to the payment institution (through the payment method's callbank method)

2. Payment's return, the payment institution initiates a call to the ``PaymentController::callback`` action

This is a secured, server-to-server call ; this is where we'll check the payment's return (depending on the payment institution's security policies), and update the order's status depending on the feedback given by the payment institution.

3. User's return

If the user didn't simply close the window, he's redirected to our website : this can be to the ``PaymentController::error`` action if there was an error, or he canceled ; otherwise, if the payment is successful, the user is redirected to the ``PaymentController::confirmation`` action which will only check that the order's payment is indeed confirmed and display a confirmation message to the user.

.. image:: ../../../images/dsPayment.svg