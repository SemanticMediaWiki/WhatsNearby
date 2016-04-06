<?php

namespace WNBY;

use Parser;
use MapsMappingServices;
use Html;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class NearbyParserFunction {

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @since  1.0
	 *
	 * @return Parser $parser
	 */
	public function __construct( Parser $parser ) {
		$this->parser = $parser;
	}

	/**
	 * @since  1.0
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function parse( array $params ) {

		$parameters = array();
		$class = 'whats-nearby';

		if( isset( $params[0] ) && $params[0] instanceof \Parser ) {
			array_shift( $params );
		}

		foreach ( $params as $key => $value ) {
			$this->doValidateKeyValueForParameterMatch( $key, $value, $class, $parameters );
		}

		$this->addMapServicesToOutput( $parameters );
		$geoip = true;

		if ( isset( $parameters['nolocation'] ) ||
			( isset( $parameters['detectlocation'] ) && !$parameters['detectlocation'] ) ) {
			$geoip = false;
		}

		// Is to signal the OutputPageParserOutput hook
		$this->parser->getOutput()->setExtensionData(
			'wnby-geoip',
			$geoip
		);

		$this->parser->getOutput()->addModules(
			'ext.whats.nearby'
		);

		return $this->getHtmlFor( $class, $parameters );
	}

	private function getHtmlFor( $class, $parameters ) {
		return Html::rawElement(
			'div',
			array(
				'class' => $class . ( isset( $parameters['class'] ) ? ' ' . $parameters['class'] : '' ),
				'data-parameters' => json_encode( $parameters )
			),
			Html::rawElement( 'div', array( 'id' => 'controls',  'style' => 'display:none' ) ) .
			Html::rawElement( 'div', array( 'id' => 'selection', 'style' => 'display:none' ) ) .
			Html::rawElement( 'div', array( 'id' => 'status' ),
				Html::rawElement( 'span', array( 'class' => 'geolocation' ) ) .
				Html::rawElement( 'span', array( 'class' => 'localcache' ) ) .
				Html::rawElement( 'span', array( 'class' => 'error' ) )
			) .
			Html::rawElement( 'div', array( 'id' => 'output', 'style' => 'opacity: 0.5;' ), wfMessage( 'wnby-loading' )->text() )
		);
	}

	private function doValidateKeyValueForParameterMatch( $key, $value, &$class, &$parameters ) {

		if ( $key == 0 && strpos( $value, '=' ) === false ) {
			$parameters['condition'] = $value;
		}

		// Build printrequest identifier for a template
		// extending argument
		if ( $value !== '' && $value{0} === '?' ) {
			$parameters['pr-' . $key] = $value;
			return;
		}

		if ( strpos( $value, '=' ) === false ) {
			return;
		}

		list( $k, $v ) = explode( '=', $value );
		$parameters[strtolower( $k )] = $v;
	}

	private function addMapServicesToOutput( &$parameters ) {

		if ( isset( $parameters['format'] ) &&
			in_array( $parameters['format'], array( 'openlayers', 'leaflet', 'googlemaps', 'googlemaps3', 'maps', 'google' ) ) ) {
			$parameters['maps'] = $parameters['format'];
		}

		if ( !isset( $parameters['maps'] ) || !class_exists( 'MapsMappingServices' ) ) {
			return;
		}

		if ( $parameters['maps'] === 'openlayers' ) {
			$mapsOpenLayers = MapsMappingServices::getServiceInstance( 'openlayers' );
			$mapsOpenLayers->addDependencies( $this->parser );
			$this->parser->getOutput()->addJsConfigVars( $mapsOpenLayers->getConfigVariables() );
		}

		if (
			$parameters['maps'] === 'googlemaps' ||
			$parameters['maps'] === 'googlemaps3' ||
			$parameters['maps'] === 'maps' ||
			$parameters['maps'] === 'google' ) {
			$mapsGoogleMaps = MapsMappingServices::getServiceInstance( 'googlemaps3' );
			$mapsGoogleMaps->addDependencies( $this->parser );
		}

		if ( $parameters['maps'] === 'leaflet' || $parameters['maps'] === 'leafletmaps' ) {
			$mapsLeaflet = MapsMappingServices::getServiceInstance( 'leaflet' );
			$mapsLeaflet->addDependencies( $this->parser );
		}
	}

}
