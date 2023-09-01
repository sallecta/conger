<?php if(!defined('APP')){ die('you cannot load this page directly.'); }
class client
{
	private static $arr = array();
	
	const js = 1;
	const css = 2;
	
	
	public static function get($a_event, $a_exec, $a_exec_args = array())
	{
		$events = &self::$events; 
		$events[] = array
		(
			'name' => $a_event,
			'exec' => $a_exec,
			'args' => (array) $a_exec_args
		);
	}
	
	public static function set($a_name)
	{
		return 'not implemented';
	}
}

