<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>

<?php get_template('header', av::get('site_name').' &raquo; '.i18n_r('field/admin_title')); ?>
<?php require_once(av::get('spath_admin').'template/include-nav.php'); ?>
<div class="bodycontent clearfix">
	
	<div id="maincontent">
	<?php //event::create('field-main');?>
		<div class="main">
			<h3 class="floated"><?php i18n('field/fields_management'); ?></h3>
			<div class="edit-nav clearfix" >
				<a href="" id="filtertable" accesskey="<?php echo find_accesskey(i18n_r('FILTER'));?>" ><?php i18n('FILTER'); ?></a>
				<a href="" id="show-characters" accesskey="<?php echo find_accesskey(i18n_r('TOGGLE_STATUS'));?>" ><?php i18n('TOGGLE_STATUS'); ?></a>
			</div>
			<div id="filter-search">
				<form><input type="text" autocomplete="off" class="text" id="q" placeholder="<?php echo strip_tags(lowercase(i18n_r('FILTER'))); ?>..." /> &nbsp; <a href="pages.php" class="cancel"><?php i18n('CANCEL'); ?></a></form>
			</div>
			
<p class="clear"><?php i18n('field/DESCR'); ?></p>
<style>
	
form input.text, form select.text {
	width: 100%;
	box-sizing: border-box;
	height: 25px;
}

table#editfields {
width: 100%;
display: inline-block;
}

table#editfields td:nth-child(-n+5) { width:19%;}
table#editfields td:nth-child(6) { width:5%;}

table#editfields a.delete {
	line-height: 21px;
	padding: 2px;
}
</style>
<form method="post" id="field_form">
	<table id="editfields" >
		<thead>
			<tr>
				<th><?php i18n('field/NAME'); ?></th>
				<th><?php i18n('field/about'); ?></th>
				<th><?php i18n('field/scope'); ?></th>
				<th><?php i18n('field/TYPE'); ?></th>
				<th><?php i18n('field/VALUE'); ?></th>
				<?php if (field::$issearch) { ?>
				<th><?php i18n('field/INDEX'); ?></th>
				<?php } ?>
				<th></th>
			</tr>
		</thead>
		<tbody>
<?php field_admin::render_fields(); ?>
			<tr>
				<td colspan="5"><a href="" class="add"><?php i18n('field/ADD'); ?></a></td>
				<td class="secondarylink"><a href="" class="add" title="<?php i18n('field/ADD'); ?>">+</a></td>
			</tr>
		</tbody>
	</table>
	<input type="submit" name="save" value="<?php i18n('field/SAVE'); ?>" class="submit"/>
</form>
<script type="text/javascript" src="<?=field::$cpath;?>/js/jquery-ui.sort.min.js"></script>
<script type="text/javascript">
	function fields_renumber()
	{
		$('#field_form table tbody tr').each(function(i,tr)
			{
				$(tr).find('input, select, textarea').each(function(k,elem)
					{
						var name = $(elem).attr('name').replace(/_\d+_/, '_'+(i)+'_');
						$(elem).attr('name', name);
					}
				);
			}
		);
	}
	$(function()
		{
			$('select[name$=_type]').change(function(e)
				{
					var val = $(e.target).val();
					var $ta = $(e.target).closest('td').find('textarea');
					if (val == 'dropdown')
					{
						$ta.css('display','inline');
					}
					else
					{
						$ta.css('display','none');
					}
					<?php if (field::$issearch) { ?>
					var $cb = $(e.target).closest('tr').find('input[type=checkbox]');
					if (val == 'text' || val == 'textfull' || val == 'dropdown' || val == 'textarea' || val == 'checkbox')
					{
						$cb.show();
					}
					else
					{
						$cb.attr('checked',false).hide();
					}
					<?php } ?>
				}
			);
			$('a.delete').click(function(e)
				{
					e.preventDefault();
					$(e.target).closest('tr').remove();
					el_total = document.querySelector("#fields_total");
					var total_fields = Number(el_total.innerHTML);
					el_total.innerHTML=total_fields-1;
					el_total.style.fontWeight = 'bold';
					fields_renumber();
				}
			);
			$('a.add').click(function(e)
				{
					e.preventDefault();
					var $tr = $(e.target).closest('tbody').find('tr.hidden');
					$tr.before($tr.clone(true).removeClass('hidden').addClass('sortable'));
					el_total = document.querySelector("#fields_total");
					var total_fields = Number(el_total.innerHTML);
					el_total.innerHTML=total_fields+1;
					el_total.style.fontWeight = 'bold';
					fields_renumber();
				}
			);
			$('#field_form tbody').sortable(
				{
				items:"tr.sortable", handle:'td',
				update:function(e,ui) { fields_renumber(); }
				}
			);
			fields_renumber();
			<?php if (field::$cmsg) { ?>
			$('div.bodycontent').before('<div class="updated" style="display:block;">'+<?php echo json_encode(field::$cmsg); ?>+'</div>');
			<?php } ?>
		}
	);
</script>
			<p id="pg_counter" style="margin-top:20px;"><em id="fields_total"><?=$fields_total;?></em> <?=i18n('field/total_fields');?>.</p>
		</div>
	</div><!-- end maincontent -->
	
	<div id="sidebar" >
		<?php require(field::$spath.'client/field_admin_sidebar.tpl.php'); ?>
	</div>
</div>
<?php get_template('footer'); ?>
