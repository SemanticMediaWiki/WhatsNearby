<?php

namespace WNBY\Tests;

use WNBY\HookRegistry;
use Title;

/**
 * @covers \WNBY\HookRegistry
 * @group whats-nearby
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\WNBY\HookRegistry',
			new HookRegistry()
		);
	}

	public function testRegister() {

		$title = Title::newFromText( __METHOD__ );

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$instance = new HookRegistry();
		$instance->register();

		$this->doTestParserFirstCallInit( $instance, $parser );
		$this->doTestResourceLoaderGetConfigVars( $instance );
	}

	public function doTestParserFirstCallInit( $instance, $parser ) {

		$handler = 'ParserFirstCallInit';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$parser )
		);
	}

	public function doTestResourceLoaderGetConfigVars( $instance ) {

		$handler = 'ResourceLoaderGetConfigVars';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$vars = array();

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$vars )
		);
	}

	private function assertThatHookIsExcutable( \Closure $handler, $arguments ) {
		$this->assertInternalType(
			'boolean',
			call_user_func_array( $handler, $arguments )
		);
	}

}
