UPGRADE 2.x
===========

UPGRADE FROM 2.1 to 2.2
=======================

### Sonata\CustomerBundle\Form\Type\AddressType

If you redefined this class, note that missing `basket` property was added, which means it is no longer public.

UPGRADE FROM 2.0 to 2.1
=======================

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes. 
You can't extend them anymore, because they are only loaded when running internal tests. 
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
