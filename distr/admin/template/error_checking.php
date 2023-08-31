<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
		<?=dev::rhtmlcom('start');?>
<?php
	$tr='i18n_r';
	if ( file_exists(GSUSERSPATH._id($USR).".xml.reset") && get_filename_id()!='index' && get_filename_id()!='resetpassword' )
{?>
	<div class="error"><p><?=$tr('ER_PWD_CHANGE');?></p></div>
<?php }
	
if ((!defined('GSNOAPACHECHECK') || GSNOAPACHECHECK == false) and !server_is_apache())
{?>
	  <div class="error"><?=$tr('WARNING');?>: <a href="health-check.php"><?=$tr('SERVER_SETUP');?> non-Apache</a></div>
<?php
}

if(!isset($update)) $update = '';
$err = '';
$restored = '';
$filter_opt=FILTER_SANITIZE_SPECIAL_CHARS;
if(isset($_GET['upd'])) $update = ( function_exists( "filter_var") ) ? filter_var ( $_GET['upd'], $filter_opt)  : htmlentities($_GET['upd']);
if(isset($_GET['success'])) $success = ( function_exists( "filter_var") ) ? filter_var ( $_GET['success'], $filter_opt)  : htmlentities($_GET['success']);
if(isset($_GET['error'])) $error = ( function_exists( "filter_var") ) ? filter_var ( $_GET['error'], $filter_opt)  : htmlentities($_GET['error']);
if(isset($_GET['err'])) $err = ( function_exists( "filter_var") ) ? filter_var ( $_GET['err'], $filter_opt)  : htmlentities($_GET['err']);
if(isset($_GET['id'])) $errid = ( function_exists( "filter_var") ) ? filter_var ( $_GET['id'], $filter_opt)  : htmlentities($_GET['id']);
if(isset($_GET['updated']) && $_GET['updated'] ==1)	$success = $tr('SITE_UPDATED');

switch ( $update )
{
	case 'bak-success':
		echo '<div class="updated"><p>'. sprintf($tr('ER_BAKUP_DELETED'), $errid) .'</p></div>';
		break;
	case 'bak-err':
		echo '<div class="error"><p><b>'.$tr('ERROR').':</b> '.$tr('ER_REQ_PROC_FAIL').'</p></div>';
		break;
	case 'edit-success':
		echo '<div class="updated"><p>';
		if ($ptype == 'edit')
		{ 
			echo sprintf($tr('ER_YOUR_CHANGES'), $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.$tr('UNDO').'</a>';
		}
		elseif ($ptype == 'restore')
		{
			echo sprintf($tr('ER_HASBEEN_REST'), $id);
		}
		elseif ($ptype == 'delete')
		{
			echo sprintf($tr('ER_HASBEEN_DEL'), $errid) .'. <a href="backup-edit.php?p=restore&id='. $errid .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.$tr('UNDO').'</a>';
		}
		else if($ptype == 'new')
		{
			echo sprintf($tr('ER_YOUR_CHANGES'), $id) .'. <a href="deletefile.php?id='. $id .'&nonce='.get_nonce("delete", "deletefile.php").'">'.$tr('UNDO').'</a>';
		}
		echo '</p></div>';
		break;
	case 'clone-success':
		echo '<div class="updated"><p>'.sprintf($tr('CLONE_SUCCESS'), '<a href="edit.php?id='.$errid.'">'.$errid.'</a>').'.</p></div>';
		break;
	case 'edit-index':
		echo '<div class="error"><p><b>'.$tr('ERROR').':</b> '.$tr('ER_CANNOT_INDEX').'.</p></div>';
		break;
	case 'edit-error':
		echo '<div class="error"><p><b>'.$tr('ERROR').':</b> '. var_out($ptype) .'.</p></div>';
		break;
	case 'pwd-success':
		echo '<div class="updated"><p>'.$tr('ER_NEW_PWD_SENT').'. <a href="index.php">'.$tr('LOGIN').'</a></p></div>';
		break;
	case 'pwd-error':
		echo '<div class="error"><p><b>'.$tr('ERROR').':</b> '.$tr('ER_SENDMAIL_ERR').'.</p></div>';
		break;
	case 'del-success':
		echo '<div class="updated"><p>'.$tr('ER_FILE_DEL_SUC').': <b>'.$errid.'</b></p></div>';
		break;
	case 'flushcache-success':
		echo '<div class="updated"><p>'.$tr('FLUSHCACHE-SUCCESS').'</p></div>';
		break;
	case 'del-error':
		echo '<div class="error"><p><b>'.$tr('ERROR').':</b> '.$tr('ER_PROBLEM_DEL').'.</p></div>';
		break;
	default:
		if ( isset( $error ) )
		{
			echo '<div class="error"><p><b>'.$tr('ERROR').':</b> '. $error .'</div>';
		}
		else if ($restored == 'true')
		{
			echo '<div class="updated"><p>'.$tr('ER_OLD_RESTORED').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.$tr('UNDO').'</a></p></div>';
		}
		else if ( isset($_GET['rest']) && $_GET['rest']=='true' )
		{
			echo '<div class="updated"><p>'.$tr('ER_OLD_RESTORED').'. <a href="support.php?undo&nonce='.get_nonce("undo", "support.php").'">'.$tr('UNDO').'</a></p></div>';
		}
		elseif (isset($_GET['cancel']))
		{
			echo '<div class="error"><p>'.$tr('ER_CANCELLED_FAIL').'</p></div>';
		}
		elseif (isset($error))
		{
			echo '<div class="error"><p>'.$error.'</div>';
		}
		elseif (!empty($err))
		{
			echo '<div class="error"><p><b>'.$tr('ERROR').':</b> '.$err.'</p></div>';
		}
		elseif (isset($success))
		{
		dev::ehtmlcom(['case default',$update]);
			echo '<div class="updated"><p>'.$tr('success').'</p></div>';
		}
		elseif ( $restored == 'true') 
		{
			echo '<div class="updated"><p>'.$tr('ER_OLD_RESTORED').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.$tr('UNDO').'</a></p></div>';
		}
}
?>
		<?=dev::rhtmlcom('end');?>
