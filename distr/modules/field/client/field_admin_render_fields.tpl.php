<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php
?>
<tr class="<?php echo $field_class; ?>">
	<td>
		<input type="text" class="text" name="field_<?php echo $ndx; ?>_key" value="<?php echo $field['key'];?>"/>
	</td>
	<td>
		<input type="text" class="text" name="field_<?php echo $ndx; ?>_about" value="<?php echo $field['about'];?>"/>
	</td>
	<td>
		<select name="field_<?php echo $ndx; ?>_scope" class="text short">
			<option value="all"<?=$oselected('all','scope',$field);?>><?php i18n('field/scope_all'); ?></option>
			<option value="system"<?=$oselected('system','scope',$field);?>><?php i18n('field/scope_sys'); ?></option>
			<option value="page"<?=$oselected('page','scope',$field);?>><?php i18n('field/scope_page'); ?></option>
		</select>
	</td>
	<td>
		<select name="field_<?php echo $ndx; ?>_type" class="text short">
			<option value="text"<?=$oselected('text','type',$field);?>><?php i18n('field/TEXT_FIELD'); ?></option>
			<option value="textfull"<?=$oselected('textfull','type',$field);?>><?php i18n('field/LONG_TEXT_FIELD'); ?></option>
			<option value="dropdown"<?=$oselected('dropdown','type',$field);?>><?php i18n('field/DROPDOWN_BOX'); ?></option>
			<option value="checkbox"<?=$oselected('checkbox','type',$field);?>><?php i18n('field/CHECKBOX'); ?></option>
			<option value="web_editor"<?=$oselected('web_editor','type',$field);?>><?php i18n('field/web_editor'); ?></option>
			<option value="image"<?=$oselected('image','type',$field);?>><?php i18n('field/IMAGE'); ?></option>
			<option value="file"<?=$oselected('file','type',$field);?>><?php i18n('field/FILE'); ?></option>
			<option value="link"<?=$oselected('link','type',$field);?>><?php i18n('field/LINK'); ?></option>
		</select>
		<?php if ( $isdropdown ) { ?>
		<textarea class="text" name="field_<?php echo $ndx; ?>_options"><?php echo $options; ?></textarea> 
		<?php } /* $isdropdown */  ?>
	</td>
	<td>
		<input type="text" class="text"name="field_<?php echo $ndx; ?>_value" value="<?php echo $field['value'];?>"/>
	</td>
	<?php if (field::$issearch) { ?>
	<td>
		<input type="checkbox" name="field_<?php echo $ndx; ?>_index" <?php echo $field['index'] ? 'checked="checked"' : ''; ?> <?php echo !$indexable ? 'style="display:none"' : ''; ?> />
	</td>
	<?php } /* field::$issearch */ ?>
	<td class="delete"><a href="" class="delete" title="<?php i18n('field/DELETE'); ?>">X</a></td>
</tr>
