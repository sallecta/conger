<?php if(!defined('APP')){ die('you cannot load this page directly.'); }
class filter // actually 'modifier'
{
	private static $filters = array();
	public static function join($a_filter, $a_exec)
	{
		self::$filters[] = array
		(
			'name' => $a_filter,
			'exec' => $a_exec
		);
	}
	
	public static function create($a_filter,$a_data)
	{
		$filters = &self::$filters;
		foreach ( $filters as $filter )
		{
			if ($filter['name'] == $a_filter)
			{
				$ready = false;
				$exec=&$filter['exec'];
				$exec_type = gettype($exec);
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
				if ( $ready )
				{
					$a_data = call_user_func_array($exec, [$a_data]);
				}
			}
		}
		return $a_data;
	} // create
} // filter

/* GetSimple compatability */

function add_filter($a_filter, $a_exec)
{
	filter::join($a_filter, $a_exec);
}

function exec_filter($a_filter,$a_data)
{
	return filter::create($a_filter,$a_data);
}

