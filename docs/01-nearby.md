
In general, `Whats Nearby` only provides additional information to templates which
are then parsed [asynchronously][async] to a page view. It is therefore expected
that templates contain the actual `#ask` query to display (or generate) possible
nearby information.

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
}}
```

## Parameters

- querytemplate: specifies the template that contains the actual `#ask` query and any
  other condition one wishes to display (to allow for a selection of different templates
  separate them with a comma as in Sightseeing spots, Libraries)
- detectlocation: whether HTML5 [geolocation][geoloc] should be used or not (opt-out)
- watchlocation: monitor the location or location changes (opt-in)
- coordinates: can be set as starting parameters in case geolocation doesn't work or is disabled
- class: a simple css class to manipulate the output display
- controls: slider or button
- radius: the expected starting radius (e.g. "200m", "4 km")
- interval: to describe an interval a search should be continued
- max: defines the maximum limit or radius to be permitted for selection
- localcache: defines the time in seconds (with the default of 300 or no to disable
  it) with which results from the back-end are stored using the local browser

Parameters not listed will be made available to a `template` as-is.

## Query templates

If more than one template is listed (e.g. `querytemplate=Foo,Bar`) then the first
template will be used for the initial display while all others can be selected from
(as a drop-down list) during a page view.

## Geolocation

The parameters `detectlocation` and `watchlocation` can be used to enable HTML5 [geolocation][geoloc] and
in case the location detection is disrupted or not supported by the browser a message such as "Geolocation
detection is not supported by this browser." will be displayed. If fallback coordinates have been added
(with a parameter `coordinates`) then another message will inform the user about "Using the default
coordinates as fallback.".

## Nolocation

The `nolocation` parameter allows disable the `geolocation` feature completely for
a selected query and can be used to create a dynamic results for those
formats that do not require additional JavaScript modules to be loaded (`table`,
`embedded`), `#nearby`.

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

## Placeholder

If a user wants to compose __conditions__ that rely on ad-hoc replacement without
having to hard code them within a template (as for the above example), the
following placeholders ( `@@radius`, `@@unit`, `@@latitude`, and `@@longitude`)
are provided to adjust those values dynamically for a new query request.

```
{{#nearby: [[Has coordinates::50° 50' 48" N, 4° 21' 10" E (@@radius @@unit)]] [[Category:City]]
 |?Has coordinates
 |format=openlayers
 |querytemplate=semantic.query.tmpl
 ...
}}
```

Things like `{{{radius}}}` or `{{{unit}}}` can only be used within a template
which is why we need a different placeholder mechanism in order for `Whats Nearby`
to replace the values before the MW parser reaches a string component.
```

[async]: https://en.wikipedia.org/wiki/Asynchrony_(computer_programming)
[geoloc]: https://dev.w3.org/geo/api-/spec-source.html
