<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>

		<h3 class="floated"><?php i18n(PLGID_CATALOG.'/MANAGECAT'); ?></h3>
		<div class="edit-nav clearfix" style="">
				<a href="#" class="ra_help_button"><?php i18n(PLGID_CATALOG.'/ADD_NCAT'); ?></a>
		</div>
		<div class="ra_help" style="display:none;padding:10px;background-color:#f6f6f6;margin:10px;">
			<h3><?php i18n(PLGID_CATALOG.'/ADD_CAT'); ?></h3>  
			<form action="" method="post" accept-charset="utf-8">
				<input type="hidden" name="new_category" />
				<p>
					<input type="text" name="title" class="text" style="width:600px;" value="<?php i18n(PLGID_CATALOG.'/CAT_TITLE'); ?>" onFocus="if(this.value == '<?php i18n(PLGID_CATALOG.'/CAT_TITLE'); ?>') {this.value = '';}" onBlur="if (this.value == '') {this.value = '<?php i18n(PLGID_CATALOG.'/CAT_TITLE'); ?>';}" />
				</p>
				<input type="submit" class="submit" value="<?php i18n(PLGID_CATALOG.'/ADD_CAT'); ?>" style="float:right;"/>
			</form>		
			<div style="clear:both">&nbsp;</div>
			<script type="text/javascript">
				$(document).ready(function() {
					$('.ra_help_button').click(function() {
						$('.ra_help').show();
						$('.ra_help_button').hide();
					})
				})
			</script>
		</div>
		<table class="highlight">
	<?php
		$content_count = '0';
			$showings_count = 0;
		foreach($faq_data->category as $category)
		{
			$content_count++;
			$showings_count++;
			$c_atts= $category->attributes();
			?>
			<form action="" method="post">
				<input type="hidden" name="edit_category_name" value="<?php echo $c_atts['name']; ?>"/>
			<tr>
				<td>
							<input class="title<?php echo $showings_count; ?>" style="display:none;width:270px;float:left;" name="title" value="<?php echo $c_atts['name']; ?>">
							<input class="submit<?php echo $showings_count; ?>" type="submit" value="<?php i18n(PLGID_CATALOG.'/SUBMITQ'); ?>" style="display:none;float:right;margin-right:30px;padding:2px;font-size:10px;" />
							<span class="title<?php echo $showings_count; ?>" ONCLICK="showinput('title<?php echo $showings_count; ?>','submit<?php echo $showings_count; ?>')" style="color:inherit;font-size:inherit;line-height:inherit;"><?php echo $c_atts['name']; ?></span>
				</td>
				<td class="delete">
					<a href="load.php?id=catalog&faq_categories&delete_category=<?php echo urlencode($c_atts['name']); ?>" class="delconfirm" title="<?php i18n(PLGID_CATALOG.'/DEL_CAT1'); echo str_replace('"', "&quot;", $c_atts['name']); i18n(PLGID_CATALOG.'/DEL_CAT2'); ?>">
					X
					</a>
				</td>
			</tr>
			</form>
	<?php
	}
	?>
			<script type="text/javascript">
				function showinput(a_type,a_submit){
						$('input.'+[a_type]).show();
						$('input.'+[a_submit]).show();
						$('span.'+[a_type]).hide();
				}
			</script>
			</table>
		<?php
		echo '<p><b>', $content_count, '</b> ', i18n_r(PLGID_CATALOG.'/CATCOUNT'), '</p>';
