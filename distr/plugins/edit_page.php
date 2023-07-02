<?php
/*
Plugin Name: Edit Page
Description: Adds an "edit page" link into the footer of every page.
Version: 1.0
Author: Manuel Kehl
Author URI: http://www.manuel-kehl.de
*/

# get correct id for plugin
$plgid=basename(__FILE__, ".php");
# add in this plugin's language file
i18n_merge($plgid) || i18n_merge($plgid, 'en_US');

# register plugin
register_plugin(
	$plgid, //Plugin id
	'Edit Page', 	//Plugin name
	'1.1', 		//Plugin version
	'Manuel Kehl, Alexander Gribkov',  //Plugin author
	'http://example.org/', //author website
	'Adds an "edit page" link into the footer of every page.', //Plugin description
	'pages', //Page type
	''  //main function (administration) - there is no need for it, as there is no administration backend yet
);

//Change hook name to alter the position where the link is being displayed
event::join('content-bottom', 'edit_page', array());


# functions
function edit_page()
{
	global $plgid;
	if (cookie_check())
	{
		$slug=get_page_slug(false);
		//Echo the link
		echo '<a class="btn_adm" href="'.av::get('cpath').'admin/edit.php?id='.$slug.'">'.i18n_r($plgid.'/EDIT_PAGE').'.</a>';
	}
}

?>
