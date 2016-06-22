<?php

/**
 * DO NOT EDIT!
 *
 * The following default settings are to be used by the extension itself,
 * please modify settings in the LocalSettings file.
 *
 * @codeCoverageIgnore
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of the WhatsNearby extension, it is not a valid entry point.' );
}

/**
 * Specifies whether an external service should be used
 * to help with resolving a Geolocation.
 *
 * If `wnbyExternalGeoipService` is set true then
 * https://meta.wikimedia.org/geoiplookup is being used.
 *
 * `wnbyExternalGeoipService` can also hold an https service
 * provider.
 */
$GLOBALS['wnbyExternalGeoipService'] = true;
