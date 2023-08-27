<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php
$tr='i18n_r';
$id='f'.$ndx;
$tname=$tr('field/NAME');
$tabout=$tr('field/about');
$tscope=$tr('field/scope');
$ttype=$tr('field/TYPE');
$tvalue=$tr('field/VALUE');
;?>
					<div class="field">
						<div class="cfg">
							<div>
								<label for="<?=$id;?>_1"><?=$tname;?></label>
								<input id="<?=$id;?>_1" type="text" class="text" name="field_<?=$ndx; ?>_key" value="<?=$field['key'];?>"/>
							</div>
							<div>
								<label for="<?=$id;?>_2"><?=$tabout;?></label>
								<input id="<?=$id;?>_2" type="text" class="text" name="field_<?=$ndx; ?>_about" value="<?=$field['about'];?>"/>
							</div>
							<div>
								<label for="<?=$id;?>_3"><?=$tscope;?></label>
								<select id="<?=$id;?>_3" name="field_<?=$ndx; ?>_scope" class="text short">
									<option value="all"<?=$oselected('all','scope',$field);?>><?=$tr('field/scope_all'); ?></option>
									<option value="system"<?=$oselected('system','scope',$field);?>><?=$tr('field/scope_sys'); ?></option>
									<option value="page"<?=$oselected('page','scope',$field);?>><?=$tr('field/scope_page'); ?></option>
								</select>
							</div>
							<div>
								<label for="<?=$id;?>_4"><?=$ttype;?></label>
								<select data-field_item="type" id="<?=$id;?>_4" name="field_<?=$ndx; ?>_type" class="text short">
									<option value="text"<?=$oselected('text','type',$field);?>><?=$tr('field/TEXT_FIELD'); ?></option>
									<option value="textfull"<?=$oselected('textfull','type',$field);?>><?=$tr('field/LONG_TEXT_FIELD'); ?></option>
									<option value="dropdown"<?=$oselected('dropdown','type',$field);?>><?=$tr('field/DROPDOWN_BOX'); ?></option>
									<option value="checkbox"<?=$oselected('checkbox','type',$field);?>><?=$tr('field/CHECKBOX'); ?></option>
									<option value="web_editor"<?=$oselected('web_editor','type',$field);?>><?=$tr('field/web_editor'); ?></option>
									<option value="image"<?=$oselected('image','type',$field);?>><?=$tr('field/IMAGE'); ?></option>
									<option value="file"<?=$oselected('file','type',$field);?>><?=$tr('field/FILE'); ?></option>
									<option value="link"<?=$oselected('link','type',$field);?>><?=$tr('field/LINK'); ?></option>
								</select>
<?php if ( $isdropdown ) { ?>
								<textarea class="text" name="field_<?=$ndx; ?>_options"><?=$options; ?></textarea> 
<?php } /* $isdropdown */  ?>
							</div>
							<div class="delete"><a href="" class="delete" data-cmd="field_del" title="<?=$tr('del'); ?>">✘</a></div>
						</div>
						<div class="control">
							<?=dev::rhtmlcom(['type',$field['type']]);?>
							<label for="<?=$id;?>_5"><?=$tvalue;?></label>
<?php if ('web_editor' == $field['type'] ) { ?>
							<textarea data-field_item="value" class="text" name="field_<?=$ndx;?>_value" id="<?=$id;?>_5" class="text"><?=$field['value'];?></textarea>
<?php }
else { ?>
							<input id="<?=$id;?>_5" type="text" class="text" data-field_item="value" name="field_<?=$ndx; ?>_value" value="<?=$field['value'];?>"/>
<?php } ?>
						</div>
					</div> <?=dev::rhtmlcom('field');?>
