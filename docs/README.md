- [#nearby](01-nearby.md) contains a description about the parser function

## Geolocation

`Whats Nearby` tries to determine the geolocation by first using the [HTML5 detection][geoloc]
mechanism and if this fails and `$GLOBALS['wnbyExternalGeoipService']` is enabled
then `meta.wikimedia.org/geoiplookup` is being pinged to resolve coordinates on behalf
of the browser IP address.

If above methods did not return any meaningful response and where the `coordinates`
parameter has been given information then those coordinates are used as
default fallback for computing distance queries.

## Examples

- [sandbox](http://sandbox.semantic-mediawiki.org/wiki/Category:Whats_Nearby_example) provides several live examples
- [example.semantic.distance.maps.tmpl](02-example.semantic.distance.maps.tmpl.md) contains an
  example template using [`#ask` queries][smw] to find local libraries based on the
  position determined by `#nearby`.
- [example.semantic.query.tmpl.md](02-example.semantic.query.tmpl.md) a simple `#ask` replacement
  query to create dynamic results

[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[geoloc]: https://dev.w3.org/geo/api-/spec-source.html
