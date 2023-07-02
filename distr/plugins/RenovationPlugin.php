<?php
/*
Plugin Name: Renovation Theme Settings
Description: Settings for the default GetSimple Theme: Renovation
Version: 1.2
*/

# get correct id for plugin
$thisfile_renov=basename(__FILE__, ".php");
$renovation_file=GSDATAOTHERPATH .'RenovationSettings.xml';

# add in this plugin's language file
i18n_merge($thisfile_renov) || i18n_merge($thisfile_renov, 'en_US');

# register plugin
register_plugin(
	$thisfile_renov, 								# ID of plugin, should be filename minus php
	i18n_r($thisfile_renov.'/RENOVATION_TITLE'), 	# Title of plugin
	'1.2', 											# Version of plugin
	'Chris Cagle',									# Author of plugin
	'http://chriscagle.me', 						# Author URL
	i18n_r($thisfile_renov.'/RENOVATION_DESC'), 	# Plugin Description
	'theme', 										# Page type of plugin
	'renovation_show'  								# Function that displays content
);
$hidemenu = true;
# hooks
# enable side menu is theme is renovation or on theme page and enabling renovation, handle plugin exec before global is set
if(	!$hidemenu || (
	( $TEMPLATE == "Renovation" || 	( get_filename_id() == 'theme' && isset($_POST['template']) && $_POST['template'] == 'Renovation') ) &&
	!( $TEMPLATE == "Renovation" && get_filename_id() == 'theme' && isset($_POST['template']) && $_POST['template'] != 'Renovation') )
)
{
	event::join('theme-sidebar','createSideMenu',array($thisfile_renov, i18n_r($thisfile_renov.'/RENOVATION_TITLE'))); 
}

$services = array(
	'facebook',
	'googleplus',
	'twitter',
	'linkedin',
	'tumblr',
	'instagram',
	'youtube',
	'vimeo',
	'github'
);

# get XML data
if (file_exists($renovation_file)) {
	$renovation_data = getXML($renovation_file);
}

function renovation_show() {
	global $services,$renovation_file, $renovation_data, $thisfile_renov;
	$success=$error=null;
	
	// submitted form
	if (isset($_POST['submit'])) {		
		foreach($services as $var){			
			if ($_POST[$var] != '') {
				if (validate_url($_POST[$var])) {
					$resp[$var] = $_POST[$var];
				} else {
					$error .= i18n_r($thisfile_renov.'/'.strtoupper($var).'_ERROR').' ';
				}
			}			
		}
		
		# if there are no errors, save data
		if (!$error) {
			$xml = @new SimpleXMLElement('<item></item>');
			foreach($services as $var){			
				if(isset($resp[$var])) $xml->addChild($var, $resp[$var]);
			}
							
			if (! $xml->asXML($renovation_file)) {
				$error = i18n_r('CHMOD_ERROR');
			} else {
				$renovation_data = getXML($renovation_file);
				$success = i18n_r('SETTINGS_UPDATED');
			}
		}
	}
	
	?>
	<h3><?php i18n($thisfile_renov.'/RENOVATION_TITLE'); ?></h3>
	
	<?php 
	if($success) { 
		echo '<p style="color:#669933;"><b>'. $success .'</b></p>';
	} 
	if($error) { 
		echo '<p style="color:#cc0000;"><b>'. $error .'</b></p>';
	}
	?>
	
	<form method="post" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
		
		<?php 
			foreach($services as $var){
				$value = '';
				if(isset($renovation_data->$var)) $value = $renovation_data->$var;
				echo '<p><label for="inn_'.$var.'" >' . i18n($thisfile_renov.'/'.strtoupper($var).'_URL') .'</label><input id="inn_'.$var.'" name="'.$var.'" class="text" value="'.$value.'" type="url" /></p>';
			}
		?>

		<p><input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="submit" /></p>
	</form>
	
	<?php
}
