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

WhatsNearby::initExtension();

$GLOBALS['wgExtensionFunctions'][] = function() {
	WhatsNearby::onExtensionFunction();
};

/**
 * @codeCoverageIgnore
 */
class WhatsNearby {

	/**
	 * @since 1.0
	 */
	public static function initExtension() {

		// Load DefaultSettings
		require_once __DIR__ . '/DefaultSettings.php';

		define( 'WNBY_VERSION', '1.0.0-alpha' );

		$GLOBALS['wgExtensionCredits']['others'][] = array(
			'path'           => __FILE__,
			'name'           => 'Whats Nearby',
			'author'         => array(
				'James Hong Kong'
			),
			'url'            => 'https://www.semantic-mediawiki.org/wiki/Extension:WhatsNearby',
			'descriptionmsg' => 'wnby-desc',
			'version'        => WNBY_VERSION,
			'license-name'   => 'GPL-2.0-or-later'
		);

		// Register message files
		$GLOBALS['wgMessagesDirs']['WhatsNearby'] = __DIR__ . '/i18n';
		$GLOBALS['wgExtensionMessagesFiles']['WhatsNearbyMagic'] = __DIR__ . '/i18n/WhatsNearby.magic.php';

		$GLOBALS['wgResourceModules']['ext.whats.nearby.geoip'] = array(
			'localBasePath' => __DIR__ ,
			'remoteExtPath' => 'WhatsNearby',
			'position' => 'bottom',
			'scripts'  => array(
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
			'styles'   => array(
				'res/ext.whats.nearby.css'
			),
			'scripts'  => array(
				'res/ext.whats.nearby.js'
			),
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
				'wnby-geolocation-geoip-no-fallback',
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
	}

	/**
	 * @since 1.0
	 */
	public static function onExtensionFunction() {
		$hookRegistry = new HookRegistry();
		$hookRegistry->register();
	}

	/**
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public static function getVersion() {
		return WNBY_VERSION;
	}

}
