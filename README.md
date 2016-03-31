# Whats NearBy

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/WhatsNearby.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/WhatsNearby)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/whats-nearby/version.png)](https://packagist.org/packages/mediawiki/whats-nearby)
[![Packagist download count](https://poser.pugx.org/mediawiki/whats-nearby/d/total.png)](https://packagist.org/packages/mediawiki/whats-nearby)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:whats-nearby/badge.png)](https://www.versioneye.com/php/mediawiki:whats-nearby)

Whats Nearby is a small extension that adds geolocation (HTML5) detection information
to templates in order for [`#ask`][smw] distance queries to generate adaptive content.

Privacy: This extension makes actively use of the HTML5 geolocation feature in case the
`nolocation` option is not used.

## Requirements

- PHP 5.3.2 or later
- MediaWiki 1.23 or later
- [Maps][maps] 3.5 or later

## Installation

The recommended way to install WhatsNearby is by using [Composer][composer] with
an entry in MediaWiki's `composer.json`.

```json
{
	"require": {
		"mediawiki/whats-nearby": "~1.0"
	}
}
```
1. From your MediaWiki installation directory, execute
   `composer require mediawiki/whats-nearby:~1.0`
2. Navigate to _Special:Version_ on your wiki and verify that the package
   have been successfully installed.

## Usage

![image](https://cloud.githubusercontent.com/assets/1245473/13100182/71f52ad6-d53a-11e5-8d57-3d1f94f510ee.png)

Add a `#nearby` parser function to a page where template content is expected
to be displayed for a geolocation.

```
{{#nearby:
 |querytemplate=Local libraries,Point of interest
 |coordinates=47° 37' 13.9368'' N,122° 20' 56.8860'' W
 |radius=300 m
 |interval=450
 |max=10000
 |maps=googlemaps
 |detectLocation=true
 |watchLocation=false
 |localCache=300
 |controls=slider
 |class=extra-nearby-location
}}
```

- querytemplate: specifies the template that contains the actual `#ask` query and any
  other condition one wishes to display (to allow for a selection of different templates
  separate them with a comma as in Sightseeing spots, Libraries)
- detectlocation: whether HTML5 [geolocation](https://dev.w3.org/geo/api-/spec-source.html) should be used or not (opt-out)
- watchlocation: monitor the location or location changes (opt-in)
- coordinates: can be set as starting parameters in case geolocation doesn't work or is disabled
- class: a simple css class to manipulate the output display
- controls: slider or button
- radius: the expected starting radius (e.g. 200m, 4km)
- interval: to describe the internal a search should be continued
- max: defines the maximum limit or radius to be permitted for selection
- localcache: defines the time in seconds (with the default of 300) with which
  results from the back-end are stored using the local browser

The `nolocation` parameter will disable the `geolocation` feature completely for
a select query. In combination with non-maps related query formats (`table`,
`embedded`), `#nearby` can equally create a dynamic result display for those
formats that do not require additional JavaScript to be loaded.

```
{{#nearby: [[Has text::~Lorem ipsum]]
 |?Has text
 |limit=5
 |max=100
 |interval=10
 |nolocation=true
 |format=table
 |localcache=no
 |controls=slider
 |querytemplate=semantic.query.tmpl
}}
```

Parameters not listed will be made available to a `querytemplate` as-is.

- [example.semantic.distance.maps.tmpl](docs/example.semantic.distance.maps.tmpl.md) contains an
  example template using [`#ask` queries][smw] to find local libraries based on the
  position determined by `#nearby`.
- [example.semantic.query.tmpl.md](docs/example.semantic.query.tmpl.md) a simple `#ask` replacement
  query to create dynamic results

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* [File an issue](https://github.com/SemanticMediaWiki/WhatsNearby/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/WhatsNearby/pulls)
* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Freenode.

## Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[maps]: https://github.com/SemanticMediaWiki/SemanticMaps
[travis]: https://travis-ci.org/SemanticMediaWiki/WhatsNearby
[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[composer]: https://getcomposer.org/
