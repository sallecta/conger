<?php

/*******************************************************************************

	@File:			user_manager.php
	@Plugin:		Multi User
	@Description:	Adds Multi-User Management Section
	@Subject:		Main plugin file
	@Revision:		20 Feb 2015
	@Version:		1.9.0
	@Author:		Mike Henken (http://michaelhenken.com/)
	@History:
	----------------------------------------------------------------------------
	Version 1.9.1 (June 2023) :: by sallecta (github.com/sallecta)
	----------------------------------------------------------------------------
	----------------------------------------------------------------------------
	Version 1.9.0 (February 2015) :: smartened by maf (www.jinan.cz)
	----------------------------------------------------------------------------
	+ reworked UX to better fit GS 3.3 admin interface look&feel
	+ complete localization with EN, RU and CS language files bundled
	+ returned "User's bio" in User's profile, fixed non-latin chars crashes
	+ CKeditor on demand on User Management page
	+ added switch to reveal password text
	+ added client-side username and empty password validation
	+ added client-side automatic landing page restriction/setting by permissions
	+ logged-in user cannot delete himself or remove his User Management rights
	+ default permissions for standard non-admin user when adding new users
	+ added several help hints
	+ huge increase of performance by removing multiple readings of CKeditor code
	- some of the above features do not work in IE<10 but plugin is still usable

	----------------------------------------------------------------------------
	Version 1.8.2+ "updated" (May 2014) :: cured by Oleg06 (www.getsimplecms.ru)
	----------------------------------------------------------------------------
	+ repaired to work with GS 3.3
	+ added server-side checks for incorrect landing page settings
	+ user with denied "Settings" rights can still edit his profile
	+ added "admin" flag in users table
	- removed User's bio from user's profile (it wrecked user on nonlatin chars)
	- incomlete localization with many Cyrillic strings

	----------------------------------------------------------------------------
	Version 1.8.2 (September 2012) :: last version by Mike Henken
	----------------------------------------------------------------------------
	+ DISCLAIMER - When I initially created this plugin I had very little
		knowledge of php and was a programming noob. I know it is in need
		of a rewrite and I will get around to it at some point
	- compromised functionality with GS 3.3

*******************************************************************************/

	// get correct id for plugin
	$plgid_usman = basename(__FILE__, ".php");

	# language support
	i18n_merge('user_manager') || i18n_merge('user_manager', 'en_US');

	// register plugin
	register_plugin(
	$plgid_usman,											# ID of plugin, should be filename minus php
	'User Manager',										# Title of plugin
	'1.9.0',											# Version of plugin
	'Mike Henken, Oleg06, maf, sallecta',				# Author of plugin
	'http://www.michaelhenken.com/',					# Author URL
	i18n_r('user_manager/PLUGIN_DESCRIPTION'),		# Plugin Description
	'settings',											# Page type of plugin
	'UserManager_interact'											# Function that displays content
	);

	// activate hooks //
	//Add Sidebar Item In Settings Page
	event::join('settings-sidebar', 'createSideMenu', array($plgid_usman, i18n_r('user_manager/SIDEBAR')));
	//Make the multiuser_perm() function run before each admin page loads
	event::join('header', 'UserManager_permissions');
	event::join('settings-user', 'UserManager_ProcessSettings');
	event::join('settings-user-extras', 'UserManager_ProfileRender');
	event::join('resetpw-success', 'UserManager_ResetPw');

	define('USERMANAGER_PATH', GSPLUGINPATH.'user_manager/');

class UserManager {
	public function __construct(){ }
	public function userData($get_Data, $data_Type = ""){
		if(get_cookie('GS_ADMIN_USERNAME') != ""){
			$current_user = get_cookie('GS_ADMIN_USERNAME');
			$dir = GSUSERSPATH . $current_user . ".xml";
			$user_file = simplexml_load_file($dir) or die(i18n_r('user_manager/ERROR_XML'));

			if($data_Type == ""){
				$return_user_data = $user_file->PERMISSIONS->$get_Data;
			}elseif($data_Type != "") {
				$return_user_data = $user_file->$get_Data;
			}
			if(is_object($return_user_data)){
				return $return_user_data;
			} else return '';
		}
	}

	public function ProcessSettings()	{
		if(get_cookie('GS_ADMIN_USERNAME') != ""){
			global $xml, $perm;
			$usersbio = (isset($_POST['users_bio'])) ? $_POST['users_bio'] : '';
			$userbio = $xml->addChild('USERSBIO');
				$node= dom_import_simplexml($userbio); // coding reused from addCData() function of SimplXMLExtended class in inc/basic.php line 215
				$no = $node->ownerDocument;
				$node->appendChild($no->createCDATASection($usersbio));
			$perm = $xml->addChild('PERMISSIONS');
			$perm->addChild('PAGES', $this->userData('PAGES'));
			$perm->addChild('FILES', $this->userData('FILES'));
			$perm->addChild('THEME', $this->userData('THEME'));
			$perm->addChild('PLUGINS', $this->userData('PLUGINS'));
			$perm->addChild('BACKUPS', $this->userData('BACKUPS'));
			$perm->addChild('SETTINGS', $this->userData('SETTINGS'));
			$perm->addChild('SUPPORT', $this->userData('SUPPORT'));
			$perm->addChild('EDIT', $this->userData('EDIT'));
			$perm->addChild('LANDING', $this->userData('LANDING'));
			$perm->addChild('ADMIN', $this->userData('ADMIN'));
			UserManager_PermissionsSave(true);
		}
	}

	public function DeleteUser(){
		$deletename = $_GET['deletefile'];
		$thedelete = GSUSERSPATH . $deletename . '.xml';
		if (file_exists(GSUSERSPATH . $deletename . '.xml')) {
			$success = @unlink($thedelete);
			if($success){
				print "<div class=\"updated\" style=\"display: block;\">".  i18n_r('user_manager/DELETED_P').' <strong>'.$deletename.'</strong> '.i18n_r('user_manager/DELETED') . "</div>";
			}
		}
		UserManager_Render();
	}

	public function AddUser(){
		if (!empty($_POST['usernamec']) && !empty($_POST['userpassword'])) {
			global $xml, $perm;
			$NUSR = _id(trim($_POST['usernamec']));
			$usrfile	= $NUSR . '.xml';
			$NLANDING = (isset($_POST['Landing']) && !empty($_POST['Landing'])) ? $_POST['Landing'] : '';
			$NPASSWD = passhash($_POST['userpassword']);
			$email = (isset($_POST['useremail'])) ? $_POST['useremail'] : '';
			$editor = (isset($_POST['usereditor'])) ? $_POST['usereditor'] : '';
			$timezone = (isset($_POST['ntimezone'])) ? $_POST['ntimezone'] : '';
			$lang = (isset($_POST['userlng'])) ? $_POST['userlng'] : '';
			$name = (isset($_POST['name'])) ? $_POST['name'] : '';
			$usersbio = (isset($_POST['users_bio'])) ? $_POST['users_bio'] : '';
			$files = (isset($_POST['Files']) && $NLANDING != 'upload.php') ? $_POST['Files'] : '';
			$pages = (isset($_POST['Pages']) && $NLANDING != 'pages.php' && $NLANDING != '') ? $_POST['Pages'] : '';
			$theme = (isset($_POST['Theme']) && $NLANDING != 'theme.php') ? $_POST['Theme'] : '';
			$plugins = (isset($_POST['Plugins']) && $NLANDING != 'plugins.php') ? $_POST['Plugins'] : '';
			$backups = (isset($_POST['Backups']) && $NLANDING != 'backups.php') ? $_POST['Backups'] : '';
			$settings = (isset($_POST['Settings'])) ? $_POST['Settings'] : '';
			$support = (isset($_POST['Support']) && $NLANDING != 'support.php') ? $_POST['Support'] : '';
			$edit = (isset($_POST['Edit']) && $NLANDING != 'edit.php') ? $_POST['Edit'] : '';
			$admin = (isset($_POST['Admin'])) ? $_POST['Admin'] : '';

			$xml = new SimpleXMLExtended('<item></item>');
			$xml->addChild('USR', $NUSR);
			$xml->addChild('PWD', $NPASSWD);
			$xml->addChild('EMAIL', $email);
			$xml->addChild('HTMLEDITOR', $editor);
			$xml->addChild('TIMEZONE', $timezone);
			$xml->addChild('LANG', $lang);
			$xml->addChild('NAME', $name);
			$userbio = $xml->addChild('USERSBIO');
			$userbio->addCData($usersbio);
			$perm = $xml->addChild('PERMISSIONS');
			$perm->addChild('PAGES', $pages);
			$perm->addChild('FILES', $files);
			$perm->addChild('THEME', $theme);
			$perm->addChild('PLUGINS', $plugins);
			$perm->addChild('BACKUPS', $backups);
			$perm->addChild('SETTINGS', $settings);
			$perm->addChild('SUPPORT', $support);
			$perm->addChild('EDIT', $edit);
			$perm->addChild('LANDING', $NLANDING);
			$perm->addChild('ADMIN', $admin);
			UserManager_PermissionsSave();
			if (!XMLsave($xml, GSUSERSPATH . $usrfile) ) {
				$error = i18n_r('user_manager/SAVEERROR');
				echo '<div class="error" style="display: block;">' . i18n_r('user_manager/SAVEERROR') . '</div>';
			} else print '<div class="updated" style="display: block;">'.i18n_r('user_manager/CREATED_P').' <strong>'.$NUSR.'</strong> '.i18n_r('user_manager/CREATED').'</div>';

		} else print '<div class="error myerror" style="display: block;">'.i18n_r('user_manager/ERROR_USR').'</div>';

		UserManager_Render(); //Show Manage Form
	}

	public function ProcessEditUser(){
		if (isset($_POST['usernamec']) && !empty($_POST['usernamec'])) {
			global $xml, $perm;
			$NUSR = $_POST['usernamec'];
			if (isset($_POST['userpassword']) && !empty($_POST['userpassword'])) {
				$NPASSWD = passhash($_POST['userpassword']);
				if (file_exists(GSUSERSPATH . $NUSR.'.xml.reset')) {
					unlink(GSUSERSPATH . $NUSR.'.xml.reset');
					global $changeResetpw; $changeResetpw = '$(".error").remove();';
				}
			} else $NPASSWD = (isset($_POST['nano']) && !empty($_POST['nano'])) ? $_POST['nano'] : '';
			if ($NPASSWD !== '') {
				$usrfile = $NUSR . '.xml';
				$NLANDING = (isset($_POST['Landing']) && !empty($_POST['Landing'])) ? $_POST['Landing'] : '';
				$email = (isset($_POST['useremail'])) ? $_POST['useremail'] : '';
				$editor = (isset($_POST['usereditor'])) ? $_POST['usereditor'] : '';
				$timezone = (isset($_POST['ntimezone'])) ? $_POST['ntimezone'] : '';
				$lang = (isset($_POST['userlng'])) ? $_POST['userlng'] : '';
				$name = (isset($_POST['name'])) ? $_POST['name'] : '';
				$usersbio = (isset($_POST['users_bio'])) ? $_POST['users_bio'] : '';
				$files = (isset($_POST['Files']) && $NLANDING != 'upload.php') ? $_POST['Files'] : '';
				$pages = (isset($_POST['Pages']) && $NLANDING != 'pages.php' && $NLANDING != '') ? $_POST['Pages'] : '';
				$theme = (isset($_POST['Theme']) && $NLANDING != 'theme.php') ? $_POST['Theme'] : '';
				$plugins = (isset($_POST['Plugins']) && $NLANDING != 'plugins.php') ? $_POST['Plugins'] : '';
				$backups = (isset($_POST['Backups']) && $NLANDING != 'backups.php') ? $_POST['Backups'] : '';
				$settings = (isset($_POST['Settings'])) ? $_POST['Settings'] : '';
				$support = (isset($_POST['Support']) && $NLANDING != 'support.php') ? $_POST['Support'] : '';
				$edit = (isset($_POST['Edit']) && $NLANDING != 'edit.php') ? $_POST['Edit'] : '';
				$admin = (isset($_POST['Admin'])) ? $_POST['Admin'] : '';
				createBak($usrfile, GSUSERSPATH, GSBACKUSERSPATH);

				// Edit user xml file - This coding was mostly taken from the 'settings.php' page..
				$xml = new SimpleXMLExtended('<item></item>');
				$xml->addChild('USR', $NUSR);
				$xml->addChild('PWD', $NPASSWD);
				$xml->addChild('EMAIL', $email);
				$xml->addChild('HTMLEDITOR', $editor);
				$xml->addChild('TIMEZONE', $timezone);
				$xml->addChild('LANG', $lang);
				$xml->addChild('NAME', $name);
				$userbio = $xml->addChild('USERSBIO');
				$userbio->addCData($usersbio);
				$perm = $xml->addChild('PERMISSIONS');
				$perm->addChild('PAGES', $pages);
				$perm->addChild('FILES', $files);
				$perm->addChild('THEME', $theme);
				$perm->addChild('PLUGINS', $plugins);
				$perm->addChild('BACKUPS', $backups);
				$perm->addChild('SETTINGS', $settings);
				$perm->addChild('SUPPORT', $support);
				$perm->addChild('EDIT', $edit);
				$perm->addChild('LANDING', $NLANDING);
				$perm->addChild('ADMIN', $admin);
				UserManager_PermissionsSave();
				if (!XMLsave($xml, GSUSERSPATH . $usrfile)) {
					$error = i18n_r('user_manager/SAVEERROR');
					echo '<div class="error" style="display: block;">' . i18n_r('user_manager/SAVEERROR') . '</div>';
				} else print '<div class="updated" style="display: block;">'.i18n_r('user_manager/SAVED').'</div>';

			} else print '<div class="error" style="display: block;">' . i18n_r('user_manager/SAVEERROR') . '</div>';

		} else print '<div class="error" style="display: block;">' . i18n_r('user_manager/SAVEERROR') . '</div>';

		UserManager_Render();
	}

	public function GetUserPermission($user, $permission=null){
		$userData = getXML(GSUSERSPATH.$user.'.xml');
		if(!is_null($permission)){
			if(isset($userData->PERMISSIONS->$permission) && is_object($userData->PERMISSIONS->$permission)){
				$permission_value = (string) $userData->PERMISSIONS->$permission;
				if($permission_value == 'no'){
					return false;
				} else {return true;}
			} else {return true;}
		} elseif (isset($userData->PERMISSIONS) && is_object($userData->PERMISSIONS)){
			foreach($userData->PERMISSIONS->children() as $permission_key => $permission_value){
				$permission_key = (string) $permission_key;
				$permission_value = (string) $permission_value;
				if($permission_value == 'no'){
					$permissions[$permission_key] = false;
				}
				else{
					$permissions[$permission_key] = true;
				}
			}
			return $permissions;
		} else {
			$blogUserPermissions = array();
			$blogUserPermissions['blogsettings'] = true;
			$blogUserPermissions['blogeditpost'] = true;
			$blogUserPermissions['blogcreatepost'] = true;
			$blogUserPermissions['blogcategories'] = true;
			$blogUserPermissions['blogrssimporter'] = true;
			$blogUserPermissions['bloghelp'] = true;
			$blogUserPermissions['blogcustomfields'] = true;
			$blogUserPermissions['blogdeletepost'] = true;
			return $blogUserPermissions;
		}
	}

	public function CheckPermissions(){
		//echo $this->userData('SETTINGS'); //only for debug purposes
		//Find Current script and trim path
		$current_file = $_SERVER["PHP_SELF"];
		$current_file = basename(rtrim($current_file, '/'));
		$current_script =  $_SERVER["QUERY_STRING"];
		$landing = (string)$this->userData('LANDING');
		$pages = (string)$this->userData('PAGES');

		//pages.php permissions - If pages is disabled, this coding will kill the pages script and redirect to the chosen alternate landing page
		if ($current_file=="pages.php" or $current_script=='id=i18n_base'){
			$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER']: '';
			$adminfolder = basename(get_admin_path());
			if ($referer and basename($referer) == $adminfolder or strpos($referer, $adminfolder.'/index.php')) {
				if ($pages == "no") {
					if ($landing == "pages.php" || $landing == "") die ('<meta http-equiv="refresh" content="0;url=logout.php">');
					else die('<meta http-equiv="refresh" content="0;url='. $landing .'">');
				}
				elseif ($landing and $landing != "pages.php") die('<meta http-equiv="refresh" content="0;url='. $landing .'">');	/*<meta http-equiv="refresh" content="0;url='. $landing .'">*/
			}
			elseif ($pages == "no") die(i18n_r('user_manager/NO_PERMISSION'));
		}
		if ($pages == "no") {
			if ($current_file=="menu-manager.php" or ($current_script=='id=i18n_navigation'||$current_script=='id=i18n_specialpages&pages'))
				die(i18n_r('user_manager/NO_PERMISSION'));
			$pages_menu = "#nav_pages, #sb_pages, #sb_i18n_base, #sb_i18n_navigation, #sb_menumanager, a[href='load.php?id=i18n_specialpages&pages'] {display:none !important;}";
			$pages_footer =  '$("#footer").find("a[href=\'pages.php\']").remove(); $("#footer").find("a[href=\'load.php?id=i18n_base\']").remove();';
		} else {$pages_menu =""; $pages_footer = "";}

		//Settings.php permissions
		if ($this->userData('SETTINGS') == "no") {
			if ($current_file == "settings.php") {
				$site_settings = '$(".main").children().not("#profile").remove(); $("#profile").css("padding", "0");';
			}
			$settings_menu = "#sb_settings {display:none !important;}";
		} else {$settings_menu =""; $site_settings = '';}

		//backups.php permisions
		if ($this->userData('BACKUPS') == "no") {
			if ($current_file == "backups.php" || $current_file == "archive.php") die(i18n_r('user_manager/NO_PERMISSION'));
			$backups_menu = "#nav_backups {display:none !important;}";
			$backups_footer = '$("#footer").find("a[href=\'backups.php\']").remove();';
		} else {$backups_menu =""; $backups_footer = "";}

		//plugins.php permissions
		if ($this->userData('PLUGINS') == "no") {
			if ($current_file == "plugins.php") die(i18n_r('user_manager/NO_PERMISSION'));
			$plugins_menu = "#nav_plugins {display:none !important;}";
			$plugins_footer = '$("#footer").find("a[href=\'plugins.php\']").remove();';
		} else {$plugins_menu =""; $plugins_footer = "";}

		//support.php & health-check.php permissions
		if ($this->userData('SUPPORT') == "no") {
			if ($current_file == "support.php") die(i18n_r('user_manager/NO_PERMISSION'));
			$support_menu = ".support {display:none !important;}";
			$support_footer = '$("#footer").find("a[href=\'support.php\']").remove();';
		} else {$support_menu = "";	$support_footer = "";}

		//uploads.php (files page) permissions
		if ($this->userData('FILES') == "no") {
			if ($current_file == "upload.php") die(i18n_r('user_manager/NO_PERMISSION'));
			$files_menu = "#nav_upload {display:none !important;}";
			$files_footer = '$("#footer").find("a[href=\'upload.php\']").remove();';
		} else {$files_menu = ""; $files_footer = "";}

		//theme.php permissions
		if ($this->userData('THEME') == "no") {
			if ($current_file == "theme.php" || $current_file == "theme-edit.php" || $current_file == "components.php") die(i18n_r('user_manager/NO_PERMISSION'));
			$theme_menu = "#nav_theme {display:none !important;}";
			$theme_footer = '$("#footer").find("a[href=\'theme.php\']").remove();';
		} else {$theme_menu = ""; $theme_footer = "";}

		//edit.php
		if ($this->userData('EDIT') == "no") {
			if ($current_file == "edit.php") die(i18n_r('user_manager/NO_PERMISSION'));
			$edit_menu = "#sb_newpage, a[href='load.php?id=i18n_specialpages&create'] {display:none !important;}";
		} else {$edit_menu = "";}

		//Admin - Do not allow permissions to edit users
		if ($this->userData('ADMIN') == "no") {
			if ($current_script == "id=user_manager") die(i18n_r('user_manager/NO_PERMISSION'));
			if ($current_file == "settings.php") {
				$htmleditor = '$("#show_htmleditor").closest("p").css("display","none");';
			}
			$admin_menu = "#user_manager_sb {display:none !important;}";
		} else {$admin_menu = ""; $htmleditor = "";}

		//Hide Menu Items
		echo '<style type="text/css">';
		echo $edit_menu."\n".$settings_menu."\n".$backups_menu."\n".$plugins_menu."\n".$pages_menu."\n".$support_menu."\n".$files_menu."\n".$theme_menu."\n".$admin_menu;
		echo '</style>';

		//Hide Footer Menu Items With Jquery
		echo '<script type="text/javascript">';
		echo "\n";
		echo '$(document).ready(function() {';
		echo "\n";
		echo $files_footer."\n".$backups_footer."\n".$plugins_footer."\n".$pages_footer."\n".$support_footer."\n".$theme_footer."\n".$site_settings."\n".$htmleditor;
		echo "\n";
		echo ' });';
		echo '</script>';
	}

	public function DownloadPlugin($id) {
		$pluginurl = $this->DownloadPlugins($id, 'file');
		$pluginfile = $this->DownloadPlugins($id, 'filename_id');

		$data = file_get_contents($pluginurl);
		$fp = fopen($pluginfile, "wb");
		fwrite($fp, $data);
		fclose($fp);

		function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true){
			if ($zip = zip_open($src_file)){
				if ($zip){
				  $splitter = ($create_zip_name_dir === true) ? "." : "/";
				  if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";

				  // Create the directories to the destination dir if they don't already exist
				  create_dirs($dest_dir);

				  // For every file in the zip-packet
					while ($zip_entry = zip_read($zip)){
						// Now we're going to create the directories in the destination directories

						// If the file is not in the root dir
						$pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
						if ($pos_last_slash !== false){
						  // Create the directory where the zip-entry should be saved (with a "/" at the end)
						  create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
						}
						// Open the entry
						if (zip_entry_open($zip,$zip_entry,"r")){

						  // The name of the file to save on the disk
						  $file_name = $dest_dir.zip_entry_name($zip_entry);

						  // Check if the files should be overwritten or not
							if ($overwrite === true || $overwrite === false && !is_file($file_name)){
								// Get the content of the zip entry
								$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
								file_put_contents($file_name, $fstream );
								// Set the rights
								chmod($file_name, 0755);
							}
						  // Close the entry
						  zip_entry_close($zip_entry);
						}
					}
				  // Close the zip-file
				  zip_close($zip);
				}
			} else {return false;}
			return true;
		}

		/**
		 * This function creates recursive directories if it doesn't already exist
		 *
		 * @param String  The path that should be created
		 *
		 * @return  void
		 */
		function create_dirs($path){
		  if (!is_dir($path)){
			$directory_path = "";
			$directories = explode("/",$path);
			array_pop($directories);

			foreach($directories as $directory){
			  $directory_path .= $directory."/";
			  if (!is_dir($directory_path)){
				mkdir($directory_path);
				chmod($directory_path, 0777);
			  }
			}
		  }
		}

		$pluginname = $this->DownloadPlugins($id, 'name');

		 /* Unzip the source_file in the destination dir
		 *
		 * @param   string	  The path to the ZIP-file.
		 * @param   string	  The path where the zipfile should be unpacked, if false the directory of the zip-file is used
		 * @param   boolean	 Indicates if the files will be unpacked in a directory with the name of the zip-file (true) or not (false) (only if the destination directory is set to false!)
		 * @param   boolean	 Overwrite existing files (true) or not (false)
		 *
		 * @return  boolean	 Succesful or not
		 */

		// Extract C:/zipfiletest/zip-file.zip to C:/another_map/zipfiletest/ and doesn't overwrite existing files. NOTE: It doesn't create a map with the zip-file-name!
		$success = unzip($pluginfile, "../plugins/", true, true);
		if ($success){
		  print '<div class="updated">' . $pluginname . ' ' . i18n_r('user_manager/UPDATED') . '</div>';
		} else {print '<div class="updated">' . i18n_r('user_manager/UPDATEERROR') . '</div>';}
		UserManager_Render();
	}

	public function DownloadPlugins($id, $get_field){
		$my_plugin_id = $id; // replace this with yours

		$apiback = file_get_contents('http://get-simple.info/api/extend/?id='.$my_plugin_id);
		$response = json_decode($apiback);
		if ($response->status == 'successful') {
			// Successful api response sent back.
			$get_field_data = $response->$get_field;
		}
		return $get_field_data;
	}
	public function ResetPw(){
		global $data, $random, $file;
		$xml = new SimpleXMLExtended('<item></item>');
		$xml->addChild('USR', $data->USR);
		$xml->addChild('PWD', passhash($random));
		$xml->addChild('EMAIL', $data->EMAIL);
		$xml->addChild('HTMLEDITOR', $data->HTMLEDITOR);
		$xml->addChild('TIMEZONE', $data->TIMEZONE);
		$xml->addChild('LANG', $data->LANG);
		$xml->addChild('NAME', $data->NAME);
		$userbio = $xml->addChild('USERSBIO');
		$userbio->addCData($data->USERSBIO);
		$perm = $xml->addChild('PERMISSIONS');
		$perm->addChild('PAGES', $data->PERMISSIONS->PAGES);
		$perm->addChild('FILES', $data->PERMISSIONS->FILES);
		$perm->addChild('THEME', $data->PERMISSIONS->THEME);
		$perm->addChild('PLUGINS', $data->PERMISSIONS->PLUGINS);
		$perm->addChild('BACKUPS', $data->PERMISSIONS->BACKUPS);
		$perm->addChild('SETTINGS', $data->PERMISSIONS->SETTINGS);
		$perm->addChild('SUPPORT', $data->PERMISSIONS->SUPPORT);
		$perm->addChild('EDIT', $data->PERMISSIONS->EDIT);
		$perm->addChild('LANDING', $data->PERMISSIONS->LANDING);
		$perm->addChild('ADMIN', $data->PERMISSIONS->ADMIN);
		XMLsave($xml, GSUSERSPATH . $file);
	}
}

function UserManager_Render(){
	global $changeResetpw, $USR, $TEMPLATE, $SITEURL;
	$inst_UserManager = new UserManager;
	# get all available language files
	$lang_handle = opendir(GSLANGPATH) or die(i18n_r('user_manager/ERROR_LNG') . GSLANGPATH);
	while ($lfile = readdir($lang_handle)) {
		if( is_file(GSLANGPATH . $lfile) && $lfile != "." && $lfile != ".." )	{
			$lang_array[] = basename($lfile, ".php");
		}
	}
	if (count($lang_array) != 0) {
		sort($lang_array);
		$count = '0'; $sel = ''; $langs = '';
		foreach ($lang_array as $larray){
			$langs .= '<option value="'.$larray.'" >'.$larray.'</option>';
			$count++;
		}
	}

	//Configure CK Editor for User's Bio
	//global $TOOLBAR;
	$EDHEIGHT = '300px';
	$EDTOOL = defined('GSEDITORTOOL') ? GSEDITORTOOL : 'basic';
	$EDLANG = defined('GSEDITORLANG') ? GSEDITORLANG : i18n_r('CKEDITOR_LANG');
	$EDOPTIONS = defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS) != '' ? ', ' . GSEDITOROPTIONS : '';

	if ($EDTOOL == 'advanced'){
		$TOOLBAR = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
	'/', ['Styles','Format','Font','FontSize']";
	}
	elseif ($EDTOOL == 'basic') {
		$TOOLBAR = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
	}
	else{
		$TOOLBAR = GSEDITORTOOL;
	}

	//Get Available Timezones
	ob_start(); include ( GSADMINPATH . "inc/timezone_options.txt");$Timezone_Include = ob_get_contents();ob_end_clean();

	//Styles For Form
?>

<style>
label>em, label>b {  /* Do not show em's italics from main language file
			   (permission labels should read the same as main administration tabs */
	font-style: inherit;
	font-weight: inherit;
}

.wrapper table th {
	line-height: 1em !important;
}

hr { /* <hr />s are included in markup for better non-CSS usability */
	display: none;
}

h4 {
	font-size: 15px;
	font-family: Georgia, Times, Times New Roman, serif;
	font-weight: normal;
	font-style: italic;
	color: hsl(15, 95%, 42%);
	margin: 1em 0 .5em;
	text-shadow: 1px 1px 0px hsl(0, 0%, 100%);
}

h4, em.user {
	font-family: Georgia, Times, Times New Roman, serif;
	text-shadow: 1px 1px 0px hsl(0, 0%, 100%);
}

em.user {
	display: block;
	width: 85%;
	margin-bottom: 0px;
	font-size: 2em;
	font-style: italic;
	font-weight: lighter;
	padding-top: 4px;
	padding-left: .5em;
	color: hsl(200, 20%, 40%);
}

.flag {
	color: hsl(200, 50%, 50%);
}

.logedin {
}

.logedin td:nth-child(-n+2) { /* Current user's login and name */
	font-weight: bold;
}

td + td {
	color: hsl(0, 0% ,50%);
}

.user_tr {
	text-shadow: 1px 1px 0px hsl(0, 0%, 100%);
}

.user_tr:nth-child(4n) { /* alternare rows colors */
	background-color: hsl(0, 0%, 97%);
}

.user_tr:hover {
	background-color: hsl(60, 100%, 92%) !important;
	text-shadow: none;
}

.deletedrow {
	background-color: hsl(13, 100%, 80%) !important;
}

#usertable .user_sub_tr {
	background-color: hsl(200, 25%, 97%);
	display:none;
	border-top: 2px solid hsl(200, 25%, 50%);
	border-bottom: 2px solid hsl(200, 25%, 50%);
}

#usertable .user_sub_tr > td, #newuser {
	padding: 12px;
}

.userform .floated {
	float: left;
}

h4, .bioeditor, p#submit_line, p.submit_line {
	clear: left;
}

.userform div.floated {
	width: 85%;
	margin-bottom: 1em;
}

.userform p.floated {
	width: 28%;
	margin-right: 1em;
	margin-bottom: 0.5em;
}

.userform div.floated p {
	display: inline-block;
	width: 30%;
	margin: 0px 2% 1em 0;
	vertical-align: top;
}

.userform .text { /* Input Fields and Selects */
	box-sizing: border-box;
	display: block;
	width: 100%;
}

.userform .bioeditor textarea {
	height: 7em;
}

.userform label a.show {
	float: right;
	margin-right: .25em;
	font-weight: lighter;
}

.userform .text::-moz-placeholder {
	font-family: Georgia, Times, Times New Roman, serif;
	font-style: italic;
}

.userform .htmleditor input {
	display: block;
}

.userform select.landing {
	transition: background-color 250ms;
}

.userform option:disabled {
	color: hsl(0,0%,75%);
	text-decoration: line-through;
}

.custom_perm_div h4:only-child  {
	display: none;
}

p.perm_div {
	line-height: 2em;
	margin-bottom: 1em;
}

.perm_div label {
	display: inline;
	margin-right: 1em;
	white-space: nowrap;
}

p.floated + p.perm_div {
	margin-left: 33%;
}

p.floated + p.perm_div:after {
	content: "";
	display: block;
	clear: left;
}

.perm_div label input {
	margin-right: .2em;
	vertical-align: -1px;
}

p#submit_line, p.submit_line {
	margin-top: 2em;
}

#maincontent span.info {
	cursor: pointer;
	font-weight: 800;
	color: hsl(200, 50%, 50%);
	position: relative;
	bottom: 0.5em;
}

</style>

<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>

<script language="javascript">
	var loadingAjaxIndicator = $('#loader');
	loadingAjaxIndicator.show();

	jQuery(document).ready(function(){
		$('div.main input:checkbox[data-check]:checked').each(function(){adjustLanding($(this))}); // Disables landing page options for denied permissions
		$('a.pass').click(function(){showHidePw(this); return false;});
		$('a.edit').click(function(){showEditor(this); return false;});
		$('a.deleteuser').click(function(){deleteUser(this); return false;});
		$('a.showhideuser').click(function(){showHideUser(this); return false;});
		$('div.main input:checkbox[data-check]').change(function(){adjustLanding($(this))});
		$("#add-user").click(function(){ // top button
			$("#add-user").css("visibility","hidden");
			$(".user_sub_tr:visible").hide();
			$("#newuser").show();
		});
		$(".submit_line").find(".cancel").click(function (){
			$(this).closest("tr").hide(500);
			return false;
		});
		$("#submit_line").find(".cancel").click(function (){
			$("#newuser").hide(500);
			$("#add-user").css("visibility","visible");
		});
		setTimeout(function(){$(".updated, .myerror").slideUp('slow');}, 4000);
		<?php echo (isset($changeResetpw)) ? $changeResetpw : ''; ?>
		loadingAjaxIndicator.fadeOut(500);
	});

	function adjustLanding(checkbox){
		var check = checkbox.prop("checked");
		var which = checkbox.data("check");
		var option = checkbox.closest("form").find(".landing option[data-check=" + which + "]");
		option.prop("disabled", check);
		if (option.prop("selected") && check) {
			option.prop("selected", false);
			option.closest("select").css("background-color", "hsl(13, 100%, 80%)");
			setTimeout(function(){option.closest("select").css("background-color", "")}, 250);
		};
	};

	function popAlertMsg() {
		/* legacy, see jquery extend popit() and closeit() */
		$('.updated').fadeOut(500).fadeIn(500);
		$('.error').fadeOut(500).fadeIn(500);
		$('.notify').popit(); // allows legacy use
	};

	function decision(message, url){
		if(confirm(message)) location.href = url;
	};

	function showHideUser(srctag){
		$("#newuser:visible").hide();
		$(srctag).closest("tr").next("tr").siblings(".user_sub_tr:visible").hide();
		$(srctag).closest("tr").next("tr").toggle(500);
	};

	function showHidePw(srctag){
		var tag=srctag.parentElement.getElementsByTagName('input')[0];
		tag.type=='password' ? tag.type='text' : tag.type='password';
	};

	function deleteUser(srctag){
	  var message = $(srctag).attr("title") + " ?";
	  var dlink = $(srctag).attr("href");
	  var mytr = $(srctag).closest("tr");
	  mytr.css("font-style", "italic");
	  var answer = confirm(message);
	  if (answer) {
		if (!$(srctag).hasClass('noajax')) {
		  loadingAjaxIndicator.show();
		  mytr.addClass('deletedrow');
		  mytr.fadeOut(500, function(){
			$.ajax({
			  type: "GET",
			  url: dlink,
			  success: function(response){
				mytr.next("tr").remove();
				mytr.remove();
				if ($("#pg_counter").length) {
				  counter = $("#pg_counter").html();
				  $("#pg_counter").html(counter - 1);
				}
				$('div.wrapper .updated').remove();
				$('div.wrapper .error').remove();
				if ($(response).find('div.error').html()) {
				  $('div.bodycontent').before('<div class="error"><p>' + $(response).find('div.error').html() + '</p></div>');
				  popAlertMsg();
				}
				if ($(response).find('div.updated').html()) {
				  $('div.bodycontent').before('<div class="updated"><p>' + $(response).find('div.updated').html() + '</p></div>');
				popAlertMsg();
				}
			  }
			});
			loadingAjaxIndicator.fadeOut(500);
		  });
		  return false;
		}
	  } else {
		mytr.css('font-style', 'normal');
		return false;
	  }
	};

	function showEditor(srctag){
		loadingAjaxIndicator.show();
		$(srctag).hide();
		var tag = srctag.parentElement.getElementsByTagName('textarea')[0];
		var editor = CKEDITOR.replace(tag.id, {
		skin : 'getsimple',
		forcePasteAsPlainText : true,
		language : '<?php echo $EDLANG; ?>',
		defaultLanguage : 'en',
		<?php
		if (file_exists(GSTHEMESPATH . $TEMPLATE . '/editor.css')) {
			$path = suggest_site_path();
			?>
			contentsCss: '<?php echo $path; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
			<?php
		}
		?>
		entities : true,
		uiColor : '#FFFFFF',
		height: '<?php echo $EDHEIGHT; ?>',
		baseHref : '<?php echo $SITEURL; ?>',
		toolbar :
		[

[ 'Format','Styles' ],
[ 'PasteFromWord','RemoveFormat' ],
[ 'Find','Replace' ],
[ 'Undo','Redo' ],
[ 'About' ],
[ 'ShowBlocks','Source','Maximize' ],
'/',
[ 'Bold','Italic','Underline','Strike','Subscript','Superscript' ],
[ 'NumberedList','BulletedList','-','Outdent','Indent' ],
[ 'Blockquote','CreateDiv' ],
[ 'Link','Unlink','Anchor' ],
[ 'Image','Table','HorizontalRule' ]
		]
		,
		tabSpaces:10,
		filebrowserBrowseUrl : 'filebrowser.php?type=all',
		filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
		filebrowserWindowWidth : '730',
		filebrowserWindowHeight : '500'
	  });
	  loadingAjaxIndicator.fadeOut(500);
	};

</script>


<!-- Below are headers for Users table -->
<h3 class="floated"><?php i18n('user_manager/TITLE'); ?></h3>

<div class="edit-nav clearfix">
	<p>
		<a href="#newuser" id="add-user"><?php i18n('user_manager/ADDUSER'); ?></a>
	</p>
</div>
<hr />
<table id="usertable" class="userform paginate">
<tr>
	<th><?php i18n('USERNAME'); ?></th>
	<th><?php i18n('user_manager/NAME'); ?></th>
	<th><?php i18n('LABEL_EMAIL'); ?></th>
	<th><?php i18n('user_manager/HTML_ED'); ?></th>
	<th><?php i18n('user_manager/ADMIN_FLAG'); ?> <span class="info" title="<?php i18n('user_manager/TITLE'); ?>">?</span></th>
	<th> </th>
	<th> </th>
</tr>
<?php
  // Open Users Directory And Put Filenames Into Array
	$dir = GSUSERSPATH."*.xml";

  // Make Edit Form For Each User XML File Found
	foreach (glob($dir) as $file) {
		$xml = simplexml_load_file($file) or die(i18n_r('user_manager/ERROR_XML'));

		// PERMISSIONS CHECKBOXES - Checks XML File To Find Existing Permissions Settings //
		//Pages
		$pageschecked = ($xml->PERMISSIONS->PAGES != "") ? "checked" : "";
		//Files
		$fileschecked = ($xml->PERMISSIONS->FILES != "") ? "checked" : "";
		//Theme
		$themechecked = ($xml->PERMISSIONS->THEME != "") ? "checked" : "";
		//Plugins
		$pluginschecked = ($xml->PERMISSIONS->PLUGINS != "") ? "checked" : "";
		//Backups
		$backupschecked = ($xml->PERMISSIONS->BACKUPS != "") ? "checked" : "";
		//Settings
		$settingschecked = ($xml->PERMISSIONS->SETTINGS != "") ? "checked" : "";
		//Support
		$supportchecked = ($xml->PERMISSIONS->SUPPORT != "") ? "checked" : "";
		//Admin
		$adminchecked = ($xml->PERMISSIONS->ADMIN != "") ? "checked" : "";
		//Landing Page
		$landingselected = ($xml->PERMISSIONS->LANDING != "") ? $xml->PERMISSIONS->LANDING : "";
		//Edit
		$editchecked = ($xml->PERMISSIONS->EDIT != "") ? "checked" : "";
		//Html Editor
		if ($xml->HTMLEDITOR != "") {
			$htmledit = i18n_r('YES');
			$cchecked = "checked";
		}
		else {
			$htmledit = i18n_r('NO');
			$cchecked = "";
		}
?>

<tr class="user_tr<?php echo ($xml->USR == $USR) ? ' logedin" title="' . i18n_r('user_manager/LOGEDIN') : '' ; ?>">
	<td><?php echo $xml->USR; ?></td>
	<td><?php echo $xml->NAME; ?></td>
	<td><?php echo $xml->EMAIL; ?></td>
	<td><?php echo $htmledit; ?></td>
	<td class="flag"><?php echo ($xml->PERMISSIONS->ADMIN =='') ? i18n_r('user_manager/ADMIN_FLAG') : '' ; ?></td>
	<td class="secondarylink"><a class="showhideuser" title="<?php i18n('user_manager/SHOW'); ?>/<?php i18n('user_manager/HIDE'); ?>" href="#">#</a></td>
	<td class="delete"><?php echo ($xml->USR == $USR) ? ' ' : '<a class="deleteuser" title="' . i18n_r('user_manager/DELETE') . ': ' . $xml->USR . '" href="load.php?id=user_manager&amp;deletefile=' . $xml->USR . '">×</a>'; ?></td>
</tr>
<tr class="user_sub_tr">
	<td colspan="7">
		<hr />

<!-- Begin 'Edit User' Form -->
		<form method="post" action="load.php?id=user_manager">

		<h3><?php i18n('user_manager/EDITUSER'); ?></h3>

		<div class="floated">

			<!-- Show Username -->
			<p>
				<label><?php i18n('USERNAME'); ?>: <em class="user"><?php echo $xml->USR; ?></em></label>
			</p>

			<!-- Change Name -->
			<p>
				<label><?php i18n('LABEL_DISPNAME'); ?>: <input class="text" name="name" type="text" value="<?php echo $xml->NAME; ?>" /></label>
			</p>

			<!-- Change Email -->
			<p>
				<label><?php i18n('LABEL_EMAIL'); ?>: <input autocomplete="off" class="text" name="useremail" type="email" value="<?php echo $xml->EMAIL; ?>" /></label>
			</p>

			<!-- Change Password -->
			<p>
				<label><?php i18n('PASSWORD'); ?>: <a href="#" class="show pass" tabindex="-1" title="<?php i18n('user_manager/SHOW'); ?>/<?php i18n('user_manager/HIDE'); ?>">#</a> <input autocomplete="off" class="text" name="userpassword" type="password" /></label>
			</p>

			<!-- Change Language -->
			<p>
				<label><?php i18n('LANGUAGE'); ?>:
					<select name="userlng" class="text"><?php echo $langs; ?></select>
				</label>
			</p>

			<!-- Change Timezone -->
			<p>
				<label><?php i18n('LOCAL_TIMEZONE'); ?>:
					<select class="text" name="ntimezone">
						<option value="<?php echo $xml->TIMEZONE; ?>" selected="selected"><?php echo $xml->TIMEZONE; ?></option>
						<?php echo $Timezone_Include; ?>
					</select>
				</label>
			</p>

		</div>

		<!-- HTML Editor Use Switch -->
		<p class="htmleditor">
			<label><?php i18n('ENABLE_HTML_ED'); ?> <input name="usereditor" type="checkbox" value="1" <?php echo $cchecked; ?> /></label>
		</p>

		<!-- User Bio Editor -->
		<p class="bioeditor">
			<label>
				<?php global $editor_id; $editor_id = (string) $xml->USR; ?>
				<?php i18n('user_manager/USER_BIO'); ?>:
				<span class="info" title="<?php i18n('user_manager/USERBIO_LABEL'); ?>">?</span>
				<a href="#" class="show edit" tabindex="-1" title="<?php i18n('user_manager/HTML_ED'); ?>">#</a>
				<textarea name="users_bio" class="text" id="bio-<?php echo $editor_id; ?>"><?php echo $xml->USERSBIO; ?></textarea>
				<!--<?php include USERMANAGER_PATH."ckeditor.php"; ?>-->
			</label>
		</p>

		<h4><?php i18n('user_manager/PERM') ?></h4>

		<!-- Permissions Checkboxes -->
		<p class="perm_div">
			<label><input type="checkbox" data-check="pages" name="Pages" value="no" <?php echo $pageschecked; ?> /> <?php i18n('TAB_PAGES'); ?></label>
			<label><input type="checkbox" data-check="files" name="Files" value="no" <?php echo $fileschecked; ?> /> <?php i18n('TAB_FILES'); ?></label>
			<label><input type="checkbox" data-check="theme" name="Theme" value="no" <?php echo $themechecked; ?> /> <?php i18n('TAB_THEME'); ?></label>
			<label><input type="checkbox" data-check="backups" name="Backups" value="no" <?php echo $backupschecked; ?> /> <?php i18n('TAB_BACKUPS'); ?></label>
			<label><input type="checkbox" data-check="plugins" name="Plugins" value="no" <?php echo $pluginschecked; ?> /> <?php i18n('PLUGINS_NAV'); ?></label>
			<label><input type="checkbox" data-check="support" name="Support" value="no" <?php echo $supportchecked; ?> /> <?php i18n('TAB_SUPPORT'); ?></label>
			<label><input type="checkbox" name="Settings" value="no" <?php echo $settingschecked; ?> /> <?php i18n('SETTINGS'); ?> <span class="info" title="<?php i18n('user_manager/WEBSETTINGS_LABEL'); ?>">?</span></label>
		</p>

		<!-- Landing Page Settings -->
		<p class="floated">
			<label><?php i18n('user_manager/LAND'); ?>: <span class="info" title="<?php i18n('user_manager/LANDINGPAGE_LABEL'); ?>">?</span>
				<select name="Landing" class="text landing">
					<option data-check="pages" value=""><?php i18n('TAB_PAGES'); ?></option>
					<!--<option value="pages.php"<?php echo ($landingselected == "pages.php")?' selected="selected"': ''; ?>><?php i18n('TAB_PAGES'); ?></option>  -->
					<option data-check="files" value="upload.php"<?php echo ($landingselected == "upload.php")?' selected="selected"': ''; ?>><?php i18n('TAB_FILES'); ?></option>
					<option data-check="theme" value="theme.php"<?php echo ($landingselected == "theme.php")?' selected="selected"': ''; ?>><?php i18n('TAB_THEME'); ?></option>
					<option data-check="backups" value="backups.php"<?php echo ($landingselected == "backups.php")?' selected="selected"': ''; ?>><?php i18n('TAB_BACKUPS'); ?></option>
					<option data-check="plugins" value="plugins.php"<?php echo ($landingselected == "plugins.php")?' selected="selected"': ''; ?>><?php i18n('PLUGINS_NAV'); ?></option>
					<option data-check="support" value="support.php"<?php echo ($landingselected == "support.php")?' selected="selected"': ''; ?>><?php i18n('TAB_SUPPORT'); ?></option>
					<option value="settings.php"<?php echo ($landingselected == "settings.php")?' selected="selected"': ''; ?>><?php i18n('TAB_SETTINGS'); ?></option>
					<option data-check="edit" value="edit.php"<?php echo ($landingselected == "edit.php")?' selected="selected"': ''; ?>><?php i18n('CREATE_NEW_PAGE'); ?></option>
				</select>
			</label>
		</p>

		<!-- Edit Pages and User Management Permissions -->
		<p class="perm_div">
			<label><input type="checkbox" data-check="edit" name="Edit" value="no" <?php echo $editchecked; ?> /> <?php i18n('user_manager/EDIT_PAGE'); ?></label>
			<label><input type="checkbox" name="Admin" value="no" <?php echo $adminchecked; echo ($xml->USR == $USR) ? 'disabled' : ''; ?>/> <?php i18n('user_manager/ADMIN'); echo ($xml->USR == $USR) ? ' <span class="info" title="' . i18n_r('user_manager/CANNOT_DENY') . '">?</span>' : ''; ?></label>

		</p>

	   <!-- Custom Permissions -->
		<div class="custom_perm_div">
			<?php UserManager_PermissionsRender($file); ?>
		</div>

		<!-- Submit -->
		<p class="submit_line">
			<input type="hidden" name="nano" value="<?php echo $xml->PWD; ?>"/>
			<input type="hidden" name="usernamec" value="<?php echo $xml->USR; ?>"/>
			<span><input class="submit" type="submit" name="edit-user" value="<?php i18n('user_manager/SAVE'); ?>"/></span>
			&nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp;
			<a class="cancel" href="#top"><?php i18n('CANCEL'); ?></a>
		</p>

		</form>

		<hr />
	</td>
</tr>

<?php } ?>

</table>

<!-- Below is the html form to add a new user. It is proccesed with 'readxml.php' -->
<div id="newuser" class="userform" style="display:none;">
	<form method="post" action="load.php?id=user_manager">
		<h3>
			<?php i18n('user_manager/ADDUSER'); ?>
		</h3>

		<div class="floated">

			<!-- Enter Username -->
			<p>
				<label><?php i18n('LABEL_USERNAME'); ?>: <input class="text" name="usernamec" type="text" pattern="[a-z0-9_][a-z0-9_-]*" placeholder="a-z  0-9  _  -" required /></label>
			</p>

			<!-- Enter Name -->
			<p>
				<label><?php i18n('LABEL_DISPNAME'); ?>: <input class="text" id="name" name="name" type="text" /></label>
			</p>

			<!-- Enter Email -->
			<p>
				<label><?php i18n('LABEL_EMAIL'); ?>: <input autocomplete="off" class="text" name="useremail" type="email" /></label>
			</p>

			<!-- Enter Password -->
			<p>
				<label><?php i18n('PASSWORD'); ?>: <a href="#" class="show pass" tabindex="-1" title="<?php i18n('user_manager/SHOW'); ?>/<?php i18n('user_manager/HIDE'); ?>">#</a> <input autocomplete="off" class="text" name="userpassword" type="password" pattern=".*" required /></label>
			</p>

			<!-- Enter Language -->
			<p>
				<label><?php i18n('LANGUAGE'); ?>:
					<select name="userlng" class="text"><?php echo $langs ?></select>
				</label>
			</p>

			<!-- Enter Timezone -->
			<p>
				<label><?php i18n('LOCAL_TIMEZONE'); ?>:
					<select class="text" name="ntimezone">
						<option value="<?php echo $inst_UserManager->userData('TIMEZONE', true); ?>"  selected="selected"><?php echo $xml->TIMEZONE; ?></option>
						<?php echo $Timezone_Include; ?>
					</select>
				</label>
			</p>

		</div>

		<!-- HTML Editor Use Switch -->
		<p class="htmleditor">
			<label><?php i18n('ENABLE_HTML_ED'); ?> <input name="usereditor" type="checkbox" value="1" checked="checked" /></label>
		</p>

		<!-- User Bio Editor -->
		<p class="bioeditor">
			<label>
				<?php global $editor_id; $editor_id = (string) '-new'; ?>
				<?php i18n('user_manager/USER_BIO'); ?>:
				<span class="info" title="<?php i18n('user_manager/USERBIO_LABEL'); ?>">?</span>
				<a href="#" class="show edit" tabindex="-1" title="<?php i18n('user_manager/HTML_ED'); ?>">#</a>
				<textarea name="users_bio" class="text" id="bio--new"></textarea>
				<!--<?php include USERMANAGER_PATH."ckeditor.php"; ?>-->
			</label>
		</p>

		<h4><?php i18n('user_manager/PERM'); ?></h4>

		<!-- Permissions Checkboxes -->
		<p class="perm_div">
			<label><input type="checkbox" data-check="pages" name="Pages" value="no" /> <?php i18n('TAB_PAGES'); ?></label>
			<label><input type="checkbox" data-check="files" name="Files" value="no" /> <?php i18n('TAB_FILES'); ?></label>
			<label><input type="checkbox" data-check="theme" name="Theme" value="no" checked="checked" /> <?php i18n('TAB_THEME'); ?></label>
			<label><input type="checkbox" data-check="backups" name="Backups" value="no" /> <?php i18n('TAB_BACKUPS'); ?></label>
			<label><input type="checkbox" data-check="plugins" name="Plugins" value="no" checked="checked" /> <?php i18n('PLUGINS_NAV'); ?></label>
			<label><input type="checkbox" data-check="support" name="Support" value="no" checked="checked" /> <?php i18n('TAB_SUPPORT'); ?></label>
			<label><input type="checkbox" name="Settings" value="no" checked="checked" /><?php i18n('SETTINGS'); ?> <span class="info" title="<?php i18n('user_manager/WEBSETTINGS_LABEL'); ?>">?</span></label>
		</p>

		<!-- Landing Page Settings -->
		<p class="floated">
			<label><?php i18n('user_manager/LAND'); ?>: <span class="info" title="<?php i18n('user_manager/LANDINGPAGE_LABEL'); ?>">?</span>
				<select name="Landing" class="text landing">
					<option data-check="pages" value=""><?php i18n('TAB_PAGES'); ?></option>
					<option data-check="files" value="upload.php"><?php i18n('TAB_FILES'); ?></option>
					<option data-check="theme" value="theme.php"><?php i18n('TAB_THEME'); ?></option>
					<option data-check="backups" value="backups.php"><?php i18n('TAB_BACKUPS'); ?></option>
					<option data-check="plugins" value="plugins.php"><?php i18n('PLUGINS_NAV'); ?></option>
					<option data-check="support" value="support.php"><?php i18n('TAB_SUPPORT'); ?></option>
					<option value="settings.php"><?php i18n('TAB_SETTINGS'); ?></option>
					<option data-check="edit" value="edit.php"><?php i18n('CREATE_NEW_PAGE'); ?></option>
				</select>
			</label>
		</p>

		<!-- Edit Pages and User Management Permissions -->
		<p class="perm_div">
			<label><input type="checkbox" data-check="edit" name="Edit" value="no" /> <?php i18n('user_manager/EDIT_PAGE'); ?></label>
			<label><input type="checkbox" name="Admin" value="no" checked="checked" /> <?php i18n('user_manager/ADMIN'); ?></label>
		</p>

		<!-- Custom Permissions -->
		<div class="custom_perm_div">
			<?php UserManager_PermissionsRender($file); ?>
		</div>

		<!-- Submit -->
		<p id="submit_line" >
			<span><input class="submit" type="submit" name="add-user" value="<?php i18n('user_manager/ADDUSER'); ?>" /></span>
			&nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp;
			<a class="cancel" href="#top"><?php i18n('CANCEL'); ?></a>
		</p>

	</form>
<hr />
</div>

<?php
}

function UserManager_interact(){
	$inst_UserManager = new UserManager;
	if(!isset($_POST['usernamec'])  && !isset($_GET['deletefile']) && !isset($_POST['add-user']) && !isset($_GET['download_id'])){
		UserManager_Render();
	}
	if(isset($_POST['edit-user'])){
		$inst_UserManager->ProcessEditUser();
	}
	if(isset($_GET['deletefile'])){
		$inst_UserManager->DeleteUser();
	}
	if(isset($_POST['add-user'])){
		$inst_UserManager->AddUser();
	}
	if(isset($_GET['download_id'])){
		$inst_UserManager->DownloadPlugin($_GET['download_id']);
	}
}

function UserManager_permissions(){
	$inst_UserManager = new UserManager;
	$inst_UserManager->CheckPermissions();
}

function UserManager_ProcessSettings(){
	$inst_UserManager = new UserManager;
	$inst_UserManager->ProcessSettings();
}

function UserManager_ResetPw(){
	$mm_resetpw = new UserManager;
	$mm_resetpw->ResetPw();
}

function UserManager_ProfileRender(){ // show User's Bio form on Settings (User's Profile) page
	i18n_merge('user_manager') || i18n_merge('user_manager', 'en_US');
	//Configure CK Editor for User's Bio
	global $EDHEIGHT, $EDTOOL, $EDLANG, $EDOPTIONS, $TOOLBAR;
	$EDHEIGHT = '300px';
	$EDTOOL = defined('GSEDITORTOOL') ? GSEDITORTOOL : 'basic';
	$EDLANG = defined('GSEDITORLANG') ? GSEDITORLANG : i18n_r('CKEDITOR_LANG');
	$EDOPTIONS = defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS) != '' ? ', ' . GSEDITOROPTIONS : '';

	if ($EDTOOL == 'advanced'){
		$TOOLBAR = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
	'/', ['Styles','Format','Font','FontSize']";
	}
	elseif ($EDTOOL == 'basic') {
		$TOOLBAR = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
	}
	else{
		$TOOLBAR = GSEDITORTOOL;
	}

	$inst_UserManager = new UserManager;
	?>
<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
<p>
	<?php global $editor_id; $editor_id = (string) ''; ?>
	<label for="bio-">
		<?php i18n('user_manager/USER_BIO'); ?>:
	</label>
	<textarea name="users_bio" id="bio-"><?php echo $inst_UserManager->userData('USERSBIO', true); ?></textarea>
	<?php include USERMANAGER_PATH."ckeditor.php"; ?>
</p>
<?php
}

function UserManager_PermissionsRender($file_path){
	global $permission_actions;
	if(is_array($permission_actions) && !empty($permission_actions)) {
		echo '<h4>'.i18n_r('user_manager/CUSTOM_PERM').'</h4>';
		echo '<p class="perm_div">';
		$userData = getXML($file_path);
		foreach ($permission_actions as $permission) {
			$permission_value = (string)$userData->PERMISSIONS->$permission['name'];
			$checked = ($permission_value == 'no') ? 'checked' : '';
			echo '<label><input type="checkbox" name="Custom-'.$permission['name'].'" value="no" '.$checked.' /> '.$permission['label'].'</label> ';
		}
		echo '</p>';
	}
}

function UserManager_PermissionsSave($settings_page=false){
	$inst_UserManager = new UserManager;
	global $xml, $perm, $permission_actions;
	if(is_array($permission_actions) && !empty($permission_actions)){
		if($settings_page == false){
			foreach ($permission_actions as $permission){
				if(isset($_POST['Custom-'.$permission['name']])){
					$perm->addChild($permission['name'], $_POST['Custom-'.$permission['name']]);
				} else	{$perm->addChild($permission['name'], '');}
			}
		} else	{
			foreach ($permission_actions as $permission) {
				$perm_value = $inst_UserManager->userData($permission['name']);
				$perm->addChild($permission['name'], $perm_value);
			}
		}
	}
}

/**
 * Add Custom Permission
 * This can be used by other plugins to add custom permission to the the user management section
 *
 * @param string $name Name of node to save permission as in user xml file
 * @param string $label The label that will be seen next to the permission on the "Edit User" page
 */
function UserManager_PermissionAdd($name, $label) {
	global $permission_actions;
	$permission_actions[] = array(
		'name' => $name,
		'label' => $label);
}

/**
 * Check individual user permission
 *
 * @param string $user the username to get permission
 * @param string $permission the permission to get - needs to be the name of the node in the user xml file
 * @return bool whether user is allowed
 */
function UserManager_PermissionGet($user, $permission){
	$inst_UserManager = new UserManager;
	return $inst_UserManager->GetUserPermission($user, $permission);
}

/**
 * Returns array of all user permissions
 *
 * @param string $user the username to get permissions
 * @param array the permissions
 */
//function check_user_permissions($user){
	//$inst_UserManager = new UserManager;
	//return $inst_UserManager->GetUserPermission($user);
//}
?>
