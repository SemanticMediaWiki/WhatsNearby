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

		$nearByParserFunctionDefinition = function( $parser ) {

			$nearByParserFunction = new NearByParserFunction(
				$parser
			);

			return $nearByParserFunction->parse( func_get_args() );
		};

		return array( 'nearby', $nearByParserFunctionDefinition, 0 );
	}

}
