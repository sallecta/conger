<?php if(!defined('APP')){ die('you cannot load this page directly.'); }
class event
{
	private static $events = array();
	
	public static function join($a_event, $a_exec, $a_exec_args = array())
	{
		$events = &self::$events; 
		$events[] = array
		(
			'name' => $a_event,
			'exec' => $a_exec,
			'args' => (array) $a_exec_args
		);
	}
	
	public static function create($a_name)
	{
		$events = &self::$events;
		
		foreach ($events as $event)
		{
			if ($event['name'] == $a_name)
			{
				$ready = false;
				$exec=&$event['exec'];
				$exec_type = gettype($exec);
				//echo '<pre> event: '.$a_name. ': executable: '. 
					//json_encode($exec).'</pre>';
				if ( $exec_type == 'string' )
				{
					if ( function_exists($exec) )
					{
						$ready = true;
					}
				}
				elseif (  $exec_type == 'array'  )
				{
					if ( method_exists($exec[0],$exec[1]) )
					{
						$ready = true;
					}
				}
				else
				{
					//echo '<pre>'.basename(__FILE__).': event: '.
						//$a_name. ': wrong executable '. json_encode($exec) .
						//' type ('.$exec_type.')</pre>';
				}
				if ( $ready )
				{
					call_user_func_array($exec, $event['args']);
				}
				else
				{
					//echo '<pre>'.basename(__FILE__).': event: '.
						//$a_name. ': executable '. json_encode($exec) .
						//' not found</pre>';
				}
			}
		}
	}
}


/* GetSimle compatability*/

function add_event($a_event, $a_exec, $a_exec_args = array())
{
	event::join($a_event, $a_exec, $a_exec_args);
}

function exec_event($a_name) 
{
	event::create($a_name);
}
