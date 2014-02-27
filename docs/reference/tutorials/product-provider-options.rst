.. index::
    single: Product provider
    pair: Product provider; Tutorial

===================================
Basic options for product providers
===================================

You can add options to your product provider.

Available options
=================

There is some available options you can enable in your product provider class in order to alter the way your product is used.

Here are some built-in options you can use:

Enable a modal for "add to basket" product page button
------------------------------------------------------

This option will display your product in a modal (popin) after clicking on "add to basket" button on the product page
with a small summary of your product.

Start by adding the option in your product provider:

.. code-block:: php

    <?php

    namespace Application\Sonata\ProductBundle\Provider;

    use JMS\Serializer\SerializerInterface;

    use Sonata\ProductBundle\Model\BaseProductProvider;

    /**
     * TrainingProductProvider class
     */
    class TrainingProductProvider extends BaseProductProvider
    {
        /**
         * {@inheritdoc}
         */
        public function __construct(SerializerInterface $serializer)
        {
            $this->serializer = $serializer;
            $this->setOptions(array(
                'product_add_modal' => true
            ));
        }

        // ...

You also have to create a template file to display your products properties. Those will be rendered via
a ``Resources/views/Training/properties.html.twig`` template. It can be something like:

.. code-block:: html+jinja

    <dl>
        {% if not product.isMaster %}
            <dt>{{ 'training.level_title'|trans([], 'SonataProductBundle') }}</dt>
            <dd>{{ product.level|trans([], 'SonataProductBundle') }}</dd>
        {% endif %}
    </dl>