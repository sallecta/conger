<?php
// Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();
$dirsSorted=null;$filesSorted=null;$foldercount=null;
$folder_img=av::get('cpath_modules_client').'admin/img/folder.png';

if (isset($_GET['path']) && !empty($_GET['path']))
{
	$req_path = tsl($_GET['path']);
	$spath = av::get('spath_data_uploads').$req_path;
	$cpath = av::get('cpath_data_uploads').$req_path;
	if(!path_is_safe($spath))
	{
		die('!path_is_safe');
	}
}
else
{ 
	$spath = av::get('spath_data_uploads');
	$req_path = ''; 
	$cpath = av::get('cpath_data_uploads');
}
// check if host uses Linux (used for displaying permissions
$isUnixHost = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? false : true);

// if a file was uploaded
if (isset($_FILES['file']))
{
	$uploadsCount = count($_FILES['file']['name']);
	if($uploadsCount > 0)
	{
	 $errors = array();
	 $messages = array();
	 for ($i=0; $i < $uploadsCount; $i++)
	{
		if ($_FILES["file"]["error"][$i] > 0)
		{
			$errors[] = i18n_r('ERROR_UPLOAD');
		}
		else
		{
			
			//set variables
			$count = '1';
			$file = $_FILES["file"]["name"][$i];

			$extension = pathinfo($file,PATHINFO_EXTENSION);
			if(getDef('GSUPLOADSLC',true)) $extension = lowercase($extension);
	  		$name      = pathinfo($file,PATHINFO_FILENAME);
			$name      = clean_img_name(to7bit($name));
			$base      = $name . '.' . $extension;

			$file_loc = $spath . $base;
			
			//prevent overwriting
			while ( file_exists($file_loc) )
			{
				$file_loc = $spath . $count.'-'. $base;
				$base = $count.'-'. $base;
				$count++;
			}
			
			//validate file
			if (validate_safe_file($_FILES["file"]["tmp_name"][$i], $_FILES["file"]["name"][$i])){
				move_uploaded_file($_FILES["file"]["tmp_name"][$i], $file_loc);
				if (defined('GSCHMOD')){
					chmod($file_loc, GSCHMOD);
				} else{
					chmod($file_loc, 0644);
				}
				event::create('file-uploaded');
				
				// generate thumbnail
				require_once('inc/imagemanipulation.php');
				genStdThumb($req_path,$base);
				$messages[] = i18n_r('FILE_SUCCESS_MSG').': <a href="'. $SITEURL .'/data/uploads/'.$req_path.$base.'">'. $SITEURL .'/data/uploads/'.$req_path.$base.'</a>';
			} else{
				$errors[] = $_FILES["file"]["name"][$i] .' - '.i18n_r('ERROR_UPLOAD');
			}
			//successfull message
		}
	}
	 // after uploading all files process messages
		if(sizeof($messages) != 0)
		{
			foreach($messages as $msg)
			{
				$success = $msg.'<br />';
			}
		}
		if(sizeof($errors) != 0)
		{
			foreach($errors as $msg)
			{
				$error = $msg.'<br />';
			}
		}
	}
}
// if creating new folder
if (isset($_GET['newfolder']))
{
	// check for csrf
	if (!defined('GSNOCSRF') || (GSNOCSRF == FALSE) )
	{
		$nonce = $_GET['nonce'];
		if(!check_nonce($nonce, "createfolder"))
		{
			die("CSRF detected!");
		}
	}
	$newfolder = $_GET['newfolder'];
	// check for invalid chars
	$cleanname = clean_url(to7bit(strippath($newfolder), "UTF-8"));
	if (file_exists($spath.$cleanname) || $cleanname=='')
	{
			$error = i18n_r('ERROR_FOLDER_EXISTS');
	}
	else
	{
		if (defined('GSCHMOD'))
		{ 
			$chmod_value = GSCHMOD; 
		}
		else
		{
			$chmod_value = 0755;
		}
		if (mkdir($spath . $cleanname, $chmod_value))
		{
			//create folder for thumbnails
			$thumbFolder = GSTHUMBNAILPATH.$req_path.$cleanname;
			if (!(file_exists($thumbFolder)))
			{
				mkdir($thumbFolder, $chmod_value);
			}
			$success = sprintf(i18n_r('FOLDER_CREATED'), $cleanname);
		}
		else
		{ 
			$error = i18n_r('ERROR_CREATING_FOLDER'); 
		}
	}
}

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('FILE_MANAGEMENT')); 

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php echo i18n('UPLOADED_FILES'); ?><span id="filetypetoggle">&nbsp;&nbsp;/&nbsp;&nbsp;<?php echo i18n('SHOW_ALL'); ?></span></h3>
			<div id="file_load">
<?php
$count="0";
$dircount="0";
$counter = "0";
$totalsize = 0;
$filesArray = array();
$dirsArray = array();
$filenames = getFiles($spath);
if (count($filenames) != 0)
{
	foreach ($filenames as $file)
	{
		if ( $file == "." || $file == ".." || $file == ".htaccess" || $file == "index.php" )
		{
			// not a upload file
		} 
		elseif (is_dir($spath . $file))
		{
			$dirsArray[$dircount]['name'] = $file;
			clearstatcache();
			$ss = @stat($spath . $file);
			$dirsArray[$dircount]['date'] = @date('M j, Y',$ss['mtime']);
			$dircount++;
		}
		else
		{
			$filesArray[$count]['name'] = $file;
			$ext = substr($file, strrpos($file, '.') + 1);
			$extention = get_FileType($ext);
			$filesArray[$count]['type'] = $extention;
			clearstatcache();
			$ss = @stat($spath . $file);
			$filesArray[$count]['date'] = @date('M j, Y',$ss['ctime']);
			$filesArray[$count]['size'] = fSize($ss['size']);
			$totalsize = $totalsize + $ss['size'];
			$count++;
		}
	}
	$filesSorted = subval_sort($filesArray,'name');
	$dirsSorted = subval_sort($dirsArray,'name');
}
?>
				<div class="edit-nav" >
					<select id="imageFilter" >
						<option value="All"><?=i18n_r('SHOW_ALL');?></option>;
<?php
if ($filesSorted && count($filesSorted) > 0)
{
	foreach ($filesSorted as $filter)
	{
		$filterArr[] = $filter['type'];
	}
	if (count($filterArr) != 0)
	{
		$filterArray = array_unique($filterArr);
		$filterArray = subval_sort($filterArray,'type');
		foreach ($filterArray as $type)
		{
			# check for image type
			if (strstr($type, ' Images'))
			{ 
				$typeCleaned = 'Images';
				$typeCleaned_2 = str_replace(' Images', '', $type);
			} else
			{
				$typeCleaned = $type;
				$typeCleaned_2 = $type;
			}
			echo '<option value="'.$typeCleaned.'">'.$typeCleaned_2.'</option>';
		}
	}
}?>
					</select>
					<div class="clear" ></div>
				</div>
<?php
			$pathParts = explode("/",$req_path);
			$urlPath = null;?>
				<div class="h5 clearfix">
					<div class="crumbs">/ <a href="upload.php">uploads</a> / 
<?php
foreach ($pathParts as $pathPart)
{
	if ($pathPart!='')
	{
		$urlPath .= $pathPart.'/';?>
		<a href="?path=<?=$urlPath;?>"><?=$pathPart;?></a> /
<?php
	}
} ?>
					</div>
					<div id="new-folder">
						<a href="#" id="createfolder"><?=i18n_r('CREATE_FOLDER');?></a>
						<form action="upload.php">
							<input type="hidden" name="path" value="<?=$req_path;?>" />
							<input type="hidden" name="nonce" value="<?=get_nonce("createfolder");?>" />
							<input type="text" class="text" name="newfolder" id="foldername" />
							<input type="submit" class="submit" value="<?=i18n_r('CREATE_FOLDER');?>" />
							<a href="#" class="cancel"><?=i18n_r('CANCEL');?></a>
						</form>
					</div>
				</div>
				<table class="highlight" id="imageTable">
					<tr>
						<th class="imgthumb" ></th>
						<th><?=i18n_r('FILE_NAME');?></th>
						<th style="text-align:right;"><?=i18n_r('FILE_SIZE');?></th>
						<th style="text-align:right;"><?=i18n_r('DATE');?></th>
						<th><!-- actions --></th>
					</tr>  
<?php
if ($dirsSorted && count($dirsSorted) != 0)
{
	$foldercount = 0;
	foreach ($dirsSorted as $upload)
	{
		# check to see if folder is empty
		$directory_delete = null;
		if ( check_empty_folder($spath.$upload['name']) )
		{
			$directory_delete = '<a class="delconfirm" title="'.i18n_r('DELETE_FOLDER').': '. rawurlencode($upload['name']) .'" href="deletefile.php?path='.$urlPath.'&amp;folder='. rawurlencode($upload['name']) . '&amp;nonce='.get_nonce("delete", "deletefile.php").'">&times;</a>';
		}
		$directory_size = '<span>'.folder_items($spath.$upload['name']).' '.i18n_r('ITEMS').'</span>';
		$newreq_path = $req_path . rawurlencode($upload['name']);
?>
					<tr class="folder <?=$upload['name'];?>" >
						<td class="imgthumb" >
							<img src="<?=$folder_img;?>" width="11" /></td>
						<td>
							<a href="upload.php?path=<?=$newreq_path;?>" ><strong><?=htmlspecialchars($upload['name']);?></strong></a>
						</td>
						<td style="width:80px;text-align:right;" ><span><?=$directory_size;?></span></td>
						<td style="width:85px;text-align:right;" ><span><?=shtDate($upload['date']);?></span></td>
						<td class="delete" ><?=$directory_delete;?></td>
					</tr>
<?php
		$foldercount++;
	}
}
if ( $filesSorted && count($filesSorted) != 0)
{
	foreach ($filesSorted as $upload)
	{
		$counter++;
		if ($upload['type'] == i18n_r('IMAGES') .' Images')
		{
			$cclass = 'iimage';
		}
		else
		{
			$cclass = '';
		}
		echo '<tr class="All '.$upload['type'].' '.$cclass.'" >';
		echo '<td class="imgthumb" >';
		if ($upload['type'] == i18n_r('IMAGES') .' Images')
		{
			$gallery = 'rel=" facybox_i"';
			$pathlink = 'image.php?i='.rawurlencode($upload['name']).'&amp;path='.$req_path;
			$thumbLink = $urlPath.'thumbsm.'.$upload['name'];
			$thumbLinkEncoded = $urlPath.'thumbsm.'.rawurlencode($upload['name']);
			if (file_exists(GSTHUMBNAILPATH.$thumbLink)){
				$imgSrc='<img src="../data/thumbs/'. $thumbLinkEncoded .'" />';
			} else{
				$imgSrc='<img src="inc/thumb.php?src='. $urlPath . rawurlencode($upload['name']) .'&amp;dest='. $thumbLinkEncoded .'&amp;f=1" />';
			}
			echo '<a href="'. $cpath . rawurlencode($upload['name']) .'" title="'. rawurlencode($upload['name']) .'" rel=" facybox_i" >'.$imgSrc.'</a>';
		}
		else
		{
			$gallery = '';
			$controlpanel = '';
			$pathlink = $cpath . $upload['name'];
		}
		echo '</td><td><a title="'.i18n_r('VIEW_FILE').': '. htmlspecialchars($upload['name']) .'" href="'. $pathlink .'" class="primarylink">'.htmlspecialchars($upload['name']) .'</a></td>';
		echo '<td style="width:80px;text-align:right;" ><span>'. $upload['size'] .'</span></td>';
		echo '<td style="width:85px;text-align:right;" ><span>'. shtDate($upload['date']) .'</span></td>';
		echo '<td class="delete" ><a class="delconfirm" title="'.i18n_r('DELETE_FILE').': '. htmlspecialchars($upload['name']) .'" href="deletefile.php?file='. rawurlencode($upload['name']) . '&amp;path=' . $urlPath . '&amp;nonce='.get_nonce("delete", "deletefile.php").'">&times;</a></td>';
		echo '</tr>';
	}
}
event::create('file-extras');
?>
				</table>
<?php
if ($counter > 0)
{ 
	$sizedesc = '('. fSize($totalsize) .')';
}
else
{
	$sizedesc = '';
}
$totalcount = (int)$counter+(int)$foldercount;?>
				<p><em><span id="pg_counter"><?=$totalcount;?></span> <?=i18n_r('TOTAL_FILES');?> <?=$sizedesc;?></em></p>
			</div> <!-- file_load -->
		</div> <!-- main -->
	</div> <!-- maincontent -->
<?php include('template/sidebar-files.php'); ?>
</div> <!-- bodycontent clearfix -->
<?php get_template('footer'); ?>
