================
Create a product
================

Before we start adding any products, we will have to create a *prototype*. A *prototype* is mandatory for any product definition. It can be compared to its skeleton in the application.

In our case, we will need two kind of items:

* a mug prototype that will have no specific options, 
* a jersey one, which can provide various sizes.

In order to ease that work, a command has been implemented to create the required files quickly.

Without variation
=================
We will start the fastest way : the mug prototype. This step will be splitted in two parts : 

* the "files" configuration to provide every elements required 
* the "backoffice" configuration, to create the product itself.

Configuration files
-------------------
Run the following command to create the files :
::

  php app/console sonata:product:generate Mug sonata.ecommerce_demo.product.mug

The required base files will be created in ``src/Application/Sonata/ProductBundle``. To finalize the installation, we have to define the missing parameters like the type itself and the related manager. These data have to be provided in ``src/Application/Sonata/ProductBundle/Resources/config/product.yml``.
::

    services:
        sonata.ecommerce_demo.product.mug.manager:
            class: Sonata\ProductBundle\Entity\ProductManager
            arguments:
                - Application\Sonata\ProductBundle\Entity\Mug
                - @sonata.product.entity_manager

        sonata.ecommerce_demo.product.mug.type:
            class: Application\Sonata\ProductBundle\Provider\MugProductProvider
            arguments:
                - @serializer

Don't forget to load this file adding the following lines in the ``app/config/config.yml``
::

    imports:
        - { resource: @ApplicationSonataProductBundle/Resources/config/product.yml }

And finally, add in the ``app/config/sonata/sonata_product.yml`` the following data
::

    sonata_product:
        products:
            sonata.ecommerce_demo.product.mug:
                provider: sonata.ecommerce_demo.product.mug.type
                manager: sonata.ecommerce_demo.product.mug.manager


Once you are done with this, edit the ``src/Application/Sonata/ProductBundle/Entity/Mug.php`` to make it inherits the ``Product`` class.

Backoffice configuration
-------------------------
Now that we have all the required files, we can process the creation of the product itself. 
Go to the admin dashboard and select "Product" in the "e-Commerce" menu. After clicking on "Add new" on the top right of the page, a list with 3 items type should be displayed:
::

    sonata.ecommerce_demo.product.goodie
    sonata.ecommerce_demo.product.training
    sonata.ecommerce_demo.product.mug

The "goodie" and "training" are part of the original sandbox so we will select the "mug" one.

In the first tab, note that the VAT type of field must be a percent.

Now switch to the "Categories" tab, and attach our product to the correct category, "Arizona Cardinals" in our case. Don't forget to enable the relation by checking the checkbox.

We will repeat the same process in the "Collection" tab using the "Mugs" collection that we have previously created.

Since the delivery part is covered in a whole chapter, we won't provide any information about it for now.

You should now be able to browse your first product in the frontoffice part !

With variation(s)
=================

Configuration files
-------------------
In order to create a product with a variation (a jersey in our example), we will have to repeat the same steps as explained in the previous chapter, in the "Configuration files" part. For the purpose of this exemple, we will use ``Jersey`` as entity name and ``sonata.ecommerce_demo.product.jersey`` as service name.

Once you've completed the whole process, we will learn how to add variable fields. In our case, it will be the size. To do so, add the "size" property in the entity (``src/Application/Sonata/ProductBundle/Entity/Jersey.php``)
::

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

Also, still in the same file, we will provide a list of possible values for this field by adding, still in the same files, the size list
::

    const SIZE_S = 'Small (S)';
    const SIZE_M = 'Medium (M)';
    const SIZE_L = 'Large (L)';
    const SIZE_XL = 'Extra Large (XL)';
    const SIZE_XXL = 'Extra Extra Large (XXL)';

    /**
     * @return array
     */
    public static function getSizeList()
    {
        return array(
            static::SIZE_S => static::SIZE_S,
            static::SIZE_M => static::SIZE_M,
            static::SIZE_L => static::SIZE_L,
            static::SIZE_XL => static::SIZE_XL,
            static::SIZE_XXL => static::SIZE_XXL,
        );
    }

Now, we have to add this field in our entity. Considering you are using Doctrine ORM, you should add the following line in ``src/Application/Sonata/ProductBundle/Resources/config/doctrine/Jersey.orm.xml``
::

    <field name="size" column="size" type="string" length="50" nullable="true" />

And finally, tell our app that we will be using the "size" field as a variation. To define this, in the ``app/config/sonata/sonata_product.yml``, after the manager definition line of our prototype, add the following code
::

    variations:
        fields: [size]

As the variation is stored as a real field in our model, we now have to update our database schema. Run the following command to control everything is fine
::

    php app/console doctrine:schema:update --dump-sql

And if everything is ok, perform to the modification
::

    php app/console doctrine:schema:update --force

If you go back to the product creation page, you should be able to see our provider and display its page without any error. Though, the size field is not available yet. We have to enable it manually overriding the ``JerseyProductProvider::buildEditForm()`` method. You first should add the usage of ``Application\Sonata\ProductBundle\Entity\Jersey`` class
::

    public function buildEditForm(FormMapper $formMapper, $isVariation = false)
    {
        parent::buildEditForm($formMapper, $isVariation);

        if ($isVariation) {
            $formMapper->with('Product')
                ->add('size', 'choice', array(
                    'choices'            => Jersey::getSizeList(),
                    'translation_domain' => 'ApplicationSonataProductBundle',
                ))
            ->end();
        }
    }

Once we have done this, we should still have no error but the "size" field shouldn't be available yet though. It's simply because we first have to create a *base* product and each of its variations will be real products. You can picture this as an abstract class (the *base* product) extended by many concrete classes (one per variation). Let's do this !


Back office configuration
-------------------------
Repeat the same steps as indicated for products with no variations. Once you have completed this step, you should be able to browse the created product, without any variation yet. This is the default behavior : as long as you enable a product supposed to have any variations, it will be displayed if **none** are provided. If you have one disabled, the product will be considered as disabled. But let's get back to our product.

Go to the list page. Thick in the checkbox in front of our recently created product ("Arizona Cardinals Replica Jersey - Fitzgerald - Tough Red") and in the dropdown menu select and validate the "Create a variation" option. You should be prompted to confirm the variation creation. As you can see, the created variation is disabled by default so we need to first edit it, and then enable it. You might have noticed that the product is not available in the frontend anymore as explained previously. The "edit" page should now look a bit different : less fields, but we have the "size" one !

Once you have edited the product and enabled it, it should now appear in the frontend. Congratulation, you have created your first variation !

For the purpose of this tutorial, we strongly advice you to add a couples of other items like a cool Brady' jersey from New England Patriots or a Deasean Jackson' one from Philadelphia Eagles ;-)
