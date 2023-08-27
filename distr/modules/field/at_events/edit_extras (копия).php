<?php if(!defined('APP')){ die('you cannot load this page directly.'); }



global $SITEURL,$TEMPLATE;
global $data_edit; // SimpleXML to read from
$isI18N = function_exists('return_i18n_languages');
$creDate = $data_edit->creDate ? (string) $data_edit->creDate : (string) $data_edit->pubDate;
field::get_fields_and_types();
$fields = &field::$fields;

echo '<input type="hidden" name="creDate" value="'.htmlspecialchars($creDate).'"/>';

if (!$fields || count($fields) <= 0)
{
	return;
}
$id = $_GET['id'];

$EDLANG = i18n_r('CKEDITOR_LANG');
$EDTOOL = 'advanced';//basic/advanced
$EDOPTIONS = ''; 
$toolbar = "['Source', 'btns_h1']";
// Editor settings end


?>
<h4><?=i18n_r('field/fields_management')?> (<?=i18n_r('by_module')?> <?=i18n_r('field/TITLE')?>).</h4>
<table class="formtable" style="clear:both;width:100%;margin-left:0;">
	<tbody>
<?php
$ndx = 0;
foreach ($fields as $field)
{
	$scope=&$field['scope'];
	if ( $scope !=='page' && $scope !=='all' )
	{
		continue;
	}
?>
		<tr style="border:0 none;">
<?php
	$ndx++;
	$key = $field['key'];
	$about = $field['about'];
	$type = $field['type'];
	$value = $id ? (isset($data_edit->$key) ? $data_edit->$key : '') : (isset($field['value']) ? $field['value'] : '');
	$value = htmlspecialchars($value, ENT_QUOTES);

	if ($field['predef'] ) // draw a full width TextBox
	{
?>
			<td colspan="2" style="border:0 none;"><p><strong><?=$key?></strong> (<?=i18n_r('field/predef')?>)</p>
				<p><?=$field['about']?>. <?=$key?>=<?=$field['value']?></p>
			</td> 
<?php
		continue;
	}
	if ('textfull' == $type ) // draw a full width TextBox
	{
?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text" type="text" style="width:602px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
			</td> 
<?php
	}
	elseif ('dropdown' == $type )
	{?>
			<td style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<select id="post-<?=$key?>" name="post-<?=$key?>" class="text" style="width:295px"><?php
		foreach ($field['options'] as $option)
		{
			$selected = $value == $option ? ' selected="selected"' : '';?>
				<option <?=$selected?>><?=$option?></option><?php
		}?>
				</select>
			</td><?php
	}
	elseif ('checkbox' == $type ) 
	{
		if($value){ $checked=' checked=\"checked\"';}else {$checked='';}
	?>
			<td style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input type="checkbox" style="width:auto;" id="post-<?=$key?>" name="post-<?=$key?>" value="on"<?=$checked?>/>
			</td> 
	<?php
	}
	elseif ('web_editor' == $type ) 
	{
	?>
			<td style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<textarea style="width:602px;height:200px;border: 1px solid #AAAAAA;" id="post-<?=$key?>" name="post-<?=$key?>"><?=$value?></textarea>
			</td>
			<script type="text/javascript">
				$(function()
				{
					var ckedroot=<?="'".av::get('cpath_modules_client').'admin/js/ckeditor'."'";?>;
					var editor_<?php echo $ndx; ?> = CKEDITOR.replace( 'post-<?php echo $key; ?>',
						{
							customConfig: ckedroot+'/custom/config.js',
							skin : 'conger,'+ckedroot+'/custom/skins/conger/',
							<?php if (file_exists(GSTHEMESPATH.$TEMPLATE."/editor.css")) { ?>
							contentsCss: '<?=$SITEURL."theme/$TEMPLATE/style.css";?>',
							<?php } 
							else
							{ ?>
							contentsCss: ckedroot+'/custom/contents.css',
							<?php } ?>
							forcePasteAsPlainText : true,
							entities : true,
							height: '200px',
							toolbar : [ <?php echo $toolbar; ?> ],
							language : '<?php echo $EDLANG; ?>',
							filebrowserBrowseUrl : 'filebrowser.php?type=all',
							filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
							filebrowserWindowWidth : '730',
							filebrowserWindowHeight : '500',
						}
					);
					}
				);
				CKEDITOR.replace( 'editor', {extraPlugins:'btns'} );
			</script>
	<?php
	}
	elseif ('link' == $type )
	{
			$width = 500;
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text" type="text" style="width:<?=$width?>px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
				<span class="edit-nav"><a id="browse-<?=$key?>" href="#"><?=i18n_r('field/BROWSE_PAGES')?></a></span>
			</td> 
			<script type="text/javascript">
			function fill_<?php echo $ndx; ?>(url) {
			$('#post-<?php echo $key; ?>').val(url);
			}
			$(function() { 
			$('#browse-<?php echo $key; ?>').click(function(e) {
			window.open('<?php echo $SITEURL; ?>plugins/i18n_customfields/browser/pagebrowser.php?func=fill_<?php echo $ndx; ?>&i18n=<?php echo $isI18N; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
			});
			});
			</script>
	<?php
	}
	elseif ('image' == $type || 'file' == $type ) 
	{
		$width=500;
		$browsetext=($type=='image' ? i18n_r('field/BROWSE_IMAGES') : i18n_r('field/BROWSE_FILES'))
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text" type="text" style="width:<?=$width?>px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
				<span class="edit-nav"><a id="browse-<?=$key?>" href="#"><?=$browsetext?></a></span>
			</td> 
			<script type="text/javascript">
			function fill_<?php echo $ndx; ?>(url) {
			$('#post-<?php echo $key; ?>').val(url);
			}
			$(function() { 
			$('#browse-<?php echo $key; ?>').click(function(e) {
			window.open('<?php echo $SITEURL; ?>plugins/i18n_customfields/browser/filebrowser.php?func=fill_<?php echo $ndx; ?>&type=<?php echo $type=='image' ? 'images' : ''; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
			});
			});
			</script>
	<?php
	}
	elseif ('text' == $type )
	{
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type?>)<br/>
				<input class="text short" type="text" style="width:295px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
			</td> 
	<?php
	}
	else // default
	{
	?>
			<td colspan="2" style="border:0 none;"><strong><?=$key?></strong> (<?=$type.'/default'?>)<br/>
				<input class="text short" type="text" style="width:295px;" id="post-<?=$key?>" name="post-<?=$key?>" value="<?=$value?>"/>
			</td> 
	<?php
	}?>
		</tr><?php
} // foreach ($fields as $field)
?>
	</tbody>
</table>
