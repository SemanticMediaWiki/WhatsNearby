<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

print sprintf( "\n%-20s%s\n", "Whats Nearby: ", WNBY_VERSION );

if ( is_readable( $path = __DIR__ . '/../vendor/autoload.php' ) ) {
	print sprintf( "%-20s%s\n", "MediaWiki:", $GLOBALS['wgVersion'] . " (Extension vendor autoloader)" );
} elseif ( is_readable( $path = __DIR__ . '/../../../vendor/autoload.php' ) ) {
	print sprintf( "%-20s%s\n", "MediaWiki:", $GLOBALS['wgVersion'] . " (MediaWiki vendor autoloader)" );
} else {
	die( 'To run tests it is required that packages are installed using Composer.' );
}

$dateTimeUtc = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
print sprintf( "\n%-20s%s\n\n", "Execution time:", $dateTimeUtc->format( 'Y-m-d h:i' ) );


$autoloader = require $path;
$autoloader->addPsr4( 'WNBY\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'WNBY\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );
