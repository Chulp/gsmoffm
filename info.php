<?php
/*
 *  @module         Office toolset legal
 *  @version        see below
 *  @author         Gerard Smelt
 *  @copyright      2010-2015 Contracthulp B.V.
 *  @license        see below
 *  @platform       see below
 */

// include class.secure.php to protect this file and the whole CMS!
if ( defined( 'LEPTON_PATH' ) ) {
  include( LEPTON_PATH . '/framework/class.secure.php' );
} else {
  $oneback = "../"; $root = $oneback; $level = 1;
  while ( ( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) ) ) { $root .= $oneback; $level += 1; } 
  if ( file_exists( $root . '/framework/class.secure.php' ) ) {
    include( $root . '/framework/class.secure.php' );
  } else {
    trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
  }
}
// end include class.secure.php
$module_directory = 'gsmoffm';
$module_name = 'Office Module';
$module_function = 'page';
$module_version = '2.1.0';
$module_platform = '2.0.0';
$module_author = 'Gerard Smelt';
$module_license = 'All rights reserved';
$module_license_terms = 'All rights reserved';
$module_guid = 'DC0CAA6A-ACB5-4B5A-B7DC-5E91823402CC';
$module_description = 'This module provides basic functionality for the gsm office application. Tools application is to be pre-installed';
$module_home = 'http://www.contracthulp.nl';

/* guid via UUID-GUID Generator Portable 1.1. */
?>
