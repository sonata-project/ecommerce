.. index::
single: Product
    pair: Product; Tutorial

===============================
Create a custom search provider
===============================

Create provider class
=====================

First, you have to define a new ``CustomSearchProvider`` class and extends ``Sonata\ProductBundle\Search\Provider\BaseSearchProvider``:

.. code-block:: php

    <?php

    namespace Application\Sonata\ProductBundle\Search\Provider;

    use Sonata\DatagridBundle\Datagrid\Datagrid;
    use Sonata\ProductBundle\Search\Provider\BaseSearchProvider;

    use Symfony\Component\Form\FormFactoryInterface;

    /**
     * Class CustomSearchProvider
     *
     * Your custom search provider
     */
    class CustomSearchProvider extends BaseSearchProvider
    {
        /**
         * {@inheritdoc}
         */
        public function getQuery()
        {
            // Return your query builder class
        }

        /**
         * {@inheritdoc}
         */
        public function buildFilters()
        {
            // Add your custom filters
            // $this->filters[] = ...;
        }

        /**
         * {@inheritdoc}
         */
        public function buildFacets()
        {
            // Add your custom facets
            // $this->facets[] = ...;
        }

        /**
         * {@inheritdoc}
         */
        public function buildQuery()
        {
            // Build your query and add your filters & facets to your query
        }

        /**
         * {@inheritdoc}
         */
        public function buildDatagrid()
        {
            // Build the Datagrid instance with ProxyQuery, Pager, Form and options
            // $this->datagris = new Datagrid(...);
        }
    }

You can have a look at existing providers to have more information on how to implement it:

* Doctrine provider: ``Sonata\ProductBundle\Search\Provider\DoctrineSearchProvider``
* ElasticSearch provider: ``Sonata\ProductBundle\Search\Provider\ElasticaSearchProvider``


Create provider service
=======================

Add a service to use your provider:

.. code-block:: xml

    <service id="my.custom.search.provider" class="Application\Sonata\ProductBundle\Search\Provider\CustomSearchProvider">
        <argument type="service" id="form.factory" />
        <argument type="service" id="fos_elastica.manager.orm" />
        <argument>%sonata.product.product.class%</argument>
    </service>


Update configuration
====================

In order to use your custom provider, you have to update your ``sonata_product`` configuration part as follows:

.. code-block:: yaml

    sonata_product:
        search:
            provider: my.custom.search.provider


That's all, your provider is now used by product's search engine.