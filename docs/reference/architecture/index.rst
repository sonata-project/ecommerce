=============================
Sonata ECommerce Architecture
=============================

Congrats! You've successfully installed your sonata ecommerce solution! Now, time to digg in. Here's an overview of the architecture of the library.

Below, you'll find a simple Class Diagram, representing the relationships between the different entities. Be warned however! Some of those aren't DB relations, as they shouldn't be (you'll get more details as you keep reading).

.. image:: ../../images/dcEntities.svg

Technical Choices
=================

Order process (customer workflow)
---------------------------------

Currently, the order process is implemented as follows:
- The customer chooses a product
- He adds it to the basket (which can be stored either in session or in DB)
- The customer proceeds to checkout
- He fills in his informations (name, address, ...) (variant: we can also display a login/register form)

    Nota: The customer doesn't have to create an account: it can be a one-time order. In that case, we store his informations in the basket.

- He selects his delivery option

    This option depends on its country, as you can see in :doc:`product`.

- He selects his payment mode

    The choices depend on the products in his basket.

- He processes to payment

    There, the Basket is transformed into an order. This implies that all that concerns the basket is serialized and duplicated into the order.
    This is done so, if/when the customer's informations and/or the product's informations change. If the order has been checked out, it has to be fixed in time, hence the serialization.

- When the payment is completed, we edit an invoice, which can be printed out as a PDF or HTML file.

Basket <-> Order transformations and storage
--------------------------------------------

#TODO

Basket -> Order
~~~~~~~~~~~~~~~

#TODO

Order -> Basket
~~~~~~~~~~~~~~~

#TODO

Order -> Invoice
~~~~~~~~~~~~~~~~

See :doc:`invoice`.

Going in depths
===============

.. toctree::
    :maxdepth: 2
    
    product
    currency
    customer
    basket
    order
    payment
    invoice

