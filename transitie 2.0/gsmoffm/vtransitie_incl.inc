<?php
/*
 *  @module         Office module
 *  @Include        file for wettelijke renteberekening
 *  @version        see info.php of this module
 *  @author         Gerard Smelt
 *  @copyright      2015, Gerard Smelt
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
 */
// include class.secure.php to protect this file and the whole CMS!
if ( defined( 'WB_PATH' ) ) {
	include( WB_PATH . '/framework/class.secure.php' );
} else {
	$oneback = "../";
	$root    = $oneback;
	$level   = 1;
	while ( ( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) ) ) {
		$root .= $oneback;
		$level += 1;
	}
	if ( file_exists( $root . '/framework/class.secure.php' ) ) {
		include( $root . '/framework/class.secure.php' );
	} else {
		trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
	}
} 
/* change history
 * 20151207  aanpassing naar nieuw maximum (vtrantable wordt ook aangepast)
 * 20150105  correctie berekening overgangs regeling 50+ 
 */
function Gsm_login_person ($arr_in, $user_id) {
  // bepaal de persoonsgegevens indien login
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
	$arr_uit[ 'tvref0id' ] = $row ['id'];
	$hulp_1 = explode( "|", $row[ 'name' ] ) ;
	$arr_uit[ 'tvref0name' ] = sprintf("%s %s %s", 
	  ( isset( $hulp_1[ 1 ] ) ) ? $hulp_1[ 1 ] : '', 
	  ( isset( $hulp_1[ 2 ] ) ) ? $hulp_1[ 2 ] : '',
	  ( isset( $hulp_1[ 3 ] ) ) ? $hulp_1[ 3 ] : '',
	  '');  
	$arr_uit[ 'tvref0name' ]= Gsm_eval(  $arr_uit[ 'tvref0name' ], 1);
	$hulp_2 = explode( "|", $row[ 'adres' ] );
	$arr_uit[ 'tvref0adres' ] = sprintf("%s%s%s%s",
	  ( isset( $hulp_2[ 0 ] ) && strlen( $hulp_2[ 0 ] ) >3 ) ? $hulp_2[ 0 ] : '',
	  ( isset( $hulp_2[ 1 ] ) && strlen( $hulp_2[ 1 ] ) >3 ) ? CH_CR .$hulp_2[ 1 ] : '',
	  ( isset( $hulp_2[ 2 ] ) && strlen( $hulp_2[ 2 ] ) >3 ) ? CH_CR .$hulp_2[ 2 ] : '',
	  ( isset( $hulp_2[ 3 ] ) && strlen( $hulp_2[ 3 ] ) >3 ) ? CH_CR .$hulp_2[ 3 ] : '');
	$arr_uit[ 'owner_email' ] = $row ['email'];
	$arr_uit[ 'tvref1comp' ] = $row ['comp'];
	$arr_uit[ 'tvsummary' ] = 2;
	if ( $debug ) Gsm_debug( $arr_uit, __LINE__);
	$arr_uit['returnvalue']=true;
  }
  unset( $query );  
  $returnvalue = $arr_uit;
  return $returnvalue;
}

function Gsm_post_data () {
  // Get $post data formulier transitie
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
  if ( isset( $_POST[ 'tvsummary' ] ) ) $arr_uit[ 'tvsummary' ] = Gsm_eval( $_POST[ 'tvsummary' ], 2 );  
  if ( isset( $_POST[ 'tvref0' ] ) ) $arr_uit[ 'tvref0' ] = Gsm_eval( $_POST[ 'tvref0' ], 1 );
  if ( isset( $_POST[ 'owner_email' ] ) ) $arr_uit[ 'owner_email' ] = $_POST[ 'owner_email' ]; 
  if ( isset( $_POST[ 'tvcalcdate' ] ) ) $arr_uit[ 'tvcalcdate' ] = Gsm_eval( $_POST[ 'tvcalcdate' ], 9, date( "Y-m-d" ), '2015-07-01' );
 
  if ( isset( $_POST[ 'tvref0adres' ] ) ) $arr_uit[ 'tvref0adres' ] =  Gsm_eval ($_POST[ 'tvref0adres' ], 5); 
  if ( isset( $_POST[ 'tvref0name' ] ) ) $arr_uit[ 'tvref0name' ] =  Gsm_eval ($_POST[ 'tvref0name' ], 1, 255 );  
  
  if ( isset( $_POST[ 'tvref1adres' ] ) ) $arr_uit[ 'tvref1adres' ] =  Gsm_eval ($_POST[ 'tvref1adres' ], 5); 
  if ( isset( $_POST[ 'tvref1name' ] ) ) $arr_uit[ 'tvref1name' ] =  Gsm_eval ($_POST[ 'tvref1name' ], 1, 255 );  
  if ( isset( $_POST[ 'tvgebdat' ] ) ) $arr_uit[ 'tvgebdat' ] = Gsm_eval( $_POST[ 'tvgebdat' ], 9, date( "Y-m-d" ), '1940-01-01' );  
  
  if ( isset( $_POST[ 'tvref2adres' ] ) ) $arr_uit[ 'tvref2adres' ] =  Gsm_eval ($_POST[ 'tvref2adres' ], 5);  
  if ( isset( $_POST[ 'tvref2name' ] ) ) $arr_uit[ 'tvref2name' ] =  Gsm_eval( $_POST[ 'tvref2name' ] , 1, 255 );  
  if ( isset( $_POST[ 'tvref2comp' ] ) ) $arr_uit[ 'tvref2comp' ] = Gsm_eval ($_POST[ 'tvref2comp' ], 8);
  if ( isset( $_POST[ 'tvref2kvk' ] ) ) $arr_uit[ 'tvref2kvk' ] = Gsm_eval ($_POST[ 'tvref2kvk' ] , 1, 30 );    
  if ( isset( $_POST[ 'tvref2vat' ] ) ) $arr_uit[ 'tvref2vat' ] = Gsm_eval ($_POST[ 'tvref2vat' ] , 1, 30 );    

  if ( isset( $_POST[ 'tvdatin' ] ) ) $arr_uit[ 'tvdatin' ] = Gsm_eval( $_POST[ 'tvdatin' ], 9, date( "Y-m-d" ), '1960-01-01' );
  if ( isset( $_POST[ 'tvdatout' ] ) ) $arr_uit[ 'tvdatout' ] = Gsm_eval( $_POST[ 'tvdatout' ], 9, '2038-01-01', '2015-07-01' );
  if ( isset( $_POST[ 'tvbtoamt' ] ) ) $arr_uit[ 'tvbtoamt' ] = Gsm_eval( $_POST[ 'tvbtoamt' ], 8, 1000000, -1000000 );
  if ( isset( $_POST[ 'tvbtoper' ] ) ) $arr_uit[ 'tvbtoper' ] = Gsm_eval( $_POST[ 'tvbtoper' ], 8, 15, 0 );
 
  if ( isset( $_POST[ 'tvbtoext' ] ) ) $arr_uit[ 'tvbtoext' ] = Gsm_eval( $_POST[ 'tvbtoext' ], 8, 1000000, -1000000 ); 
  if ( isset( $_POST[ 'tvvakpct' ] ) ) $arr_uit[ 'tvvakpct' ] = Gsm_eval( $_POST[ 'tvvakpct' ], 8, 10, 0 );   
  if ( isset( $_POST[ 'tvprest' ] ) ) $arr_uit[ 'tvprest' ] = Gsm_eval( $_POST[ 'tvprest' ], 8, 1000000, -1000000 ); 
  if ( isset( $_POST[ 'tvgevaar' ] ) ) $arr_uit[ 'tvgevaar' ] = Gsm_eval( $_POST[ 'tvgevaar' ], 8, 1000000, -1000000 );
  if ( isset( $_POST[ 'tvorkw' ] ) ) $arr_uit[ 'tvorkw' ] = Gsm_eval( $_POST[ 'tvorkw' ], 8, 8, 0 ); 
  if ( isset( $_POST[ 'tvperc' ] ) ) $arr_uit[ 'tvperc' ] = Gsm_eval( $_POST[ 'tvperc' ], 8, 1000, 0 );
  if ( isset( $_POST[ 'tvamtc' ] ) ) $arr_uit[ 'tvamtc' ] = Gsm_eval( $_POST[ 'tvamtc' ], 8, 1000000, -1000000 );  
  
  if ( isset( $_POST[ 'comment' ] ) ) $arr_uit[ 'comment' ] = Gsm_eval( $_POST[ 'comment' ], 1, 255 );
//  foreach ($arr_uit as $key => $value) { echo __LINE__." ".$key."|".$value."<br/>"; }
  $arr_uit['returnvalue']=true;
  $returnvalue = $arr_uit;
  return $returnvalue;
}

function Gsm_set_status ($table , $recid , $status=0 ) {
  // change status field
  global $database;  
  $returnvalue = true;
  $query = "SELECT `tvstatus` FROM `" . $table . "` WHERE `id` = '".$recid."'"; 
  $results = $database->query( $query ); 
  $row = $results->fetchRow(); 
  if ($status == 3 && $status < $row['tvstatus'] ) $status = $row['tvstatus'];
  $hulpArr = array( );	
  $hulpArr ['tvstatus'] = $status; 
  if ($status==0 ) $hulpArr ['comment'] = ' verwijderd op '.date( "Y-m-d" );	
  if ($status==1 ) $hulpArr ['comment'] = ' in bewerking op '.date( "Y-m-d" );	
  if ($status==2 ) $hulpArr ['comment'] = ' compleet op '.date( "Y-m-d" );	
  if ($status==3 ) $hulpArr ['comment'] = ' activated op '.date( "Y-m-d" );	
  $query = "UPDATE `".$table."` SET ".Gsm_parse (2,$hulpArr)."  WHERE `id` = '".$recid."'";
  $results = $database->query( $query ); 	
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
	
    $arr_uit[ 'tvstatus' ] = $row [ 'tvstatus' ];
    $arr_uit[ 'tvref0' ] = $row [ 'tvref0' ];
    $arr_uit[ 'tvdata' ] = $row [ 'tvdata' ];
    $arr_uit[ 'tvmodel' ] = $row [ 'tvmodel' ];	
    $arr_uit[ 'tvsupport' ] = $row [ 'tvsupport' ];		
    $arr_uit[ 'tvcalcdate' ] = $row [ 'tvcalcdate' ];
    $arr_uit[ 'tvremind' ] = $row [ 'tvremind' ];
	
    $arr_uit[ 'tvref0id' ] = $row [ 'tvref0id' ];
    $arr_uit[ 'tvref0name' ] = $row [ 'tvref0name' ];
    $arr_uit[ 'tvref0adres' ] = $row [ 'tvref0adres' ];
	
    $arr_uit[ 'tvref1name' ] = $row [ 'tvref1name' ];
    $arr_uit[ 'tvref1adres' ] = $row [ 'tvref1adres' ];	
	
    $arr_uit[ 'tvref2adres' ] = $row [ 'tvref2adres' ];
    $arr_uit[ 'tvref2comp' ] = $row [ 'tvref2comp' ];
    $arr_uit[ 'tvref2name' ] = $row [ 'tvref2name' ];
	$help_1= explode ("|",$row [ 'tvref2reg' ]);
	if(strpos($help_1[0], 'vat') !==false) $arr_uit[ 'tvref2vat' ] = trim(str_replace ('vat',"",  $help_1[0]));
	if(strpos($help_1[1], 'kvk') !==false) $arr_uit[ 'tvref2kvk' ] = trim(str_replace ('kvk',"",  $help_1[1]));
	
    $arr_uit[ 'tvgebdat' ] = $row [ 'tvgebdat' ];
    $arr_uit[ 'tvdatin' ] = $row [ 'tvdatin' ];
    $arr_uit[ 'tvdatout' ] = $row [ 'tvdatout' ];
	
    $arr_uit[ 'tvbtoamt' ] = $row [ 'tvbtoamt' ];
    $arr_uit[ 'tvbtoper' ] = $row [ 'tvbtoper' ];
    $arr_uit[ 'tvbtoext' ] = $row [ 'tvbtoext' ];	
    $arr_uit[ 'tvvakpct' ] = $row [ 'tvvakpct' ];
    $arr_uit[ 'tvprest' ] = $row [ 'tvprest' ];
    $arr_uit[ 'tvgevaar' ] = $row [ 'tvgevaar' ];
    $arr_uit[ 'tvperc' ] = $row [ 'tvperc' ];
    $arr_uit[ 'tvamtc' ] = $row [ 'tvamtc' ];
    $arr_uit[ 'tvorkw' ] = $row [ 'tvorkw' ];
	
    $arr_uit[ 'comment' ] = $row [ 'comment' ];
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
	  $hulpArr ['tvstatus'] = 1; 
	  $hulpArr ['tvref0'] = $arr_in[ 'tvref0' ];
	  $hulpArr ['tvdata'] = "Online data invoer"; 
	  if( isset ($arr_in[ 'tvsupport' ])) { 
	    $hulpArr ['tvsupport'] = $arr_in[ 'tvsupport' ]; 
	  } else { 
        if (isset ($arr_in[ 'owner_fileref' ])) $hulpArr ['tvsupport'] = $arr_in[ 'owner_fileref' ];
	  }
	  $hulpArr ['tvcalcdate'] = $arr_in[ 'tvcalcdate' ];
	  $hulpArr ['tvremind'] = $arr_in[ 'tvremind' ];

	  $hulpArr ['tvref0id'] = $arr_in[ 'tvref0id' ];
	  $hulpArr ['tvref0name'] = $arr_in[ 'tvref0name' ]; 
	  $hulpArr ['tvref0adres'] = $arr_in[ 'tvref0adres' ]; 

	  $hulpArr ['tvref1name'] = $arr_in[ 'tvref1name' ];
	  $hulpArr ['tvref1adres'] = $arr_in[ 'tvref1adres' ];
	  $hulpArr ['tvgebdat'] = $arr_in[ 'tvgebdat' ];
	  
	  $hulpArr ['tvref2adres'] = $arr_in[ 'tvref2adres' ];
	  $hulpArr ['tvref2comp'] = $arr_in[ 'tvref2comp' ];
	  $hulpArr ['tvref2name'] = $arr_in[ 'tvref2name' ];
	  $hulpArr ['tvref2reg'] = sprintf ("vat %s | kvk %s", (isset($arr_in[ 'tvref2vat' ])) ? $arr_in[ 'tvref2vat' ] :"", (isset($arr_in[ 'tvref2kvk' ])) ? $arr_in[ 'tvref2kvk' ]:"");

	  $hulpArr ['tvdatin'] = $arr_in[ 'tvdatin' ];
	  $hulpArr ['tvdatout'] = $arr_in[ 'tvdatout' ];
	  
	  $hulpArr ['tvbtoamt'] = $arr_in[ 'tvbtoamt' ];
	  $hulpArr ['tvbtoper'] = $arr_in[ 'tvbtoper' ];
	  $hulpArr ['tvbtoext'] = $arr_in[ 'tvbtoext' ];
	  $hulpArr ['tvvakpct'] = $arr_in[ 'tvvakpct' ];
	  $hulpArr ['tvprest'] = $arr_in[ 'tvprest' ];
	  $hulpArr ['tvgevaar'] = $arr_in[ 'tvgevaar' ];
	  
	  $hulpArr ['tvorkw'] = $arr_in[ 'tvorkw' ];
	  $hulpArr ['tvperc'] = $arr_in[ 'tvperc' ];
	  $hulpArr ['tvamtc'] = $arr_in[ 'tvamtc' ];	  

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

function Gsm_trans_datum ( $datin, $Arr, $func = 1, $datin2=0  ){
 global $msg;
  // datin formaat (yyyy-mm-dd)
  $datum = date_create_from_format('Y-m-d', $datin);
  // offset in maanden, 
  $returnvalue = "";
  switch ( $func ){
    case 1:
      // bepaal datum waarop 50
      $n=50*12;
      $n1 = (date_format($datum, 'Y')-1)*12+date_format($datum, 'm')+$n;
      $n2 = (($n1-1)%12)+1;
      $n3 = ($n1-$n2)/12+1;
      $n4 = date_create_from_format('Y-m-d', $n3.'-'.$n2.'-'.date_format($datum, 'd'));
      $returnvalue = date_format($n4, 'Y-m-d');
      break;
    case 2:
      // bepaal aow datum
      $n=0;
      foreach ( $Arr [ 'pensioen_offset'] as $key => $value )  { if ( $key <= $datin) $n=$value; }
      $n = $n + $Arr [ 'pensioen_base'];
      $n1 = (date_format($datum, 'Y')-1)*12+date_format($datum, 'm')+$n;
      $n2 = (($n1-1)%12)+1;
      $n3 = ($n1-$n2)/12+1;
      $n4 = date_create_from_format('Y-m-d', $n3.'-'.$n2.'-'.date_format($datum, 'd'));
      $returnvalue = date_format($n4, 'Y-m-d');
      break;
    case 3:
      // bepaal datum met offset in maanden
      $n= $datin2;
      $n1 = (date_format($datum, 'Y')-1)*12+date_format($datum, 'm')+$n;
      $n2 = (($n1-1)%12)+1;
      $n3 = ($n1-$n2)/12+1;
      $n4 = date_create_from_format('Y-m-d', $n3.'-'.$n2.'-'.date_format($datum, 'd'));
      $returnvalue = date_format($n4, 'Y-m-d');
      break;
    case 4:
      // bepaal verschil in volle maanden
      $datum2 = date_create_from_format('Y-m-d', $datin2);
      $n1 = (date_format($datum, 'Y')-1)*12+date_format($datum, 'm');
      $n2 = (date_format($datum2, 'Y')-1)*12+date_format($datum2, 'm');
      if (date_format($datum, 'd') > date_format($datum2, 'd')) $n2--; // correctie voor maand op basis van dagnummer
      $returnvalue = $n2-$n1;
      break;
    default:
      break;
  } //$func
  return $returnvalue;
} 

function Gsm_trans_bereken( $Arr, $dates , $func = 1 ){
  global $msg;
  $calc = array( );
  $returnvalue = "";
  $calc[99]=0; //error counter
  switch ( $func ){
    case 1:
      // geboorte
      $calc ['a0']= $Arr [ 'tvgebdat' ]; //geboortedatum
      // 50 jaar
      $calc ['a1']= Gsm_trans_datum ( $Arr [ 'tvgebdat' ], $Arr, 1);
      // aow datum berekening
      $calc ['a2']= Gsm_trans_datum ( $Arr [ 'tvgebdat' ], $Arr, 2);
      // Datum in dienst
      $calc ['a3'] = $Arr [ 'tvdatin' ];
      if ($calc ['a0'] > $calc ['a3']) $calc ['a3']= $calc ['a0'];
      // Datum uit dienst
      $calc ['a4'] = $Arr [ 'tvdatout' ];
      if ($calc ['a3'] > $calc ['a4']) $calc ['a4']= $calc ['a3'];
      // Datum uit dienst controle
      if ($calc ['a4'] < $Arr[ 'overgangsregeling' ][ 'begin' ])  $calc[99] +=1;   // kantonrechters formule 
      $n = Gsm_trans_datum ( $calc ['a4'], $Arr, 4, $calc ['a2']); // uit dienst en aow datum
      if ($n < 1) $calc[99] += 2;   // met pensioen       
      // Periode in dienst
      $calc ['a5'] = Gsm_trans_datum ( $calc ['a3'], $Arr, 4, $calc ['a4']); // periode in dienst
      // geen rechten
      if ($calc ['a5'] < 24) $calc[99] +=4;
      if ($calc [99] > 0) $calc['a5']=0;
      // overgangs regeling
      if ($Arr [ 'tvorkw' ] == 1 && $calc ['a4'] < $Arr[ 'overgangsregeling' ][ 'einde' ]) {
        $calc ['a6']= -Gsm_trans_datum ( $calc ['a3'], $Arr, 4, $Arr[ 'overgangsregeling' ][ 'kleintjes' ]); // Overgangsregeling
        if ($calc ['a6'] >0) $calc ['a6']=0;
      } else { 
        $calc ['a6']=0;
      }
      // correction
      if (($calc ['a5']+ $calc ['a6']) > $Arr [ 'tvperc' ]) { $calc ['a7'] = - $Arr [ 'tvperc' ]; } else { $calc ['a7'] = - ( $calc ['a5'] + $calc ['a6']); }; 
      if ($calc ['a7'] >0) $calc ['a7']=0;
      // arbeidsduur 
      $calc ['a8'] = $calc ['a5'] + $calc ['a6'] + $calc ['a7'];
      $calc ['a9'] = (int) ($calc ['a8']/6);
      // arbeidsduur na 50e     
      $calc ['a10'] =(int)( Gsm_trans_datum ( $calc ['a1'], $Arr, 4, $calc ['a4'])/6);
      if ($calc ['a10'] < 1) $calc ['a10']=0;
      if ($calc ['a10'] > $calc ['a9']) $calc ['a10'] = $calc ['a9'];
      // arbeidsduur voor 50e
      $calc ['a11'] = $calc ['a9'] - $calc ['a10']; 
      if ($calc ['a11'] < 1) $calc ['a11']=0;
      // arbeidsduur eerste 120 maanden
      $calc ['a12'] = 20; 
      if ($calc ['a12'] > $calc ['a9']) $calc ['a12']= $calc ['a9'];
      $calc ['a13'] = $calc ['a9'] - $calc ['a12'];    
      //Bruto loon berekening
      $calc ['a20']= $Arr[ 'tvbtoamt' ]; 
      $calc ['a21']= $Arr[ 'tvbtoper' ];
      $calc ['a22']= $Arr[ 'tvbtoext' ];
      $calc ['a23']= $Arr[ 'tvvakpct' ];
      $calc ['a24']= $Arr[ 'tvprest' ];
      $calc ['a25']= $Arr[ 'tvgevaar' ];
      // berekening bruto jaar salaris
      if (!array_key_exists($calc ['a21'], $Arr['trans_loon_periode'])) $calc ['a21']=1;
      $calc ['a26']= (($calc ['a20'] * $calc['a21'] ) + $calc ['a22']);
      if ( $calc ['a26'] > $Arr [ 'max_vakantie_over']) {$calc ['a27'] = round(($Arr [ 'max_vakantie_over'] * ($calc ['a23']/100)),2);  } else { $calc ['a27'] = round(($calc ['a26'] * ($calc ['a23']/100)),2);}
      $calc ['a28'] = $calc ['a26'] +$calc ['a27'];
      $calc ['a29'] = $calc ['a24'] + $calc ['a25'];
      $calc ['a30'] = round($calc ['a28'] /12,2);
      $calc ['a35'] = ($calc ['a12'] /6) + ($calc ['a13'] /4);
      // overgangsregel deel 2
      $calc ['a36']=0;
      if ($Arr [ 'tvorkw' ] != 1 // geen kleine werknemer
        && $calc ['a4'] < $Arr[ 'overgangsregeling' ][ 'einde' ] //geldigheids duur overgansregeling
        && $calc ['a12'] == 20 )   // arbeidsduur van tenminste 120 maanden
// vervalt 20150105          { $calc ['a36'] = $calc ['a10'] / 3 ; }   //
		  { $calc ['a36'] = $calc ['a10'] / 4 ; }   // toegevoegd 20150105   
      $calc ['a37'] = $calc ['a36']+ $calc ['a35'];
      // Bepaling bedrag
      $calc ['a40'] = round($calc ['a37']* $calc ['a30'],2);
      // check met maxima
	  // bepaal maximum 
	  $calc ['a41']=0; // toegevoegd 20151207
	  foreach( $Arr [ 'maxima_transitie'] as $date => $max) // toegevoegd 20151207
		{ if ($calc ['a4'] >= $date) $calc ['a41'] = $max; } // toegevoegd 20151207
//vervalt 20151207    $calc ['a41'] = $Arr [ 'max_transitie'];  
	  // als het jaar salaris meer is gaat max omhoog
      if ($calc ['a41'] < $calc ['a28']) $calc ['a41'] = $calc ['a28'];
      $calc ['a42'] =$Arr [ 'tvamtc'];
      // correctie eraf
      $calc ['a45'] = $calc ['a40'];
      if ($calc ['a45'] > $calc ['a41'])$calc ['a45'] = $calc ['a41'];
      $calc ['a45'] -= $calc ['a42'];
      default:
      break;
  } //$func
  $returnvalue = $calc;
  return $returnvalue;
}

function Gsm_trans_report( $arr_in, $calc, $MOD_GSMOFF, $func = 1 ){
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
      $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Geboorte datum', sprintf('%s', $calc[ 'a0' ] ),'', '' ) ;
	  $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Arbeids duur', sprintf('Vanaf : %s tot %s', $calc [ 'a3' ] , $calc [ 'a4' ] ) ,'','' ) ; 
      $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Bruto Maandsalaris', '&euro;&nbsp;'.number_format($calc [ 'a30' ], 2, ',', '.'),'', '' ) ;
      $returnvalue .= sprintf( $LINETEMP[ 84 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], 'Transitie vergoeding', '&euro;&nbsp;'.number_format($calc [ 'a45' ], 2, ',', '.'),'', '' ) ;
	  $returnvalue .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', 'Aan deze berekening vallen geen rechten of privileges te ontlenen.', '', '', '' ) ;
    default:
      break;
  } //$func
  $returnvalue .= '</table>';
  return $returnvalue;
}
