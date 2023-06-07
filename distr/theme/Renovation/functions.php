<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 			functions.php
* @Package:		GetSimple
* @Action:		Renovation theme for GetSimple CMS
*
*****************************************************/

/**
 * Renovation Parent Link
 *
 * This creates a link for a parent for the breadcrumb feature of this theme
 *
 * @param string $name - This is the slug of the link you want to create
 * @return string
 */
function Renovation_Parent_Link($name) {
	$file = GSDATAPAGESPATH . $name .'.xml';
	if (file_exists($file)) {
		$p = getXML($file);
		$title = $p->title;
		$parent = $p->parent;
		$slug = $p->slug;
		echo '<a href="'. find_url($name,'') .'">'. $title .'</a> &nbsp;&nbsp;&#149;&nbsp;&nbsp; ';
	}
}

/**
 * Renovation Settings
 *
 * This defines variables based on the theme plugin's settings
 *
 * @return bool
 */
function Renovation_Settings() {
	$file = GSDATAOTHERPATH . 'RenovationSettings.xml';
	if (file_exists($file)) {
		$p = getXML($file);
		return $p;
	} else {
		return false;
	}
}
