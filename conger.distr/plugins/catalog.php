<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
//*/
# get correct id for plugin
$plgid_catalog = basename(__FILE__, ".php");
define('PLGID_CATALOG', $plgid_catalog);

# add in this plugin's language file
i18n_merge($plgid_catalog) || i18n_merge( $plgid_catalog, 'en_US');

# register plugin
register_plugin(
	$plgid_catalog, // ID of plugin, should be filename minus php
	i18n_r(PLGID_CATALOG.'/PLUGIN_TITLE'), 	
	'1.3',
	'Alexander Gribkov, Mike Henken',
	'http://example.org/', 
	i18n_r(PLGID_CATALOG.'/PLUGIN_DESC'),
	'pages',
	'Catalog_interact'  
);
event::join('pages-sidebar','createSideMenu',array($plgid_catalog, i18n_r(PLGID_CATALOG.'/PLUGIN_SIDE')));
define('PLGCATALOGFILE', GSDATAOTHERPATH  . 'catalog/catalog.xml');
filter::join('content','Catalog_shortcodes_get');

global $EDLANG, $EDOPTIONS, $toolbar, $EDTOOL;
if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = 'en'; }
if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
//echo "<h1>e=$EDTOOL</h1>";

// thes overwrites toolbar in entire edit section, not only Catalol. Deactivated.
//if ($EDTOOL == 'advanced')
//{
	//$toolbar = "[['h2','h3','h4','h5','h6'], ['Bold','Italic','Underline'],['NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'], ['Undo','Redo'], [ 'Maximize', 'ShowBlocks','-','About' ] ]";
//}
//elseif ($EDTOOL == 'basic')
//{
	//$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
//}



function Catalog_interact()
{
	catalog::main();
}


function Catalog_shortcodes_get($content) 
{
	return catalog::shortcodes_get($content); 
}

define('PLG_CATALOG_PATH', dirname(__FILE__).'/'.$plgid_catalog);
require_once PLG_CATALOG_PATH .'/catalog.php';

?>
