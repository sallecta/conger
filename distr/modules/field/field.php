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
	public static $ui;
	public static $cfile;
	
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
		self::$cpath = av::get('cpath').'modules/'.self::$name.'/client';
		self::$ui = self::$cpath.'/field_admin.php';
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
		//self::$types = array();
		$fields = &self::$fields;
		//$types = &self::$types;
		$datafile = &self::$datafile;
		
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
					if ( $item_name == av::get('fcpath') )
					{
						continue;
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
					//$types[$field['key']] = $field['type'];
				}
			}
		}
		$predef='fields_predefined';
		$predef::add();
		//$types[av::get('fcpath')] = end($fields)['type'];
		self::$fields_total=count(self::$fields);
	} // get_fields_and_types()
	
	private static function ckeditor_config($a_editor)
	{
		//require_once(self::$spath.'ckeditor/ceditor_config.php');
	}
	private static function get_pages()
	{  
		if (function_exists('find_i18n_url') && class_exists('I18nNavigationFrontend'))
		{
			$slug = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['newid']) ? $_GET['newid'] : '');
			$pos = strpos($slug, '_');
			$lang = $pos !== false ? substr($slug, $pos+1) : null;
			$structure = I18nNavigationFrontend::getPageStructure(null, false, null, $lang);
			$pages = array();
			$nbsp = html_entity_decode('&nbsp;', ENT_QUOTES, 'UTF-8');
			$lfloor = html_entity_decode('&lfloor;', ENT_QUOTES, 'UTF-8');
			foreach ($structure as $page)
			{
				$text = ($page['level'] > 0 ? str_repeat($nbsp,5*$page['level']-2).$lfloor.$nbsp : '').cl($page['title']);
				$link = find_i18n_url($page['url'], $page['parent'], $lang ? $lang : return_i18n_default_language());
				$pages[] = array($text, $link);
			}
			return json_encode($pages);
		}
		else
		{
			return list_pages_json();
		}
	}
	// indexing content for I18N Search plugin. $item is of type I18nSearchPageItem.
	public static function at_search_index_page($item)
	{
		$fields = &self::$fields;
		foreach ($fields as $field)
		{
			if ( isset($field['index']) )
			{
				$name = @$field['key'];
				if (@$field['type'] == 'web_editor')
				{
					$item->addContent($name, html_entity_decode(strip_tags($item->$name), ENT_QUOTES, 'UTF-8'));
				}
				else if (@$field['type'] == 'checkbox')
				{
					if ($item->$name)
					{
						$item->addTags($name, array($name));
					}
				}
				else
				{
					$item->addContent($name, html_entity_decode($item->$name, ENT_QUOTES, 'UTF-8'));
				}
			}
		}
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
			if ( in_array( $inkey, self::$fields_predef) )
			{
				continue;
			}
			if ( in_array( $inkey, self::$fields_predef) )
			{
				continue;
			}
			$item = $data->addChild('item');
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
		XMLsave( $data, self::$datafile );
		return true;
	} // save_fields
	
	public static function get( $a_key, $a_default=null, $a_print=null )
	{
		foreach ( self::$fields as $key=>$subkey )
		{
			if ( $subkey['key'] == $a_key )
			{
				$out = $subkey['value'];
				break;
			}
		}
		if ( empty($out) )
		{
			if ( !empty($a_default) ){ $out = $a_default; }
			else { $out = ''; }
		}
		if ( $a_print ) { echo "$out"; }
		else { return $out; }
	}// get
	
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
					$a_out_value = $subkey['value'];
					$found=True;
					break;
				}
			}
		}
		if ( !$found )
		{ return False; }
		else
		{
			if ( !empty($a_out_value) )
			{ return True; }
			else {return False;}
		}
	} // get_by_ref
	
	public static function undo()
	{
		return copy( self::$backupfile, self::$datafile );
	}
	
	private static function value( $a_key, $a_default=null, $a_print=null )
	{ //dummy
		return basename(__FILE__).': '.__LINE__ .": emm";
	}
	private static function byref( $a_key, &$a_out, $a_opt )
	{ //dummy
		$a_out=basename(__FILE__).': '.__LINE__ .": emm";
		return true;
	}
	
	public static function at_edit_extras()
	{
		include(self::$spath.'at_events/edit_extras.php');
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
	
	public static function at_nav_tab()
	{
		global $USR;
		$ins['title']=i18n_r('field/admin_title');
		$ins['acces_key']=find_accesskey($ins['title']);
		$ins['link']=self::$ui;
	?>
	<li id="nav_field" ><a class="field_admin" href="<?php echo $ins['link'];?>" accesskey="<?php echo $ins['acces_key'];?>"><?php echo $ins['title'];?></a></li>
	<?php
	} // at_nav_tab
	
	public static function at_content($a_content) 
	{
		$tagO='\[{1}\${1}';
		$tagC='\${1}\]{1}';
		$tagName='([\w\=\,\(\)]{1,200})';
		$regex='/'.$tagO.$tagName.$tagC.'/';
		
		//$a_content='[$cpath$][$name(key=val,key=val,key=val)$][$name2_key2=val,key3=val,key4=val)$]'; 
		preg_match_all($regex,$a_content, $matches, PREG_SET_ORDER);
		if (empty($matches))
		{
			//echo 'no matches';
			return $a_content;
		}
		$arg_chars=[ '=' , '(' , ')' , ';' ];
		foreach ( $matches as $ndx => $key )
		{
			$shorcode = &$key[0];
			$field_name = &$key[1];
			$minshc=2;
		
			$ab_st=strpos($field_name, '(',$minshc);
			$ab_en=strpos($field_name, ')',-1);
			$args_block_exists = ( $ab_st && $ab_en );
			if ($args_block_exists)
			{
				//echo "--args block exists:  ". json_encode($args_block_exists). " in ".$field_name." \n";
				$args = substr($field_name,$ab_st+1);
				$args = substr($args,0,-1);
				$args = explode(',',$args);
				foreach ( $args as $key=>$value)
				{
					$kv = explode('=',$value);
					if (count($kv)!==2)
					{
						$args=false;
						break;
					}
					$args[$kv[0]]=$kv[1];
					unset($args[$key]);
				}
				$field_name=substr($field_name,0,$ab_st);
			}
			else
			{
				//echo "--args_block_exists:  ". json_encode($args_block_exists). " in ".$field_name." \n";
				$args=false;
				foreach (mb_str_split($field_name) as $char)
				{
					if ( in_array($char, $arg_chars, true) )
					{
						//echo '--bad char ['.$char.'] in shc: '.$field_name."\n";
						$field_name=false;
						break;
					}
				}
			} // no arg block
			//unset($instr);
			if ($field_name && !$args)
			{
				if (field::get_by_ref($field_name,$outval) )
				{
					$a_content = str_replace($shorcode,$outval,$a_content);
					return $a_content;
				}
			}
			if ($field_name && $args)
			{
				if (field::get_by_ref($field_name,$outval,$args) )
				{
					$a_content = str_replace($shorcode,$outval,$a_content);
					return $a_content;
				}
			}
			else
			{
				return $a_content;
			}
			//echo "field_name:  ". json_encode($field_name). " \n";
			//echo "args:  ". json_encode($args). " \n";
		} // foreach ( $matches as $ndx => $key )
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

// add item to admin nav tab
event::join('nav-tab', ['field','at_nav_tab']); 
// add search (not implemented)
if ( field::$issearch )
{
	filter::join('search-index-page', ['field','at_search_index_page']);
}
// apply shortcodes in content
filter::join('content',['field','at_content']);




