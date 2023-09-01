<?php if(!defined('APP')){ die('you cannot load this page directly.'); }
class path
{
	/* A marker $app_root_fname file in your App's root dir is required */
	protected static function get( $a_type )
	{
		$app_root_fname='app_root.php';
		$doc_root=$_SERVER["DOCUMENT_ROOT"];
		$script_dirs=$doc_root.dirname($_SERVER["SCRIPT_NAME"]);
		//echo "<pre> -- script_dirs=".$script_dirs."</pre>";
		$scr_dirs_last_ch = substr($script_dirs, -1);
		//remove starting slash
		$script_dirs=substr($script_dirs, 1);
		//remove trailing slash
		if ( substr($script_dirs, -1)=='/')
		{
			$script_dirs=substr($script_dirs, 0, -1);
		}
		//end
		$script_dirs = explode("/", $script_dirs);
		//echo "<pre> -- script_dirs=".json_encode($script_dirs)."</pre>";
		//echo "<pre> -- array_key_last(script_dirs)=".array_key_last($script_dirs)."</pre>";
		for ($ndx = array_key_last($script_dirs); $ndx >=0; $ndx--) // reverse loop
		{
			$path_current = implode("/",array_slice($script_dirs,0,$ndx+1));
			//echo "<pre> ---- searching in $path_current ($ndx)</pre>";
			$fullname = "/$path_current/$app_root_fname";
			if( file_exists($fullname) )
			{
				//echo "<pre> ------ Yes :)</pre>";
				if ( $a_type == 'client' )
				{
					$out = "$path_current";
					$out = substr($out,strlen($doc_root));
					if ( empty($out) ) { $out = '/'; }
					else { $out = "/$out/"; }
					//echo "<pre> ----- cl out=$out</pre>";
					break;
				}
				if ( $a_type == 'server' )
				{
					$out = "/$path_current/";
					//echo "<pre> ----- srv out=$out</pre>";
					break;
				}
				break;
			}
			else
			{
				//echo "<pre> ----- No :(</pre>";
				$out = '/';
			}
		}
		//echo "<hr>";
		return $out;
	}
}
