<?php

namespace MediaWiki\Extension\XAnalytics\Tests\Unit;

use MediaWiki\Api\ApiBase;
use MediaWiki\Context\RequestContext;
use MediaWiki\Extension\XAnalytics\XAnalytics;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Output\OutputPage;
use MediaWiki\Request\FauxRequest;
use MediaWiki\Rest\Handler;
use MediaWiki\Rest\Module\Module;
use MediaWiki\Rest\RequestData;
use MediaWiki\Rest\Response;
use MediaWiki\Skin\Skin;
use MediaWikiUnitTestCase;

/**
 * @covers \MediaWiki\Extension\XAnalytics\XAnalytics
 */
class XAnalyticsTest extends MediaWikiUnitTestCase {

	public function testOnBeforePageDisplay() {
		$xAnalytics = new XAnalytics( $this->getHookContainer() );
		$out = $this->getOutputPage();
		$skin = $this->createNoOpMock( Skin::class );

		$xAnalytics->onBeforePageDisplay( $out, $skin );

		$this->assertSame( 'foo=bar', $out->getRequest()->response()->getHeader( 'X-Analytics' ) );
	}

	public function testOnAPIAfterExecute() {
		$xAnalytics = new XAnalytics( $this->getHookContainer() );
		$module = $this->createNoOpMock( ApiBase::class, [ 'getOutput' ] );
		$module->method( 'getOutput' )->willReturn( $this->getOutputPage() );

		$xAnalytics->onAPIAfterExecute( $module );

		$this->assertSame( 'foo=bar', $module->getOutput()->getRequest()->response()->getHeader( 'X-Analytics' ) );
	}

	public function testOnRestAfterExecute() {
		$xAnalytics = new XAnalytics( $this->getHookContainer() );
		$module = $this->createNoOpMock( Module::class );
		$handler = $this->createNoOpMock( Handler::class );
		$path = '/foo';
		$request = new RequestData();
		$response = new Response();
		RequestContext::getMain()->setOutput( $this->getOutputPage() );

		$xAnalytics->onRestAfterExecute( $module, $handler, $path, $request, $response );

		$this->assertSame( 'foo=bar', $response->getHeaderLine( 'X-Analytics' ) );
	}

	private function getHookContainer(): HookContainer {
		return $this->createHookContainer( [
			'XAnalyticsSetHeader' => static function ( $out, &$headerItems ) {
				$headerItems['foo'] = 'bar';
			}
		] );
	}

	private function getOutputPage(): OutputPage {
		$out = $this->createNoOpMock( OutputPage::class, [ 'getRequest' ] );
		$out->method( 'getRequest' )->willReturn( new FauxRequest() );
		return $out;
	}

}
