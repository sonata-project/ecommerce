=============================
Sonata ECommerce Architecture
=============================

Congrats! You've successfully installed your sonata ecommerce solution! Now, time to digg in. Here's an overview of the architecture of the library.

Below, you'll find a simple Class Diagram, representing the relationships between the different entities. Be warned however! Some of those aren't DB relations, as they shouldn't be (you'll get more details as you keep reading).

.. image:: ../../images/dcEntities.svg

Technical Choices
=================

Order process (user workflow)
-----------------------------

#TODO

Basket <-> Order transformations and storage
--------------------------------------------

#TODO

Basket -> Order
~~~~~~~~~~~~~~~

#TODO

Order -> Basket
~~~~~~~~~~~~~~~

#TODO

Going in depths
===============

.. toctree::
    :maxdepth: 2
    
    product
    customer
    basket
    order
    payment
    invoice

