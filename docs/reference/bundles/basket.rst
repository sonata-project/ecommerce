.. index::
    single: Basket

======
Basket
======

Architecture
============

For more information about our position regarding the *basket* architecture, you can read: :doc:`../architecture/basket`.

Presentation
============

The ``SonataBasketBundle`` handles everything related to the `Basket` and the `Order` process (user workflow).
It offers Basket, Address, Payment & Shipping forms, all the needed controller actions, and exposes Basket's handling services such as Provider, Factory and Loader.

Several actions are provided by the controller:

* ``index``: (step 1) Displays the current status of the `Basket` and its update form (where you can delete elements or update their quantity)
* ``update``: Validates the submitted update `Basket` form
* ``reset``: Empties the `Basket`
* ``addProduct``: As the name says, handles the add `Product` to `Basket` form submission
* ``deliveryAddressStep``: (step 2) Renders the `Delivery` address form and handles its submission (the user needs to be authenticated)
* ``deliveryStep``: (step 3) Renders the `Delivery` method (shipping) selection form and handles its submission
* ``paymentStep``: (step 4) Renders the `Payment` method selection form and handles its submission
* ``finalReviewStep``: (step 5) Renders the `Basket` status before `Payment`, terms & conditions acceptance form and handles its submission
* ``headerPreview``: Renders the preview of the `Basket` (to put in a header)
* ``authenticationStep``: Retrieves the `Customer` related to the logged in user and links it to the basket; this will redirect to the authentication form if the user is not logged in.

Anonymous basket invalidation
=============================

If you wish to invalidate the anonymous basket stored in session when the user logs out (and if you didn't invalidate the session), you'll need to edit your ``security.yml`` file to add this to your logout configuration:

.. code-block:: yaml

    security:
        # ...
        firewalls:
            # ...
            main:                   # Your firewall name
                # ...
                logout:
                    # ...
                    # We set invalidate_session to false because we want basket
                    # to be fully persisted even when user logout and login again
                    invalidate_session: false
                    # And we add the handler to invalidate the anonymous basket once the user logs out
                    handlers: ['sonata.basket.session.factory']


Configuration
=============

Default
-------

Here's the full default configuration for `SonataBasketBundle`:

.. code-block:: yaml

    sonata_basket:

        # Services
        builder:            sonata.basket.builder.standard
        factory:            sonata.basket.session.factory       # Replace with sonata.basket.entity.factory to store in db
        loader:             sonata.basket.loader.standard

        # Model
        class:
            basket:         Application\Sonata\BasketBundle\Entity\Basket
            basket_element: Application\Sonata\BasketBundle\Entity\BasketElement
            customer:       Application\Sonata\CustomerBundle\Entity\Customer

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
Moreover, you're able to override the rest of the bundle by extending it (through `SonataEasyExtendsBundle` for instance).

Storage
-------

There are two ways to deal with `Basket` storage:

* Session storage (default)
* DB storage

To enable DB storage, you'll need to change the following configuration values:

.. code-block:: yaml

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

DB basket loading is slightly different as session one. It actually comes into play only once the customer is stored into database. Once that's done, we retrieve the baskets both from database and session, and replace basket elements in database with the ones from the session.

If you wish to customize this behavior, you'll need to create your custom basket factory (by overloading ``BasketEntityFactory`` for instance) and replace the service in your configuration.
