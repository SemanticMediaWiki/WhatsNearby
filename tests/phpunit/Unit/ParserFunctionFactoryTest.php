<?php

namespace WNBY\Tests;

use WNBY\ParserFunctionFactory;

/**
 * @covers \WNBY\ParserFunctionFactory
 * @group whats-nearby
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ParserFunctionFactoryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\WNBY\ParserFunctionFactory',
			new ParserFunctionFactory()
		);
	}

	public function testCanConstructNearByParserFunctionDefinition() {

		$instance = new ParserFunctionFactory();

		list( $name, $definition, $flag ) = $instance->newNearbyParserFunctionDefinition();

		$this->assertEquals(
			'nearby',
			$name
		);

		$this->assertInstanceOf(
			'\Closure',
			$definition
		);

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$this->assertNotEmpty(
			call_user_func_array( $definition, array( $parser ) )
		);
	}

}
