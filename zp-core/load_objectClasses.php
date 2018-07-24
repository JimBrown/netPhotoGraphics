<?php

/**
 *
 * Load the base classes (Image, Album, Gallery, etc.)
 *
 * @author Stephen Billard (sbillard)
 *
 * @package core
 */
$_zp_plugin_differed_actions = array(); //	final initialization for class plugins (mostly for language translation issues)

require_once(dirname(__FILE__) . '/classes.php');
require_once(dirname(__FILE__) . '/class-gallery.php');
require_once(dirname(__FILE__) . '/class-album.php');
require_once(dirname(__FILE__) . '/class-image.php');
require_once(dirname(__FILE__) . '/class-search.php');

$_zp_loaded_plugins = array();
// load the class & filter plugins
if (abs(OFFSET_PATH) != 2) { // setup does not need (and might have problems with) plugins
	$masks[] = CLASS_PLUGIN;
	if (OFFSET_PATH) {
		$masks[] = FEATURE_PLUGIN;
		$masks[] = ADMIN_PLUGIN;
	}

	foreach ($masks as $mask) {
		if (DEBUG_PLUGINS) {
			switch ($mask) {
				case CLASS_PLUGIN:
					debugLog('Loading the "class" plugins.');
					break;
				case FEATURE_PLUGIN:
					debugLog('Loading the "feature" plugins.');
					break;
				case ADMIN_PLUGIN:
					debugLog('Loading the "admin" plugins.');
					break;
			}
		}
		$enabled = getEnabledPlugins();
		foreach ($enabled as $extension => $plugin) {
			$priority = $plugin['priority'];
			if ($priority & $mask) {
				if (DEBUG_PLUGINS) {
					list($usec, $sec) = explode(" ", microtime());
					$start = (float) $usec + (float) $sec;
				}
				require_once($plugin['path']);
				$_zp_loaded_plugins[$extension] = $extension;
				if (DEBUG_PLUGINS) {
					zpFunctions::pluginDebug($extension, $priority, $start);
				}
			}
		}
		require_once(dirname(__FILE__) . '/auth_zp.php'); // loaded after CLASS_PLUGIN and before FEATURE_PLUGINS and ADMIN_PLUGIN
	}
} else {
	require_once(dirname(__FILE__) . '/auth_zp.php'); // setup needs this!
}
define('ZENPHOTO_LOCALE', setMainDomain());
foreach ($_zp_plugin_differed_actions as $callback) {
	call_user_func($callback);
}
?>