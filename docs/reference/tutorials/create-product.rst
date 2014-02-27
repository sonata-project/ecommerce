.. index::
    single: Product
    pair: Product; Tutorial

================
Create a product
================

Before we start adding any products, we will have to create a *prototype*. A *prototype* is mandatory for any product definition. It can be compared to its skeleton in the application.

In our case, we will need two kind of items:

* a bowl prototype that will have no specific options, 
* a spoon, which can provide various sizes.

In order to create these 2 prototypes, a command has been implemented to quickly generate the required files.

Product without variation
=========================

We will start with the easiest kind of product: the *bowl prototype*. This step will be splitted in two parts:

* configuration via *files'* edition, to provide every required element
* configuration via the *backoffice*, to create the product itself

Configuration - files' edition
------------------------------
Run the following command to create the files:

.. code-block:: bash

	php app/console sonata:product:generate Bowl sonata.ecommerce_demo.product.bowl

The required base files will be created in ``src/Application/Sonata/ProductBundle``. 
To finalize the installation, we have to define the missing parameters like the type itself and the related manager. These data have to be provided in ``src/Application/Sonata/ProductBundle/Resources/config/product.yml``.

.. code-block:: yaml

    # src/Application/Sonata/ProductBundle/Resources/config/product.yml

    services:
        sonata.ecommerce_demo.product.bowl.manager:
            class: Sonata\ProductBundle\Entity\ProductManager
            arguments:
                - Application\Sonata\ProductBundle\Entity\Bowl
                - @sonata.product.entity_manager

        sonata.ecommerce_demo.product.bowl.type:
            class: Application\Sonata\ProductBundle\Provider\BowlProductProvider
            arguments:
                - @serializer

Don't forget to load this file by adding the following lines in the ``app/config/config.yml``

.. code-block:: yaml

    # app/config/config.yml

    imports:
        - { resource: @ApplicationSonataProductBundle/Resources/config/product.yml }

And finally, add in the ``app/config/sonata/sonata_product.yml`` the following data:

.. code-block:: yaml

    # app/config/sonata/sonata_product.yml

    sonata_product:
        products:
            sonata.ecommerce_demo.product.bowl:
                provider: sonata.ecommerce_demo.product.bowl.type
                manager: sonata.ecommerce_demo.product.bowl.manager


This being done, edit the ``src/Application/Sonata/ProductBundle/Entity/Bowl.php`` to make it inherits the ``Product`` class.

Configuration - Backoffice
--------------------------

Now that we have all the required files, we can process the creation of the `Product` itself.
Go to the *admin dashboard* and select *Product* in the *e-commerce* menu. After clicking on ``Add new`` on the top right of the page, a list with 3 items type should be displayed:

* sonata.ecommerce_demo.product.goodie
* sonata.ecommerce_demo.product.training
* sonata.ecommerce_demo.product.bowl

In the first tab, note that the VAT type of field must be a percent.
The *goodie* and *training* are part of the original sandbox so we will select the *bowl* one.
                                                                                                             
Now switch to the *Categories* tab, and attach our product to the correct category, "Dishes" in our case. Don't forget to enable the relation by checking the checkbox.

We will repeat the same process in the "Collection" tab using the "Bowls" collection that we have previously created.                                        

Since the delivery part is covered in a whole chapter, we won't provide any information about it for now.

You should now be able to browse your first product on the frontoffice!

Product with variation(s)
=========================

Configuration - files' edition
------------------------------
In order to create a `Product` with a variation (a `spoon` in our example), we will have to repeat the same steps as explained in the previous section, in the *Configuration - files' edition* part. For the purpose of this example, we will use ``Spoon`` as entity name and ``sonata.ecommerce_demo.product.spoon`` as service name.

Once you've completed the whole process, we will now learn how to add variable fields. In our case, it will be the size. To do so, add the "size" property in the entity (``src/Application/Sonata/ProductBundle/Entity/Spoon.php``):

.. code-block:: php

    // src/Application/Sonata/ProductBundle/Entity/Spoon.php

    <?php

    // ...

    /**
     * @var string
     */
    protected $size;

    /**
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

Still in the same file, we will provide a list of possible values for this field by adding the size list:

.. code-block:: php

    // src/Application/Sonata/ProductBundle/Entity/Spoon.php

    <?php

    // ...

    const SIZE_TSP = 'Small (Tea spoon)';
    const SIZE_S = 'Medium (Spoon)';
    const SIZE_TBSP = 'Large (Tablespoon)';

    /**
     * @return array
     */
    public static function getSizeList()
    {
        return array(
            static::SIZE_TSP => static::SIZE_TSP,
            static::SIZE_S => static::SIZE_S,
            static::SIZE_TBSP => static::SIZE_TBSP,
        );
    }

Now, we have to add this field in our entity. Considering you are using Doctrine ORM, you should add the following line in ``src/Application/Sonata/ProductBundle/Resources/config/doctrine/Jersey.orm.xml``:


.. code-block:: xml

    // src/Application/Sonata/ProductBundle/Resources/config/doctrine/Jersey.orm.xml

    <field name="size" column="size" type="string" length="50" nullable="true" />

Finally, tell our app that we will be using the "size" field as a variation. To define this, in the ``app/config/sonata/sonata_product.yml``, after the manager definition line of our prototype, add the following code:

.. code-block:: yaml

    # app/config/sonata/sonata_product.yml

    variations:
        fields: [size]

As the variation is stored as a real field in our model, we now have to update our database's schema. Run the following command to control everything is fine:

.. code-block:: bash

    php app/console doctrine:schema:update --dump-sql

And if everything is ok, perform to the modification:

.. code-block:: bash

    php app/console doctrine:schema:update --force

If you go back to the *product creation* page, you should be able to see our provider and display its page without any error. Though, the size field is not available yet. We have to enable it manually by overriding the ``SpoonProductProvider::buildEditForm()`` method. 
You first should add the usage of ``Application\Sonata\ProductBundle\Entity\Spoon`` class:

.. code-block:: php

    <?php

    public function buildEditForm(FormMapper $formMapper, $isVariation = false)
    {
        parent::buildEditForm($formMapper, $isVariation);

        if ($isVariation) {
            $formMapper->with('Product')
                ->add('size', 'choice', array(
                    'choices'            => Spoon::getSizeList(),
                    'translation_domain' => 'ApplicationSonataProductBundle',
                ))
            ->end();
        }
    }

Once we have done this, we should still have no error but the *size* field shouldn't be available yet. It's simply because we first have to create a *base product* and each of its variations will be *real products*. 

You can picture this as an abstract class (the *base product*) extended by many concrete classes (one per variation). Let's do this !


Configuration - Backoffice
--------------------------
Repeat the same steps as indicated for products with no variations. Once you have completed this step, you should be able to browse the created product, without any variation yet. 

This is the default behavior : as long as you enable a product supposed to have any variations, it will be displayed if **none** are provided. If you have one disabled, the product will be considered as disabled. But let's get back to our product.

Go to the *list page*. Check the checkbox in front of our recently created product ("Mommy's tea spoon") and in the dropdown menu select and validate the "Create a variation" option. You should be prompted to confirm the variation creation. As you can see, the created variation is disabled by default so we need to first edit it, and then enable it. You might have noticed that the product is not available in the frontend anymore as explained previously. The "edit" page should now look a bit different : less fields, but we have the "size" one !

Once you have edited the product and enabled it, it should now appear in the frontoffice. Congratulations, you have created your first variation !