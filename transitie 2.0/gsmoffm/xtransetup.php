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
  'module' => 'transitie',
// voor versie display
  'modulen' => 'xtranssetup',
  'versie' => '  v20151130 ',
// general parameters  
  'app' => 'setup tables transitie vergoeding ',
// file en directory parameters  
  'file_1' => "sv_transitie",
  'file' => CH_DBBASE.'_sv_transitie',
  'file0' => 'media/legal',  // the directory for the documents
  'file1' => 'transitie',  // subdirectory with the administered documents
);
if ( $debug ) {
  Gsm_debug( $settingArr, __LINE__);
  Gsm_debug( $_POST, __LINE__);
  Gsm_debug( $regelsArr, __LINE__);
}
/* 
 * Ophalen van reference when files do not yet exist
 */
$jobs    = array( );
$jobs[ 1 ] = "CREATE TABLE IF NOT EXISTS `" . $regelsArr[ 'file' ] . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `zoek` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tvstatus` int(7) NOT NULL,
  `tvref0` varchar(255) NOT NULL,
  `tvdata` varchar(255) NOT NULL,
  `tvmodel` int(7) NOT NULL,
  `tvsupport` varchar(16) NOT NULL,
  `tvcalcdate` date NOT NULL,
  `tvremind` int(7) NOT NULL,
  `tvref0id` int(11) NOT NULL,
  `tvref0name` varchar(255) NOT NULL,
  `tvref0adres` varchar(255) NOT NULL,
  `tvref1name` varchar(255) NOT NULL,
  `tvref1adres` varchar(255) NOT NULL,
  `tvref2adres` varchar(255) NOT NULL,
  `tvref2comp` int(7) NOT NULL,
  `tvref2name` varchar(255) NOT NULL,
  `tvref2reg` varchar(255) NOT NULL,
  `tvgebdat` date NOT NULL,
  `tvdatin` date NOT NULL,
  `tvdatout` date NOT NULL,
  `tvbtoamt` decimal(9,2) NOT NULL,
  `tvbtoper` int(7) NOT NULL,
  `tvvakpct` decimal(9,2) NOT NULL,
  `tvbtoext` decimal(9,2) NOT NULL,
  `tvprest` decimal(9,2) NOT NULL,
  `tvgevaar` decimal(9,2) NOT NULL,
  `tvperc` int(11) NOT NULL,
  `tvamtc` decimal(9,2) NOT NULL,
  `tvorkw` int(7) NOT NULL,
  `comment` varchar(255) NOT NULL, 
  PRIMARY KEY (`id`) )
  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
  
$jobs[ 2 ] = "SELECT * FROM `".CH_DBBASE."` WHERE `section`='0' AND`table`= '".$regelsArr[ 'file_1' ]."' AND `name` = 'opzoek'"; 
$jobs[ 3 ] = "UPDATE `".CH_DBBASE."` SET `section` = '0', `table` = '".$regelsArr[ 'file_1' ]."', `value` = 'name' WHERE `id` = '{recid}'"; 
$jobs[ 4 ] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr[ 'file_1' ]."','opzoek', 'name')"; 
/*
 * Create tables 
 */
$errors = array();
$query = "SHOW TABLES LIKE '".$regelsArr[ 'file' ]."'";
if ( $debug ) $msg[ 'bug' ] .= __LINE__.$query . '</br>';
$results = $database->query( $query );
if ( !$results || $results->numRows() == 0 ) {
  $results = $database->query( $jobs[ 1 ] );   
  if ( $database->is_error() ) $errors[] = $database->get_error();
} else {
}
/*
 * update settings file 
 */
$results = $database->query( $jobs[ 2 ] );
if ( $database->is_error() ) $errors[] = $database->get_error();
if ( !$results || $results->numRows() != 0 ) {
  $row = $results->fetchRow();
  $parseViewArray = array( 'recid' => $row['id']);
  $jobs[ 3 ] = Gsm_prout ($jobs[ 3 ], $parseViewArray);
  $results = $database->query( $jobs[ 3 ] );
  if ( $database->is_error() ) $errors[] = $database->get_error();
} else {
  $results = $database->query( $jobs[ 4 ] );
  if ( $database->is_error() ) $errors[] = $database->get_error();
}
if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
/*
 * Create directories 
 */
$dir_to = WB_PATH. "/" . $regelsArr['file0'] ;
if (!file_exists($dir_to. "/" )) { mkdir($dir_to, 0777); } // create if not exist  
$dir_to = WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] ;  
if (!file_exists($dir_to. "/" )) { mkdir($dir_to, 0777); } // create if not exist  		  
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?> 