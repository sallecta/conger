<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php


define('ondev',true);//true/false

class dev
{
	public static $jpretty = JSON_PRETTY_PRINT;
	public static function epre($arg=Null)
	{
		if(!ondev){ return; }
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
		if(!ondev){ return; }
		if(!$a_bt) { $a_bt = debug_backtrace(); }
		$line=$a_bt[0]['line'];
		$file=basename($a_bt[0]['file']);
		$out='<!-- '.$file.': '.$line;
		$out=$out.': '.json_encode($arg,JSON_UNESCAPED_UNICODE);
		$out=$out.' -->'."\n";
		return $out;
	}
	public static function rcsscom($arg=Null,$a_bt=Null)
	{
		if(!ondev){ return; }
		if(!$a_bt) { $a_bt = debug_backtrace(); }
		$line=$a_bt[0]['line'];
		$file=basename($a_bt[0]['file']);
		$out='/* '.$file.': '.$line;
		$out=$out.': '.json_encode($arg,JSON_UNESCAPED_UNICODE);
		$out=$out.' */'."\n";
		return $out;
	}
	public static function ehtmlcom($arg=Null)
	{
		if(!ondev){ return; }
		printf(self::rhtmlcom($arg,debug_backtrace()));
	}
	public static function ecsscom($arg=Null)
	{
		if(!ondev){ return; }
		printf(self::rcsscom($arg,debug_backtrace()));
	}
	public static function enl($arg=Null)
	{
		if(!ondev){ return; }
		if ( $arg ) {printf($arg . "\n");}
	}
	public static function pr($arg=Null)
	{
		if(!ondev){ return; }
		if ( $arg ) {printf($arg);}
	}
}
