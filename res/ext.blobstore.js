/**
 * Simple storage engine
 */

/*global jQuery */
/*jslint white: true */

( function( $ ) {

	'use strict';

	/**
	 * localStorage wrapper with time-based eviction using
	 * ttl comparison
	 *
	 * @since  1.0
	 * @constructor
	 */
	var LS = function ( namespace ) {
		this.namespace = namespace;
		this.hasLocalStorage = typeof( localStorage ) !== "undefined";
	};

	/**
	 * @since  1.0
	 *
	 * @param key {string}
	 * @param value {string}
	 * @param ttl {integer}
	 */
	LS.prototype.set = function( key, value, ttl ) {

		if( !this.hasLocalStorage ) {
			return false;
		}

		var items = JSON.parse( localStorage.getItem( this.namespace ) || "0" ),
			now = new Date();

		if ( !items ) {
			items = {};
		}

		items[key] = {
			ttl   : ( ttl * 1000 ) || 0, // in seconds
			time  : now.getTime(),
			value : value
		};

		localStorage.setItem( this.namespace, JSON.stringify( items ) );
	};

	/**
	 * @since  1.0
	 *
	 * @param key {string}
	 *
	 * @return null|mixed
	 */
	LS.prototype.get = function( key ) {

		if( !this.hasLocalStorage ) {
			return null;
		}

		var items = JSON.parse( localStorage.getItem( this.namespace ) || "0" ),
			now = new Date();

		if ( !items || !items.hasOwnProperty( key ) ) {
			return null;
		}

		if ( items[key].ttl && items[key].ttl + items[key].time < now.getTime() ) {
			delete items[key];
			localStorage.setItem( this.namespace, JSON.stringify( items ) );
			return null;
		}

		return items[key].value;
	};

	/**
	 * @since  1.0
	 * @constructor
	 *
	 * @param namespace {string}
	 * @param engine {string}
	 *
	 * @return {this}
	 */
	var blobstore = function ( namespace, engine ) {

		this.VERSION = 1;

		this.namespace = namespace;
		this.engine = engine;

		if ( this.engine === '' || this.engine === undefined ) {
			this.engine = new LS( namespace );
		}

		// indexedDB ??

		return this;
	};

	/**
	 * @since  1.0
	 *
	 * @param key {string}
	 * @param value {string}
	 * @param ttl {integer}
	 */
	blobstore.prototype.set = function( key, value, ttl ) {
		this.engine.set( key, value, ttl );
	};

	/**
	 * @since  1.0
	 *
	 * @param key {string}
	 *
	 * @return null|mixed
	 */
	blobstore.prototype.get = function( key ) {
		return this.engine.get( key );
	};

	window.blobstore = blobstore;

}( jQuery ) );
