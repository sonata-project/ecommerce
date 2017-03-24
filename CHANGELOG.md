# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
