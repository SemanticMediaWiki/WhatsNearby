<noinclude>Template that shows libraries for a selected radius.</noinclude><includeonly>
Shows libraries from point {{#ask: [[Has coordinates::{{{latitude}}}, {{{longitude}}}]] |limit=1|link=none|default={{{latitude}}}, {{{longitude}}} |searchlabel= }} in a distance of {{{radius}}} {{{unit|}}}.

{{#ask: [[Category:Library]] [[Has coordinates::{{{latitude}}}, {{{longitude}}}  ({{{radius}}} {{{unit|}}})]]
 |?Has coordinates
 |?Has library
 |format = {{{maps|}}}
 |width=250
 |height=250
 |userparam={{{latitude}}},{{{longitude}}}
 |template=Distance Popup
 |default=No location found for the current distance.
}}

{{#ask: [[Category:Library]] [[Has coordinates::{{{latitude}}}, {{{longitude}}}  ({{{radius}}} {{{unit|km}}})]]
 |?Has library
 |format=ul
 |headers=hide
 |order=Has library
}}
</includeonly>
