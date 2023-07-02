<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php
define('dev',true);
class dev
{
	public static function epre($arg=Null)
	{
		if(!defined('dev')){ return; }
		$bt = debug_backtrace();
		$line=$bt[0]['line'];
		$file=basename($bt[0]['file']);
		$out='<pre>'.$file.': '.$line;
		$out=$out.': '.json_encode($arg,JSON_UNESCAPED_UNICODE);
		$out=$out.'</pre>';
		echo $out;
	}
	public static function rhtmlcom($arg=Null,$a_bt=Null)
	{
		if(!defined('dev')){ return; }
		if(!$a_bt) { $a_bt = debug_backtrace(); }
		$line=$a_bt[0]['line'];
		$file=basename($a_bt[0]['file']);
		$out='<!-- '.$file.': '.$line;
		$out=$out.': '.json_encode($arg,JSON_UNESCAPED_UNICODE);
		$out=$out.' -->';
		return $out;
	}
	public static function ehtmlcom($arg=Null)
	{
		if(!defined('dev')){ return; }
		echo self::rhtmlcom($arg,debug_backtrace());
	}
	public static function enl($arg=Null)
	{
		if(!defined('dev')){ return; }
		if ( $arg ) echo $arg . "\n";
	}
}
