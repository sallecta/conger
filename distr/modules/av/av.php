<?php if(!defined('APP')){ die('you cannot load this page directly.'); }

require_once(dirname(__FILE__).'/path.php');
class av extends path// app variables
{
	private static $ready;
	//
	//builtin fields
	private static $fcpath='cpath';
	//
	private static $cpath; // root path of Conger from client (frontend) side
	private static $cadmin; // root path of Conger from client (frontend) side
	private static $ctheme;
	private static $dev=false; // development mode
	//
	private static $cpath_shortcode='[$cpath$]';
	private static $cpath_admin;
	private static $cpath_admintemplate;
	private static $cpath_themes;
	private static $cpath_modules;
	private static $cpath_modules_client;
	private static $cpath_data;
	private static $cpath_data_uploads;
	private static $cpath_data_thumbs;
	//
	private static $cmode='html';
	private static $spath;
	private static $spath_data;
	private static $spath_data_pages;
	private static $spath_data_cache;
	private static $spath_admin;
	private static $spath_admin_inc;
	private static $spath_backup;
	private static $spath_admintemplate;
	private static $spath_themes;
	private static $spath_plugins;
	private static $spath_data_uploads;
	private static $spath_data_thumbs;
	//
	private static $http_header;
	//
	private static $name;
	private static $version;
	private static $url;
	//
	private static $site_name;
	//
	private static $ro;
	
	public static function setup()
	{
		if ( self::$ready ) { return; }
		self::$name='Conger';
		self::$version='3.4.3';
		self::$url='https://github.com/sallecta/conger'; 
		self::$cpath=path::get('client'); 
		self::$cadmin=self::$cpath.'admin/'; 
		self::$ctheme; //theme client path
		self::$spath=path::get('server');
		self::$spath_data=self::$spath.'data/';
		self::$spath_data_pages=self::$spath_data.'/pages/';
		self::$spath_data_cache=self::$spath_data.'/cache/';
		self::$spath_backup=self::$spath.'backups/';
		self::$spath_admin=self::$spath.'admin/';
		self::$spath_admintemplate=self::$spath.'admin/template/';
		self::$spath_themes=self::$spath.'theme/';
		self::$spath_plugins=self::$spath.'plugins/';
		self::$spath_admin_inc=self::$spath.'admin/inc/';
		self::$spath_data_uploads=self::$spath.'data/uploads/';
		self::$spath_data_thumbs=self::$spath.'data/thumbs/';
		//
		self::$cpath_admin=self::$cpath.'admin/';
		self::$cpath_admintemplate=self::$cpath.'admin/template/';
		self::$cpath_themes=self::$cpath.'theme/';
		self::$cpath_modules=self::$cpath.'modules/';
		self::$cpath_modules_client=self::$cpath.'modules/client/';
		self::$cpath_data=self::$cpath.'data/';
		self::$cpath_data_uploads=self::$cpath.'data/uploads/';
		self::$cpath_data_thumbs=self::$cpath.'data/thumbs/';
		//
		self::$http_header=Null;
		//
		self::$site_name='';
		/**/
		self::$ro = array('cpath','version','name');
		self::$ready=true;
	}
	
	public static function get( $a_name )
	{
		if ( property_exists( 'av', $a_name ) )
		{
			return self::$$a_name;
		}
		else
		{
			if ( self::$dev )
			{
				self::echo_and('stop', __METHOD__ .": bad a_name: $a_name", $args['bt'] );
			}
		}
	}

	public static function set( $a_name, $a_value )
	{
		if ( property_exists( 'av', $a_name ) )
		{
			if ( in_array($a_name, self::$ro) )
			{
				if ( self::$dev )
				{
					self::echo_and('stop', __METHOD__ .": $a_name is read only",debug_backtrace() );
				}
				else
				{
					return;
				}
			}
			self::$$a_name = $a_value;
		}
		else
		{
			if ( self::$dev )
			{
				self::echo_and( 'stop',__METHOD__ .": bad a_name: $a_name",debug_backtrace() );
			}
		}
	}
	
	public static function cpath_to( $a_name )
	{
		if ( gettype($a_name) == 'string' )
		{
			return self::$cpath . $a_name . '/';
		}
		else
		{
			if ( self::$dev )
			{
				$md = __METHOD__; $bt = debug_backtrace();
				$type = gettype($a_name);
				self::echo_and( 'stop',"$md: bad key type: $a_name: $type", $bt );
			}
		}
	}
	private static function msg($a_msg, $a_bt)
	{
		$caller='somescript';
		if (!empty($a_bt[0]) && is_array($a_bt[0]))
		{
			$caller = basename($a_bt[0]['file']).": ". $a_bt[0]['line'];
			
		}
		$out=basename(__FILE__).": $a_msg ($caller)";
		if ( self::$cmode == 'json' )
		{ return json_encode($out); }
		else { return $out; }
	}
	private static function echo_and($a_opts=false, $a_msg, $a_bt)
	{
		echo self::msg($a_msg, $a_bt);
		if ( $a_opts === 'stop' ) { exit; }
	}
} // class av extends path
av::setup();

/* GetSimple compatability */

global $SITEURL,$ASSETURL;
$SITEURL=av::get('cpath');
$ASSETURL=av::get('cpath');

function get_root_path()
{
	return av::get('spath');
}

function tsl($a_path)
{ 
/* trailing slash */
	if ( substr("a_path", -1) !== '/' ) { return $a_path.'/'; }
	else { return $a_path; }
}
function get_admin_path()
{
	return av::get('spath').'admin';
}
function isFile($file, $path, $type = 'xml')
{
	if( is_file(tsl($path) . $file) && $file != "." && $file != ".." && (strstr($file, $type))  ) {
		return true;
	} else {
		return false;
	}
}
define('GSROOTPATH', av::get('spath'));
define('GSADMINPATH', av::get('spath').'admin/');
define('GSADMININCPATH', av::get('spath').'admin/inc/');
define('GSPLUGINPATH', av::get('spath').'plugins/');
define('GSLANGPATH', av::get('spath').'admin/lang/');
define('GSDATAPATH', av::get('spath').'data/');
define('GSDATAOTHERPATH', av::get('spath').'data/other/');
define('GSDATAPAGESPATH', av::get('spath').'data/pages/');
define('GSDATAUPLOADPATH', av::get('spath'). 'data/uploads/');
define('GSTHUMBNAILPATH', av::get('spath'). 'data/thumbs/');
define('GSBACKUPSPATH', av::get('spath'). 'backups/');
define('GSTHEMESPATH', av::get('spath'). 'theme/');
define('GSUSERSPATH', av::get('spath'). 'data/users/');
define('GSBACKUSERSPATH', av::get('spath'). 'backups/users/');
define('GSCACHEPATH', av::get('spath'). 'data/cache/');
define('GSAUTOSAVEPATH', av::get('spath'). 'data/pages/autosave/');

$site_version_no    = av::get('version');
define('GSVERSION', av::get('version'));

function get_theme_url($echo=true)
{
	global $TEMPLATE;
	$myVar = trim(av::get('cpath')."theme/$TEMPLATE");
	if ($echo)
	{
		echo $myVar;
	}
	else 
	{
		return $myVar;
	}
}
