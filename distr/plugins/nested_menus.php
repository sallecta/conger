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
$thisfile = basename(__FILE__, '.php'); // This gets the correct ID for the plugin.

// register plugin
register_plugin(
	$thisfile,	// ID of plugin, should be filename minus php
	'Nested Menus',	# Title of plugin
	'1.4',	// Version of plugin
	'Chris Bloom',	// Author of plugin
	'http://www.christopherbloom.com/',	// Author URL
	'Provides a set of template tags to return or print the menu as a set of nested arrays, i.e. child items under parent.',	// Plugin Description
	'template',	// Page type of plugin
	'get_nested_navigation'	// Function that displays content
);

// activate actions
add_action('changedata-save','nested_menus__clear_cache');
add_action('page-delete', 'nested_menus__clear_cache');
add_action('cache-delete', 'nested_menus__clear_cache');

// functions
function nested_menus__clear_cache() 
{
	$cachepath = GSDATAOTHERPATH.'nested_menu_cache/';
	if (is_dir($cachepath))
	{
		$dir_handle = @opendir($cachepath) or exit('Unable to open nested_menu_cache folder');
		$filenames = array();
		
		while ($filename = readdir($dir_handle))
		{
			$filenames[] = $filename;
		}
		
		if (count($filenames) != 0)
		{
			foreach ($filenames as $file) 
			{
				if (!($file == '.' || $file == '..' || is_dir($cachepath.$file) || $file == '.htaccess'))
				{
					unlink($cachepath.$file) or exit('Unable to clean up nested_menu_cache folder');
				}
			}
		}
	}
}

function nested_menu_data()
{
	$menu_data = menu_data();
	// echo "<pre>menu_data\n----------\n" . print_r($menu_data, 1) . "</pre>";
	usort($menu_data, 'nested_menu_data__sort_menu_parents_first');
	// echo "<pre>menu_data after sort\n----------\n" . print_r($menu_data, 1) . "</pre>";
	$nested_menu_data = array();
	foreach($menu_data as $item) 
	{
		if (empty($item['menu_status'])) { continue; }
		if (empty($item['menu_text'])) { $item['menu_text'] = $item['title']; }
		if (empty($item['title'])) { $item['title'] = $item['menu_text']; }
		if (empty($item['parent_slug']))
		{
			$nested_menu_data[$item['slug']] = $item;
			$nested_menu_data[$item['slug']]['children'] = array();
		}
		elseif(isset($nested_menu_data[$item['parent_slug']]))
		{
			$nested_menu_data[$item['parent_slug']]['children'][] = $item;
		}
	}
	// echo "<pre>nested_menu_data\n----------\n" . print_r($nested_menu_data, 1) . "</pre>";
	usort($nested_menu_data, 'nested_menu_data__sort_by_menu_priority');
	// echo "<pre>nested_menu_data after initial sort\n----------\n" . print_r($nested_menu_data, 1) . "</pre>";
	foreach($nested_menu_data as $key => $item)
	{
		$children = $nested_menu_data[$key]['children'];
		if (sizeof($children))
		{
			usort($children, 'nested_menu_data__sort_by_menu_priority');
			$nested_menu_data[$key]['children'] = $children;
		}
	}
	// echo "<pre>nested_menu_data after internal sort\n----------\n" . print_r($nested_menu_data, 1) . "</pre>";
	return $nested_menu_data;
}

function get_nested_navigation($echo = true) 
{
	$active_page=return_page_slug();
	$cachepath = GSDATAOTHERPATH.'nested_menu_cache/'.$active_page.'.cache';
	
	if (is_file($cachepath)) //We have a cached file, use it.
	{
		echo file_get_contents($cachepath);
	}
	else //We do not have a cached file, create a new one.
	{
		$nested_menu_data = nested_menu_data();
		$items = array();
		$num_items = sizeof($items);
		foreach($nested_menu_data as $key => $item)
		{
			$num_children = sizeof($item['children']);
			$classes = array($item['slug']);
			if ($key == 0) { $classes[] = 'first'; }
			elseif($key == ($num_items - 1)) { $classes[] = 'last'; }
			if ($num_children) { $classes[] = 'submenu'; }
			if ($item['slug'] == $active_page) { $classes[] = 'current'; }
			$string = '<li class="%s"><a href="'. $item['url'] . '" title="'. strip_quotes(stripslashes($item['title'])) .'">'.stripslashes($item['menu_text']).'</a>';
			if ($num_children)
			{
				$string .= "\n  <ul>";
				foreach ($item['children'] as $sub_key => $sub_item)
				{
					$string .= "\n    <li class=\"" . $sub_item['slug'];
					if ($sub_key == 0) { $string .= ' first'; }
					elseif($sub_key == ($num_children - 1)) { $string .= ' last'; }
					if ($sub_item['slug'] == $active_page)
					{
						$classes[] = 'current_parent';
						$string .= ' current';
					}
					$string .= '"><a href="'. $sub_item['url'] . '" title="'. strip_quotes(stripslashes($sub_item['title'])) .'">'.stripslashes($sub_item['menu_text']).'</a></li>';
				}
				$string .= "\n  </ul>\n";
			}
			$string .= '</li>';
			$items[] = sprintf($string, implode(' ', $classes));
		}
		if (sizeof($items))
		{
			$items = implode("\n", $items);
			if ($echo)
			{
				nested_menus__cache_file($cachepath, $items);
				echo '<!-- un-cached -->' . $items;
			}
			else return $items;
		}
		else return "";
	}
}

function nested_menu_data__sort_menu_parents_first($a, $b) {
  if (empty($a['parent_slug']) && empty($b['parent_slug'])) {
    return 0;
  }
  elseif(empty($a['parent_slug'])) {
    return -1;
  }
  else return 1;
}

function nested_menu_data__sort_by_menu_priority($a, $b) {
  if (intval($a['menu_priority']) == intval($b['menu_priority'])) {
    return 0;
  }
  return (intval($a['menu_priority']) < intval($b['menu_priority'])) ? -1 : 1;
}

function nested_menus__cache_file($cachepath, $content) {
  //Check if cache folder exists.
	if (is_dir(GSDATAOTHERPATH.'nested_menu_cache')==false)
	{
		mkdir(GSDATAOTHERPATH.'nested_menu_cache', 0755) or exit('Unable to create nested_menu_cache folder');
	}
	
	//Save cached child menu file.
	$fp = @fopen($cachepath, 'w') or exit('Unable to save ' . $cachepath);
	fwrite($fp, $content);
	fclose($fp);
}
?>
