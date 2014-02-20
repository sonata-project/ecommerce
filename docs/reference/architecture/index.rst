.. index::
    single: Class Diagram
    single: Order
    single: Payment
    single: Price
    single: Delivery
    single: Invoice

==============================
Sonata e-commerce Architecture
==============================

Congratulations! You've successfully installed the e-commerce bundles of Sonata! Now, it's time to digg in. Here's an overview of the architecture of the library.

Below, you'll find a simple Class Diagram, representing the relationships between the different entities. Be warned however! Some of those aren't DB relations, as they shouldn't be (you'll get more details as you keep reading).

.. image:: ../../images/dcEntities.svg

Technical Choices
=================

Entity Mappings
---------------

You might be surprised not to find entity mappings in the doctrine configuration files. Those mappings are actually defined in each bundle's extensions, in order to enable the mapping files overrides. So if you're looking for them, feel free to check those files.

If you'd like to add relations to your entities, you may add them in your overridden mapping files.

Order process (customer workflow)
---------------------------------

Currently, the order process is implemented as follows:
- The customer chooses a product
- He adds it to the basket (which can be stored either in session or in DataBase)
- The customer proceeds to checkout
- And he is invited to complete his profile, Sonata provides two options for this:

    The customer creates an account/authenticate on the website (option set by default when you installed the sonata framework)
    Or he fills a list of information in a one-time order. In that case, we store his informations in the basket, and he needs to fill the form for each order he will make.

- He selects his delivery option

    This option depends on its country, as you can see in :doc:`product`.

- He selects his payment mode

    The choices depend on the products in his basket.

- He processes to payment

    There, the Basket is transformed into an order. This implies that the data that concerns the basket is serialized and duplicated into an order.
    This process is made in case the customer's informations and/or the product's informations change. If the order has been checked out, it has to be fixed in time, hence the serialization.

- When the payment is completed, we edit an invoice, which can be printed out as a PDF or HTML file.


Product variations
------------------

Each product can be splitted into "sub-products" : we call them "variations". The main product is called "master product".

The master product is used to be displayed in search results, recent products listing and catalog. The variations are displayed on a product view page (the default one is the cheapest one).


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

Along the checkout process, once the Basket has been validated by the Customer and is about to be paid for, we'll transform the Basket (which is submitted to change) into an Order (which will be fixed in time). Therefore, Basket & Order entities are quite similar, but we'll need to copy data from one to the other. We can't afford having relationships / dependencies that may evolve later in the Order entity, therefore we copy or serialize those.

Basket -> Order
~~~~~~~~~~~~~~~

At the Order instance construction depending on the Basket, we'll compute the final Order price, based on BasketElements, Delivery, etc. We use the ``BasketTransformer::transformIntoOrder`` method to do so. This will check the Basket validity and create an Order instance based on that.

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
    api
