<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }



function get_page_content() {
	global $content;
	event::create('content-top');
	$content = strip_decode($content);
	$content = filter::create('content',$content);
	if(getDef('GSCONTENTSTRIP',true)) $content = strip_content($content);
	echo $content;
	event::create('content-bottom');
}


function get_page_excerpt($len=200, $striphtml=true, $ellipsis = '...') {
	GLOBAL $content;
	if ($len<1) return '';
	$content_e = strip_decode($content);
	$content_e = filter::create('content',$content_e);
	if(getDef('GSCONTENTSTRIP',true)) $content_e = strip_content($content_e);	
	echo getExcerpt($content_e, $len, $striphtml, $ellipsis);
}


/**
 * Get Page Meta Keywords
 *
 * @since 2.0
 * @uses $metak
 * @uses strip_decode
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_meta_keywords($echo=true) {
	global $metak;
	$myVar = encode_quotes(strip_decode($metak));
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Meta Description
 *
 * @since 2.0
 * @uses $metad
 * @uses strip_decode
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_meta_desc($echo=true) {
	global $metad;
	$myVar = encode_quotes(strip_decode($metad));
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Title
 *
 * @since 1.0
 * @uses $title
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_title($echo=true) {
	global $title;
	$myVar = strip_decode($title);
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Clean Title
 *
 * This will remove all HTML from the title before returning
 *
 * @since 1.0
 * @uses $title
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_clean_title($echo=true) {
	global $title;
	$myVar = strip_tags(strip_decode($title));
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Slug
 *
 * This will return the slug value of a particular page
 *
 * @since 1.0
 * @uses $url
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_slug($echo=true) {
	global $url;
	$myVar = $url;
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Parent Slug
 *
 * This will return the slug value of a particular page's parent
 *
 * @since 1.0
 * @uses $parent
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_parent($echo=true) {
	global $parent;
	$myVar = $parent;
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Date
 *
 * This will return the page's updated date/timestamp
 *
 * @since 1.0
 * @uses $date
 * @uses $TIMEZONE
 *
 * @param string $i Optional, default is "l, F jS, Y - g:i A"
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_date($i = "l, F jS, Y - g:i A", $echo=true) {
	global $date,$dataw;
	global $TIMEZONE;
	if ($TIMEZONE != '')
	{
		if (function_exists('date_default_timezone_set'))
		{
			date_default_timezone_set($TIMEZONE);
		}
	}
	if ((string)$dataw->PAGE_TIME_STAMP == '1')
	{
		$myVar = date($i, strtotime($date));
	}
	else
	{
		$myVar='-';
	}
	if ($echo) { echo $myVar;} else { return $myVar; }
}

/**
 * Get Page Full URL
 *
 * This will return the full url
 *
 * @since 1.0
 * @uses $parent
 * @uses $url
 * @uses $SITEURL
 * @uses $PRETTYURLS
 * @uses find_url
 *
 * @param bool $echo Optional, default is false. True will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_url($echo=false) {
	global $url;
	global $SITEURL;
	global $PRETTYURLS;
	global $parent;
	if (!$echo)
	{
		echo find_url($url, $parent);
	}
	else
	{
		return find_url($url, $parent);
	}
}

/**
 * Get Page Header HTML
 *
 * This will return header html for a particular page. This will include the 
 * meta desriptions & keywords, <s>canonical</s> and title tags
 *
 * @since 1.0
 * @uses event::create
 * @uses get_page_url
 * @uses strip_quotes
 * @uses get_page_meta_desc
 * @uses get_page_meta_keywords
 * @uses $metad
 * @uses $title
 * @uses $content
 * @uses $site_full_name from configuration.php
 * @uses GSADMININCPATH
 *
 * @return string HTML for template header
 */
function get_header($full=true) {
	global $metad;
	global $title;
	global $content;
	include(av::get('spath_admin_inc').'configuration.php');
	// favicon, shortcut icon
	$favicon=field::get('favicon');
	if( !empty($favicon) )
	{?>
	<link rel="shortcut icon" href="<?=av::get('cpath').$favicon;?>" type="image/x-icon" />
	<?php }
	// meta description	
	if ($metad != '') {
		$desc = get_page_meta_desc(FALSE);
	}
	else if(getDef('GSAUTOMETAD',true))
	{
		// use content excerpt, NOT filtered
		$desc = strip_decode($content);
		if(getDef('GSCONTENTSTRIP',true)) $desc = strip_content($desc);
		$desc = cleanHtml($desc,array('style','script')); // remove unwanted elements that strip_tags fails to remove
		$desc = getExcerpt($desc,160); // grab 160 chars
		$desc = strip_whitespace($desc); // remove newlines, tab chars
		$desc = encode_quotes($desc);
		$desc = trim($desc);
	}

	if(!empty($desc)) echo '<meta name="description" content="'.$desc.'" />'."\n";

	// meta keywords
	$keywords = get_page_meta_keywords(FALSE);
	if ($keywords != '') echo '<meta name="keywords" content="'.$keywords.'" />';
	//if ($full)
	//{
		//echo '<link rel="canonical" href="'. get_page_url(true) .'" />'."\n";
	//}	// script queue
	get_scripts_frontend();
	event::create('theme-header');
}

/**
 * Get Page Footer HTML
 *
 * This will return footer html for a particular page. Right now
 * this function only executes a plugin hook so developers can hook into
 * the bottom of a site's template.
 *
 * @since 2.0
 * @uses event::create
 *
 * @return string HTML for template header
 */
function get_footer() {
	get_scripts_frontend(TRUE);
	event::create('theme-footer');
}

/**
 * Get Site URL
 *
 * This will return the site's full base URL
 * This is the value set in the control panel
 *
 * @since 1.0
 * @uses $SITEURL
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_url($echo=true) {
	global $SITEURL;
	if ($echo)
	{
		echo $SITEURL;
	}
	else 
	{
		return $SITEURL;
	}
}



/**
 * Get Site's Name
 *
 * This will return the value set in the control panel
 *
 * @since 1.0
 * @uses $SITENAME
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_name($echo=true) {
	global $SITENAME;
	$myVar = cl($SITENAME);
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Administrator's Email Address
 * 
 * This will return the value set in the control panel
 * 
 * @depreciated as of 3.0
 *
 * @since 1.0
 * @uses $EMAIL
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_email($echo=true) {
	global $EMAIL;
	$myVar = trim(stripslashes($EMAIL));
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}


/**
 * Get Site Credits
 *
 * This will return HTML that displays 'Powered by GetSimple X.XX'
 * It will always be nice if developers left this in their templates 
 * to help promote GetSimple. 
 *
 * @since 1.0
 * @uses $site_link_back_url from configuration.php
 * @uses $site_full_name from configuration.php
 * @uses GSVERSION
 * @uses GSADMININCPATH
 *
 * @param string $text Optional, default is 'Powered by'
 * @return string 
 */
function get_site_credits($text ='Powered by ') {
	include(GSADMININCPATH.'configuration.php');
	
	$site_credit_link = '<a href="'.$site_link_back_url.'" target="_blank" >'.$text.' '.$site_full_name.'</a>';
	echo stripslashes($site_credit_link);
}

/**
 * Menu Data
 *
 * This will return data to be used in custom navigation functions
 *
 * @since 2.0
 * @uses GSDATAPAGESPATH
 * @uses find_url
 * @uses getXML
 * @uses subval_sort
 *
 * @param bool $xml Optional, default is false. 
 *				True will return value in XML format. False will return an array
 * @return array|string Type 'string' in this case will be XML 
 */
function menu_data($id = null,$xml=false)
{
	$menu_extract = array();
	global $pagesArray; 
	$pagesSorted = subval_sort($pagesArray,'menuOrder');
	if (count($pagesSorted) != 0)
	{
		$logged_in = cookie_check();
		$count = 0;
		if (!$xml)
		{
			foreach ($pagesSorted as $page)
			{
				$text = (string)$page['menu'];
				$pri = (string)$page['menuOrder'];
				$parent = (string)$page['parent'];
				$title = (string)$page['title'];
				$slug = (string)$page['url'];
				$menuStatus = (string)$page['menuStatus'];
				$private = (string)$page['private'];
				/* hide private pages from public */
				if ( !$logged_in && $private=='Y' )
				{
					continue;
				}
				/* end */
				$pubDate = (string)$page['pubDate'];  
				$url = find_url($slug,$parent);
				$specific = array("slug"=>$slug,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);
				if ($id == $slug)
				{ 
					return $specific; 
					exit; 
				}
				else
				{
					$menu_extract[] = $specific;
				}
			}
			return $menu_extract;
		}
		else
		{
			$xml = '<?xml version="1.0" encoding="UTF-8"?><channel>';    
			foreach ($pagesSorted as $page)
			{
				$text = $page['menu'];
				$pri = $page['menuOrder'];
				$parent = $page['parent'];
				$title = $page['title'];
				$slug = $page['url'];
				$pubDate = $page['pubDate'];
				$menuStatus = $page['menuStatus'];
				$private = $page['private'];
				/* hide private pages from public */
				if ( !$logged_in && $private=='Y' )
				{
					continue;
				}
				/* end */
				$url = find_url($slug,$parent);
				$xml.="<item>";
				$xml.="<slug><![CDATA[".$slug."]]></slug>";
				$xml.="<pubDate><![CDATA[".$pubDate."]]></pubDate>";
				$xml.="<url><![CDATA[".$url."]]></url>";
				$xml.="<parent><![CDATA[".$parent."]]></parent>";
				$xml.="<title><![CDATA[".$title."]]></title>";
				$xml.="<menuOrder><![CDATA[".$pri."]]></menuOrder>";
				$xml.="<menu><![CDATA[".$text."]]></menu>";
				$xml.="<menuStatus><![CDATA[".$menuStatus."]]></menuStatus>";
				$xml.="<private><![CDATA[".$private."]]></private>";
				$xml.="</item>";
			}
			$xml.="</channel>";
			return $xml;
		}
	}
}

function get_component($a_name)
{
	global $components;
	// normalize a_name
	$a_name = to7bit($a_name, 'UTF-8');
	$a_name = clean_url($a_name);
	if (!$components)
	{
		if (file_exists(GSDATAOTHERPATH.'components.xml'))
		{
			$data = getXML(GSDATAOTHERPATH.'components.xml');
			$components = $data->item;
		}
		else
		{
			$components = array();
		}
	}
	if (count($components) > 0)
	{
		foreach ($components as $component)
		{
			if ($a_name == $component->slug)
			{ 
				/*eval("?>" . strip_decode($component->value) . "<?php ");*/ 
				echo(strip_decode($component->value)); 
			}
		}
	}
}

function get_navigation($currentpage = "",$classPrefix = "") {

	$menu = '';

	global $pagesArray,$id;
	if(empty($currentpage)) $currentpage = $id;
	
	$pagesSorted = subval_sort($pagesArray,'menuOrder');
	if (count($pagesSorted) != 0) { 
		foreach ($pagesSorted as $page) {
			$sel = ''; $classes = '';
			$url_nav = $page['url'];
			
			if ($page['menuStatus'] == 'Y') { 
				$parentClass = !empty($page['parent']) ? $classPrefix.$page['parent'] . " " : "";
				$classes = trim( $parentClass.$classPrefix.$url_nav);
				if ($currentpage == $url_nav) $classes .= " current active";
				if ($page['menu'] == '') { $page['menu'] = $page['title']; }
				if ($page['title'] == '') { $page['title'] = $page['menu']; }
				$menu .= '<li class="'. $classes .'"><a href="'. find_url($page['url'],$page['parent']) . '" title="'. encode_quotes(cl($page['title'])) .'">'.strip_decode($page['menu']).'</a></li>'."\n";
			}
		}
		
	}
	
	echo filter::create('menuitems',$menu);
}

/**
 * Check if a user is logged in
 * 
 * This will return true if user is logged in
 *
 * @since 3.2
 * @uses get_cookie();
 * @uses $USR
 *
 * @return bool
 */	
function is_logged_in(){
  global $USR;
  if (isset($USR) && $USR == get_cookie('GS_ADMIN_USERNAME')) {
    return true;
  }
}	
	

	
/**
 * @depreciated as of 2.04
 */
function return_page_title() {
	return get_page_title(FALSE);
}
/**
 * @depreciated as of 2.04
 */
function return_parent() {
	return get_parent(FALSE);
}
/**
 * @depreciated as of 2.04
 */
function return_page_slug() {
  return get_page_slug(FALSE);
}
/**
 * @depreciated as of 2.04
 */
function return_site_ver() {
	return get_site_version(FALSE);
}	
/**
 * @depreciated as of 2.03
 */
if(!function_exists('set_contact_page')) {
	function set_contact_page() {
		#removed functionality	
	}
}
?>
