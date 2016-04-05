/**
 * Copied from the ULS GeoIP client
 */
( function ( $, mw ) {
	'use strict';

	mw.wnby = mw.wnby || {};
	mw.wnby.setGeo = function ( data ) {
		window.Geo = data;
	};

	var currentProto, httpOnly, settings,
		service = mw.config.get( 'whats-nearby' ).wnbyExternalGeoIpService;

	// Call the service only if defined, and if the current
	// protocol is https, only if the service is not configured
	// with http:// as the protocol
	if ( service ) {
		httpOnly = service.substring( 0, 7 ) === 'http://';
		currentProto = document.location.protocol;
		if ( !httpOnly || currentProto === 'http:' ) {
			settings = {
				cache: true,
				dataType: 'jsonp',
				jsonpCallback: 'mw.wnby.setGeo'
			};

			$.ajax( service, settings );
		}
	}

}( jQuery, mediaWiki ) );
