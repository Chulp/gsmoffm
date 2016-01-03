<?php
/*
 *  @module         Office toolset legal
 *  @version        see info.php versie below
 *  @author         Gerard Smelt
 *  @copyright      2015, Contracthulp B.V.
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
function Gsm_login_person ($arr_in, $user_id) {
  // bepaal de uit te rekenen datums lees persoonsgegevens indien login
  global $msg;
  $debug=false;
  global $database;
  $arr_uit = array ( 'returnvalue'=> false);
  $query = "SELECT 
    `" . $arr_in[ 'adres' ] . "`.`referlist`,
    `" . $arr_in[ 'adres' ] . "`.`id`,
    `" . $arr_in[ 'adres' ] . "`.`name`,
    `" . $arr_in[ 'adres' ] . "`.`adres`,  
    `" . $arr_in[ 'adres' ] . "`.`email`, 
    `" . $arr_in[ 'adres' ] . "`.`comp`
    FROM `" . $arr_in[ 'adres' ] . "`
    LEFT JOIN `" . $arr_in[ 'table' ] . "`
    ON `" . $arr_in[ 'adres' ] . "`.`email` = `" . $arr_in[ 'table' ] . "`.`email`
    WHERE `" . $arr_in[ 'table' ] . "`.`user_id`= '".$user_id."'"; 
  $results = $database->query( $query );  
  if ( $results && $results->numRows() != 0 ) {
    $row = $results->fetchRow();
    if ( $debug ) Gsm_debug( $arr_uit, __LINE__);
	if ( $debug ) Gsm_debug( $row, __LINE__);
    $arr_uit[ 'owner' ] = $row ['referlist'];
    $arr_uit[ 'owner_fileref' ] = substr($arr_uit[ 'owner' ], 0,6);
	$arr_uit[ 'wrref1id' ] = $row ['id'];
	$hulp_1 = explode( "|", $row[ 'name' ] ) ;
	$arr_uit[ 'wrref1name' ] = sprintf("%s %s %s", 
//	  ( isset( $hulp_1[ 0 ] ) ) ? $hulp_1[ 0 ] : '', // titel onderdrukken
	  ( isset( $hulp_1[ 1 ] ) ) ? $hulp_1[ 1 ] : '', 
	  ( isset( $hulp_1[ 2 ] ) ) ? $hulp_1[ 2 ] : '',
	  ( isset( $hulp_1[ 3 ] ) ) ? $hulp_1[ 3 ] : '',
	  '');  
	$arr_uit[ 'wrref1name' ]= Gsm_eval(  $arr_uit[ 'wrref1name' ], 1);
	$hulp_2 = explode( "|", $row[ 'adres' ] );
	$arr_uit[ 'wrref1adres' ] = sprintf("%s%s%s%s",
	  ( isset( $hulp_2[ 0 ] ) && strlen( $hulp_2[ 0 ] ) >3 ) ? $hulp_2[ 0 ] : '',
	  ( isset( $hulp_2[ 1 ] ) && strlen( $hulp_2[ 1 ] ) >3 ) ? CH_CR .$hulp_2[ 1 ] : '',
	  ( isset( $hulp_2[ 2 ] ) && strlen( $hulp_2[ 2 ] ) >3 ) ? CH_CR .$hulp_2[ 2 ] : '',
	  ( isset( $hulp_2[ 3 ] ) && strlen( $hulp_2[ 3 ] ) >3 ) ? CH_CR .$hulp_2[ 3 ] : '');
//	$arr_uit[ 'wrref1adres' ] = nl2br ( $arr_uit[ 'wrref1adres' ] );
	$arr_uit[ 'owner_email' ] = $row ['email'];
	$arr_uit[ 'wrref1comp' ] = $row ['comp'];
	$arr_uit[ 'wrsummary' ] = 2;
	if ( $debug ) Gsm_debug( $arr_uit, __LINE__);
	$arr_uit['returnvalue']=true;
  }
  unset( $query );  
  $returnvalue = $arr_uit;
  return $returnvalue;
}
function Gsm_post_data () {
  // Get $post data formulier schuldeiser
  $arr_uit = array ( 'returnvalue'=> false);
  if ( isset( $_POST[ 'recid' ] ) ) $arr_uit[ 'recid' ] = $_POST[ 'recid' ];
  if ( isset( $_POST[ 'memory' ] ) ) {
    $arr_uit[ 'memory' ] = $_POST[ 'memory' ];
    $help_1  = explode( '|', $arr_uit[ 'memory' ] );
    $arr_uit[ 'memored_mutaties' ] = (isset( $help_1 [ 0 ])) ? $help_1[ 0 ] : "1";
	if ($arr_uit[ 'memored_mutaties' ]=="clear") { 
	    $arr_uit[ 'memored_recid' ] = "";
        $arr_uit[ 'memored_email' ] = "";
		$arr_uit[ 'memored_mutaties'] = 1;
		unset ($_POST);
	} else {
      $arr_uit[ 'memored_recid' ] = (isset( $help_1[ 1 ])) ? $help_1[ 1 ] : "";
      $arr_uit[ 'memored_email' ] = (isset( $help_1[ 2 ])) ? $help_1[ 2 ] : "";
	}
  }
  if ( isset( $_POST[ 'wrsummary' ] ) ) $arr_uit[ 'wrsummary' ] = $_POST[ 'wrsummary' ];
  if ( isset( $_POST[ 'wrref0' ] ) ) $arr_uit[ 'wrref0' ] =  $_POST[ 'wrref0' ];
  if ( isset( $_POST[ 'wrref1name' ] ) ) $arr_uit[ 'wrref1name' ] =  $_POST[ 'wrref1name' ];
  if ( isset( $_POST[ 'wrref1adres' ] ) ) $arr_uit[ 'wrref1adres' ] =  Gsm_eval ($_POST[ 'wrref1adres' ], 5);  
  if ( isset( $_POST[ 'owner_email' ] ) ) $arr_uit[ 'owner_email' ] = $_POST[ 'owner_email' ];
  if ( isset( $_POST[ 'wrref1comp' ] ) ) $arr_uit[ 'wrref1comp' ] = Gsm_eval ($_POST[ 'wrref1comp' ], 8);
  if ( isset( $_POST[ 'wrref1dienst' ] ) ) $arr_uit[ 'wrref1dienst' ] = $_POST[ 'wrref1dienst' ];  
  if ( isset( $_POST[ 'comment' ] ) ) $arr_uit[ 'comment' ] = $_POST[ 'comment' ];  
  
  if ( isset( $_POST[ 'wrref2adres' ] ) ) $arr_uit[ 'wrref2adres' ] =  Gsm_eval ($_POST[ 'wrref2adres' ], 5);  
  if ( isset( $_POST[ 'wrref2comp' ] ) ) $arr_uit[ 'wrref2comp' ] = Gsm_eval ($_POST[ 'wrref2comp' ], 8);
  if ( isset( $_POST[ 'wrref2kvk' ] ) ) $arr_uit[ 'wrref2kvk' ] = $_POST[ 'wrref2kvk' ];   
  if ( isset( $_POST[ 'wrref2vat' ] ) ) $arr_uit[ 'wrref2vat' ] = $_POST[ 'wrref2vat' ];  
  
  if ( isset( $_POST[ 'wrcalcdate' ] ) && $arr_uit[ 'wrsummary' ] > 1 ) $arr_uit[ 'wrcalcdate' ] = Gsm_eval( $_POST[ 'wrcalcdate' ], 9, date( "Y-m-d" ), '2002-01-01' );
  if ( isset( $_POST[ 'wrink' ] ) && $arr_uit[ 'wrsummary' ] > 1 ) $arr_uit[ 'wrink' ] = Gsm_eval( $_POST[ 'wrink' ], 8, 5, 0 );
  if ( isset( $_POST[ 'wrrente' ] ) && $arr_uit[ 'wrsummary' ] > 1 ) $arr_uit[ 'wrrente' ] = Gsm_eval( $_POST[ 'wrrente' ], 8, 5, 0 );
  if ( isset( $_POST[ 'wrrentepct' ] ) && $arr_uit[ 'wrsummary' ] > 1 ) $arr_uit[ 'wrrentepct' ] = Gsm_eval( $_POST[ 'wrrentepct' ], 8, 20, 0 );
  if ( isset( $_POST[ 'wrremind' ] ) && $arr_uit[ 'wrsummary' ] > 1 ) { 
	$arr_uit[ 'wrremind' ] = $_POST[ 'wrremind' ];
	$help_2 = $arr_uit[ 'wrremind' ];
  } else  { $help_2= 30;}
  for ( $i = 1; $i <= $arr_uit[ 'memored_mutaties' ]; $i++ ) {
    ( isset( $_POST[ '1|' . $i ] ) ) ? $arr_uit['wrfacref'][ $i ] = Gsm_eval( $_POST[ '1|' . $i ], 2 ): "";
    ( isset( $_POST[ '2|' . $i ] ) ) ? $arr_uit['wrfacdat'][ $i ] = Gsm_eval( $_POST[ '2|' . $i ], 9, date( "Y-m-d" ), '2002-01-01' ): '2002-01-01';
    ( isset( $_POST[ '3|' . $i ] ) ) ? $arr_uit['wrfacamt'][ $i ] = Gsm_eval( $_POST[ '3|' . $i ], 8, 1000000, -1000000 ): "0";
	
    if ($help_2>=7) {
      $arr_uit['wrfacverv'][ $i ]=date("Y-m-d", strtotime($arr_uit['wrfacdat'][ $i ])+($help_2*24*60*60));
    } else {
      $arr_uit['wrfacverv'][ $i ] = $arr_uit['wrfacdat'][ $i ];
    }
    if ( isset( $_POST[ '4|' . $i ] ) && $arr_uit[ 'wrsummary' ] > 1 ) $arr_uit['wrfacverv'][ $i ] = Gsm_eval( $_POST[ '4|' . $i ], 9, date( "Y-m-d" ), $arr_uit['wrfacverv'][ $i ]);
	if ($arr_uit['wrfacamt'] [ $i ] <= 0 ) $arr_uit['wrfacverv'][ $i ] = $arr_uit['wrfacdat'][ $i ]; 	// correctie voor deel betalingen
  } 
  $arr_uit['returnvalue']=true;
  $returnvalue = $arr_uit;
  return $returnvalue;
}
function Gsm_file_data ($table , $recid , $status=0 ) {
  // reload file data
    global $msg;
  $debug=false;
  global $database;
  $arr_uit = array ( 'returnvalue'=> false);
  $query = "SELECT * 
    FROM `" . $table . "` 
	WHERE `id` = '".$_GET ['recid']."'";
    $results = $database->query( $query ); 
  if ( $results && $results->numRows() != 0 ) {
    $row = $results->fetchRow();
    $arr_uit[ 'recid' ] = $row [ 'id' ];
	$arr_uit[ 'memored_recid' ] = $row [ 'id' ];
	$arr_uit[ 'memored_email' ] = $row [ 'name' ];
	$arr_uit[ 'owner_email' ] = $row [ 'name' ];	
    $arr_uit[ 'wrstatus' ] = $row [ 'wrstatus' ];
    $arr_uit[ 'wrref0' ] = $row [ 'wrref0' ];
    $arr_uit[ 'wrdata' ] = $row [ 'wrdata' ];
    $arr_uit[ 'wrmodel' ] = $row [ 'wrmodel' ];	
    $arr_uit[ 'wrsupport' ] = $row [ 'wrsupport' ];		
    $arr_uit[ 'wrcalcdate' ] = $row [ 'wrcalcdate' ];
    $arr_uit[ 'wrremind' ] = $row [ 'wrremind' ];
    $arr_uit[ 'wrref1id' ] = $row [ 'wrref1id' ];
    $arr_uit[ 'wrref1ref' ] = $row [ 'wrref1ref' ];
    $arr_uit[ 'wrref1name' ] = $row [ 'wrref1name' ];
    $arr_uit[ 'wrref1adres' ] = $row [ 'wrref1adres' ];
    $arr_uit[ 'wrref1comp' ] = $row [ 'wrref1comp' ];
    $arr_uit[ 'wrref1dienst' ] = $row [ 'wrref1dienst' ];
    $arr_uit[ 'wrref2adres' ] = $row [ 'wrref2adres' ];
    $arr_uit[ 'wrref2comp' ] = $row [ 'wrref2comp' ];
	$help_1= explode ("|",$row [ 'wrref2reg' ]);
	if(strpos($help_1[0], 'vat') !==false) $arr_uit[ 'wrref2vat' ] = trim(str_replace ('vat',"",  $help_1[0]));
	if(strpos($help_1[1], 'kvk') !==false) $arr_uit[ 'wrref2kvk' ] = trim(str_replace ('kvk',"",  $help_1[1]));
    $arr_uit[ 'wrrente' ] = $row [ 'wrrente' ];
    $arr_uit[ 'wrrentepct' ] = $row [ 'wrrentepct' ];
    $arr_uit[ 'wrink' ] = $row [ 'wrink' ];
    $arr_uit[ 'comment' ] = $row [ 'comment' ];
	$hulp_amt= explode ('|', $row['wrfacamt']);
    $hulp_ref= explode ('|', $row['wrfacref']);
 	$hulp_dat= explode ('|', $row['wrfacdat']);
    $hulp_verv= explode ('|', $row['wrfacverv']);
	$arr_uit[ 'memored_mutaties' ]=0;
	foreach($hulp_amt as $key => $value) {
	  if ($value != 0) {
		$arr_uit[ 'memored_mutaties' ]++;
		$arr_uit['wrfacamt'][ $arr_uit[ 'memored_mutaties' ] ] = $value;
		$arr_uit['wrfacref'][ $arr_uit[ 'memored_mutaties' ] ] = $hulp_ref [$key];
		$arr_uit['wrfacdat'][ $arr_uit[ 'memored_mutaties' ] ] = $hulp_dat [$key];
		$arr_uit['wrfacverv'][ $arr_uit[ 'memored_mutaties' ] ] = $hulp_verv [$key];
	  }
	}
	$arr_uit['returnvalue']=true;
  }
  unset( $query );  
  $returnvalue = $arr_uit;
  return $returnvalue;
}
function Gsm_record_data ($table, $arr_in , $status=0 ) {
// reload file data
  global $msg;
  $debug=false;
  global $database;
  $arr_uit = array ( 'returnvalue'=> false);
  $arr_uit[ 'toegift' ] = $arr_in[ 'toegift' ];
  $arr_uit[ 'memored_email' ] = $arr_in[ 'memored_email'];
// email adress change
  if ( $arr_in[ 'memored_email' ] != $arr_in[ 'owner_email'] ) {
    if (strlen($arr_in[ 'memored_email'])>12 ) $arr_uit[ 'toegift' ] .= 'what if the e-mail adress changes'.NL;   
	$arr_uit[ 'memored_email' ] = $arr_in[ 'owner_email'];
  }
  if (isset($arr_in[ 'memored_recid' ]) && isset($arr_in[ 'memored_email' ]) &&  $arr_in[ 'memored_recid' ] >0) {
    $query = "SELECT * FROM `" . $table . "` WHERE `id`= '".$arr_in[ 'memored_recid' ]."' AND `name`= '".$arr_in[ 'memored_email' ]."'";
    $results = $database->query( $query );  
  } else {
    $query = "INSERT INTO `" . $table . "` ( `name` ) VALUES ( '".$arr_uit[ 'memored_email' ]."')";
    $results = $database->query( $query );  
    $query = "SELECT * FROM `" . $table . "` WHERE `name`= '".$arr_uit[ 'memored_email' ]."' ORDER BY `updated` DESC"; 
    $results = $database->query( $query ); 
  }
  if ( $results && $results->numRows() != 0 ) {
    $row = $results->fetchRow(); 
	$arr_uit[ 'memored_recid' ] = $row['id'];
    $hulpArr = array( );	
	  $hulpArr ['name'] = $arr_uit[ 'memored_email' ];
	  $hulpArr ['wrstatus'] = 1; 
	  $hulpArr ['wrref0'] = (isset ($arr_in[ 'wrref0' ])) ? $arr_in[ 'wrref0' ] :"";
	  $hulpArr ['wrdata'] = "Online data invoer"; 
	  if( isset ($arr_in[ 'wrsupport' ])) { 
	    $hulpArr ['wrsupport'] = $arr_in[ 'wrsupport' ]; 
	  } else { 
        if (isset ($arr_in[ 'owner_fileref' ])) $hulpArr ['wrsupport'] = $arr_in[ 'owner_fileref' ];
	  }
	  if (isset($arr_in ['wrcalcdate'])) $hulpArr ['wrcalcdate'] = $arr_in[ 'wrcalcdate' ];
	  $hulpArr ['wrremind'] = $arr_in[ 'wrremind' ];
	  if (isset($arr_in ['wrref1id'])) $hulpArr ['wrref1id'] = $arr_in[ 'wrref1id' ];
	  if( isset ($arr_in[ 'owner_fileref' ])) $hulpArr ['wrref1ref'] = $arr_in[ 'owner_fileref' ];	
	  $hulpArr ['wrref1name'] = $arr_in[ 'wrref1name' ];
	  $hulpArr ['wrref1adres'] = $arr_in[ 'wrref1adres' ];
	  $hulpArr ['wrref1comp'] = $arr_in[ 'wrref1comp' ];
	  $hulpArr ['wrref1dienst'] = $arr_in[ 'wrref1dienst' ];
	  if (isset ($arr_in[ 'wrref2adres' ])) $hulpArr ['wrref2adres'] = $arr_in[ 'wrref2adres' ];
//	  $hulpArr ['wrref2adres'] = $arr_in[ 'wrref2adres' ];
	  $hulpArr ['wrref2comp'] = $arr_in[ 'wrref2comp' ];
	  $hulpArr ['wrref2reg'] = sprintf ("vat %s | kvk %s", (isset($arr_in[ 'wrref2vat' ])) ? $arr_in[ 'wrref2vat' ] :"", (isset($arr_in[ 'wrref2kvk' ])) ? $arr_in[ 'wrref2kvk' ]:"");
	  $hulpArr ['wrrente'] = $arr_in[ 'wrrente' ];
	  if( isset ($arr_in[ 'wrrentepct' ])) $hulpArr ['wrrentepct'] = $arr_in[ 'wrrentepct' ];
	  $hulpArr ['wrink'] = $arr_in[ 'wrink' ];
	  $hulpArr ['wrfacref'] = '';
	  $hulpArr ['wrfacdat'] = '';
	  $hulpArr ['wrfacamt'] = '';
	  $hulpArr ['wrfacverv'] = '';
	  foreach ($arr_in[ 'wrfacamt' ] as $key => $value) {
        $hulpArr ['wrfacamt'].= $value.'|';
        $hulpArr ['wrfacref'] .= $arr_in ['wrfacref'][$key].'|';
        $hulpArr ['wrfacdat'] .= $arr_in ['wrfacdat'][$key].'|';
        $hulpArr ['wrfacverv'] .= $arr_in ['wrfacverv'][$key].'|';
      }
	  $hulpArr ['comment'] = $arr_in[ 'comment' ];	
    $query = "UPDATE `".$table."` SET ".Gsm_parse (2,$hulpArr)."  WHERE `id` = '".$row ['id']."'";
    if ( $debug ) $msg[ 'bug' ] .= __LINE__.' '.$query.NL;
    $results = $database->query( $query ); 	
    if ( $debug ) $arr_uit[ 'toegift' ] .= 'data recorded'.NL; 
    $arr_uit[ 'memory' ] = $arr_in[ 'memored_mutaties' ]."|".$arr_uit[ 'memored_recid' ]."|".$arr_uit[ 'memored_email' ];
	$arr_uit['returnvalue']=true;
  }
  unset( $query );  
  $returnvalue = $arr_uit;
  return $returnvalue;
}
function Gsm_attachment ($allow_ext, $file0, $file1, $recid, $som_att, $allow_size=6000000) {
  // Get attachmnet 
  global $msg;
  $debug=true;
  $returnvalue = false;
  if ( isset( $_FILES[ "Attdoc" ][ "error" ] ) && $_FILES[ "Attdoc" ][ "error" ] == 0 ) {
    // there is an attachment
	$help=explode( ".", $_FILES[ "Attdoc" ][ "name" ] ) ;
    $extension  = end($help);
    if (in_array( $extension, $allow_ext) ) { // extension test
      if ($_FILES[ "Attdoc" ][ "size" ] < $allow_size )  $returnvalue = true; // file size test
	}
    if ($returnvalue ) {
      $dir_to = WB_PATH. "/" . $file0 ;
      if (!file_exists($dir_to. "/" )) { mkdir($dir_to, 0777); } // create if not exist  
      $dir_to = WB_PATH. "/" . $file0. "/" . $file1 ;  
      if (!file_exists($dir_to. "/" )) { mkdir($dir_to, 0777); } // create if not exist  
      $atttype = ( isset( $_POST[ 'atttype' ] ) ) ? $som_att[$_POST[ 'atttype' ]]: "overig";  
      $attoms = ( isset( $_POST[ 'attoms' ] ) ) ? $_POST[ 'attoms' ]: "xx";   		
      $guid4 = strtolower(substr(func_guid(), 0, 6));
      $filename = str_replace (" ", "-", $recid."_".$atttype."_".$attoms."_".$guid4.".".$extension);
      if ( move_uploaded_file( $_FILES[ "Attdoc" ][ "tmp_name" ], $dir_to. "/". $filename))  {
        if ($debug) $msg['bug'] .= __LINE__ . ' '. $dir_to. "/". $filename.' added'.NL;
        $msg[ 'inf' ] .= $filename . ' '.' added'.NL;
  }	} } 
  return $returnvalue;
}
function func_guid( $size=0 ) { 
  // create guid
//  if (function_exists('com_create_guid') === true) { return trim(com_create_guid(), '{}'); }
  $returnvalue = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
  if ($size > 0) $returnvalue = substr( $returnvalue, 0, $size); 
  return $returnvalue; 
} 
function Gsm_set_status ($table , $recid , $status=0 ) {
  // change status field
  global $database;  
  $returnvalue = true;
  $query = "SELECT `wrstatus` FROM `" . $table . "` WHERE `id` = '".$recid."'"; 
  $results = $database->query( $query ); 
  $row = $results->fetchRow(); 
  if ($status == 3 && $status < $row['wrstatus'] ) $status = $row['wrstatus'];
  $hulpArr = array( );	
  $hulpArr ['wrstatus'] = $status; 
  if ($status==0 ) $hulpArr ['comment'] = ' verwijderd op '.date( "Y-m-d" );	
  if ($status==1 ) $hulpArr ['comment'] = ' in bewerking op '.date( "Y-m-d" );	
  if ($status==2 ) $hulpArr ['comment'] = ' compleet op '.date( "Y-m-d" );	
  if ($status==3 ) $hulpArr ['comment'] = ' activated op '.date( "Y-m-d" );	
  $query = "UPDATE `".$table."` SET ".Gsm_parse (2,$hulpArr)."  WHERE `id` = '".$recid."'";
  $results = $database->query( $query ); 	
  return $returnvalue;
}
function Gsm_som_datum( $arr_in, $rek_date, $arr_rente_dat, $func = 1 ){
  // bepaal de uit te rekenen datums
  global $msg;
  $arr_uit = array( );
  $returnvalue = "";
  switch ( $func ){
    case 1:
    default:
      // input
      foreach ( $arr_in as $key => $value ) {
        $arr_uit[ $value ] = "a" . $key; } //$arr_in as $key => $value
      // add end date
      $arr_uit[ $rek_date ] = "b";
      // determine  and lowest higest
      ksort( $arr_uit );
      $first = true;
      foreach ( $arr_uit as $key => $value ) {
        if ( $first )
          $val_low = $key;
          $first    = false;
          $val_high = $key; } //$arr_uit as $key => $value
      if ( $val_high > $rek_date )  $msg[ 'inf' ] .= ' Rekenen tot datum is niet passend <br/>';
      // add rate periods
      foreach ( $arr_rente_dat as $key => $value ) {
        if ( $val_high > $key && $val_low < $key ) $arr_uit[ $key ] = "c"; } //$arr_rente_dat as $key => $value
      // add samengesteld periodes
      for ( $i = 2002; $i < 2099; $i++ ) {
        $addyear = $i . substr( $val_low, 4 );
        if ( $val_high > $addyear && $val_low < $addyear ) $arr_uit[ $addyear ] = "d"; } //$i = 2002; $i < 2099; $i++
      ksort( $arr_uit );
      // uit te rekenen datums zijn nu bekend
      break;
  } //$func
  $returnvalue = $arr_uit;
  return $returnvalue;
}
function Gsm_som_bereken( $Arr, $dates , $arr_rente_dat, $func = 1 ){
  global $msg;
  $calc = array( array( ) );
  $returnvalue = "";
  switch ( $func ){
    case 1:
      $i = 0;
      foreach ( $dates as $key => $value )  { $calc[ $i ][ 0 ] = $key;  $i++; } //$rek2 as $key => $value
      // einddatum periode toevoegen
      for ( $i = 0; $i < $Arr['max']; $i++ ) {
        if ( isset( $calc[ $i ][ 0 ] ) ) {
          if ( isset( $calc[ $i + 1 ][ 0 ] ) ) {
            $calc[ $i ][ 1 ] = $calc[ $i + 1 ][ 0 ];
          } else {
            $calc[ $i ][ 1 ] = $calc[ $i ][ 0 ];
          }
          // rentedagen berekenen per periode
          $interval        = round( ( strtotime( $calc[ $i ][ 1 ] ) - strtotime( $calc[ $i ][ 0 ] ) ) / 60 / 60 / 24, 0 );
          $yearinterval    = round( ( strtotime( substr( $calc[ $i ][ 0 ], 0, 4 ) . '-12-31' ) - strtotime( substr( $calc[ $i ][ 0 ], 0, 4 ) . '-01-01' ) ) / 60 / 60 / 24, 0 ) + 1;
          $calc[ $i ][ 2 ] = sprintf( "%s / %s", $interval, $yearinterval );
          $calc[ $i ][ 3 ] = $interval / $yearinterval;
          $calc[ $i ][ 4 ] = 0;
          $calc[ $i ][ 5 ] = 0;
          $calc[ $i ][ 6 ] = '';
          $calc[ $i ][ 7 ] = 0;
          $calc[ $i ][ 8 ] = 0;
          $calc[ $i ][ 9 ] = 0;
        } //isset( $calc[ $i ][ 0 ] )
      } //$i = 0; $i < $Arr['max']; $i++
      // rente perentage 
      for ( $i = 0; $i < $Arr['max']; $i++ ) {
        if ( isset( $calc[ $i ][ 0 ] ) ) {
          $notfound   = true;
          $vorig_perc = 0;
          foreach ( $arr_rente_dat as $key => $value ) {
            $vorig_perc = $value;
            if ( $notfound ) $calc[ $i ][ 4 ] = $value;
            if ( $notfound ) {
              if ( $calc[ $i ][ 0 ] == $key ) { $notfound = false; }
            } //$notfound
            if ( $notfound ) {
              if ( $calc[ $i ][ 0 ] < $key ) {
                $calc[ $i ][ 4 ] = $vorig_perc;
                $notfound        = false;
              } //$calc[ $i ][ 0 ] < $key
            } //$notfound
          } //$consument_rente_percentages as $key => $value
        } else { $i = $Arr['max']; }
      } //$i = 0; $i < $Arr['max']; $i++
      // correctie voor contractuele rente verhoging
	  if ( $Arr[ 'wrrente' ] == 2 ) {
//     if ( $Arr[ 'wrcont' ] == 1 && $Arr[ 'wrrektpe' ] == 2 ) {
//       $msg[ 'bug' ] .= __LINE__ . ' access <br/>';
        for ( $i = 0; $i < $Arr['max']; $i++ ) {
          (isset($calc[ $i ][ 4 ]))  ? $calc[ $i ][ 4 ] = $calc[ $i ][ 4 ] + $Arr[ 'wrrentepct' ]: $i = $Arr['max'] ; } 
      } 
      // correctie voor contractuele rente vervanging
	  if ( $Arr[ 'wrrente' ] == 1 ) {
//      if ( $Arr[ 'wrcont' ] == 1 && $Arr[ 'wrrektpe' ] == 1 ) {
//       $msg[ 'bug' ] .= __LINE__ . ' access <br/>';
        for ( $i = 0; $i < $Arr['max']; $i++ ) { $calc[ $i ][ 4 ] = $Arr[ 'wrrentepct' ]; } 
      } //$Arr[ 'wrcont' ] == 1 && $Arr[ 'wrrektpe' ] == 1
      // bedrag ophalen
      foreach ( $Arr['wrfacamt'] as $key => $value ) {
        $notfound = true;
        for ( $i = 0; $i < $Arr['max']; $i++ ) {
          if ( isset( $calc[ $i ][ 0 ] ) ) {
            if ( $Arr[ 'wrfacverv' ][ $key ] == $calc[ $i ][ 0 ] ) {
              $calc[ $i ][ 5 ] += $value;
              $calc[ $i ][ 6 ] .= $Arr['wrfacref'][ $key ]."|";
              if ($Arr['wrfacdat'][ $key ] != '2002-01-01') { $calc[ $i ][ 6 ] .= "dd ".$Arr['wrfacdat'][ $key ]."|"; }
              $notfound = false;
            } //$Arr[ 'wrfacverv' ][ $key ] == $calc[ $i ][ 0 ]
          } else { $i = $Arr['max']; }
        } //$i = 0; $i < $Arr['max']; $i++
      } //$wrrekamt as $key => $value
      // eindelijk rekenen
      $first = true;
      for ( $i = 0; $i < $Arr['max']; $i++ ) {
        $samen = false;
        if ( isset( $calc[ $i ][ 0 ] ) ) {
          if ( $first ) { $val_low = $calc[ $i ][ 0 ]; } //$first
          // rente over
          if ( $first ) {
            $calc[ $i ][ 7 ] = $calc[ $i ][ 5 ];
          } else {
            $calc[ $i ][ 7 ] = $calc[ $i - 1 ][ 7 ] + $calc[ $i ][ 5 ];
          }
          // controle samengesteld
          if ( !$first && substr( $val_low, 4 ) == substr( $calc[ $i ][ 0 ], 4 ) ) $samen = true;
          // samengesteld
          if ( $samen )$calc[ $i ][ 7 ] += $calc[ $i - 1 ][ 9 ];
          // rente
          $calc[ $i ][ 8 ] = round($calc[ $i ][ 7 ] * $calc[ $i ][ 3 ] * $calc[ $i ][ 4 ] / 100, 2);
          if ( $calc[ $i ][ 8 ] < 0 ) $calc[ $i ][ 8 ] = 0;
          if ( $samen ) { 
            $calc[ $i ][ 9 ] = $calc[ $i ][ 8 ];
          } else {
            if ( $first ) {
              $calc[ $i ][ 9 ] = $calc[ $i ][ 8 ];
            } else {
              $calc[ $i ][ 9 ] = $calc[ $i - 1 ][ 9 ] + $calc[ $i ][ 8 ];
            }
          }
          if ( $samen ) $calc[ $i ][ 6 ] .= "+|";
          $first = false;
        } else { $i = $Arr['max']; }
      } //$i = 0; $i < $Arr['max']; $i++
    default:
      break;
  } //$func
  $returnvalue = $calc;
  return $returnvalue;
}
function Gsm_som_bereken2( $calc, $arr, $func = 1 ){
  global $msg;
  $arr_uit[ 'toegift' ] = $arr[ 'toegift' ];
  $arr_uit ['som_hoogste'] = 0;
  $arr_uit ['som_ik'] = 0;
  switch ( $func ){
    case 1:
	$first = true;
    for ( $i = 0; $i < $arr [ 'max']; $i++ ) {
      if ( isset( $calc[ $i ][ 0 ] ) ) {
        if ( $first ) $arr_uit ['low_val'] = $calc[ $i ][ 0 ];
        if ( $first ) { $arr_uit ['som_hoofd'] = $calc[ $i ][ 5 ]; } else { $arr_uit ['som_hoofd'] += $calc[ $i ][ 5 ]; }
        $arr_uit ['som_totaal'] = $calc[ $i ][ 7 ] + $calc[ $i ][ 9 ];
        $arr_uit ['som_rente']  =$arr_uit ['som_totaal'] - $arr_uit ['som_hoofd'];
        if ( $calc[ $i ][ 5 ] > 0 ) $arr_uit ['som_hoogste'] += $calc[ $i ][ 5 ];
        $arr_uit ['som_high_val'] = $calc[ $i ][ 1 ];
        $first = false;
      } else { $i = $arr [ 'max']; }
    }
	//  incassokosten    
    if ( $arr_uit ['som_rente'] > 0  ) { 
      if ( $arr[ 'wrink' ] == 1 ) {
        $calc_val[ 0 ] = array( );
        $calc_val[ 0 ] = $arr_uit ['som_hoogste'];
        $calc_val[ 1 ] = 0;
        $calc_val[ 2 ] = 0;
        $calc_val[ 3 ] = 0;
        foreach ( $arr ['ink_tar'] as $key => $value ) {
          if ( $calc_val > 0 ) {
            $calc_val[ 1 ] = substr( $key, 1 );
            if ( $calc_val[ 0 ] > $calc_val[ 1 ] ) {
              $calc_val[ 2 ] = $calc_val[ 1 ];
              $calc_val[ 0 ] = $calc_val[ 0 ] - $calc_val[ 1 ];  } //$calc_val[ 0 ] > $calc_val[ 1 ]
            else {
              $calc_val[ 2 ] = $calc_val[ 0 ];
              $calc_val[ 0 ] = 0;}
            $calc_val[ 3 ] = $calc_val[ 3 ] + ( $calc_val[ 2 ] * $value / 100 ); 
          } 
        } 
        if ( $calc_val[ 3 ] <  $arr ['ink_tar_min'] ) $calc_val[ 3 ] = $arr ['ink_tar_min'];
        // check max
        if ( $calc_val[ 3 ] > $arr ['ink_tar_max'] ) $calc_val[ 3 ] = $arr ['ink_tar_max'];
       $arr_uit ['som_ik'] = $calc_val[ 3 ];
        // btw compensatie
        if ( $arr [ 'wrref1comp' ] <= 2 && $arr[ 'wrref2comp' ] <= 2 )  $arr_uit ['som_ik'] = $arr_uit ['som_ik'] * $arr ['ink_tar_btw']; 
        $arr_uit ['som_totaalik'] = $arr_uit ['som_totaal'] + $arr_uit ['som_ik'];      
      } //$regelsArr[ 'wrink' ] == 1
    } 
      default:
      break;
  } //$func
  $returnvalue = $arr_uit;
  return $returnvalue;
}
function Gsm_som_report( $arr_in, $calc, $MOD_GSMOFF, $func = 1 ){
  global $msg;
  $returnvalue = $arr_in[ 'toegift' ];
  $LINETEMP[ 81 ] = '<tr %1$s><td>%2$s</td><td colspan="4">%3$s</td></tr>'.CH_CR;
  $LINETEMP[ 83 ] = '<tr %1$s><td colspan="2">%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td></tr>'.CH_CR;
  $LINETEMP[ 84 ] = '<tr %1$s><td colspan="2">%2$s</td><td align="right">%3$s</td><td>%4$s</td><td>%5$s</td></tr>'.CH_CR;
  $returnvalue .= '<table class="container" width="100%">';
  $returnvalue .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], '', ucfirst( 'Overzicht' ), '', '', '' ) ;
  switch ( $func ){
    case 1:
	  $first = true;
      $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Hoofdsom', '&euro;&nbsp;'.number_format($arr_in ['som_hoofd'], 2, ',', '.'),'', '' ) ;
	  $returnvalue .= sprintf( $LINETEMP[ 83 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Rente periode', sprintf('Vanaf : %s tot %s.', $arr_in ['low_val'] , $arr_in [ 'wrcalcdate' ] ) ,'','' ) ; 
      $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Rente', '&euro;&nbsp;'.number_format($arr_in ['som_rente'], 2, ',', '.'),'', '' ) ;
      $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Hoofdsom met rente', '&euro;&nbsp;'.number_format($arr_in ['som_totaal'], 2, ',', '.'),'', '' ) ;
	  if ($arr_in ['som_ik'] >1) {
        $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Incassokosten', '&euro;&nbsp;'.number_format($arr_in ['som_ik'], 2, ',', '.'),'', '' ) ;
	    $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Hoofdsom met rente en incassokosten', '&euro;&nbsp;'.number_format($arr_in ['som_totaalik'], 2, ',', '.'),'', '' ) ;
	  }
	  $returnvalue .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', 'Aan deze berekening vallen geen rechten of privileges te ontlenen.', '', '', '' ) ;
    default:
      break;
  } //$func
  $returnvalue .= '</table>';
  return $returnvalue;
}