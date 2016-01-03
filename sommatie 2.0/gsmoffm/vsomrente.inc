<?php
/*
 *  @module         Office toolset legal
 *  @version        see info.php versie below
 *  @author         Gerard Smelt
 *  @copyright      2010 - 2015, Contracthulp B.V.
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
 */
 
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

$consument_rente_percentages = array (
  '2002-01-01'=>7,
  '2003-08-01'=>5,
  '2004-01-01'=>4,
  '2007-01-01'=>6,
  '2009-07-01'=>4,
  '2010-01-01'=>3,
  '2011-07-01'=>4,
  '2012-07-01'=>3,
  '2015-01-01'=>2);
  
$inkasso_tarieven= array( 
  'a2500'=>15,
  'b2500'=>10,
  'c5000'=>5,
  'd190000'=>1,
  'e5000000'=>0.5);
$inkasso_tarieven_btwcompensatie=1.21;
$inkasso_tarieven_min=40;
$inkasso_tarieven_max=6675;
$handels_rente_percentages = array (
  '2002-01-01' =>7,
  '2002-12-01' =>10.35,
  '2003-01-01' =>9.85,
  '2003-07-01' =>9.10,
  '2004-01-01' =>9.02,
  '2004-07-01' =>9.01,
  '2005-01-01' =>9.01,
  '2005-07-01' =>9.05,
  '2006-01-01' =>9.25,
  '2006-07-01' =>9.83,
  '2007-01-01' =>10.58,
  '2007-07-01' =>11.07,  
  '2008-01-01' =>11.20,
  '2008-07-01' =>11.07, 
  '2009-01-01' =>9.5,
  '2009-07-01' =>8,  
  '2010-01-01' =>8,  
  '2011-01-01' =>8, 
  '2011-07-01' =>8.25,     
  '2012-01-01' =>8,
  '2013-01-01' =>7.75,
  '2013-07-01' =>8.5,
  '2014-01-01' =>8.25,
  '2014-07-01' =>8.15,
  '2015-01-01' =>8.05);
 
$regios = array (
'0000'=> 'AV',
'8000'=> 'CH', 
'9999'=> 'AV
'); 
  
  