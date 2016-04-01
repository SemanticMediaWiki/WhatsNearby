<?php

namespace WNBY;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ParserFunctionFactory {

	/**
	 * @since  1.0
	 *
	 * @return array
	 */
	public function newNearbyParserFunctionDefinition() {

		$nearbyParserFunctionDefinition = function( $parser ) {

			$nearbyParserFunction = new NearbyParserFunction(
				$parser
			);

			return $nearbyParserFunction->parse( func_get_args() );
		};

		return array( 'nearby', $nearbyParserFunctionDefinition, 0 );
	}

}
