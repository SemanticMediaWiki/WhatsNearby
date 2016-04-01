<?php

namespace WNBY\Tests;

use WNBY\NearbyParserFunction;

/**
 * @covers \WNBY\NearbyParserFunction
 * @group whats-nearby
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class NearbyParserFunctionTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\WNBY\NearbyParserFunction',
			new NearbyParserFunction( $parser )
		);
	}

	public function testParseWithoutMaps() {

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$instance = new NearbyParserFunction(
			$parser
		);

		$this->assertContains(
			'<div class="whats-nearby" data-parameters="{&quot;foo&quot;:&quot;bar&quot;}">',
			$instance->parse( array( 'foo=bar', 'no-parameter' ) )
		);
	}

	public function testParseWithGoogleMapsParameters() {

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parserOutput->expects( $this->atLeastOnce() )
			->method( 'addHeadItem' );

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$instance = new NearbyParserFunction(
			$parser
		);

		$this->assertContains(
			'<div class="whats-nearby" data-parameters="{&quot;foo&quot;:&quot;bar&quot;,&quot;maps&quot;:&quot;googlemaps&quot;}">',
			$instance->parse( array( 'foo=bar', 'no-parameter', 'maps=googlemaps' ) )
		);
	}

	public function testParseWithOpenLayersParameters() {

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parserOutput->expects( $this->atLeastOnce() )
			->method( 'addJsConfigVars' );

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$instance = new NearbyParserFunction(
			$parser
		);

		$this->assertContains(
			'<div class="whats-nearby" data-parameters="{&quot;foo&quot;:&quot;bar&quot;,&quot;maps&quot;:&quot;openlayers&quot;}">',
			$instance->parse( array( 'foo=bar', 'no-parameter', 'maps=openlayers' ) )
		);
	}

	public function testParseWithLeafletParameters() {

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$instance = new NearbyParserFunction(
			$parser
		);

		$this->assertContains(
			'<div class="whats-nearby" data-parameters="{&quot;foo&quot;:&quot;bar&quot;,&quot;format&quot;:&quot;leaftlet&quot;,&quot;pr-3&quot;:&quot;?Has coordinates&quot;}">',
			$instance->parse( array( 'foo=bar', 'no-parameter', 'format=leaftlet', '?Has coordinates' ) )
		);
	}

}
