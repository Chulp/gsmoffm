<?php
/*
 *  @module         Office toolset legal
 *  @version        see info.php versie below
 *  @author         Gerard Smelt
 *  @copyright      2010 - 2015, Contracthulp B.V.
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
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

/*
 * initial settings 
 */
$regelsArr_display = array(
// voor versie display
  'modulen' => 'view',
  'versie' => ' v20151121 '
); 
$hulp = explode("\\", str_replace (WB_PATH,"",dirname(__FILE__)));
if (isset($hulp[2])){  // stand alone
  define('CH_MODULE', substr($hulp[2], 0, -1));
  define('CH_SUFFIX', substr($hulp[2], -1));
} else { // on-line
  $hulp = explode("/", str_replace (WB_PATH,"",dirname(__FILE__)));
  define('CH_MODULE', substr($hulp[2], 0, -1));
  define('CH_SUFFIX', substr($hulp[2], -1));
}
//location of the settings data
require_once (WB_PATH . MEDIA_DIRECTORY . '/'.CH_MODULE.'/settings/init.php');
/*
 * application settings load 
 */
$query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='".$page_id."' OR `section`='0' ORDER BY `section`";
$message = __LINE__." EN : Oeps unexpected case" . $query . "</br>";
$results = $database->query( $query ); 
if ( !$results || $results->numRows() == 0 ) die( $message );
$settingsArr = array ();
while ( $row = $results->fetchRow() ) { 
  if (strlen($row['table'])<1) $settingArr[$row['name']]= $row['value']; 
}
unset ($query);
$debug=false;
if (isset($settingArr['debug']) && $settingArr['debug']=="yes") $debug=true; 
/*
 * Location of files
 */
//places of the includes
$place_incl = (dirname( __FILE__ )).'/';
//places of the language file 
$place_lang  = ( dirname( __FILE__ ) ) . '/languages/' . LANGUAGE . '.php';
// load module language file
require_once(!file_exists($place_lang) ? (dirname(__FILE__)) . '/languages/EN.php' : $place_lang );
// load includes
require_once($place_incl.'includes.php' );

// define section menu, default (the first) and the description
$set_mode = (isset($settingArr['mode'])) ? $settingArr['mode'] : "file";
if (isset($settingArr['menu'])) { 
  $hulp= explode ("|", $settingArr['menu']); 
  foreach ($hulp as $key => $value) { $set_menu['v'.$value]= strtolower($value) ; }
} else { 
  $set_menu['vdummy']="------"; 
} 
// rights depening menu structure applicable ??
if (isset($_SESSION[ 'GROUP_ID' ]) && isset($_SESSION[ 'GROUP_NAME' ]) && isset($settingsArr[$_SESSION[ 'GROUP_NAME' ][ $_SESSION[ 'GROUP_ID' ]]])) {
  $set_menu= array(); // clear standard set 
  $hulp= explode ("|", $settingsArr[$_SESSION[ 'GROUP_NAME' ][ $_SESSION[ 'GROUP_ID' ]]]); 
  foreach ($hulp as $key => $value) { $set_menu['v'.$value]= strtolower($value) ; }
} 
/*
 * Layout template
 *
 * in language module
 * $template[0-3] in language module
 * $icontemp [0-19]
 * $linetemp [0-19]
 */
// $MOD_GSMOFF['menu']['help'] = "help";
foreach ($set_menu as $key => $value) { if (isset($MOD_GSMOFF['menu'][$key])) { $set_menu[$key]= $MOD_GSMOFF['menu'][$key]; } }
/*
 * debug
 */
if ( $debug ) {
  echo Gsm_post( 1 );
  echo Gsm_post( 2 );
  echo Gsm_post( 4 );
  Gsm_debug($settingArr, __LINE__, 2);
  Gsm_debug($set_menu, __LINE__, 2);
}	
/*
 * initial settings
 */
$prout="";
$print="";
$module_function = 0;
$vmodule='';
$vselection='';
$vsetting=false;
/*
 * Get menu input if any
 */
if ( isset( $_GET[ 'module' ] ) ) $vmodule = strtolower( $_GET[ 'module' ] );
if ( isset( $_POST[ 'module' ] ) ) $vmodule = strtolower( $_POST[ 'module' ] );
if ( isset( $_GET[ 'selection' ] ) ) $vselection = strtolower( $_GET[ 'selection' ]);
if ( isset( $_POST[ 'selection' ] ) ) $vselection = strtolower( $_POST[ 'selection' ] );
if ( isset( $_POST[ 'setting' ] ) ) $vsetting=true;
if (substr($vmodule, 0, 1)!="v")  $vmodule="v".$vmodule;
/*
 * Menu display needed ?
 */
if (count($set_menu) == 1) {
  if (strlen ($vmodule)<3) {  // vmodule al gevuld ??
    reset($set_menu);
    $hlp = each($set_menu);
    $vmodule=$hlp[0];	
    if ($debug) echo ">reset1|".$vmodule."<|</br>";
  }
} else {
  $menu=$MOD_GSMOFF['module'];
  foreach ( $set_menu as $key => $value ) { 
    if ( $menu == $MOD_GSMOFF['module'] && $vmodule == '') $vmodule=$value;
    $menu .= '&nbsp;<input class="settings" name="module" type="submit" value="' . $value . '" /> ' ; 
  }
  $parseViewArray = array(
    'return' => CH_RETURN,
    'menu' => $menu);
  $print = Gsm_prout ($TEMPLATE[ 0 ], $parseViewArray);
}
/*
 * load the module ?
 */
if ( strlen ($vmodule)>2) {
  if ( $debug ) {
    $msg[ 'bug' ] .= __LINE__.' ==> ' . $vmodule . '.php';
    $msg[ 'bug' ] .= ($vselection) ? ' selection: '.$vselection : ' no selection ' ;
    $msg[ 'bug' ] .= ($vsetting) ? ' settings' : 'no settings' ;
    $msg[ 'bug' ] .= '<br/>';
  }
  require_once(!file_exists($place_incl.$vmodule.'.php' )? $place_incl.'vdummy.php' : $place_incl.$vmodule.'.php');
}
/*
 * the screen output
 */
echo $print;
echo $prout;
?>