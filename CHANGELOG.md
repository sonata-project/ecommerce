# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.0.1](https://github.com/sonata-project/ecommerce/compare/2.3.0...3.0.0) - 2019-11-24
### Fixed
- crash when using doctrine bundle 2

## [3.0.0](https://github.com/sonata-project/ecommerce/compare/2.3.0...3.0.0) - 2019-11-16
### Fixed
- Fixed missing security.context service error
- Increase precision for decimal price fields in `BaseProduct`, `BaseOrder`,
  `BaseOrderElement`

### Removed
- support for symfony 2
- compatibility with FOSRest `<2.2`

## [2.3.0](https://github.com/sonata-project/ecommerce/compare/2.2.0...2.3.0) - 2019-11-16
### Fixed
- Changed the authentication check in the CustomerSelector, check for
`IS_AUTHENTICATED_REMEMBERED`.
- removed usage of deprecated classes and interfaces
- Missing relation for `productCollection` field

### Removed
- support for php 5 and php 7.0

## [2.2.0](https://github.com/sonata-project/ecommerce/compare/2.1.1...2.2.0) - 2017-12-25
### Added
- Added missing `basket` property in `AddressType`
- Added `discriminator-field-name` to fix JMS Serializer compatibility
- Added russian translations

### Changed
- changed skeleton to create abstract class for Product Entity

### Fixed
- Made `choice` fields compatible with >=SF 2.7
- usage of `MopaBootstrapBundle`
- Removed `NotNull` constraint from `BaseDelivery` validation
- Fixed usage of not persisted addresses when the customer is taken from a session.
- Fix SonataProductExtension loading
- Fixed usage of deprecated methods in controllers (Improve compatibility with SF 3)
- Fixed `ProductCategoryManager` compatibility with PostgreSQL
- Fixed clearing basket with deleted from db products
- Removed not existing directory from autoload-dev
- Fixed contributors homepage link
- Fixed compatibility with SonataAdminBundle ^3.29
- Fixed resetting full basket stored in session
- Removed duplicated code from `BasketSessionFactory`

### Deprecated
- Deprecated `ProductAdmin::getProductClass` method

### Removed
- Support for old versions of PHP and Symfony.

## [2.1.1](https://github.com/sonata-project/ecommerce/compare/2.1.0...2.1.1) - 2017-04-04
### Changed
- Replaced types for the FQCN's

### Fixed
- Fixed `AddressType` forms `choices` option for SF>=2.7
- Fixed `BasketElement`, `CurrencyPriceCalculator` price calculation with `priceIncludingVat=true`
- Fixed `setPriceIncludingVat` param type in `BasketElementInterface` and `ProductInterface`
- use `AbstractAdmin` instead of deprecated `Admin` class
- Fixed typo in `TransformerEvents` consts values

## [2.1.0](https://github.com/sonata-project/ecommerce/compare/2.0.0...2.1.0) - 2017-03-23
### Added
- Added `sonata.payment.generator.postgres` service

### Changed
- use `configureSettings ` instead of `setDefaultSettings`
- `CurrencyDoctrineType` now extends `Doctrine\DBAL\Types\Type` instead of `Doctrine\DBAL\Types\StringType`
- Changed `BaseProduct::setSlug` to use `cocur-slugify`
- Deprecated `BaseProduct::slugify`

### Fixed
- Use `AbstractAdmin` instead of deprecated `AdminClass` and added dependency for `SonataAdminBundle`
- fix missing configuration to the CoreBundle's FormHelper
- Fix jQuery `add_basket_button` form  selector for `ProductBundle:Product:view.html` and `ProductBundle:Product:view_thumbnail.html`
- Fixed tests namespaces
- Replaced deprecated `getMock` with `createMock`
- Added support for FOSRestBundle >= 2.0
- Replaced deprecated `BaseBlockService`
- Replaced deprecated `$form->bind($request)` with `$form->handleRequest($request)`
- Replaced twig deprecated `renderMetadatas ` with `getMetadatas `
- Increased block-bundle dependency to ^3.2
- Fixed missing default `transformers` section in `PaymentBundle` configuration

### Removed
- internal test classes are now excluded from the autoloader
