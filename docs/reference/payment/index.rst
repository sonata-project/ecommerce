=======
Payment
=======

Methods
=======
.. toctree::
    :maxdepth: 2

    Scellius (Credit Card) #TODO <scellius>
    Ogone (Credit Card) <ogone>
    Paypal <paypal>
    Check #TODO <check>

Interaction with payment institution
====================================

Regardless of the payment method, the payment process is as follows:

1. ``PaymentController::callbankAction``

Redirects the user to the payment institution (through the payment method's callbank method)

2. Payment's return, the payment institution initiates a call to the ``PaymentController::callback`` action

This is a secured, server-to-server call ; this is where we'll check the payment's return (depending on the payment institution's security policies), and update the order's status depending on the feedback given by the payment institution.
    
    - (Optionnaly) If everything is well, Sonata ecommerce sends a request to the server to notify it that we did indeed handle its callback.
    - If the payment method doesn't handle this case, we simply update our data and don't notify the server.

3. User's return

If the user didn't simply close the window, he's redirected to our website : this can be to the ``PaymentController::error`` action if there was an error, or he canceled ; otherwise, if the payment is successful, the user is redirected to the ``PaymentController::confirmation`` action which will only check that the order's payment is indeed confirmed and display a confirmation message to the user.

.. image:: ../../images/dsPayment.svg

Data transformation during payment
==================================

- Before calling the payment institution:
    - The basket is transformed into an order via the basketTransformer linked to the payment method chosen by the user and the order is saved.
    - The status of the order at this point is: ``OrderInterface::STATUS_OPEN`` ; its payment status is: ``TransactionInterface::STATUS_OPEN``
    
- On payment institution's callback (not redirection):
    - Creating a new transaction based on the callback's parameters (``GET`` & ``POST`` ; ``POST`` parameters overriding ``GET`` parameters)
    - Validating the transaction (its status is then updated)
    
- Notifying callback handling to payment institution
    - Update the order's status & payment status: ``OrderInterface::STATUS_VALIDATED`` and ``TransactionInterface::STATUS_VALIDATED`` if it's ok, ``TransactionInterface::STATUS_ERROR_VALIDATION`` if not.
    
- User redirected to confirmation
    - No data alteration, only checks
    
- User redirected to error
    - If Order is cancelable, set its status to ``OrderInterface::STATUS_STOPPED``
    - Set transaction's status to ``TransactionInterface::STATUS_CANCELLED``

