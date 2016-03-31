<?php

namespace WNBY;

use Hooks;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistry {

	/**
	 * @var array
	 */
	private $handlers = array();

	/**
	 * @since 1.0
	 *
	 * @param array $configuration
	 */
	public function __construct( $configuration = array () ) {
		$this->registerCallbackHandlers( $configuration );
	}

	/**
	 * @since  1.0
	 */
	public function register() {
		foreach ( $this->handlers as $name => $callback ) {
			Hooks::register( $name, $callback );
		}
	}

	/**
	 * @since  1.0
	 */
	public function deregister() {
		foreach ( array_keys( $this->handlers ) as $name ) {
			Hooks::clear( $name );
		}
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return Callable|false
	 */
	public function getHandlerFor( $name ) {
		return isset( $this->handlers[$name] ) ? $this->handlers[$name] : false;
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function isRegistered( $name ) {
		return Hooks::isRegistered( $name );
	}

	private function registerCallbackHandlers( $configuration ) {

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
		 */
		$this->handlers['ParserFirstCallInit'] = function ( &$parser ) {

			$parserFunctionFactory = new ParserFunctionFactory();

			list( $name, $definition, $flag ) = $parserFunctionFactory->newNearbyParserFunctionDefinition();

			$parser->setFunctionHook( $name, $definition, $flag );

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ResourceLoaderGetConfigVars
		 */
		$this->handlers['ResourceLoaderGetConfigVars'] = function ( &$vars ) {

			$vars['whats-nearby'] = array(
				'wgCachePrefix'  => $GLOBALS['wgCachePrefix'] === false ? wfWikiID() : $GLOBALS['wgCachePrefix'],
				'wgLanguageCode' => $GLOBALS['wgLang']->getCode()
			);

			return true;
		};
	}

}
