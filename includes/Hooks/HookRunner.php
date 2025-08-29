<?php

namespace MediaWiki\Extension\XAnalytics\Hooks;

use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Output\OutputPage;

/**
 * This is a hook runner class, see docs/Hooks.md in core.
 * @internal
 */
class HookRunner implements
	XAnalyticsSetHeaderHook
{
	public function __construct( private readonly HookContainer $hookContainer ) {
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
