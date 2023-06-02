<?php if(!defined('APP')){ die('you cannot load this page directly.'); }
class paths
{
	private static $path_client;
	public static function client($arg_file_path)
	{
		if ( self::$path_client )
		{ return self::$path_client; }
		$doc_root = $_SERVER["DOCUMENT_ROOT"];
		$len_doc_root = strlen($doc_root);
		$spath = dirname($arg_file_path);
		$cmp_result = strncmp($spath,$doc_root,strlen($spath));
		if ( $cmp_result > 0 )
		{
			$client_path = substr($spath,$len_doc_root);
		}
		else 
		{
			$client_path = '/';
		}
		self::$path_client=$client_path;
		return $client_path;
	}
}
