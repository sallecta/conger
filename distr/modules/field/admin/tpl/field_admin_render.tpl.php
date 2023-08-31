<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php 
get_template('header', av::get('site_name').' &raquo; '.i18n_r('field/admin_title'));
require_once(av::get('spath_admin').'template/include-nav.php');
$tr='i18n_r';
$ak='find_accesskey';
?>
<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main">
			<h3 class="floated"><?=$tr('field/fields_management');?></h3>
			<div class="edit-nav clearfix" >
				<a href="" id="filtertable" accesskey="<?=$ak($tr('FILTER'));?>" ><?=$tr('FILTER');?></a>
			</div>
			<div id="filter-search">
				<form>
					<p>todo</p>
					<input type="text" autocomplete="off" class="text" id="q" placeholder="<?=strip_tags(lowercase($tr('todo')));?>" />
						<a href="pages.php" class="cancel"><?=$tr('CANCEL');?></a>
				</form>
			</div>
			<form method="post" id="field_form" class="module_field" autocomplete="off" >
				<div id="editfields" class="module_field" >
<?php field_admin::render_field();?>
				</div> <?=dev::rhtmlcom('module_field');?>
			</form>
			<div class="module_field controls">
				<a href="" class="toggle" data-cmd="field_dragtoggle" >...</a>
				<a href="" data-cmd="field_collapsetoggle" >...</a>
				<input type="submit" form="field_form" name="save" value="<?=$tr('save');?>" class="submit"/>
				<a href="" class="add" data-cmd="field_add" ><?=$tr('add');?></a>
				<a href="" class="up" data-cmd="field_up" ><?=$tr('up');?></a>
				<a href="" class="down" data-cmd="field_down"><?=$tr('down');?></a>
			</div>
			<p id="pg_counter" style="margin-top:20px;"><?=$tr('field/total_fields:');?> <em class="fields_total"><?=$fields_total;?></em>.</p>
		</div> <?=dev::rhtmlcom('main');?>
	</div><?=dev::rhtmlcom('maincontent');?>
<?php field_admin::render_sidebar();?>
</div> <?=dev::rhtmlcom('bodycontent');?>
<?php get_template('footer');?>
