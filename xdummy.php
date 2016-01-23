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
/* change history
 */
/*
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'dummy',
// voor versie display
  'modulen' => 'xdummy',
  'versie' => ' v20151120 '
);
/*
 * Layout template
 *
 * in language module
 * $template[0-3] in language module
 * $icontemp [0-19]
 * $linetemp [0-19]
 */
$MOD_GSMOFF [ 'VERS' ] = ' no functionality';

/*
 * debug used data
 */
if ( $debug ) {
  Gsm_debug( $regelsArr, __LINE__ );
  Gsm_debug( $settingArr, __LINE__);
  Gsm_debug( $_POST, __LINE__ );
  Gsm_debug( $_GET, __LINE__ );
  Gsm_debug( $place, __LINE__ );
}  
/*
 * the output to the screen
 */
$regelsArr[ 'hash' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
$_SESSION[ 'page_h' ] = $regelsArr[ 'hash' ];
if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
switch ( $regelsArr[  'mode'  ] ) {
  default:
    $parseViewArray = array(
      'header' => "",
      'message' => message( $msg, $debug ),
      'return' => CH_LOGIN,
      'module' => $regelsArr[ 'module' ],
      'page_id' => $page_id,
      'section_id' => $section_id,
      'hash' => $regelsArr[ 'hash' ],
      'recid' => "", 
      'memory' => "", 
      'kopregels' => "",
      'description' => "",
      'selection' => "",
      'rapportage' => "",
      'toegift' => ""
    );
    $prout .= Gsm_prout ($TEMPLATE[ 2 ], $parseViewArray);
    break;
}
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie'].$MOD_GSMOFF [ 'VERS' ]."</small>";}
?>