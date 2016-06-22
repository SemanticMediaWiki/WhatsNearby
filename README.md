# Whats Nearby

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/WhatsNearby.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/WhatsNearby)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/WhatsNearby/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/whats-nearby/version.png)](https://packagist.org/packages/mediawiki/whats-nearby)
[![Packagist download count](https://poser.pugx.org/mediawiki/whats-nearby/d/total.png)](https://packagist.org/packages/mediawiki/whats-nearby)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:whats-nearby/badge.png)](https://www.versioneye.com/php/mediawiki:whats-nearby)

Whats Nearby is a small extension that adds geolocation (HTML5) detection information
to templates in order for [`#ask`][smw] distance queries to generate adaptive content.

This extension can be used to:

- Display queryable content that depends on variable location information (`detectLocation`,
  `watchLocation`)
- Instantly modify distance queries  (`@@radius`) from a page view that rely on static coordinates
- Generate dynamic queryable `#ask` lists

Privacy: This extension makes actively use of the HTML5 geolocation feature in case the
`nolocation` parameter is not used.

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

Add a `#nearby` parser function to a page where the content is expected to be
displayed for a geolocation.

```
{{#nearby:
 |querytemplate=Local libraries,Point of interest
 |coordinates=47° 37' 13.9368'' N,122° 20' 56.8860'' W
 |radius=300 m
 |interval=450
 |max=10000
 |format=googlemaps
 |detectLocation=true
 |watchLocation=false
 |localCache=300
 |controls=slider
 |class=extra-nearby-location
}}
```

Detailed information about the `#nearby` parser function and how to make use of templates
can be found [here](docs/README.md).

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
[maps]: https://github.com/JeroenDeDauw/Maps
[travis]: https://travis-ci.org/SemanticMediaWiki/WhatsNearby
[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[composer]: https://getcomposer.org/
[geoloc]: https://dev.w3.org/geo/api-/spec-source.html
