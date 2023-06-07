<?php if(!defined('APP')){ die('you cannot load this page directly.'); }
class action
{
	public static function add($a_action, $a_added_function, $a_added_func_args = array(), $a_bt=null)
	{
		global $plugins, $live_plugins; 
		if ( !$a_bt ) { $a_bt = debug_backtrace(); }
		$shift=count($a_bt) - 4; // plugin name should be  
		// call_user_func and call_user_func_array missing in php 7
		if(getDef('GSBTFIX',true) && version_compare(PHP_VERSION, '7.0.0', '>='))
		{
		    $shift--;
		}
		$caller = array_shift($a_bt);
		$realPathName=pathinfo_filename($caller['file']);
		$realLineNumber=$caller['line'];
		while ($shift > 0)
		{
			 $caller = array_shift($a_bt);
			 $shift--;
		}
		$pathName= pathinfo_filename($caller['file']);
		if (( isset ($live_plugins[$pathName.'.php']) && $live_plugins[$pathName.'.php']=='true') || $shift<0 )
		{
			if ($realPathName!=$pathName)
			{
				$pathName=$realPathName;
				$lineNumber=$realLineNumber;
			}
			else
			{
				$lineNumber=$caller['line'];
			}
			$plugins[] = array
			(
				'hook' => $a_action,
				'function' => $a_added_function,
				'args' => (array) $a_added_func_args,
				'file' => $pathName.'.php',
				'line' => $caller['line']
			);
		}
	}
	
	public static function name($a_name)
	{
		global $plugins;
		
		foreach ($plugins as $hook)
		{
			if ($hook['hook'] == $a_name)
			{
				call_user_func_array($hook['function'], $hook['args']);
			}
		}
	}
}


/* GetSimle compatability*/

/* deprecated */
function add_action($a_action, $a_added_function, $a_added_func_args = array())
{
	global $plugins, $live_plugins;
	$bt = debug_backtrace(); 
	action::add($a_action, $a_added_function, $a_added_func_args, $bt);
}

function exec_action($a_name) /* deprectated */
{
	action::name($a_name);
}
