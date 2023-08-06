<?php

namespace MediaWiki\Extension\XAnalytics\Hooks;

use MediaWiki\HookContainer\HookContainer;
use OutputPage;

/**
 * This is a hook runner class, see docs/Hooks.md in core.
 * @internal
 */
class HookRunner implements
	XAnalyticsSetHeaderHook
{
	private HookContainer $hookContainer;

	public function __construct( HookContainer $hookContainer ) {
		$this->hookContainer = $hookContainer;
	}

	/**
	 * @inheritDoc
	 */
	public function onXAnalyticsSetHeader( OutputPage $out, array &$headerItems ) {
		return $this->hookContainer->run(
			'XAnalyticsSetHeader',
			[ $out, &$headerItems ]
		);
	}
}
