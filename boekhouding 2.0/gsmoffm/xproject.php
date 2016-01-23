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
  $oneback = "../";
  $root = $oneback;
  $level = 1;
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
// end include class.secure.php
/* change history
 */
/*
 * variables
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'project',  
// voor versie display
  'modulen' => 'xproject',
  'versie' => ' vv201601916 ', 
// general parameters
  'app' => "bookproject",  
  'file' => "bkproject", 
  'table' =>   CH_DBBASE."_bkproject", 
  'owner' => (isset($settingArr[ 'logo'])) ? $settingArr[ 'logo'] : '', 
// for display en pdf output 
  'seq' => (isset($_POST[ 'next' ])) ?  $regelsArr[ 'seq']= $_POST[ 'next' ]: 0,
  'n' => 0,
  'qty' => (isset($settingArr['oplines'])) ? $settingArr['oplines'] : 30,  
  'project' => '',
// search
  'search' => '',
  'search_mysql' => '', 
  'volgorde' => 'name', 
  'opzoek'=> 'name',  
//display
  'descr' => '',
  'head' => '',
  'select' => '', 
  'update' => '',
  'toegift' => '',
  'rapport' => '',
  'memory' => '',
  'hash' => '', 
  'recid' => '',
  'record_update' => false,
  'pdf_ok' => true,
  'edit_ok' => true,
  'add_ok' => true,
  'today' => date( "Y-m-d" ),
// pdf
  'print_regels' => 1, 
  'cols'=> array(55, 70, 20, 10, 10, 10),
  'leeg' => array( 1=>"",2=>"",3=>"",4=>"",5=>"",6=>"" ),
  'today_pf' => date( "_Ymd" ),
);
$regelsArr['project'] = $regelsArr[ 'app' ] . ' - Overzicht';
/*
 * Lay-out strings
 */
// icontemp 1-19 is defined in language module
// linetemp 1-19 is defined in language module
/*
 * various functions
 */
 /*
function func_guid() { // create guid
  if (function_exists('com_create_guid') === true) { return trim(com_create_guid(), '{}'); }
  return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
} 
function func_checks ( $table, $func=1, $first='') {  // Check table or preload parts of it
  global $database;
  global $MOD_GSMOFF; 
  global $msg;
  $oke = true;
  $returnvalue = '';
  switch ( $func ) {
    case 1: //precense
 	  break;
    case 2: // preload rekening nummers
	  break;
    case 3: // preload projects
	  break;
    default:
      break;
  }
  return $returnvalue;
}
*/
function func_table_preload ( $table, $func=1, $first='') {  // Check table precense or preload parts of it
  global $database;
  global $MOD_GSMOFF; 
  $oke = true;
  $returnvalue = '';
  switch ( $func ) {
  /*
    case 1: //precense
      $check_query = "SHOW TABLES LIKE '" . $table . "'";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error0' ] . $table . " missing</br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
	  $returnvalue=$oke;
	  break;
    case 2: // preload rekening nummers
	  (is_array($first)) ?$rekeningArr = $first : $rekeningArr = array( );
      $check_query   = "SELECT * FROM `" . $table . "` ORDER BY `rekeningnummer`";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error2' ] . $table . " br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
      while ( $result = $check_results->fetchRow() ) $rekeningArr[ $result[ 'id' ] ] = $result[ 'rekeningnummer' ] . " - " . $result[ 'name' ];
      $returnvalue=$rekeningArr;
	  break;
    case 3: // preload projects
	  (is_array($first)) ? $projectArr = $first : $projectArr = array( );
      $check_query   = "SELECT * FROM `" . $table . "` ORDER BY `name`";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error2' ] . $table . " </br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
      while ( $result = $check_results->fetchRow() ) $projectArr[ $result[ 'name' ] ] = $result[ 'name' ] . " - " . $result[ 'project' ];
      $returnvalue=$projectArr;
	  break;
	  */
    case 8: // get the fields
	  (is_array($first)) ? $fieldArr = $first : $fieldArr = array( );
      $check_query ="DESCRIBE ". $table;
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error0' ] . $table . " missing</br>";	  
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
	  while ( $result = $check_results->fetchRow() ) $fieldArr[$result['Field']]= $result['Type']; 
	  $returnvalue=$fieldArr;
	  break;
    case 9: // file parameters	
      // $ first contains section	
      $settingsArr = array( );	
	  $check_query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='".$first."' ORDER BY `id`";
      $check_results = $database->query( $check_query );
      while ( $result = $check_results->fetchRow() ) $settingArr[$result['name']]= $result['value']; 
	  $check_query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='0' AND `table`='".$table."' ORDER BY `id`";
      $check_results = $database->query( $check_query );
      while ( $result = $check_results->fetchRow() ) $settingArr[$result['name']]= $result['value']; 
      $returnvalue=$settingArr;
	  break;
    default:
      break;
  }
  return $returnvalue;
}
/*
 * Debug data
 */
if ( $debug ) {
  Gsm_debug( $settingArr, __LINE__);
  Gsm_debug( $regelsArr, __LINE__ );
  Gsm_debug( $place, __LINE__ );
}
/*
 * Table exists
 */
func_table_preload ( $regelsArr[ 'table' ], 1); //check precense
$settingArr=array_merge($settingArr, func_table_preload ( $regelsArr[ 'file' ], 9 , $page_id)); // Pick up settings
$fieldArr = func_table_preload ( $regelsArr[ 'table' ], 8 ); // get fields
// all fields collected now remove the standard fields except name
unset ($fieldArr['id']);
unset ($fieldArr['zoek']);
unset ($fieldArr['updated']);
/*
 * processing settings array
 */
if (!isset( $settingArr ['mode'])) $settingArr ['mode']= ""; 
if (CH_LOC == "front") {
  if (strstr($settingArr ['mode'], "lis")) $regelsArr ['pdf_ok'] = true;
  if (strstr($settingArr ['mode'], "cha")) {
    $regelsArr ['edit_ok'] = true;
    if (strstr($settingArr ['mode'], "add")) $regelsArr ['add_ok'] = true;
  }
} elseif (CH_LOC == "tools"){
  $regelsArr ['pdf_ok'] = true;
  $regelsArr ['edit_ok'] = true;
  $regelsArr ['add_ok'] = true;
} else {
  if (strstr($settingArr ['mode'], "pdf")) $regelsArr ['pdf_ok'] = true;
  if (strstr($settingArr ['mode'], "edi")) {
    $regelsArr ['edit_ok'] = true;
    if (strstr($settingArr ['mode'], "add")) $regelsArr ['add_ok'] = true;
  }  
}
/*
 * Heading
 */
if (isset( $settingArr ['head'])) $regelsArr ['veldhead']= explode ("|",$settingArr ['head']);
if (CH_LOC == "front" && isset ($regelsArr ['veldname'][0])) $regelsArr['print_regels']=$regelsArr ['veldhead'][0];
//  $regelsArr ['veldhead'][0]=''; //  ** heading names to be listed can be inserted, relevance of settings can be removed ** 
// Velden
if (isset( $settingArr ['field']))  $regelsArr ['veldname']=explode ("|",$settingArr ['field']);
if (CH_LOC != "front" && isset ($regelsArr ['veldname'][0])) $regelsArr['print_regels']=$regelsArr ['veldname'][0];
//  $regelsArr ['veldname'][0]='';  //  ** fields names to be listed can be inserted, relevance of settings can be removed ** 
if (!isset($regelsArr ['veldname'])) { 
	$regelsArr ['veldname'] = $regelsArr ['leeg'];
	$i=1; 
	foreach ($fieldArr as $key => $value ){ $regelsArr ['veldname'][$i]=$key; $i++;} 
}
if (!isset($regelsArr ['veldhead'])) $regelsArr ['veldhead'] = $regelsArr ['veldname'];
/*
 * is a record selected
 */  
if (isset($_POST['vink'][0]) && $_POST['vink'][0] > 0) $regelsArr['recid'] = $_POST['vink'][0];
if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
if ($regelsArr['recid'] <1 && isset($_POST['recid']) && $_POST['recid'] > 0 ) $regelsArr['recid'] = $_POST['recid'];
if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
if ($regelsArr['recid'] <1 && isset($_GET['recid']) && $_GET['recid'] > 0 ) $regelsArr['recid'] = $_GET['recid'];
if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
if ($regelsArr['recid'] >0) {
  $regelsArr['memory']=$regelsArr['recid'];
  if ($debug) $msg['bug'] .= __LINE__ . ' '.$regelsArr['recid'].' access <br/>';
  /*
   * if data available get if from the database
   */  
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE `id`= '". $regelsArr['recid']."'";
  if ($debug) $msg['bug'] .= __LINE__.' '.$query.' <br/>';
  $results = $database->query($query);
  if (!$results || $results->numRows() >= 1) {
    $row = $results->fetchRow();
    foreach ($fieldArr as $key => $value) {
      $regelsArr['x' . $key] = $row[$key];
    } //$fieldArr as $key => $value
  } //!$results || $results->numRows() >= 1
  if ($debug) Gsm_debug($regelsArr, __LINE__);
} //$regelsArr['recid'] >0
unset( $query );
/*
 * process the input 
 */
if (isset($_POST['command'])) {
/*
 * process the input 
 */ 
  foreach ($fieldArr as $key => $value) {
    switch ($MOD_GSMOFF['file_type'][$value]){
      case 1: //    'varchar(255)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 1, 255,0);   
        break;
      case 3: //    'decimal(9,2)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 8, 20000, 0);
        break;
      case 4: //    'varchar(63)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 1, 63, 0);  
        break;
      case 5: //    'varchar(127)' 
        if (isset($_POST[$key])) $regelsArr['x' . $key]  = Gsm_eval($_POST[$key], 1, 127, 0); 
        break;
      case 6: //    'date' 
        if (isset($_POST[$key])) $regelsArr['x' . $key] = Gsm_eval($_POST[$key], 9, '2020-01-01', '1970-01-01');
        break;
      case 2: //    'int(11)' 
      case 7: //    'int(7)'   
      default:
        if (isset($_POST[$key])) $regelsArr['x' . $key] = $_POST[$key];
        break;
  } } }
/*
 * Debug data
 */
if ( $debug ) {
  Gsm_debug( $settingArr, __LINE__);
  Gsm_debug( $regelsArr, __LINE__ );
  Gsm_debug( $fieldArr, __LINE__);
  Gsm_debug( $_POST, __LINE__ );
  Gsm_debug( $_GET, __LINE__ ); 
}
/*
 * some job to do ?
 */
// Check for set-up
if ( isset( $_POST[ 'command' ] ) && isset( $_POST[ 'selection' ] ) && strstr($settingArr ['mode'], "alt") && $_POST[ 'selection' ] =="###") {
// Set-up
   $regelsArr[ 'mode'] = 1;
} elseif ( isset( $_POST[ 'command' ] ) ) {
  switch ( $_POST[ 'command' ] ) {
      
    case $MOD_GSMOFF[ 'tbl_icon' ][0]: // cancel
      if ( isset( $_POST[ 'sel' ] ) && strlen( $_POST[ 'sel' ] ) >= 1 ) {       // check selection
        $regelsArr[ 'search' ] = trim( $_POST[ 'sel' ] );
        $help = "%" . str_replace( ' ', '%', $regelsArr[ 'search' ] ) . "%";
        $regelsArr[ 'search_mysql' ] .= "WHERE `zoek` LIKE '" . $help . "'";
      }
      if ( isset( $_POST[ 'nxt' ] ) ) {       // volgende pagina ?
        $har = explode ("|", trim($_POST[ 'nxt' ]));
        if (isset ($har[1]) && $har[1]==$regelsArr[ 'search' ] ){
          $regelsArr[ 'seq' ] = $har[0];
        }
      }  
      $regelsArr[ 'mode'] = 9;
      break;  
    case $MOD_GSMOFF[ 'tbl_icon' ][1]: // voorbereiden voor wijzigen/edit
      $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
      $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
      $regelsArr[ 'mode'] = 7;
      break;
    case $MOD_GSMOFF[ 'tbl_icon' ][3]: //Toevoegen/add
      $regelsArr[ 'qty' ]= 3;
      $regelsArr[ 'mode'] = 8;
      break;
    case $MOD_GSMOFF[ 'tbl_icon' ][4]: // opslaan 
      if (isset ($_POST[ 'recid' ]) &&  $_POST[ 'recid' ] >=1 ) {
        $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
        $regelsArr ['record_update'] = true;
      }
    case $MOD_GSMOFF[ 'tbl_icon' ][5]:  // opslaan als nieuw of update
      if ( !isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] ) ) {
        $msg[ 'err' ] .= $MOD_GSMOFF[ 'error4' ] . '</br>';
        unset ($_POST);
        break;}
      $hulpArr = array(); // array field 
      foreach ($fieldArr as $key => $value ){
        if (isset($_POST[ $key ])) { 
          $hulpArr[$key]=stripslashes(htmlspecialchars($regelsArr['x' . $key])); 
        } else { 
          $hulpArr[$key]="";
        }
      }
      $hulpArr['zoek']=""; // voeg zoek toe
      $hulp=explode ("|", $settingArr['opzoek']);
      foreach ($hulp as $key=> $value) { 
        if (isset($hulpArr[$value])) $hulpArr['zoek'].=$hulpArr[$value]."|";
      }
      $hulpArr['zoek'].=$regelsArr[ 'recid' ];
      if ($regelsArr ['record_update']) {
        $query = "UPDATE `".$regelsArr ['table']."` SET ".Gsm_parse (2,$hulpArr)."  WHERE  `id`= '".$regelsArr ['recid']."'";
        if ( $debug ) $msg[ 'bug' ] .= __LINE__." ".$query.'<br/>';
        $results = $database->query( $query );
        $msg[ 'inf' ] .= 'Updated'.'<br/>';
      } else {
        $query = "INSERT INTO `".$regelsArr[ 'table' ]."` ". Gsm_parse (1,$hulpArr);
        if ( $debug ) $msg[ 'bug' ] .= __LINE__." ".$query.'<br/>';
        $results = $database->query( $query );
        $msg[ 'inf' ] .= $MOD_GSMOFF['added'].'<br/>'; 
      }
      unset( $query );
      $regelsArr[ 'mode'] = 9;
      break;
    case $MOD_GSMOFF[ 'tbl_icon' ][6]: // delete
      $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
      if ( !isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] ) ) {
        $msg[ 'err' ] .= $MOD_GSMOFF[ 'ERR0R4' ] . '</br>';
        unset ($_POST);
        break;}
      unset( $_SESSION[ 'page_h' ] );
      $query = "DELETE FROM `". $regelsArr[ 'table' ] . "` WHERE `id`='". $regelsArr[ 'recid' ] . "'";
      $results = $database->query( $query );
      $msg[ 'inf' ] .= $MOD_GSMOFF['deleted'].'<br/>'; 
      unset( $query );
      $regelsArr[ 'mode'] = 9;
      break;
    case $MOD_GSMOFF[ 'tbl_icon' ][10]: // veld
      if(isset ($_POST[ 'veld' ]) && isset ($_POST[ 'veld_type' ])) { // veld toevoegen probably not supported
        $query = "ALTER TABLE `". $regelsArr[ 'table' ] . "` ADD `".str_replace(" ", "_", trim($_POST[ 'veld' ]))."` ";
        $i=1;
        foreach ($MOD_GSMOFF['file_type'] as $key => $value) {
          if ($_POST[ 'veld_type' ] == $value) { $i=$key;}
          }
        $query .= $i;
		$query .= " NOT NULL";
		if ( $debug ) $msg[ 'bug' ] .= __LINE__.'  '.$query.'<br/>'; ;
        $results = $database->query( $query );
        unset( $query );
      }
      $regelsArr[ 'mode'] = 9;
      break;  
    case $MOD_GSMOFF['tbl_icon'][11]: //print
	  if ($regelsArr ['pdf_ok'] ) $regelsArr['filename_pdf'] = strtolower( $regelsArr[ 'project' ] . $regelsArr[ 'today_pf' ] ) . '.pdf';
      $regelsArr ['mode'] = 9;
      break;
    default:
	  if ( $debug ) $msg[ 'bug' ] .= __LINE__." get: ".$_POST[ 'command' ] .'<br/>'; 
      $regelsArr[ 'mode'] = 9;
      break;
  }
} elseif ( isset( $_GET[ 'command' ] ) ) {
  switch ( $_GET[ 'command' ] ) {
    case 'view':
      $regelsArr[ 'recid' ] = $_GET[ 'recid' ];
      $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
      $regelsArr[ 'mode'] = 6;
      break;
    default:
	  if ( $debug ) $msg[ 'bug' ] .= __LINE__." get: ".$_GET[ 'command' ] .'<br/>'; 
      $regelsArr[ 'mode'] = 9;
      break;
  }
} else { // so standard display
  $msg[ 'bug' ] .= __LINE__.' access <br/>';
  /*
   * standard display job with or without search
   */
}
if (!isset($query) && $regelsArr ['mode']==9 ) {
  if ( isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 2 ) {
    $regelsArr[ 'search' ] = trim( $_POST[ 'selection' ] );
    $help  = "%" . str_replace( ' ', '%', $regelsArr[ 'search' ] ) . "%";
    $regelsArr[ 'search_mysql' ] = " WHERE  `zoek` LIKE '" . $help . "'";
  }
  // loop through the records
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`" . $regelsArr[ 'search_mysql' ] . " ORDER BY `".$regelsArr['volgorde']."`";
  if ( $debug ) $msg[ 'bug' ] .= __LINE__.' '.$query.' <br/>';
  $results = $database->query( $query );
  $regelsArr ['n'] = $results->numRows();
  if (isset($regelsArr['filename_pdf'])) {
    require_once( $place_incl . 'pdf.inc' );   
    /*
     * pdf process initiation
     */
    $pdf = new PDF();
    global $title;
    global $owner;
    $owner = $regelsArr['owner'];
    $title = $regelsArr['project'];
    $run = date("Ymd_His");    
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->ChapterTitle(1,ucfirst($regelsArr ['app']));
    $pdf_text='';
    $pdf_data = array();
	$notfirst = false;
    $pdf->SetFont('Arial','',8);

// header
    if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
    if ($regelsArr['print_regels'] >1 ) { 
      $pdf_header = array(ucfirst($MOD_GSMOFF['tbl_label']), ucfirst($MOD_GSMOFF['tbl_value']));
    } else {
      $pdf_header = array();
        for ($i=1;$i<=6;$i++) { 
        if (isset ($regelsArr[ 'veldhead' ][$i]) && strlen($regelsArr[ 'veldhead' ][$i])>1) {
          $pdf_header[]=ucfirst($regelsArr[ 'veldhead' ][$i]); 
        }
      }
// $pdf_header = array(ucfirst($regelsArr[ 'veldhead' ][1]), ucfirst($regelsArr[ 'veldhead' ][2]),  ucfirst($regelsArr[ 'veldhead' ][3]), ucfirst($regelsArr[ 'veldhead' ][4]), ucfirst($regelsArr[ 'veldhead' ][5]),ucfirst($regelsArr[ 'veldhead' ][6]));
    }
// loop through records
    while ( $row = $results->fetchRow() ) {
      if ($regelsArr['print_regels'] >1 ) { 
        for($i=1;$i<=$regelsArr['print_regels'];$i++) {
          if ($i==1) {
            $line = sprintf("%s;%s;%s;;;",
               $row[$regelsArr[ 'veldname' ][$i]]." (".$row['id'].")",
               ucfirst($regelsArr[ 'veldhead' ][$i]),
               "",
               "");
              $pdf_data[] = explode(';',trim($line)); 
          } else {
            $line = sprintf("%s;%s;%s;;;",
              '',
              (isset($regelsArr[ 'veldname' ][$i])) ? ucfirst($regelsArr[ 'veldhead' ][$i]) : '',
              (isset($regelsArr[ 'veldname' ][$i]) && isset($row[$regelsArr[ 'veldname' ][$i]])) ? $row[$regelsArr[ 'veldname' ][$i]] : '');
              $pdf_data[] = explode(';',trim($line)); 
          }
        }
        $line = ";;;;;";
        $pdf_data[] = explode(';',trim($line));
      } else {
        $line = sprintf("%s;%s;%s;%s;%s;%s",
          (isset($regelsArr[ 'veldname' ][1]) && isset($row[$regelsArr[ 'veldname' ][1]])) ? $row[$regelsArr[ 'veldname' ][1]]." (".$row['id'].")" : '',
          (isset($regelsArr[ 'veldname' ][2]) && isset($row[$regelsArr[ 'veldname' ][2]])) ? $row[$regelsArr[ 'veldname' ][2]] : '',
          (isset($regelsArr[ 'veldname' ][3]) && isset($row[$regelsArr[ 'veldname' ][3]])) ? $row[$regelsArr[ 'veldname' ][3]] : '',
          (isset($regelsArr[ 'veldname' ][4]) && isset($row[$regelsArr[ 'veldname' ][4]])) ? $row[$regelsArr[ 'veldname' ][4]] : '',
          (isset($regelsArr[ 'veldname' ][5]) && isset($row[$regelsArr[ 'veldname' ][5]])) ? $row[$regelsArr[ 'veldname' ][5]] : '',
          (isset($regelsArr[ 'veldname' ][6]) && isset($row[$regelsArr[ 'veldname' ][6]])) ? $row[$regelsArr[ 'veldname' ][6]] : ''
        );
        $pdf_data[] = explode(';',trim($line)); 
      }
    }
    $pdf->DataTable( $pdf_header, $pdf_data, $regelsArr[ 'cols' ] );
	$pdf_data = array();
    if(isset($settingArr['opinfo'])) $pdf_text .="\n".stripslashes(htmlspecialchars($settingArr['opinfo']));
	$pdf_text .= CH_CR.CH_CR.$regelsArr['filename_pdf'].CH_CR ;
    $pdf_text .= CH_CR . "Aantal records : " . $regelsArr ['n'].CH_CR ;
    $pdf_text .= CH_CR . "Document created on : " . str_replace("_", " ",$run ). CH_CR;
    if ( $debug ) $pdf_text .= CH_CR. "Version : " . $regelsArr ['module'].$regelsArr ['versie'] . CH_CR;
    if (strlen($regelsArr[ 'search' ]) >1) $pdf_text .= CH_CR."Selection : " . $regelsArr[ 'search' ]; 
    if ($regelsArr[ 'volgorde' ] != 'name' ) $pdf_text .=CH_CR.$MOD_GSMOFF['tbl_volgorde'] . $regelsArr[ 'volgorde' ];
    $pdf->ChapterBody( $pdf_text );
    $pdf->Output($place['pdf'].$regelsArr['filename_pdf'], 'F');
    $msg[ 'inf' ] .= ' report created</br>';
  }
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` " . $regelsArr[ 'search_mysql' ] . " ORDER BY `".$regelsArr['volgorde']."` LIMIT " . $regelsArr[ 'seq' ] . ", " . $regelsArr[ 'qty' ];
} 
if (!isset ($query))  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` " . $regelsArr[ 'search_mysql' ] . " ORDER BY `".$regelsArr['volgorde']."` LIMIT " . $regelsArr[ 'seq' ] . ", " . $regelsArr[ 'qty' ];
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ($debug) $msg[ 'bug' ] .= __LINE__ ."mode: ". $regelsArr[ 'mode']. ' query: ' . $query . '</br>';
/*
 * display preparation
 */
switch ( $regelsArr[ 'mode'] ) {
  case 1: //alter
    $tint=false;
    foreach ( $fieldArr as $key => $value) { 
      if ($tint) {$hulp = $MOD_GSMOFF['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
      $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ],$hulp,    
        $key,
        $value,
        '','','','',''
      );
    }
    $tint=false;
    if ($tint) {$hulp = $MOD_GSMOFF['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $hulp, 
        '<input maxlength="20" size="20" type="text" name="veld" value="" placeholder="veld" />', 
        '<SELECT name="veld_type" >'.Gsm_option( $MOD_GSMOFF['autocomplete'], '' ).'</SELECT>',
        '','','','',''
    );
    break;
  case 7: // Update
    $results = $database->query( $query );
    $row = $results->fetchRow();
    $regelsArr[ 'update' ] = $row['updated'];
  case 8: // invoer
    $regelsArr[ 'head' ]  .= sprintf( $LINETEMP[ 7 ], '', ucfirst($MOD_GSMOFF['tbl_label']), '', ucfirst($MOD_GSMOFF['tbl_value']),'','','','','');
    foreach ($fieldArr as $key=>$value) {
      $h1=(isset($row[$key])) ? $row[$key] : "";
      $h2 = array_search($key, $regelsArr ['veldname']);
      if (isset($regelsArr ['veldhead'][$h2])) {$h3=(strlen($regelsArr ['veldhead'][$h2])>1) ? $regelsArr ['veldhead'][$h2] : $key ;}
      switch ( $MOD_GSMOFF['file_type'][$value] ) {
        case 1: //Text veld varchar(255)
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 255, 55, $h1, ucfirst($MOD_GSMOFF['autocomplete'][1])), "" );  
          break;
        case 2: //Veld met int(11)
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 12, 15, $h1, ucfirst($MOD_GSMOFF['autocomplete'][2])), "");  
          break;
        case 3: //Veld met amount)
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 12, 15, $h1, ucfirst($MOD_GSMOFF['autocomplete'][3])), "");  
          break;
        case 4: //Veld met E-mail address varchar(63)
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 63, 55, $h1, ucfirst($MOD_GSMOFF['autocomplete'][4])), "" );  
          break;
        case 5: //Veld met een URL varchar(127)
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 127, 55, $h1, ucfirst($MOD_GSMOFF['autocomplete'][5])), "");  
          break;
        case 6: //Veld met int(11)
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 12, 15, $h1, $MOD_GSMOFF['autocomplete'][6]), "");  
          break;
        case 7: //Veld met Ja/Nee flag int(7)
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 12, 15, $h1, ucfirst($MOD_GSMOFF['autocomplete'][7])), "");  
          break;
        default: // new list
          $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 5 ], ucfirst($h3)), 
            sprintf($LINETEMP[ 8 ], $key, 12, 55, $h1, "" ), "");  
          break;
      }
    }
    break;
  default: // default list 
    $results = $database->query( $query );
    if ( $results && $results->numRows() > 0 ) {
      if (!isset($regelsArr ['veldname'])) { $regelsArr ['veldname'] = $regelsArr ['leeg'];$i=1; foreach ($fieldArr as $key => $value ){ $regelsArr ['veldname'][$i]=$key; $i++;} }
      if (!isset($regelsArr ['veldhead'])) $regelsArr ['veldhead'] = $regelsArr ['veldname'];
      if ($regelsArr[ 'mode'] == 6) {
        $regelsArr[ 'head' ]  .= sprintf( $LINETEMP[ 7 ],ucfirst($MOD_GSMOFF['tbl_label']), ucfirst($MOD_GSMOFF['tbl_value']),'','','','','');
      } else {
        $regelsArr[ 'head' ]  .= sprintf( $LINETEMP[ 7 ],
        (isset($regelsArr[ 'veldhead' ][1])) ? ucfirst($regelsArr[ 'veldhead' ][1]) : '', 
        (isset($regelsArr[ 'veldhead' ][2])) ? ucfirst($regelsArr[ 'veldhead' ][2]) : '', 
        (isset($regelsArr[ 'veldhead' ][3])) ? ucfirst($regelsArr[ 'veldhead' ][3]) : '',
        (isset($regelsArr[ 'veldhead' ][4])) ? ucfirst($regelsArr[ 'veldhead' ][4]) : '',
        (isset($regelsArr[ 'veldhead' ][5])) ? ucfirst($regelsArr[ 'veldhead' ][5]) : ''); 
      }
      $tint=true;
      while ( $row = $results->fetchRow() ) {
        if ($regelsArr[ 'mode']== 6) {
          if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
          foreach ($regelsArr ['veldname'] as $key => $value) {
            if ($key >0) {
              if ($tint) {$hulp = $MOD_GSMOFF['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
              $h1=(isset($row[$value])) ? $row[$value] : "";
              if (isset($regelsArr ['veldhead'][$key])) {$h3=(strlen($regelsArr ['veldhead'][$key])>1) ? $regelsArr ['veldhead'][$key] : $value ;}
              $regelsArr[ 'descr' ] .= sprintf($LINETEMP[ 2 ],$hulp, 
                ucfirst($h3),
                $h1,
                '','','','','');
            }
          }
          if (CH_LOC != "front" ) {
            $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 2 ], "", "Id", $row['id'], '','','','',''); 
            $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 2 ], "", "Zoek", $row['zoek'], '','','','',''); 
            $regelsArr[ 'descr' ] .=sprintf($LINETEMP[ 2 ], "", "Updated", $row['updated'],'','','','',''); 
          }          
        } else {
          if ($tint) {$hulp = $MOD_GSMOFF['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 3 ],$hulp, $row['id'], 
            (isset($regelsArr[ 'veldname' ][1]) && isset($row[$regelsArr[ 'veldname' ][1]])) ? $row[$regelsArr[ 'veldname' ][1]] : '',
            (isset($regelsArr[ 'veldname' ][2]) && isset($row[$regelsArr[ 'veldname' ][2]])) ? $row[$regelsArr[ 'veldname' ][2]] : '',
            (isset($regelsArr[ 'veldname' ][3]) && isset($row[$regelsArr[ 'veldname' ][3]])) ? $row[$regelsArr[ 'veldname' ][3]] : '',
            (isset($regelsArr[ 'veldname' ][4]) && isset($row[$regelsArr[ 'veldname' ][4]])) ? $row[$regelsArr[ 'veldname' ][4]] : '',
            (isset($regelsArr[ 'veldname' ][5]) && isset($row[$regelsArr[ 'veldname' ][5]])) ? $row[$regelsArr[ 'veldname' ][5]] : ''
          );
        } 
      }
    } else {
      $regelsArr[ 'descr' ] .= $MOD_GSMOFF[ 'nodata' ];
    }
    break;
}
/*
 * Selection
 */
switch ( $regelsArr[ 'mode'] ) {
  case 6: // detail
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];  
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));}
    $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 2 ], "", 
      ($regelsArr ['edit_ok']) ? $ICONTEMP[ 1 ] : "", 
      $ICONTEMP[ 2 ], "" ,"" , 
      ($regelsArr ['add_ok']) ?$ICONTEMP[ 6 ] : "" );
    break;  
  case 7: // Update
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];  
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));}
    $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 2 ], "", 
      ($regelsArr ['edit_ok']) ?$ICONTEMP[ 4 ] : "", 
      $ICONTEMP[ 2 ],"","", 
      ($regelsArr ['add_ok']) ?$ICONTEMP[ 5 ] : "");
    break;
  case 8: // Nieuw
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];  
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));}
    $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 2 ], "", 
      ($regelsArr ['edit_ok']) ? $ICONTEMP[ 4 ] : "", 
      $ICONTEMP[ 2 ] ,"","", "");
    break;  
  default: // new list
    $msg[ 'bug' ] .= __LINE__ . '</br>';
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];
    if (isset($settingArr['opinfo'])) {$regelsArr[ 'select' ] .= sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo']))); }
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 9 ], "", "", Gsm_next ($regelsArr[ 'search' ], $regelsArr[ 'n' ] ,$regelsArr[ 'seq' ], $regelsArr[ 'qty' ] ), "", "");
    $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 2 ], "", 
      ($regelsArr ['add_ok'] && $regelsArr[ 'mode']!= 1) ? $ICONTEMP[ 3 ] : "", 
	  ($regelsArr[ 'mode'] == 1) ? $ICONTEMP[ 10 ] : "",
	  ($regelsArr ['pdf_ok'] && $regelsArr[ 'mode']!= 1) ? $ICONTEMP[ 11 ]: "",
      (isset($regelsArr['filename_pdf'])) ? sprintf($ICONTEMP[18], "", $regelsArr['filename_pdf']) : "",
	  "");
    break;
}  
/*
 * display
 */
switch ( $regelsArr ['mode'] ) {
  case 9: // display
  default: // default
    $_SESSION[ 'page_h' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
    $parseViewArray = array(
      'header' => strtoupper ($regelsArr ['project']),
      'page_id' => $page_id,	
      'section_id' => $section_id,
      'kopregels' => $regelsArr[ 'head' ],
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ),
      'selection' => $regelsArr[ 'select' ],
      'return' => CH_RETURN,
      'parameter' => $regelsArr[ 'search' ],
      'sel' => $regelsArr[ 'search' ],   
      'module' => $regelsArr ['module'],
      'memory' => $regelsArr[ 'memory' ]."|",   
      'toegift' => $regelsArr[ 'toegift' ],        
      'recid' => $regelsArr[ 'recid' ],
      'rapportage' => $regelsArr[ 'rapport' ],
      'hash' => $_SESSION[ 'page_h' ]);
    $prout .= Gsm_prout ($TEMPLATE[ 2 ], $parseViewArray);
    break;
}
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?>