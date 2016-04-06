<?php

use WNBY\HookRegistry;

/**
 * @see https://github.com/SemanticMediaWiki/WhatsNearby/
 * @link https://www.semantic-mediawiki.org/wiki/Extension:WhatsNearby
 *
 * @defgroup wnby WhatsNearby
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of the WhatsNearby extension, it is not a valid entry point.' );
}

if ( version_compare( $GLOBALS[ 'wgVersion' ], '1.23', 'lt' ) ) {
	die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/WhatsNearby/">WhatsNearby</a> is only compatible with MediaWiki 1.23 or above. You need to upgrade MediaWiki first.' );
}

// Do not initialize more than once.
if ( defined( 'WNBY_VERSION' ) ) {
	return 1;
}

define( 'WNBY_VERSION', '1.0.0-alpha' );

/**
 * @codeCoverageIgnore
 */
call_user_func( function() {

	$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
		'path' => __FILE__,
		'name' => 'Whats Nearby',
		'author' =>array( 'mwjames' ),
		'url' => 'https://www.semantic-mediawiki.org/wiki/Extension:WhatsNearby',
		'description' => 'wnby-desc',
		'version'  => WNBY_VERSION,
		'license-name'   => 'GPL-2.0+',
	);

	// Register message files
	$GLOBALS['wgMessagesDirs']['whats-nearby'] = __DIR__ . '/i18n';
	$GLOBALS['wgExtensionMessagesFiles']['whats-nearby-magic'] = __DIR__ . '/i18n/WhatsNearby.magic.php';

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

	$GLOBALS['wgResourceModules']['ext.whats.nearby.geoip'] = array(
		'localBasePath' => __DIR__ ,
		'remoteExtPath' => 'WhatsNearby',
		'position' => 'bottom',
		'scripts' => array(
			'res/ext.whats.nearby.geoip.js'
		),
		'targets' => array(
			'mobile',
			'desktop'
		)
	);

	$GLOBALS['wgResourceModules']['ext.whats.nearby'] = array(
		'localBasePath' => __DIR__ ,
		'remoteExtPath' => 'WhatsNearby',
		'position' => 'bottom',
		'styles' => array( 'res/ext.whats.nearby.css' ),
		'scripts' => array( 'res/ext.whats.nearby.js' ),
		'dependencies'  => array(
			'mediawiki.api',
			'mediawiki.api.parse',
			'ext.maps.services',
			'onoi.rangeslider',
			'onoi.blockUI',
			'onoi.md5',
			'onoi.blobstore'
		),
		'messages' => array(
			'wnby-geolocation-disabled',
			'wnby-geolocation-unsupported',
			'wnby-geolocation-unknown-error',
			'wnby-geolocation-timeout-error',
			'wnby-geolocation-position-unavailable',
			'wnby-geolocation-permission-denied',
			'wnby-no-fallback-location',
			'wnby-geolocation-geoip-fallback',
			'wnby-default-fallback-location',
			'wnby-invalid-coordinates-format',
			'wnby-localcache-use',
			'wnby-template-parameter-missing',
			'wnby-loading'
		),
		'targets' => array(
			'mobile',
			'desktop'
		)
	);

	$GLOBALS['wgExtensionFunctions'][] = function() {
		$hookRegistry = new HookRegistry();
		$hookRegistry->register();
	};

} );
