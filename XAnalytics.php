<?php
/**
 * Emit structured analytics data via an X-Analytics HTTP header.
 *
 * @see https://wikitech.wikimedia.org/wiki/X-Analytics
 * @author Ori Livneh <ori@wikimedia.org>
 * @license GPLv2
 * @version 0.1
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'XAnalytics',
	'version' => '0.1',
	'url' => 'https://wikitech.wikimedia.org/wiki/X-Analytics',
	'author' => 'Ori Livneh',
	'descriptionmsg' => 'xanalytics-desc',
	'license-name' => 'GPL-2.0+'
);

// Messages

$wgMessagesDirs['XAnalytics'] = __DIR__ . '/i18n';

// Autoload

$wgAutoloadClasses['XAnalytics'] = __DIR__ . '/XAnalytics.class.php';

// Hooks

$wgHooks['BeforePageDisplay'][] = 'XAnalytics::onBeforePageDisplay';
$wgHooks['APIAfterExecute'][] = 'XAnalytics::onAPIAfterExecute';
