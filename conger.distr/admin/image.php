<?php 
$load['plugin'] = true;
$tr='i18n_r';

// Include common.php
include('inc/common.php');

// Variable Settings
login_cookie_check();

$subPath = (isset($_GET['path'])) ? $_GET['path'] : "";
if ($subPath != '') { $subPath = tsl($subPath); }


$fname = strippath($_GET['i']);
$src_folder = av::get('cpath_data_uploads');
$cpath_thumb =  av::get('cpath_data_thumbs').$subPath;
$spath_thumb =  av::get('spath_data_thumbs').$subPath;

$simg =  av::get('spath_data_uploads').$subPath.$fname;
$cimg =  av::get('cpath_data_uploads').$subPath.$fname;

$sthumb =  av::get('spath_data_thumbs').$subPath.'thumbnail.'.$fname;
$cthumb =  av::get('cpath_data_thumbs').$subPath.'thumbnail.'.$fname;

if (!filepath_is_safe($simg))
{
	redirect("upload.php");
}

list($width, $height, $type, $attr) = getimagesize($simg);
$img_nfo = '<a href="'.$cimg.'" rel="facybox_i" ><img src="'.$cimg.'">'.$tr('IMAGE')." $width x $height px</a>";
$imgwh=$width + $height;

if (file_exists($sthumb))
{
dev::ehtmlcom($sthumb);
	list($width, $height, $type, $attr) = getimagesize($sthumb);
		$thumb_nfo = ' <a href="'.$cthumb.'" rel="facybox_i" > <img src="'.$cthumb.'">'.$tr('THUMBNAIL')." $width x $height px</a>";
	$img_nfo = $img_nfo . $thumb_nfo;
}
else if ( $imgwh > 400 )
{
	// if thumb is missing recreate it
	require_once('inc/imagemanipulation.php');
	if(genStdThumb($subPath,$fname))
	{
		list($width, $height, $type, $attr) = getimagesize($sthumb);
		$thumb_nfo = ' <a href="'.$cthumb.'" rel="facybox_i" ><img src="'.$cthumb.'"> '.$tr('THUMBNAIL')." $width x $height px</a>";
		$img_nfo = $img_nfo . $thumb_nfo;
	}
}
else
{
	$img_nfo = $img_nfo . 'thumbnail not needed';
}

dev::ehtmlcom($cimg);
get_template('header', cl($SITENAME).' &raquo; '.$tr('FILE_MANAGEMENT').' &raquo; '.$tr('IMAGES')); 

include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main">
			<h3><?=$tr('IMG_CONTROl_PANEL');?></h3>
			<div class="admin_image">
				<?=$img_nfo;?>
			</div>
		</div>
	</div>
	<div id="sidebar" >
		<?php include('template/sidebar-files.php'); ?>
	</div>
</div>
<?php get_template('footer'); ?>
