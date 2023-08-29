<?php
class th 
{
	public static $cpath, $lang,$title,$sitename,$title_tag, $favicon,
	$img_wide,$logo,$site_role,$nav,$sidebar2,$sidebar1;
	public static function setup()
	{
		global $TEMPLATE, $LANG, $data_index;
		self::$cpath = av::get('cpath_themes').$TEMPLATE.'/';
		self::$lang = &$LANG;
		self::$title = get_page_clean_title(false);
		self::$sitename = get_site_name(false);
		//dev::ehtmlcom($data_index->url->__toString());
		if($data_index->url->__toString() == 'index' )
		{
			self::$title_tag=self::$title;
		}
		else
		{
			self::$title_tag=self::$title .' | '.self::$sitename;
		}
		// favicon
		if( self::fgr('favicon',self::$favicon) )
		{
			if ( !empty(self::$favicon) )
			{
				self::$favicon=av::get('cpath').self::$favicon;
			}
		}
		else
		{
			self::$favicon=self::$cpath.'client/images/icons/favicon.png';
		}
		// img_wide
		self::fgr('img_wide',self::$img_wide);
		if (self::$img_wide)
		{
			self::$img_wide=av::get('cpath').self::$img_wide;
		}
		// site_logo
		if( self::fgr('site_logo',self::$logo) )
		{
			if ( !empty(self::$logo) )
			{
				self::$logo=av::get('cpath').self::$logo;
			}
		}
		else
		{
			self::$logo=self::$cpath.'client/images/icons/logo.png';
		}
		// site_role
		self::fgr('site_role',self::$site_role);
		// nav
		self::$nav = nested_menu::ret(['ntnd'=>4]);
		// sidebar2
		field::get_by_ref('sidebar1', self::$sidebar1);
		field::get_by_ref('sidebar2', self::$sidebar2);
	} // setup
	public static function fgr($a_name, &$a_out)
	{
		return field::get_by_ref($a_name, $a_out);
	}
	public static function sc(&$a_out)
	{
		return field::shortcode($a_out);
	}
	public static function fg($a_name, $args=false)
	{
		return field::get($a_name, $args);
	}
	public static function breadcrumbs()
	{
		global $data_index;
		global $pagesArray;
		$home_title=$pagesArray['index']['title'];
		$ake='array_key_exists';
		$a_subonly=self::fg('breadcrumbs_subonly');
		$curr_parent=$data_index->parent->__toString();
		$curr_title = $data_index->title;
		$curr_slug = $data_index->url->__toString();
		$curr_path = av::cpath_to($curr_slug);
		foreach ( menu_data() as $menu_k=>$menu_item )
		{
			if ( $curr_slug !== $menu_item['slug'] )
			{
				continue;
			}
			$out='<p>';
			if ( $menu_item['slug'] !== 'index' )
			{
				$out=$out.'<a href="'.av::get('cpath').'">'.$home_title.'</a> / ';
			}
			if ( !empty($curr_parent) )
			{
				$par_path = av::cpath_to($pagesArray[$curr_parent]['url']);
				$p_title =$pagesArray[$curr_parent]['title'];
				$out=$out.'<a href="'.$par_path.'">'.$p_title.'</a> / ';
			}
			if ( empty($curr_parent) && $a_subonly=='Y' )
			{
				return '';
			}
			$out = $out."<a href=\"$curr_path\">$curr_title</a>";
			return $out.'</p>';
		}
		return '';
	} // breadcrumbs
}
th::setup();
