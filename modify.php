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

/*
 * initial settings 
 */
$regelsArr_display = array(
// voor versie display
  'modulen' => 'modify',
  'versie' => ' v20151004 '
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
$settingArr = array ();
while ( $row = $results->fetchRow() ) { 
  if (strlen($row['table'])<1) $settingArr[$row['name']]= $row['value']; 
}
unset ($query);
$debug=false;
if (isset($settingArr['debug']) && $settingArr['debug']=="yes")  $debug=true; 
/*
 * Location of files
 */
//places of the includes
$place_incl = (dirname( __FILE__ )).'/';
//places of the language file 
$place_lang  = ( dirname( __FILE__ ) ) . '/languages/' . LANGUAGE . '.php';
// load module language file
require_once((dirname(__FILE__)) . '/languages/EN.php' );
if (LANGUAGE!='EN' && file_exists($place_lang)) { require_once($place_lang);}
// load includes
require_once($place_incl.'includes.php' );

// define section menu, default (the first) and the description
$set_mode = (isset($settingArr['mode'])) ? $settingArr['mode'] : "file";
if (isset($settingArr['function'])) { 
  $hulp= explode ("|", $settingArr['function']); 
  foreach ($hulp as $key => $value) { $set_menu['x'.$value]= strtolower($value); }
  $xmodule=$hulp[0];
} else { 
  $xmodule = "xdummy";
  $set_menu['xdummy']="------"; 
}
/*
 * Layout template
 *
 * in language module
 * $template[0-3] in language module
 * $icontemp [0-19]
 * $linetemp [0-19]
 */
$MOD_GSMOFF['menu']['help'] = "help";
foreach ($set_menu as $key => $value) { if (isset($MOD_GSMOFF['menu'][$key])) { $set_menu[$key]= $MOD_GSMOFFL['menu'][$key]; } }
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
$xselection='';
$xsetting=false;
/*
 * Get menu input if any
 */
if ( isset( $_GET[ 'module' ] ) ) $xmodule = strtolower( $_GET[ 'module' ] );
if ( isset( $_POST[ 'module' ] ) ) $xmodule = strtolower( $_POST[ 'module' ] );
if ( isset( $_GET[ 'selection' ] ) ) $xselection = strtolower( $_GET[ 'selection' ]);
if ( isset( $_POST[ 'selection' ] ) ) $xselection = strtolower( $_POST[ 'selection' ] );
if ( isset( $_POST[ 'setting' ] ) ) $xsetting=true;
if (substr($xmodule, 0, 1)!="x")  $xmodule="x".$xmodule;
/*
 * Menu display needed ?
 */
if (count($set_menu) == 1) {
  if (strlen ($xmodule)<3) {
    reset($set_menu);
    $hlp= each($set_menu);
    $xmodule=$hlp[0];	
    if ($debug) echo ">reset|".$xmodule."<|</br>";
  }
} else {
  $parseViewArray = array(
    'return' 	=> CH_RETURN, 
    'parameter' => $xselection,
    'module' 	=> Gsm_option( $set_menu, $xmodule ),
    'add_needed' => '',
    'mod' 		=> $xmodule,
    'sel' 		=> $xselection
  );
  $print = Gsm_prout ($TEMPLATE[ 1 ], $parseViewArray);
}

/*
 * load the module ?
 */
if ( strlen ($xmodule)>2) {
	if ( $debug ) {
		$msg[ 'bug' ] .= __LINE__.' ==> ' . $xmodule . '.php';
		$msg[ 'bug' ] .= ($xselection) ? ' selection: '.$xselection : ' no selection ' ;
		$msg[ 'bug' ] .= ($xsetting) ? ' settings' : 'no settings' ;
		$msg[ 'bug' ] .= '<br/>';
	}
	require_once(!file_exists($place_incl.$xmodule.'.php' )? $place_incl.'xdummy.php' : $place_incl.$xmodule.'.php');
}	
/*
 * the screen output
 */
echo $print;
echo $prout;
?>