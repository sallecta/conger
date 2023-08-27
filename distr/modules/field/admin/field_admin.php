<?php
require_once '../../../admin/inc/common.php';
login_cookie_check();
require_once(av::get('spath_admin_inc').'plugin_functions.php');

class field_admin extends field
{
	private static function option_selected($a_option, $a_key, &$a_field)
	{
		if ( empty($a_field[$a_key]) ) { return ''; }
		$field_opt = &$a_field[$a_key];
		if ( $field_opt == $a_option )
		{
			return ' selected="selected"';
		}
		else
		{
			return '';
		}
	} // option_selected
	private static function render_field()
	{
		$ndx = 0;
		$fields_total=&field::$fields_total;
		$oselected='field_admin::option_selected';
		if ($fields_total > 0)
		{
			foreach (field::$fields as $field)
			{
				$isdropdown = $field['type'] == 'dropdown';
				$indexable = !$field['type'] || in_array($field['type'],array('text','textfull','dropdown','web_editor', 'checkbox'));
				$options = "\r\n";
				if ($isdropdown && isset($field['options']) && count($field['options']) > 0)
				{
					foreach ($field['options'] as $option) $options .= $option . "\r\n";
				}
				require(field::$spath.'admin/tpl/field_admin_render_field.tpl.php'); 
				$ndx++;
			}
			$field = array();
			$field_class='hidden';
			//require('field_admin_render_field.tpl.php'); 
		}
	} // render_field
	
	
	private static function render_sidebar()
	{
		require_once(field::$spath.'admin/tpl/field_admin_sidebar.tpl.php');
	}
	
	public static function render()
	{
		$_SESSION['render'] = 'green';
		field::$active=true;
		if (isset($_GET['undo']) && !isset($_POST['save']))
		{
			if (field::undo())
			{
				field::$cmsg = i18n_r('field/UNDO_SUCCESS');
				$success = true;
			}
			else
			{
				field::$cmsg = i18n_r('field/UNDO_FAILURE');
			}
		}
		else if (isset($_POST['save']))
		{
			$names = field::fields_sys_in_request();
			if (!$names && field::save_fields())
			{
				field::$cmsg = i18n_r('field/SAVE_SUCCESS');
				if ( file_exists( field::$backupfile ) )
				{
					field::$cmsg .= ' <a href="'.field::$cpath.'admin/field_admin.php?undo">'.i18n_r('UNDO').'</a>';
				}
				field::get_fields_and_types();
				$success = true;
			}
			else
			{
				field::$cmsg = i18n_r('field/SAVE_FAILURE');
			}
		}
		$fields_total=&field::$fields_total;
		//dev::ehtmlcom(field::$spath.'admin/tpl/field_admin_render.tpl.php');
		require_once(field::$spath.'admin/tpl/field_admin_render.tpl.php');
	} // render
} // a_class field_admin extends field

field_admin::render();
