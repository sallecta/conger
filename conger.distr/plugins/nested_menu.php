<?php
/*
Plugin Name: Nested Menus
Description: Provides a set of template tags to return or print the menu as a set of nested arrays, i.e. child items under parent.
Version: 1.4
Author: Chris Bloom
Author URI: http://www.christopherbloom.com/
Additonal Credit: Based heavily on the child_menu plugin by Erik (http://www.fohlin.net/getsimple-child-menu-plugin)
*/

// get correct id for plugin
$nesmenfile = 'nested_menu'; // This gets the correct ID for the plugin.

// register plugin
register_plugin(
	$nesmenfile,	// ID of plugin, should be filename minus php
	'Nested Menu',	# Title of plugin
	'1.5',	// Version of plugin
	'Chris Bloom',	// Author of plugin
	'http://www.christopherbloom.com/',	// Author URL
	'Provides a set of template tags to return or print the menu as a set of nested arrays, i.e. child items under parent.',	// Plugin Description
	'template',	// Page type of plugin
	'nested_menu_deprecated'	// useless? Function that displays content
);
require_once('nested_menu/nested_menu.php');
nested_menu::main();

// functions


function nested_menu_deprecated()
{
	dev::ehtmlcom('nested_menu_deprecated');
}


?>
