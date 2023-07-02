<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>

		<h3 class="floated"><?php i18n(PLGID_CATALOG.'/MANAGETHINGS'); ?></h3>
			<div class="edit-nav clearfix" style="">
				<a href="load.php?id=catalog&add_faq" class="ra_help_button"><?php i18n(PLGID_CATALOG.'/ADD_T'); ?></a>
		</div>
		
	<?php

		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');

		foreach($faq_file->category as $category)
		{
			$content_count = '0';
			$c_atts=$category->attributes();
			echo '<h2 style="font-size:16px;">'.$c_atts['name'].'</h2><table class="highlight">';
			foreach($category->content as $the_content)
			{	
				$content_count++;
				$atts = $the_content->attributes();
				?>
				<tr>
					<td>
						<a href="load.php?id=catalog&edit_faq=<?php echo urlencode($atts['title']); ?>" title="<?php i18n(PLGID_CATALOG.'/EDIT_CONTENT'); echo Catalog::item_title($atts); ?>">
						<?php echo $atts['title']; ?>
						</a>
					</td>
					<td class="delete">
						<a href="load.php?id=catalog&delete=<?php echo urlencode($atts['title']); ?>&category_of_deleted=<?php echo urlencode($c_atts['name']); ?>" class="delconfirm" title="<?php i18n(PLGID_CATALOG.'/DEL_CONTENT'); echo Catalog::item_title($atts); ?>?">X</a>
					</td>
				</tr>
<?php
			}
			echo '</table>';
			echo '<p><b>', $content_count, '</b> ', i18n_r(PLGID_CATALOG.'/THINGSCNT'), '</p>';
		}
