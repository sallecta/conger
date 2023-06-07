<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }


# Get this theme's settings based on what was entered within its plugin. 
# This function is in functions.php 
$renov_settings = Renovation_Settings();

if ((string)$dataw->PAGE_TIME_STAMP == '1')
{ $timestamp=true; }
else
{ $timestamp=false; }
