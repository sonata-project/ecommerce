.. index::
    single: Class Diagram
    single: Order
    single: Payment
    single: Price
    single: Delivery
    single: Invoice

=============================
Sonata ECommerce Architecture
=============================

Congrats! You've successfully installed your Sonata e-commerce solution! Now, time to digg in. Here's an overview of the architecture of the library.

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

Price computation
-----------------

Well... We're entering the depths of it, aren't we? To be crystal clear, we'll try to answer the basic questions:

**When?**
    Each time you add a product (ie. BasketElement) to your Basket, the BasketElement price is calculated & the Basket price is calculated as well.

**Based on?**
    The price is computed based on defined product price, VAT if any, quantity, and currency.

**Who/How?**
    As you can see below, when the BasketElements of the basket are altered (added/removed), the buildPrices method is called, which for each element will compute its price. To do that, we go through the product provider of the basket element's product (which you can override easily in your implementation), and then through the currency price calculator (whose default behavior is only to return the product's price).

.. image:: ../../images/dsPrice.svg


Basket <-> Order transformations and storage
--------------------------------------------

Along the checkout process, once the Basket has been validated by the Customer and is about to be paid for, we'll transform the Basket (which is submitted to change) into an Order (which will be fixed in time). Hence, Basket & Order entities are quite similar, but we'll need to copy data from one to the other. We can't afford having relationships / dependencies that may evolve later in the Order entity, hence we copy or serialize those.

Basket -> Order
~~~~~~~~~~~~~~~

At the Order instance construction depending on the Basket, we'll compute the final Order price, based on BasketElements, Delivery, etc. We use the ``BasketTransformer::transformIntoOrder`` method to do so. This will check for the Basket validity and create an Order instance based on that.

You may, of course, override this transformer and the associated service (service id is ``sonata.payment.transformer.basket``).

Order -> Basket
~~~~~~~~~~~~~~~

You may need sometimes to rebuild a basket from a previous Order. In order to do so, the ``OrderTransformer::transformIntoBasket`` method does the job. Based on the stored product id, you'll be able to rebuild a Basket with the potentially new information (if you changed your product) of your website.

You may, of course, override this transformer and the associated service (service id is ``sonata.payment.transformer.order``).

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