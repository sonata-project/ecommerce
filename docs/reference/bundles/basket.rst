======
Basket
======

Presentation
============

The BasketBundle handles everything related to the Basket and the Order process (user workflow). It offers Basket, Address, Payment & Shipping forms, all the needed controller actions, and exposes Basket's handling services such as Provider, Factory and Loader.

Several actions are provided by the controller:

* index: (step 1) Displays the current status of the Basket and its update form (where you can delete elements or update their quantity)
* update: Validates the submitted update basket form
* reset: Empties the basket
* addProduct: As the name says, handles the add product to basket form submission
* deliveryAddressStep: (step 2) renders the delivery address form and handles its submission
* deliveryStep: (step 3) renders the delivery method (shipping) selection form and handles its submission
* paymentStep: (step 4) renders the payment method selection form and handles its submission
* finalReviewStep: (step 5) renders the terms & conditions acceptance form and handles its submission
* headerPreview: Renders the preview of the basket
* authentificationStep: Retrieves the customer and links it to the basket

There are two ways to deal with Basket storage:

* Session storage (default)
* DB storage

To enable DB storage, you'll need to change the following configuration values:

.. code-block:: yaml
    :linenos:

    sonata_basket:
        # ...
        factory: sonata.basket.entity.factory     # This is where you switch to DB stored Basket ; sonata.basket.session.factory for session
        # ...


    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataBasketBundle: ~
                        SonataBasketBundle: ~



Configuration
=============

Here's the full default configuration for SonataBasketBundle:

.. code-block:: yaml
    :linenos:

    sonata_basket:

        # Services
        builder:            sonata.basket.builder.standard
        factory:            sonata.basket.session.factory
        loader:             sonata.basket.loader.standard

        # Model
        class:
            basket:         Application\\Sonata\\BasketBundle\\Entity\\Basket
            basket_element: Application\\Sonata\\BasketBundle\\Entity\\BasketElement
            customer:       Application\\Sonata\\CustomerBundle\\Entity\\Customer

        # Forms
        basket:
            form:
                type:       sonata_basket_basket
                name:       sonata_basket_basket_form
        shipping:
            form:
                type:       sonata_basket_shipping
                name:       sonata_basket_shipping_form
        payment:
            form:
                type:       sonata_basket_payment
                name:       sonata_basket_payment_form


As you can see, you can override the builder, factory & loader services ; basket, basket_element & customer classes and the various forms.
Moreover, you're able to override the rest of the bundle by extending it (through SonataEasyExtendsBundle for instance).