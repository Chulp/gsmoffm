<?php
/*
 *  @module         Office toolset
 *  @version        see below
 *  @author         Gerard Smelt
 *  @copyright      2010-2016 Contracthulp B.V.
 *  @license        see below
 *  @platform       see below
 */
// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {  
  include(WB_PATH.'/framework/class.secure.php'); 
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
  include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
  $subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));  $dir = $_SERVER['DOCUMENT_ROOT'];
  $inc = false;
  foreach ($subs as $sub) {
    if (empty($sub)) continue; $dir .= '/'.$sub;
    if (file_exists($dir.'/framework/class.secure.php')) { 
  include($dir.'/framework/class.secure.php'); $inc = true;  break; 
    } 
  }
  if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include class.secure.php

// module description
$module_description = 'Basic Office Module functionality (NL).';

// declare module language array

$MOD_GSMOFF [ 'add' ] = 'Nieuw';
?>