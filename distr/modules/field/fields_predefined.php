<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php
class fields_predefined extends field
{
	protected static function add()
	{
		$fields = &self::$fields;
		$fields[] = array(
		'key'=>av::get('fcpath'), 
		'about'=>'predefined_var',
		'scope'=>'all',
		'type' => 'text',
		'value' => av::get('cpath'),
		'predef'=> true
		);
		
		$fields[] = array(
		'key'=>'pages', 
		'about'=>'predefined_fn',
		'scope'=>'page',
		'type' => 'text',
		'value' => 'pages',
		'predef'=> true
		);
	}
	
	public static function pages($a_=null)
	{
		// Variable settings
		global $pagesArray;
		$pagesSorted = $pagesArray;
		array_multisort (array_column($pagesSorted, 'title'), SORT_ASC, $pagesSorted);
		
		if (count($pagesSorted) <= 0)
		{
			return '';
		}
		$out='';
		ob_start();
		foreach ($pagesSorted as $page)
		{
			if ($page['url'] == '404')
			{
				continue;
			}
			if ($page['private'] == 'Y')
			{
				continue;
			}
			if ( !empty($a_['parent']) && $a_['parent'] !== $page['parent'])
			{
				continue;
			}
			
			$page_url = find_url($page['url'], $page['parent']);
			?>
			<h2><a href="<?=$page_url?>"><?=$page['title']?></a></h2>
			<?php 
			if (!empty($page['about'])) 
			{?>
			<?=$page['about'];?>
			<?php 
			}
		}
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}
