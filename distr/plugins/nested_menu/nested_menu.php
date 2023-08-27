<?php if(!defined('APP')){ die('you cannot load this page directly.'); }?>
<?php
class nested_menu
{
	private static $ready;
	
	public static $file;
	public static $name;
	public static $spath;
	public static $cpath;
	
	public static function setup()
	{
		if ( self::$ready ) { return; }
		self::$file = __FILE__;
		self::$name = basename(self::$file,'.php');
		self::$spath = av::get('spath').'plugins/'.self::$name.'/';
		self::$cpath = av::get('cpath').'plugins/'.self::$name.'/';
		self::$ready = true;
	}
	
	private static function nested_menus__cache_file($cachepath, $content)
	{ // ?? 
		//Check if cache folder exists.
		if (is_dir(GSDATAOTHERPATH.'nested_menu_cache')==false)
		{
			mkdir(GSDATAOTHERPATH.'nested_menu_cache', 0755) or exit('Unable to create nested_menu_cache folder');
		}
		
		//Save cached child menu file.
		$fp = @fopen($cachepath, 'w') or exit('Unable to save ' . $cachepath);
		fwrite($fp, $content);
		fclose($fp);
	} // nested_menus__cache_file
	
	public static function ev_nested_menu_cache_clear() 
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
	} // ev_nested_menu_cache_clear
	
	private static function sort_parents_first($a, $b)
	{
		if (empty($a['parent_slug']) && empty($b['parent_slug']))
		{
			return 0;
		}
		elseif(empty($a['parent_slug']))
		{
			return -1;
		}
		else return 1;
	} // sort_parents_first
	
	private static function sort_by_menu_priority($a, $b)
	{
		if (intval($a['menu_priority']) == intval($b['menu_priority']))
		{
			return 0;
		}
		return (intval($a['menu_priority']) < intval($b['menu_priority'])) ? -1 : 1;
	} // sort_by_menu_priority
	
	private static function data_get()
	{
		$menu_data = menu_data();
		usort($menu_data, ['nested_menu','sort_parents_first']);
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
		usort($nested_menu_data, ['nested_menu','sort_by_menu_priority']);
		foreach($nested_menu_data as $key => $item)
		{
			$children = $nested_menu_data[$key]['children'];
			if (sizeof($children))
			{
				usort($children, ['nested_menu','sort_by_menu_priority']);
				$nested_menu_data[$key]['children'] = $children;
			}
		}
		return $nested_menu_data;
	} // data_get
	
	public static function ret_ul($echo = true) 
	{
		
	}
	public static function ret(  $arg=['ntnd'=>0] ) 
	{
		function ntnd( $a_times )
		{
			if (  $a_times<1 ) { return $a_times; }
			return str_repeat("\t",$a_times);
		}
		$nt=$arg['ntnd'];
		$active_page=return_page_slug();
		$cachepath = GSDATAOTHERPATH.'nested_menu_cache/'.$active_page.'.cache';
		/**/
		//if (is_file($cachepath)) //We have a cached file, use it.
		//{
			//echo file_get_contents($cachepath);
			//return file_get_contents($cachepath);
		//}
		/**/
		//We do not have a cached file<s>, create a new one<s>.
		$nested_menu_data = self::data_get();//nested_menu_data();
		if ( count($nested_menu_data) < 1 )
		{
			dev::ehtmlcom('no menu data');
			return '';
		}
		$items = array();
		$items[] = ntnd($nt).'<ul>';
		foreach($nested_menu_data as $key => $item)
		{
			$num_items = sizeof($nested_menu_data);
			//dev::ehtmlcom(['$key',$key,'$num_items',$num_items]);
			$num_children = sizeof($item['children']);
			$classes = array($item['slug']);
			if ($key == 0)
			{
				$classes[] = 'first';
			}
			elseif($key == ($num_items - 1))
			{
				$classes[] = 'last';
			}
			if ($num_children)
			{
				$classes[] = 'submenu';
			}
			if ($item['slug'] == $active_page)
			{
				$classes[] = 'current';
			}
			$string = ntnd($nt+1).'<li class="%s"><a href="'. $item['url'] . '" title="'. strip_quotes(stripslashes($item['title'])) .'">'.stripslashes($item['menu_text']).'</a>';
			if ($num_children)
			{
				$string = $string . "\n".ntnd($nt+1)."<ul>";
				foreach ($item['children'] as $sub_key => $sub_item)
				{
					$string .= "\n".ntnd($nt+2)."<li class=\"" . $sub_item['slug'];
					if ($sub_key == 0) { $string .= ' first'; }
					elseif($sub_key == ($num_children - 1)) { $string .= ' last'; }
					if ($sub_item['slug'] == $active_page)
					{
						$classes[] = 'current_parent';
						$classes[] = 'current';
						$string .= ' current';
					}
					$string .= '"><a href="'. $sub_item['url'] . '" title="'. strip_quotes(stripslashes($sub_item['title'])) .'">'.stripslashes($sub_item['menu_text']).'</a></li>';
				}
				$string .= "\n".ntnd($nt+1)."</ul>";
			}
			else
			{
				$string .= '</li>';
			}
			$items[] = sprintf($string, implode(' ', $classes));
		}
		$items[] = ntnd($nt).'</ul>';
		$items = implode("\n", $items);
		return dev::rhtmlcom('not cached'). $items;
	} // ret
	
	public static function main()
	{
		//dev::ehtmlcom('main');
		event::join('changedata-save',['nested_menu','ev_nested_menu_cache_clear']);
		event::join('page-delete', ['nested_menu','ev_nested_menu_cache_clear']);
		event::join('cache-delete', ['nested_menu','ev_nested_menu_cache_clear']);
	}
} // nested_menu
nested_menu::setup();
