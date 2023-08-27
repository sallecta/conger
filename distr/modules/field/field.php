<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php 

# get correct id for plugin
$mod_id = basename(__FILE__, ".php");


require_once('fields_predefined.php');
class field
{
	private static $ready;
	
	public static $file;
	public static $name;
	public static $spath;
	public static $cpath;
	public static $cfile;
	
	public static $active;
	
	public static $fields;
	public static $fields_total;
	public static $types;
	public static $datafile;
	public static $backupfile;
	
	public static $cmsg;
	
	private static $fields_predef;
	private static $fields_sys;
	
	public static $issearch;
	
	
	
	
	public static function setup()
	{
		self::$file = __FILE__;
		self::$name = basename(self::$file,'.php');
		self::$spath = av::get('spath').'modules/'.self::$name.'/';
		self::$cpath = av::get('cpath').'modules/'.self::$name.'/';
		self::$datafile = av::get('spath_data').'field/fields.xml';
		self::$backupfile = av::get('spath_backup').'field/fields.xml';
		self::$fields_predef = ['cpath','pages'];
		self::$fields_sys = ['pubDate','title','url','meta','metad','menu','menuStatus','menuOrder',
		'template','parent','content','private','creDate','user'];
		self::$issearch = defined('I18N_ACTION_INDEX');
		self::get_fields_and_types();
		//
		if ( !is_dir( dirname(self::$backupfile) ) )
		{
			mkdir( dirname(self::$backupfile), 0777, true);
		}
	}
	public static function get_fields_and_types()
	{
		self::$fields = array();
		$fields = &self::$fields;
		$datafile = &self::$datafile;
		$pfields_added = 0;
		
		if ( !file_exists($datafile) )
		{
			self::$state='<pre>'.basename(__FILE__) .': '.__LINE__ .': datafile not found: '.$datafile.'</pre>';
			$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
			if ( !is_dir( dirname($datafile) ) )
			{
				mkdir( dirname($datafile), 0777, true);
			}
			XMLsave($data, $datafile);
		}
		else
		{
			$data = getXML($datafile);
			$items = $data->item;
			if (count($items) > 0)
			{
				foreach ($items as $item)
				{
					$item_name = (string)$item->key;
					$field = array();
					if ( in_array( $item_name, self::$fields_predef) )
					{
						$pitem=fields_predefined::ret($item_name);
						$field['key'] = $item_name;
						$field['about'] = $pitem['about'];
						$field['scope'] = $pitem['scope'];
						$field['type'] = $pitem['type'];
						$field['value'] = $pitem['value'];
						$pfields_added=$pfields_added+1;
					}
					else
					{
						$field['key'] = $item_name;
						$field['about'] = (string) $item->about;
						$field['scope'] = (string) $item->scope;
						$field['type'] = (string) $item->type;
						$field['value'] = (string) $item->value;
					}
					if ($item->type == "dropdown")
					{
						$field['options'] = array();
						foreach ($item->option as $option)
						{
							$field['options'][] = (string) $option;
						}
					}
					$field['index'] = (bool) $item->index;
					$fields[] = $field;
				}
			}
		}
		//$predef='fields_predefined';
		if ( count( self::$fields_predef ) != $pfields_added )
		{
			fields_predefined::add();
		}
		self::$fields_total=count(self::$fields);
	} // get_fields_and_types()
	
	private static function ckeditor_config($a_editor)
	{
		//require_once(self::$spath.'ckeditor/ceditor_config.php');
	}
	
	public static function fields_sys_in_request()
	{
		$fields_sys = &self::$fields_sys;
		$names = array(); 
		for ($i=0; isset($_POST['field_'.$i.'_key']); $i++)
		{
			if (in_array($_POST['field_'.$i.'_key'], $fields_sys))
			{
				$names[] = $_POST['field_'.$i.'_key'];
			}
		}
		return count($names) > 0 ? $names : null;
	}
	
	public static function save_fields()
	{
		if ( file_exists( self::$datafile ) )
		{
			if ( !copy( self::$datafile, self::$backupfile ))
			{
				return false;
			}
		}
		$data = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
		for ($i=0; isset($_POST['field_'.$i.'_key']); $i++)
		{
			$inkey=&$_POST['field_'.$i.'_key'];
			if ( empty($inkey) )
			{
				continue;
			}
			$item = $data->addChild('item');
			if ( in_array( $inkey, self::$fields_predef) )
			{
				$pitem=fields_predefined::ret($inkey);
				$item->addChild('key')->addCData($inkey);
				$item->addChild('about')->addCData('predefined');
				$item->addChild('scope')->addCData('predefined');
				$item->addChild('type')->addCData('predefined');
				$item->addChild('value')->addCData('predefined');
			}
			else
			{
				$item->addChild('key')->addCData(htmlspecialchars(stripslashes($_POST['field_'.$i.'_key']), ENT_QUOTES));
				$item->addChild('about')->addCData(htmlspecialchars(stripslashes($_POST['field_'.$i.'_about']), ENT_QUOTES));
				$item->addChild('scope')->addCData(htmlspecialchars(stripslashes($_POST['field_'.$i.'_scope']), ENT_QUOTES));
				$item->addChild('type')->addCData(htmlspecialchars(stripslashes($_POST['field_'.$i.'_type']), ENT_QUOTES));
				if ($_POST['field_'.$i.'_value'])
				{
					$item->addChild('value')->addCData(htmlspecialchars(stripslashes($_POST['field_'.$i.'_value']), ENT_QUOTES));
				}
				if ($_POST['field_'.$i.'_options'])
				{
					$options = preg_split("/\r?\n/", rtrim(stripslashes($_POST['field_'.$i.'_options'])));
					foreach ($options as $option)
					{
						$item->addChild('option')->addCData(htmlspecialchars($option, ENT_QUOTES));
					} 
				}
				if ($_POST['field_'.$i.'_index'])
				{
					$item->addChild('index')->addCData(1);
				}
			}
		}
		XMLsave( $data, self::$datafile );
		return true;
	} // save_fields
	
	public static function get_by_ref( $a_key, &$a_out_value, $a_fnargs=false )
	{
		$found=False;
		foreach ( self::$fields as $key=>$subkey )
		{
			if ( $subkey['key'] == $a_key )
			{
				if ( is_array($a_fnargs) )
				{
					if ( !method_exists('fields_predefined',$subkey['value']) )
					{
						$found=False;
						break;
					}
					$a_out_value = call_user_func('fields_predefined::'.$subkey['value'],$a_fnargs);
					$found=True;
					break;
				}
				else
				{
					$a_out_value = html_entity_decode($subkey['value']);
					$found=True;
					break;
				}
			}
		}
		if ( !$found )
		{
			return False;
		}
		else
		{
			return True;
		}
	} // get_by_ref
	
	public static function get( $a_key, $args=false )
	{
		self::get_by_ref( $a_key, $out_value, $args );
		return $out_value;
	}// get
	
	public static function undo()
	{
		return copy( self::$backupfile, self::$datafile );
	}
	
	public static function shortcode( &$a_content )
	{
		require_once(self::$spath.'/field_shortcode_parse.php');
		
		for ($ndx = 1; $ndx <= 10; $ndx++)
		{
			//dev::ehtmlcom("$ndx: working on $a_content");
			$result = parse($a_content);
			if ( !$result )
			{
				if ( $ndx > 1 ) { $result=true; break; }
				dev::ehtmlcom(['failed shortcode',mb_substr($a_content, 0, 15).'...']);
				break;
			}
		}
		//dev::ehtmlcom(["finished on $a_content",'result=',$result]);
		return $result;
	}
	
	public static function at_edit_extras()
	{
		require(self::$spath.'at_events/edit_extras.php');
	}
	
	public static function at_changedata_save() // at page save
	{
		global $USR, $xml; // SimpleXML to save to
		$fields = &self::$fields;
		//dev::epre(['$fields',$fields,'self::$fields_total',self::$fields_total]);exit;
		if ( self::$fields_total > 0)
		{
			foreach ($fields as $field)
			{
				$key = $field['key'];
				if ( $key == av::get('fcpath') )
				{
					continue;
				}
				if(isset($_POST['post-'.strtolower($key)]))
				{
					$xml->addChild(strtolower($key))->addCData(stripslashes($_POST['post-'.strtolower($key)]));	
				}
			}
		}
		// new field for creation date
		if (!isset($xml->creDate))
		{
			if ($_POST['creDate'])
			{
				$xml->addChild('creDate', $_POST['creDate']);
			}
			else
			{
				$xml->addChild('creDate', (string) $xml->pubDate);
			}
		}
		// new field for user
		if (isset($USR) && $USR && !isset($xml->user))
		{
			$xml->addChild('user')->addCData($USR);
		}
	}
	public static function at_client_admin_head() // inside header tag of admin page
	{
		require(field::$spath.'at_events/client_admin_head.tpl.php');
	}
	public static function at_client_admin_body_end() // admin page body tag end
	{
		require(field::$spath.'at_events/client_admin_body_end.tpl.php');
	}
	public static function at_nav_tab()
	{
		global $USR;
		$ins['title']=i18n_r('field/admin_title');
		$ins['acces_key']=find_accesskey($ins['title']);
		$ins['link']=self::$cpath.'admin/field_admin.php';;
		require(self::$spath.'at_events/nav_tab.tpl.php');
	} // at_nav_tab
	
	public static function at_content($a_content) 
	{
		require(field::$spath.'at_events/content.php');
		return $a_content;
	} // at_content
}// field
field::setup();

lang_merge(__FILE__);

// make custom field values available to theme
//event::join('index-pretemplate', 'field_at_index_pretemplate');

// create new inputs on the edit page -> page options screen.
event::join('edit-extras', ['field','at_edit_extras']);

// save custom field values 
event::join('changedata-save', ['field','at_changedata_save']);

event::join('ev_client_admin_head', ['field','at_client_admin_head']);
event::join('client_admin_body_end', ['field','at_client_admin_body_end']);

// add item to admin nav tab
event::join('nav-tab', ['field','at_nav_tab']); 

// apply shortcodes in content
filter::join('content',['field','at_content']);




