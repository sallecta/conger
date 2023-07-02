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
	} // opt_selected
	//public static function render_fields($a_ndx, $a_field, $a_class='', $a_issearch=false)
	private static function render_fields()
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
				$field_class='sortable';
				if ($isdropdown && isset($field['options']) && count($field['options']) > 0)
				{
					foreach ($field['options'] as $option) $options .= $option . "\r\n";
				}
				require('field_admin_render_fields.tpl.php'); 
				$ndx++;
			}
			$field = array();
			$field_class='hidden';
			require('field_admin_render_fields.tpl.php'); 
		}
	} // render_fields

	public static function render()
	{
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
					field::$cmsg .= ' <a href="'.field::$ui.'?undo">'.i18n_r('UNDO').'</a>';
				}
				field::get_fields_and_types();
				$success = true;
			}
			else
			{
				if ($names)
				{
					field::$cmsg = i18n_r('field/SAVE_INVALID').' '.implode(', ', $names);
				}
				else
				{
					field::$cmsg = i18n_r('field/SAVE_FAILURE');
				}
				field::$fields = array();
				for ($i=0; isset($_POST['cf_'.$i.'_key']); $i++)
				{
					$cf = array();
					$cf['key'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_key']), ENT_QUOTES);
					$cf['about'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_about']), ENT_QUOTES);
					$cf['type'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_type']), ENT_QUOTES);
					$cf['value'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_value']), ENT_QUOTES);
					$cf['options'] = preg_split("/\r?\n/", rtrim(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_value']), ENT_QUOTES)));
					field::$fields[] = $cf;
				}
				array_pop(field::$fields); // remove the last hidden line
			}
		}
		$fields_total=&field::$fields_total;
		require('field_admin_render.tpl.php');
	} // render
} // a_class field_admin extends field

field_admin::render();
