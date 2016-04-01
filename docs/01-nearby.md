
In general, the `Whats Nearby` extension only extends information available to a
template and parses those listed templates [asynchronously][async] to a page view
therefore it is expected that templates contain the actual `#ask` query to
display possible nearby information.

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
- detectlocation: whether HTML5 [geolocation](https://dev.w3.org/geo/api-/spec-source.html) should be used or not (opt-out)
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

## Placeholder

If a user wants to compose dynamic conditions without having to hard code
them within a template (as for the above example), the following placeholders
( `@@radius`, `@@unit`, `@@latitude`, and `@@longitude`) are provided to adjust
those values dynamically when a new result set is requested.

```
{{#nearby: [[Has coordinates::50° 50' 48" N, 4° 21' 10" E (@@radius @@unit)]] [[Category:City]]
 |?Has coordinates
 |format=openlayers
 |querytemplate=semantic.query.tmpl
 ...
}}
```

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
```

[async]: https://en.wikipedia.org/wiki/Asynchrony_(computer_programming)
