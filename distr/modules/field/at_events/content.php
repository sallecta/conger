<?php if(!defined('APP')){ die('you cannot load this page directly.'); } ?>
<?php
		$tagO='\[{1}\${1}';
		$tagC='\${1}\]{1}';
		$tagName='([\w\=\,\(\)]{1,200})';
		$regex='/'.$tagO.$tagName.$tagC.'/';
		
		preg_match_all($regex,$a_content, $matches, PREG_SET_ORDER);
		if (empty($matches))
		{
			//echo 'no matches';
			return $a_content;
		}
		$arg_chars=[ '=' , '(' , ')' , ';' ];
		$replaced_fields=[];
		foreach ( $matches as $ndx => $key )
		{
			$shorcode = &$key[0];
			$field_name = &$key[1];
			if ( in_array($field_name, $replaced_fields) )
			{
				//dev::ehtmlcom("$field_name allready replaced, skipping");
				continue;
			}
			$minshc=2;
		
			$ab_st=strpos($field_name, '(',$minshc);
			$ab_en=strpos($field_name, ')',-1);
			$args_block_exists = ( $ab_st && $ab_en );
			if ($args_block_exists)
			{
				//echo "--args block exists:  ". json_encode($args_block_exists). " in ".$field_name." \n";
				$args = substr($field_name,$ab_st+1);
				$args = substr($args,0,-1);
				$args = explode(',',$args);
				foreach ( $args as $key=>$value)
				{
					$kv = explode('=',$value);
					if (count($kv)!==2)
					{
						$args=false;
						break;
					}
					$args[$kv[0]]=$kv[1];
					unset($args[$key]);
				}
				$field_name=substr($field_name,0,$ab_st);
			}
			else
			{
				//echo "--args_block_exists:  ". json_encode($args_block_exists). " in ".$field_name." \n";
				$args=false;
				foreach (mb_str_split($field_name) as $char)
				{
					if ( in_array($char, $arg_chars, true) )
					{
						//echo '--bad char ['.$char.'] in shc: '.$field_name."\n";
						$field_name=false;
						break;
					}
				}
			} // no arg block
			if ( !$field_name )
			{
				continue;
			}
			//dev::ehtmlcom(['working on shortcode field',$field_name]);
			if ( !$args )
			{
				if (field::get_by_ref($field_name,$outval) )
				{
					$a_content = str_replace($shorcode,$outval,$a_content);
					array_push($replaced_fields, $field_name);
					//dev::ehtmlcom(['shortcode',$shorcode,' has known field',$field_name]);
				}
				else
				{
					//dev::ehtmlcom(['shortcode',$shorcode,' has uncknown field',$field_name]);
					continue;
				}
			}
			else if ( $args )
			{
				if (field::get_by_ref($field_name,$outval,$args) )
				{
					$a_content = str_replace($shorcode,$outval,$a_content);
					array_push($replaced_fields, $field_name);
				}
				else
				{
					//dev::ehtmlcom(['shortcode',$shorcode,' has uncknown field',$field_name]);
					continue;
				}
			}
			//echo "args:  ". json_encode($args). " \n";
		} // foreach ( $matches as $ndx => $key )
		dev::ehtmlcom(['replaced_fields',$replaced_fields]);
