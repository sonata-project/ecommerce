========================
Sonata demo installation
========================

Pre-requisites
================

- Git
- PHP 5.3.2 and up
- MySQL / MariaDB 5.5 and up
- Apache 2.2 and up or Nginx
- Composer [1]_


Code installation
=================
Start by cloning the demo repository:
::

    cd the/path/to/your/repositories_root
    git clone http://github.com/sonata-project/sandbox.git sonata-demo
    cd sonata-demo
    git checkout -b 2.3-develop


Remove existing git files and initialise your new project:
::

    cd sonata-demo
    rm -rf .git
    git init
    git add .gitignore *
    git commit -m "Initial commit (from the Sonata Demo)"
    cp app/config/parameters.yml.sample app/config/parameters.yml
    cp app/config/parameters.yml.sample app/config/validation_parameters.yml
    cp app/config/parameters.yml.sample app/config/production_parameters.yml
    composer install -o --prefer-source

Database installation
=====================
At this point, the ``app/console`` command should work without issues. However, you will need to edit the ``app/config/parameters.yml`` file to create database and tables.

To do so, you will have to run the following commands:
::

    app/console doctrine:database:create
    app/console doctrine:schema:create

Assets installation
===================
Your frontoffice still should look weird because bundle assets are not installed yet. Run the following command to install them for all active bundles under the public directory ``web``:
::

    app/console assets:install web


Sonata Page Bundle
==================
By default, the Sonata Page bundle is activated, so you need to run 3 commands before going further. You will be prompted for the default locale for your application:
::

    app/console sonata:page:create-site --enabled=true --name=localhost --host=localhost --relativePath=/ --enabledFrom=now --enabledTo="+10 years" --default=true
    app/console sonata:page:update-core-routes --site=all
    app/console sonata:page:create-snapshots --site=all

Notes:

* The ``update-core-routes`` populates the database with pages from the routing information.
* The ``create-snapshots`` create a snapshot (a public page version) from the created pages.

For more information about these last commands, we recommend you to read the `dedicated documentation`_.

Fixtures
========
To have some random data in your database, you should load the fixtures by running the following command:
::

    app/console doctrine:fixtures:load

.. [1] To install composer, a tutorial can be found on `its own website`_.
.. _its own website: http://getcomposer.org/doc/00-intro.md
.. _dedicated documentation: http://www.sonata-project.org/bundles/page/master/doc/index.html