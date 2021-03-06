<?php

namespace MediaWiki\Extensions\XAnalytics;

use ApiBase;
use Hooks;
use OutputPage;
use Skin;
use WebResponse;

class XAnalytics {

	/**
	 * Whether the header has already been added
	 *
	 * @var bool
	 */
	private static $addedHeader = false;

	/**
	 * Set X-Analytics header before the output buffer is flushed.
	 *
	 * The PHP output buffer is flushed from multiple places in the MediaWiki
	 * codebase (and the codebase of MediaWiki extensions), making it difficult to
	 * ensure that the header is reliably injected into every response generated by
	 * MediaWiki. This should be fixed. The output buffer of normal page view
	 * responses is done in one place, however, so for that use-case, the code is
	 * reliable.
	 *
	 * X-Analytics items can be declared by hooking into 'XAnalyticsSetHeader' or
	 * by calling XAnalytics;:addItem().
	 *
	 * @see https://wikitech.wikimedia.org/wiki/X-Analytics
	 * @param OutputPage $out
	 * @param Skin $skin
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		self::generateHeader( $out );
	}

	/**
	 * Runs the XAnalyticsSetHeader hook and adds the header if necessary
	 * @param OutputPage $out
	 */
	private static function generateHeader( OutputPage $out ) {
		if ( self::$addedHeader === true ) {
			// Only run once for API requests that use OutputPage
			return;
		}
		self::$addedHeader = true;
		$response = $out->getRequest()->response();
		$headerItems = [];
		Hooks::run( 'XAnalyticsSetHeader', [ $out, &$headerItems ] );
		// @phan-suppress-next-line PhanImpossibleCondition May set by hook
		if ( count( $headerItems ) ) {
			self::createHeader( $response, $headerItems );
		}
	}

	/**
	 * Checks to see if the X-Analytics header is already set, and add
	 * the new items to the header and set it
	 *
	 * @param WebResponse $response
	 * @param array $newItems
	 */
	private static function createHeader( WebResponse $response, array $newItems ) {
		$currentHeader = $response->getHeader( 'X-Analytics' );
		parse_str( preg_replace( '/; */', '&', $currentHeader ), $headerItems );
		$headerItems = array_merge( $headerItems, $newItems );

		$headerValue = http_build_query( $headerItems, '', ';' );
		$response->header( 'X-Analytics: ' . $headerValue, true );
	}

	/**
	 * @param ApiBase $module
	 */
	public static function onAPIAfterExecute( ApiBase $module ) {
		self::generateHeader( $module->getOutput() );
	}

	/**
	 * Add an item to the X-Analytics header that will be output
	 * @param string $name
	 * @param string $value
	 */
	public static function addItem( $name, $value ) {
		if ( self::$addedHeader ) {
			// If the header is already set, we need to append to it and replace it
			global $wgRequest;
			self::createHeader( $wgRequest->response(), [ $name => $value ] );
		}
	}
}
