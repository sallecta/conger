function web_editor ( a_el )
{
	var ckedroot=<?="'".av::get('cpath_modules_client').'admin/js/ckeditor'."'";?>;
	CKEDITOR.config.contentsCss = ckedroot+'/custom/contents.css';
	CKEDITOR.plugins.basePath = ckedroot+'/custom/plugins/';
	CKEDITOR.cpath_admin = "<?=av::get('cpath_admin');?>";
	//console.log('CKEDITOR.cpath_admin',CKEDITOR.cpath_admin);
	editor =  a_el;
	CKEDITOR.replace( a_el,
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
			//toolbar : [ <?php echo $toolbar; ?> ],
			//language : '<?php echo $EDLANG; ?>',
			//language : 'en',
			filebrowserBrowseUrl : 'filebrowser.php?type=all',
			filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
			filebrowserWindowWidth : '730',
			filebrowserWindowHeight : '500',
			removePlugins: 'format_buttons'
		}
	);
}
