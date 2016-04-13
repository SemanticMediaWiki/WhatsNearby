/**
 * JS part of the whats-nearby extension to query the current location (using
 * HTML5) and re-render template content with the information retrieved from the
 * geolocation to adjust the queries (contained in the selected template)
 * dynamically.
 */

/*global jQuery, mediaWiki, maps, md5 */
/*jslint white: true */

( function( $, mw, maps, onoi ) {

	'use strict';

	/**
	 * @since  1.0
	 * @constructor
	 *
	 * @param container {Object}
	 * @param api {Object}
	 * @param maps {Object}
	 * @param blobstore {Object}
	 *
	 * @return {this}
	 */
	var nearBy = function ( container, api, maps, blobstore ) {

		this.VERSION = "1.0.0";

		this.container = container;
		this.mwApi = api;
		this.maps = maps;
		this.blobstore = blobstore;

		this.parameters = container.data( 'parameters' );
		this.interval = 1;

		if (  this.parameters.hasOwnProperty( 'interval' ) ) {
			this.interval = parseInt( this.parameters.interval );
		};

		this.max = 1000;

		if ( this.parameters.hasOwnProperty( 'max' ) ) {
			this.max = parseInt( this.parameters.max );
		};

		this.ttl =  300;

		if ( this.parameters.hasOwnProperty( 'localcache' ) ) {
			this.ttl = parseInt( this.parameters.localcache );
		};

		this.unit = '';
		this.limit = 0;

		// Split something like "200 items"
		if ( this.parameters.hasOwnProperty( 'limit' ) ) {
			this.limit = parseInt( this.parameters.limit.replace( /[^\d.]/g, '' ) );
			this.unit = $.trim( this.parameters.limit.replace( /[^\D.]/g, '' ) );
		}

		// Split something like "300 km" or "2m"
		if ( this.parameters.hasOwnProperty( 'radius' ) ) {
			this.limit = parseInt( this.parameters.radius.replace( /[^\d.]/g, '' ) );
			this.unit = $.trim( this.parameters.radius.replace( /[^\D.]/g, '' ) );
		}

		if ( this.parameters.hasOwnProperty( 'querytemplate' ) && this.parameters.querytemplate.indexOf( "," ) > 0 ) {
			this.templates = this.parameters.querytemplate.split( ',' );
		}

		if ( this.templates !== undefined ) {
			this.template = this.templates[0];
		} else {
			this.template = this.parameters.querytemplate;
		}

		this.latitude = '';
		this.longitude = '';

		// Geoip
		this.canUseGeoipAsFallback = mw.config.get( 'whats-nearby' ).wnbyExternalGeoipService;

		if ( this.canUseGeoipAsFallback &&
			this.parameters.hasOwnProperty( 'detectlocation' ) &&
			this.parameters.detectlocation === 'false' ) {
			this.canUseGeoipAsFallback = false;
		}

		this.hasDetectedGeolocation = false;
		this.container.find( '#output' ).empty();
	};

	/* Public methods */

	/**
	 * Control any adjustments made to nearBy/template selection
	 *
	 * @since  1.0
	 */
	nearBy.prototype.searchNearby = function() {

		var self = this;

		function button ( value ) {
			return "<form id='limit-form' method='POST' action='#'>" +
				"<input type='button' value='-' class='limit-minus' field='limit' />" +
				"<input type='text' name='limit' value='" + value + "' class='limit-input' readonly />" +
				"<input type='button' value='+' class='limit-plus' field='limit' /></form>";
		}

		var controls = self.container.find( '#controls' );

		self.container.find( '#selection' ).show();

		if ( self.parameters.controls === 'slider' ) {
			controls.ionRangeSlider( {
				min: self.limit,
				max: self.max,
				step: self.interval,
				from: self.limit,
				postfix: ' ' + self.unit,
				onFinish: function ( data ) {
					self.limit = data.from;
					self.doOutput();
				}
			} );
		}

		if ( self.parameters.controls === 'button' ) {
			controls.append( button( self.limit ) );
			controls.show();

			controls.find( '.limit-plus, .limit-minus' ).click( function( e ) {
				e.preventDefault();

				var fieldName = $( this ).attr( 'field' );

				var currentVal = parseInt(
					controls.find( 'input[name=' + fieldName + ']' ).val()
				);

				if ( !isNaN( currentVal ) ) {
					var limit = $( this ).attr( 'class' ).indexOf( 'limit-plus' ) > -1 ? currentVal + self.interval : currentVal - self.interval;
					self.limit = limit < 0 ? 0 : limit;
					controls.find( 'input[name=' + fieldName + ']' ).val( self.limit );
					self.doOutput();
				} else {
					controls.find( 'input[name=' + fieldName + ']' ).val(0);
				}
			} );
		}

		// Generate a dropdown for when more than one template was added to the
		// list
		if ( self.templates !== undefined ) {
			var selection = self.container.find( '#selection' );

			selection.append( '<select id="templates"></select>' );

			$.each( self.templates , function( key, value ) {
				selection.find( "#templates" ).append( $( '<option></option>' ).val( value ).html( value ) );
			} );

			selection.change( function() {
				selection.find( "select option:selected" ).each( function() {
					self.template = $( this ).text();
					self.doOutput();
				} );
			} );
		}
	};

	/**
	 * Build the template code
	 *
	 * @since  1.0
	 */
	nearBy.prototype.buildTemplateCode = function() {

		var self = this,
			parameters = '';

		$.each( self.parameters , function( key, value ) {
			parameters = parameters + '|' + key + '=' + value;
		} );

		// Replace internal @@placeholder (cannot use {{{radius}}} as those are
		// replaced by the MW parser)
		parameters = parameters.replace( '@@radius', self.limit );
		parameters = parameters.replace( '@@unit', self.unit );
		parameters = parameters.replace( '@@latitude', self.latitude );
		parameters = parameters.replace( '@@longitude', self.longitude );

		return '{{' + self.template + parameters +
			'|latitude=' + self.latitude +
			'|longitude=' + self.longitude +
			'|radius=' + self.limit  +
			'|limit=' + self.limit  +
			'|unit=' + self.unit +
			'|maps=' + ( self.parameters.hasOwnProperty( 'maps' ) ? self.parameters.maps : '' ) +
			'|hasDetectedGeolocation=' + self.hasDetectedGeolocation +
		'}}';
	}

	/**
	 * Reload (re-render) maps or other objects that rely on JavaScript to be
	 * executed after a fresh parse.
	 *
	 * @since  1.0
	 */
	nearBy.prototype.reload = function() {

		var self = this;

		// Maps 3.5+
		self.maps.render(
			self.parameters.maps
		);

		// MW's table sorter
		if ( self.container.find( '#output table' ).text() !== '' ) {
			mw.loader.using( 'jquery.tablesorter' ).done( function () {
				self.container.find( '#output table' ).tablesorter();
			} );
		}

		// SMW tooltip/qTip (SMW 2.4+)
		if ( self.container.find( '#output .smw-highlighter' ).text() !== '' ) {
			mw.loader.using( 'ext.smw.tooltips' ).done( function () {
				var tooltip = new smw.util.tooltip();

				tooltip.render(
					self.container.find( '#output .smw-highlighter' )
				);
			} );
		}
	};

	/**
	 * Get content either from the API or from cache
	 *
	 * @since  1.0
	 */
	nearBy.prototype.doOutput = function() {

		var self = this,
			parameters = '',
			template = '',
			hash = '';

		template = self.buildTemplateCode();
		hash = md5( template + self.VERSION );

		self.container.find( '#output' ).block( {
			message: null,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.6,
				cursor: 'wait'
			}
		} );

		self.status( 'error', null );

		// Async process
		self.blobstore.get( hash, function( value ) {

			// Do a reparse
			if ( self.ttl == 0 || value === null ) {
				self.status( 'localcache', null );
				self.parse( hash, template );
			} else {
				self.container
					.find( '#output' )
					.replaceWith( "<div id='output'>" + value + "</div>" );

				self.container.find( '#output' ).unblock();

				var space = '';

				if ( self.container.find( '#status .geolocation' ).text() !== '' ) {
					space = '&#160;';
				}

				self.status( 'localcache', space + mw.msg( 'wnby-localcache-use' ) );
				self.reload();
			}
		} );

		return self;
	};

	/**
	 * Parse the template content via the API and re-render the maps
	 *
	 * @since 1.0
	 *
	 * @param hash
	 * @param template
	 */
	nearBy.prototype.parse = function( hash, template ) {

		var self = this;

		self.mwApi.get( {
			action: "parse",
			title: mw.config.get( 'wgPageName' ),
			section: 0, // Existing content will be replaced
			// summary: section, // no need for a section heading
			text: template
		} ).done( function( data ) {

			// Remove any comments retrieved from the API parse process
			var text = data.parse.text['*'].replace(/<!--[\S\s]*?-->/gm, '' );

			if ( self.ttl > 0 ) {
				self.blobstore.set( hash, text, self.ttl );
			}

			self.container
				.find( '#output' )
				.replaceWith( "<div id='output'>" + text + "</div>" );

			self.reload();

			self.status( 'localcache', null );
			self.container.find( '#output' ).unblock();
		} ).fail ( function( code, details ) {
			self.status( 'error', code + ': ' + details.textStatus );
			self.container.find( '#output' ).unblock();
		} );
	};

	/**
	 * @note Trying to access the navigator to identify the current location of
	 * a user
	 *
	 * @since  1.0
	 *
	 * @return {this}
	 */
	nearBy.prototype.detectGeolocation = function() {

		var options = {
			enableHighAccuracy: true,
			maximumAge        : 30000,
			timeout           : 27000
		};

		var self = this;

		// https://developer.mozilla.org/en-US/docs/Web/API/PositionError
		function error( message ) {

			var msgKey = '',
				canOutput = false;

			self.hasDetectedGeolocation = false;

			switch ( message.code ) {
				case message.PERMISSION_DENIED:
					message = mw.msg( 'wnby-geolocation-permission-denied' );
					break;
				case message.POSITION_UNAVAILABLE:
					message = mw.msg( 'wnby-geolocation-position-unavailable' );
					break;
				case message.TIMEOUT:
					message = mw.msg( 'wnby-geolocation-timeout-error' );
					break;
				case message.UNKNOWN_ERROR:
					message = mw.msg( 'wnby-geolocation-unknown-error' );
					break;
				case 'unsupported':
					message = mw.msg( 'wnby-geolocation-unsupported' );
					break;
				case 'disabled':
					message = mw.msg( 'wnby-geolocation-disabled' );
					break;
			}

			// Try the geoip service as fallback but avoid things like 0.000000, 0.000000
			if (
				self.canUseGeoipAsFallback &&
				window.hasOwnProperty( 'Geo' ) &&
				Geo.lat > 0 ) {
				self.latitude = Geo.lat;
				self.longitude = Geo.lon;
				self.hasDetectedGeolocation = true;
				message = mw.message( 'wnby-geolocation-geoip-fallback', self.latitude, self.longitude ).parse();
				canOutput = true;
			} else if (
				self.canUseGeoipAsFallback &&
				window.hasOwnProperty( 'Geo' ) ) {
				message = mw.message( 'wnby-geolocation-geoip-no-fallback', Geo.lat, Geo.lon ).parse();
			}

			// Try possible explicit fallback coordinates
			if (
				self.hasDetectedGeolocation === false &&
				self.parameters.hasOwnProperty( 'coordinates' ) &&
				self.parameters.coordinates.indexOf( "," ) > 0 ) {
				self.latitude = self.parameters.coordinates.split( "," )[0];
				self.longitude = self.parameters.coordinates.split( "," )[1];
				msgKey = 'wnby-default-fallback-location';
				canOutput = true;
			} else if (
				self.hasDetectedGeolocation === false &&
				self.parameters.hasOwnProperty( 'coordinates' ) ) {
				msgKey = 'wnby-invalid-coordinates-format';
				self.container.find( '#output' ).empty();
			} else if (
				self.hasDetectedGeolocation === false ) {
				msgKey = 'wnby-no-fallback-location';
				self.container.find( '#output' ).empty();
			}

			if ( canOutput ) {
				self.doOutput();
			};

			self.status(
				'geolocation',
				message + ( message !== '' ? ' ' : '' ) + ( msgKey !== '' ?  mw.msg( msgKey ) : '' )
			);
		}

		function success( position ) {
			self.latitude = position.coords.latitude;
			self.longitude = position.coords.longitude;
			self.hasDetectedGeolocation = true;
			self.doOutput();
		}

		// https://developer.mozilla.org/en-US/docs/Web/API/Geolocation/Using_geolocation#Getting_the_current_position
		// Initiates an asynchronous request to detect the user's position
		if (
			self.parameters.hasOwnProperty( 'detectlocation' ) &&
			self.parameters.detectlocation === 'false' ) {
			error( { code: 'disabled' } );
		} else if ( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition( success, error, options );
		} else {
			error( { code: 'unsupported' } );
		}

		// https://developer.mozilla.org/en-US/docs/Web/API/Geolocation/Using_geolocation#Watching_the_current_position
		if (
			self.parameters.hasOwnProperty( 'watchlocation' ) &&
			self.parameters.watchlocation === 'true' &&
			navigator.geolocation ) {
			navigator.geolocation.watchPosition( success, error, options );
		}

		return self;
	};

	/**
	 * @since 1.0
	 *
	 * @param id,
	 * @param message
	 *
	 * @return {this}
	 */
	nearBy.prototype.status = function( id, message ) {

		var self = this,
			instance = self.container.find( '#status .' + id );

		if ( message === null ) {
			instance.empty();
		} else {
			instance.replaceWith(
				"<span class='" + id + "'>" + message + "</span>"
			);
		}

		return self;
	}

	$( function( $ ) {

		/**
		 * @since 1.0
		 */
		$( '.whats-nearby' ).each( function() {

			var whatsNearby = new nearBy(
				$( this ),
				new mw.Api(),
				new maps.services( $( this ) ),
				new onoi.blobstore(
					'whats-nearby' +  ':' +
					mw.config.get( 'whats-nearby' ).wgLanguageCode + ':' +
					mw.config.get( 'whats-nearby' ).wgCachePrefix
				)
			);

			// No template -> no parse
			if ( whatsNearby.template === '' || whatsNearby.template === undefined ) {
				whatsNearby.status( 'error', mw.msg( 'wnby-template-parameter-missing' ) );
			} else if ( whatsNearby.parameters.hasOwnProperty( 'nolocation' ) ) {
				whatsNearby.doOutput().searchNearby();
			} else {
				whatsNearby.detectGeolocation().searchNearby();
			}
		} );

	} );

}( jQuery, mediaWiki, maps, onoi ) );
