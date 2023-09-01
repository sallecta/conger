<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
class catalog
{
	private static $ready;
	public static function start()
	{
		if(!file_exists(PLGCATALOGFILE))
		{
			$pathpart=dirname(PLGCATALOGFILE);
			if (!is_dir($pathpart))
			{
				mkdir($pathpart, 0770, true);
			}
			$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
			if(XMLsave($xml, PLGCATALOGFILE))
			{
					echo '<div class="updated">', i18n_r(PLGID_CATALOG.'/WRITE_OK'), '</div>';
			}
		}
		self::$ready=true;
	}
	private static function adminHeader()
	{
		require_once PLG_CATALOG_PATH .'/render/admin_header.php';
	}
	
	public static function item_title( $arg_item )
	{
		str_replace('"', "&quot;", $atts['title']);
	}
	
	public static function processFAQData($edit=null,$delete_category=null,$edit_category=null,$delete_faq=null)
	{
		$faq_file = getXML(PLGCATALOGFILE);
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
		foreach($faq_file->category as $category)
		{	
			$c_atts= $category->attributes();
			if($delete_category != null && $delete_category == $c_atts['name'])
			{
				//Do nothing. Do not add it to new xml file
			}
			elseif($edit_category != null && $edit_category == $c_atts['name'])
			{
				$c_child = $xml->addChild('category');
				$c_child->addAttribute('name', $_POST['title']);
			}
			else
			{
				$c_child = $xml->addChild('category');
				$c_child->addAttribute('name', $c_atts['name']);
			}
			
			foreach($category->content as $content)
			{
				$atts= $content->attributes();
				if($edit != null && $c_atts['name'] == $_POST['category'] && $edit == $atts['title'])
				{
					$child = $c_child->addChild('content');
					$child->addAttribute('title', $_POST['title']);
					$child->addCData($_POST['contents']);
				}
				else
				{
					if($delete_faq != null && $_GET['category_of_deleted'] ==  $c_atts['name'] && $_GET['delete'] == $atts['title'])
					{
						//Do nothing. Do not add it to new xml file
					}
					else {
						$child = $c_child->addChild('content');
						$child->addAttribute('title', $atts['title']);
						$child->addCData($content);
					}
				}
			}
			
			if(isset($_POST['add_new_faq']) && $_POST['category'] ==  $c_atts['name'])
			{
				$child = $c_child->addChild('content');
				$child->addAttribute('title', $_POST['title']);
				$child->addCData($_POST['contents']);
			}
		}
		
		if(isset($_POST['new_category']))
		{
			$c_child = $xml->addChild('category');
			$c_child->addAttribute('name', $_POST['title']);
		}
		
		if(XMLsave($xml, PLGCATALOGFILE))
		{
			if($edit != null && $delete_category == null && $delete_faq == null)
			{
				echo '<div class="updated">', i18n_r(PLGID_CATALOG.'/EDIT_OK'), '</div>';
			}
			elseif($edit != null && $delete_faq != null)
			{
				echo '<div class="updated">', i18n_r(PLGID_CATALOG.'/TDELETED'), '</div>';
			}
			elseif($delete_category != null)
			{
				echo '<div class="updated">', i18n_r(PLGID_CATALOG.'/CATDELETED'), '</div>';
			}
			else
			{
				echo '<div class="updated">', i18n_r(PLGID_CATALOG.'/CATCREATED'), '</div>';
			}
		}
	}
	
	
	public static function getFAQData($attribute, $file_data)
	{
		$data_file = getXML(PLGCATALOGFILE);
		foreach($data_file->category as $category)
		{
			foreach($category->content as $faq)
			{
				$c_atts= $faq->attributes();
				if(isset($c_atts['title']) && $c_atts['title'] == $attribute)
				{
					if($file_data == 'title')
					{
						return $c_atts['title'];
					}
					elseif($file_data == 'category')
					{
						return $category;
					}
					else
					{
						return $faq;
					}
				}
			}
		}
	}
	
	
	public static function showEditFAQ($edit_faq=null)
	{
		if($edit_faq != null)
		{
			$faq_title = str_replace('"', "&quot;", self::getFAQData($edit_faq, 'title'));
			$faq_edit_add = i18n_r(PLGID_CATALOG.'/EDIT').$edit_faq;
			$faq_category = self::getFAQData($edit_faq, 'category');
			$faq_content = self::getFAQData($edit_faq, 'content');
			$add_new_hidden_field = '
			<input type="hidden" name="edit_faq" value="'.str_replace('"', "&quot;", $edit_faq).'" />
			<input type="hidden" name="old-title" value="'.$faq_title.'" />
			';
		}
		else
		{
			$faq_edit_add = i18n_r(PLGID_CATALOG.'/ADD_T'); 
			$faq_title = i18n_r(PLGID_CATALOG.'/TITLE');
			$faq_category = '';
			$faq_content = '';
			$add_new_hidden_field = '<input type="hidden" name="add_new_faq" />';
		}
		global $EDLANG, $EDOPTIONS, $toolbar, $EDTOOL, $SITEURL;
		include_once PLG_CATALOG_PATH .'/render/edit_faq.php';
	}
	
	public static function category_manager()
	{
		$faq_data = getXML(PLGCATALOGFILE);
		include_once PLG_CATALOG_PATH .'/render/category_manager.php';
	}
	
	public static function render_help()
	{
		include_once PLG_CATALOG_PATH .'/render/help.php';
	}
	public static function render_things_manager()
	{
		$faq_file = getXML(PLGCATALOGFILE);
		include_once PLG_CATALOG_PATH .'/render/things_manager.php';
	}

	public static function render_shortcode($display_category=null)
	{
		if($display_category == null)
		{
			$display_category = '';
		}	$data_file = getXML(PLGCATALOGFILE);
		$end_result = '';
		foreach($data_file->category as $category)
		{
			$c_atts= $category->attributes();
			if($display_category == null)
			{
				$end_result .= '<ul><li>'.$c_atts['name'].'<ul>';
				
				foreach($category->content as $content)
				{
					$atts = $content->attributes();
					$end_result .= '<li>'.$atts['title'].'<ul><li>'.$content.'</li></ul></li>';
				}
				$end_result .= '</ul></li></ul>';
			}
			elseif($display_category == $c_atts['name'])
			{
				$end_result .= '<ul><li>'.$c_atts['name'].'<ul>';
				foreach($category->content as $content)
				{
					$atts = $content->attributes();
					$end_result .= '<li>'.$atts['title'].'<ul><li>'.$content.'</li></ul></li>';
				}
				$end_result .= '</ul></li></ul>';
			}
		}
		return $end_result;
		
	}

	public static function shortcodes_get($content) 
	{
		//catalog::main();
		$the_callback = preg_match('/(<p>\s*)?{\$\s*([a-zA-Z0-9_]+)(\s+[^\$]+)?\s*\$}(\s*<\/p>)?/', $content, $matches);
		if(isset($matches[0]))
		{
			$display_category = str_replace('{$ ', '', $matches[0]);
			$display_category = str_replace(' $}', '', $display_category);
			$display_category = str_replace('<p>', '', $display_category);
			$display_category = str_replace('</p>', '', $display_category);
			$faq = self::render_shortcode($display_category);
			echo str_replace($matches[0],$faq,$content);
		}
		else
		{
			return $content;
		}
	}
	public static function main()
	{
		self::adminHeader();
		if(isset($_GET['add_faq']))
		{
			if(isset($_POST['add_new_faq']))
			{
				self::processFAQData();
			}
			self::showEditFAQ();
		}
		elseif(isset($_GET['edit_faq']))
		{
			if(isset($_POST['edit_faq']))
			{
				self::processFAQData($_POST['old-title']);
				self::showEditFAQ($_POST['title']);
			}
			else
			{
				//$FAQ->showEditFAQ(urldecode($_GET['edit_faq']));
				self::showEditFAQ(urldecode($_GET['edit_faq']));
			}
		}
		elseif(isset($_GET['faq_categories']))
		{
			if(isset($_POST['new_category']))
			{
				self::processFAQData();
			}
			elseif(isset($_GET['delete_category']))
			{
				self::processFAQData(null,$_GET['delete_category']);
			}
			elseif(isset($_POST['edit_category_name']))
			{
				self::processFAQData(null,null,$_POST['edit_category_name']);
			}
			self::category_manager();
		}
		elseif(isset($_GET['delete']) && isset($_GET['category_of_deleted']))
		{
			self::processFAQData(null,null,null,$_GET['delete']);
		}
		elseif(isset($_GET['faq_help']))
		{
			self::render_help();
		}
		else
		{
			self::render_things_manager();
		}
	}
}
catalog::start();
