<?php 
/**
 * Menu Manager
 *
 * Allows you to edit the current main menu hierarchy  
 *
 * @package GetSimple
 * @subpackage Page-Edit
 */

# Setup
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();
$tr='i18n_r';

# save page priority order
if (isset($_POST['menuOrder'])) {
	$menuOrder = explode(',',$_POST['menuOrder']);
	$priority = 0;
	foreach ($menuOrder as $slug) {
		$file = GSDATAPAGESPATH . $slug . '.xml';
		if (file_exists($file)) {
			$data = getXML($file);
			if ($priority != (int) $data->menuOrder) {
				unset($data->menuOrder);
				$data->addChild('menuOrder')->addCData($priority);
				XMLsave($data,$file);
			}
		}
		$priority++;
	}
	create_pagesxml('true');
	$success = i18n_r('MENU_MANAGER_SUCCESS');
}

# get pages
getPagesXmlValues();
$pagesSorted = subval_sort($pagesArray,'menuOrder');

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('PAGE_MANAGEMENT').' &raquo; '.str_replace(array('<em>','</em>'), '', i18n_r('MENU_MANAGER'))); 

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main" >
			<h3><?=$tr('MENU_MANAGER');?></h3>
			<p><?=$tr('MENU_MANAGER_DESC');?></p>
<?php
	if (count($pagesSorted) != 0)
	{
?>
				<form method="post" action="menu-manager.php">
					<ul id="menu-order" >
<?php
		foreach ($pagesSorted as $page)
		{
			$sel = '';
			if ($page['menuStatus'] == '')
			{ continue; }
			if ($page['menuOrder'] == '')
			{ 
				$page['menuOrder'] = "N/A"; 
			} 
			if ($page['menu'] == '')
			{ 
				$page['menu'] = $page['title']; 
			}
			$ps=&$page['slug'];
			$pmo=&$page['menuOrder'];
			$pm=&$page['menu'];
			$pt=&$page['title'];
			$pp=&$page['parent'];
			$edit=av::get('cpath_admin')."edit.php?id=$ps";
			if ( $pp )
			{ $out="$pp ➛ $ps"; }
			else
			{ $out="$ps"; }
?>
						<li class="" rel="<?=$ps;?>">
							<strong>#<?=$pmo;?> ❭</strong> <em><?=$pt;?></em> ❭ <?=$out."\n";?> <a class='edit' href="<?=$edit;?>">Edit</a>
						</li>
<?php
		}
?>
					</ul>
					<input type="hidden" name="menuOrder" value=""><input class="submit" type="submit" value="<?=$tr("SAVE_MENU_ORDER");?>" />
					</form>
<?php
	}
	else
	{
?>
					<p><?=$tr('NO_MENU_PAGES');?></p>	
<?php
	}
?>
			<script>
				$("#menu-order").sortable({
					cursor: 'move',
					placeholder: "placeholder-menu",
					update: function() {
						var order = '';
						$('#menu-order li').each(function(index) {
							var cat = $(this).attr('rel');
							order = order+','+cat;
						});
						$('[name=menuOrder]').val(order);
					}
				});
				$("#menu-order").disableSelection();
			</script>
		</div>
	</div>
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>
