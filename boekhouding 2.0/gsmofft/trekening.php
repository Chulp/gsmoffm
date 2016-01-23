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
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'rekening',
// voor versie display
  'modulen' => 'trekening',  
  'versie' => ' v20160114',
// general parameters
  'app' => 'rekening',  
  'app2' => 'booking',  
  'app3' => 'bkproject',  
  'table' =>  CH_DBBASE."_rekening",
  'table2' =>  CH_DBBASE."_booking",
  'table3' =>  CH_DBBASE."_bkproject", 
  'print_regels' => 8,
  'volgorde'=> 'rekeningnummer',
  'owner' => (isset($settingArr[ 'logo'])) ? $settingArr[ 'logo'] : '',
// for display  
  'seq' => (isset($_POST[ 'next' ])) ?  $regelsArr[ 'seq']= $_POST[ 'next' ]: 0,
  'n' => 0,
  'qty' => (isset($settingArr['oplines'])) ? $settingArr['oplines'] : 30,  
  'project' => '',
// search  
  'search' => '',
  'search_mysql' => '',
//display 
  'descr' => '',
  'head' => '',
  'select' => '', 
  'update' => '',
  'hash' => '', 
  'recid' => '',
// pdf  
  'record_update' => false,
  'opmaak' => '9',
  'today_pf' => date( "_Ymd_His_" ),
  'cols'=> array(55, 35, 35, 20, 20, 20),
  'leeg' => array( 0=>"",1=>"",2=>"",3=>"",4=>"",5=>"",6=>"",7=>"",8=>""),
);
$regelsArr['project'] = $regelsArr[ 'app' ] . ' - Overzicht';
$regelsArr['kop'] = $regelsArr['leeg'];
$regelsArr['waarde'] = $regelsArr['leeg'];
/*
 * Initial file data
 */
$jobs = array();
$jobs[] = "CREATE TABLE IF NOT EXISTS `" . $regelsArr ['table'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `zoek` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `rekeningnummer` int(11) NOT NULL,
  `rekening_type` int(11) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `balans` decimal(9,2) NOT NULL,
  `balans_date` date NOT NULL,
  `budget_a` int(11) NOT NULL,
  `budget_b` int(11) NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['app']."','opzoek', 'name|rekeningnummer')";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['app']."','opinfo', 'Rekening_type: 1=Activa, 2=Passiva, 4=Uitgaven, 5=Inkomsten, 7=Tussenrekening')";
$jobs[] = "CREATE TABLE IF NOT EXISTS `" . $regelsArr ['table2'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `zoek` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `booking_date` date NOT NULL,
  `debet_id` int(11) NOT NULL,
  `debet_rekening` int(11) NOT NULL,
  `debet_amount` decimal(9,2) NOT NULL,
  `tegen1_id` int(11) NOT NULL,
  `tegen1_rekening` int(11) NOT NULL,
  `tegen1_amount` decimal(9,2) NOT NULL,
  `tegen2_id` int(11) NOT NULL,
  `tegen2_rekening` int(11) NOT NULL,
  `tegen2_amount` decimal(9,2) NOT NULL,
  `project` varchar(255) NOT NULL,
  `boekstuk` varchar(255) NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['app2']."','opzoek', 'name|debet_rekening|tegen1_rekening|tegen2_rekening|debet_amount')";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['app2']."','opinfo', '----')";
$jobs[] = "CREATE TABLE IF NOT EXISTS `" . $regelsArr ['table3'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `zoek` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `project` varchar(255) NOT NULL,
  `active` int(7) NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['app3']."','opzoek', 'name')";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['app3']."','opinfo', 'Active: 0=niet actief, 1= actief')";
/*
 * Lay-out strings
 */
 // overrule standaard  strings
$MOD_GSMOFF[ 'tbl_icon' ][13]="Budget";
$MOD_GSMOFF[ 'tbl_icon' ][16]="Budget OK";
// overrule standard function
$ICONTEMP[ 13 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][13].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 16 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][16].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 21 ] = '<input maxlength="10" size="10" type="text" name="a|%1$s|%2$s" value="%2$s" width="18" />';
// extend standard function
$LINETEMP[ 72 ] = '<td colspan="2" class="setting_name" align="left" >%1$s&nbsp;:</td>';
$LINETEMP[ 73 ] = '<td colspan="2" >%1$s</td>';
$LINETEMP[ 74 ] = '<td colspan="2" class="setting_value" ><input maxlength="%2$s" type="text" name="%1$s" value="%3$s" /></td>';
/*
 * pick up settings, create certain tables / data if not existing 
 */
$query = "SHOW TABLES LIKE '".$regelsArr[ 'table' ]."'";
$results = $database->query( $query );
if ( !$results || $results->numRows() == 0 ) {
  $msg[ 'inf' ] .= 'table creation attempt<br/>';
  $errors = array();
  foreach($jobs as $query) {$database->query( $query ); if ( $database->is_error() ) $errors[] = $database->get_error();}
  if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
} 
$query ="DESCRIBE ". $regelsArr[ 'table' ];
$message = $MOD_GSMOFF['error0'] . $query . "</br>";
$results = $database->query( $query );
if ( !$results || $results->numRows() == 0 ) die( $message );
$fieldArr = array ();
while ( $row = $results->fetchRow() ) { $fieldArr[$row['Field']]= $row['Type']; }
$query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='0' AND `table`='".$regelsArr ['app']."' ORDER BY `id`";
$message = $MOD_GSMOFF['error0'] . $query . "</br>";
$results = $database->query( $query ); 
if ( !$results || $results->numRows() == 0 ) die( $message );
while ( $row = $results->fetchRow() ) { $settingArr[$row['name']]= $row['value']; }
// all fields collected now remove the standard fields except name
unset ($fieldArr['id']);
unset ($fieldArr['zoek']);
unset ($fieldArr['updated']);
unset( $query );
if ( $debug ) {
  Gsm_debug( $regelsArr, __LINE__ );
  Gsm_debug( $settingArr, __LINE__);
  Gsm_debug( $_POST, __LINE__ );
  Gsm_debug( $_GET, __LINE__ );
  Gsm_debug( $place, __LINE__ );
  Gsm_debug($fieldArr, __LINE__); 
} 
/*
 * some job to do ?
 */
if ( isset( $_POST[ 'command' ] ) ) {
  switch ( $_POST[ 'command' ] ) {
    case $MOD_GSMOFF['tbl_icon'][1]:  // change
      $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
      $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
      $regelsArr[ 'mode'] = 7;
      break;
    case $MOD_GSMOFF['tbl_icon'][3]: //add
      $regelsArr[ 'qty' ]= 3;
      $regelsArr[ 'mode'] = 8;
      break;
    case $MOD_GSMOFF['tbl_icon'][4]: //save
	  if ((isset($_POST[ 'recid' ]) && $_POST[ 'recid' ]>=1) || (isset($_POST[ 'name' ]) && strlen($_POST['name'])>2 )) {  // update ?
		 if (isset($_POST[ 'recid' ]) && $_POST[ 'recid' ]>=1) {
			$regelsArr[ 'recid' ] = $_POST[ 'recid' ];
			$regelsArr ['record_update'] = true;
		}
        $hulpArr = array(); // array field 
        foreach ($fieldArr as $key => $value ){
          if (isset($_POST[ $key ])) { 
            $hulpArr[$key]=stripslashes(htmlspecialchars($_POST[ $key ])); 
          } else { 
            $hulpArr[$key]="";
          }
        }
        $hulpArr['zoek']=""; // voeg zoek toe
        $hulp=explode ("|", $settingArr['opzoek']);
        foreach ($hulp as $key=> $value) { 
          if (isset($hulpArr[$value])) $hulpArr['zoek'].=$hulpArr[$value]."|";
        }
        if ($regelsArr ['record_update']) {
          $query = "UPDATE `".$regelsArr ['table']."` SET ".Gsm_parse (2,$hulpArr)."  WHERE  `id`= '".$regelsArr ['recid']."'";
          if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
          $results = $database->query( $query );
          $msg[ 'inf' ] .= 'Updated'.'<br/>';
        } else {
          $query = "INSERT INTO `".$regelsArr[ 'table' ]."` ". Gsm_parse (1,$hulpArr);
          if ($debug) $msg[ 'bug' ] .= __LINE__." ".$query.'<br/>';
          $results = $database->query( $query );
          $msg[ 'inf' ] .= $MOD_GSMOFF['added'].'<br/>'; 
        }
        unset( $query );
        $regelsArr[ 'mode'] = 9;
	  } else {
        foreach ($_POST as $key => $value) {	 // check for budget update
          $budget= explode ("|", $key);
		  if ($budget[0]=='a') { // budget data
		    if ($budget[2]!=$value) { //budget different
              $hulpArr = array('budget_b' => Gsm_eval ($value, 8, 10000000,0 )); 
              $query = "UPDATE `".$regelsArr ['table']."` SET ".Gsm_parse (2,$hulpArr)."  WHERE  `id`= '".$budget[1]."'";
              if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
              $results = $database->query( $query ); 
		    }
		  }
        }
        $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `rekening_type`='4' OR `rekening_type`='5' ORDER BY `".$regelsArr['volgorde']."`";
	    $regelsArr ['mode'] = 5;
	  }
      break;
    case $MOD_GSMOFF['tbl_icon'][5]: //save as new 
      if ( !isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] ) ) {
        $msg[ 'err' ] .= $MOD_GSMOFF[ 'error4' ] . '</br>';
        unset ($_POST);
        break;}
      $hulpinArr= array(); // array field 
      $hulpinArr2= array(); // array field names 
      $hulpoutArr=array(); // array with data 
      $hulp=$settingArr['opzoek']; // hoe ziet zoek eruit
      foreach ($fieldArr as $key => $value ){
        $hulpinArr[]=$key;
        $hulpinArr2[]=$key;
        if (isset($_POST[ $key ])) { 
          $hulp1=stripslashes(htmlspecialchars($_POST[ $key ])); 
          $hulpoutArr[]=$hulp1; 
        } else { 
          $hulpoutArr[]="";
        }
      }
      $hulpinArr2[]="zoek"; // voeg zoek toe
      $hulpoutArr[]=strtoupper(str_replace($hulpinArr, $hulpoutArr, $hulp)); 
	  if (strlen($_POST['name'])>2) {
        $query = "INSERT INTO `". $regelsArr[ 'table' ] . "` ";
        $query .= "(`".implode ("`, `",$hulpinArr2)."`) VALUES ('".implode("', '", $hulpoutArr)."')";
        $results = $database->query( $query );
        $msg[ 'inf' ] .= $MOD_GSMOFF[ 'save' ]. ' ........: ' . $_POST['name']. ' ' . $MOD_GSMOFF[ 'confirm' ] . ' </br>';
	  }
      unset( $query );
      $regelsArr[ 'mode'] = 9;
      break;
    case $MOD_GSMOFF['tbl_icon'][6]: // verwijderen
      $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
      if ( !isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] ) ) {
        $msg[ 'err' ] .= $MOD_GSMOFF[ 'ERR0R4' ] . '</br>';
        unset ($_POST);
        break;}
      unset( $_SESSION[ 'page_h' ] );
      $query = "DELETE FROM `". $regelsArr[ 'table' ] . "` WHERE `id`='". $regelsArr[ 'recid' ] . "'";
      $results = $database->query( $query );
      $msg[ 'inf' ] .= $MOD_GSMOFF[ 'del' ]. ' : ' . $regelsArr[ 'recid' ] . ' ' . $MOD_GSMOFF[ 'confirm' ] . ' </br>';
      unset( $query );
      $regelsArr[ 'mode'] = 9;
      break; 
    case $MOD_GSMOFF['tbl_icon'][11]: //print model
	  $regelsArr['filename_pdf'] = strtolower( $regelsArr[ 'project' ] . $regelsArr[ 'today_pf' ] ) . '.pdf';
      $regelsArr ['mode'] = 9;
      break;
    case $MOD_GSMOFF['tbl_icon'][13]: //budget
      $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `rekening_type`='4' OR `rekening_type`='5' ORDER BY `".$regelsArr['volgorde']."`";
	  $regelsArr ['mode'] = 5;
      break; 
	case $MOD_GSMOFF['tbl_icon'][16]: //budget ok
      $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `rekening_type`='4' OR `rekening_type`='5' ORDER BY `".$regelsArr['volgorde']."`";
	  $results = $database->query( $query ); 
	  while ( $row = $results->fetchRow() ) {
	    $hulpArr = array('budget_a' => $row['budget_b']);
	    $queryc = "UPDATE `".$regelsArr ['table']."` SET ".Gsm_parse (2,$hulpArr)."  WHERE  `id`= '".$row['id']."'";
        if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
        $c_results = $database->query( $queryc ); 
	  }	
	  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `rekening_type`='4' OR `rekening_type`='5' ORDER BY `".$regelsArr['volgorde']."`";
	  $regelsArr ['mode'] = 5;
      break; 
    default:
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
      $regelsArr[ 'mode'] = 9;
      break;
  }
} else  { // so standard display
/*
 * standard display job with or without search
 */
  if ( isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 1 ) {
    $regelsArr[ 'search' ] = trim( $_POST[ 'selection' ] );
    $help = "%" . str_replace( ' ', '%', $regelsArr[ 'search' ] ) . "%";
    $regelsArr[ 'search_mysql' ] .= "WHERE `zoek` LIKE '" . $help . "'";
  }
}

if (!isset($query)) {
  // bepaal aantal records
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` " . $regelsArr[ 'search_mysql' ]. " ORDER BY `".$regelsArr['volgorde']."`";
  $c_results = $database->query( $query );
  if ( $c_results ) { $regelsArr ['n'] = $c_results->numRows(); }
  if ( $regelsArr[ 'seq' ] >= $regelsArr ['n'] ) {$regelsArr[ 'seq' ]=0;}
  if (isset($regelsArr['filename_pdf'])) {
    require_once( $place_incl . 'pdf.inc' );   
    /*
    * initiatie pdf before starting the normal process
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
    $i=0;
    foreach ($fieldArr as $key => $value ){ $regelsArr ['kop'][$i]=$key; $i++;}
    if ($regelsArr['print_regels'] >1 ) { 
      $pdf_header = array(ucfirst($regelsArr[ 'kop'][0]), '', '', '', '','');
    } else {
      $pdf_header = array(ucfirst($regelsArr[ 'kop'][0]), ucfirst($regelsArr[ 'kop'][1]), ucfirst($regelsArr[ 'kop'][2]), ucfirst($regelsArr[ 'kop'][3]), ucfirst($regelsArr[ 'kop'][4]),ucfirst($regelsArr[ 'kop'][5]));
    }
    while ( $c_row = $c_results->fetchRow() ) {
      $i=0;
      foreach ($fieldArr as $key => $value ){ $regelsArr[ 'waarde'][$i]=$c_row[$key]; $i++;}
      if ($regelsArr['print_regels'] >1 ) { 
        $line= sprintf("%s;%s;%s;%s;%s;%s",$regelsArr[ 'waarde'][0],$regelsArr[ 'kop'][1],$regelsArr[ 'waarde'][1],'','','');
        $pdf_data[] = explode(';',trim($line));
        for ($i=2; $i<=$regelsArr['print_regels']; $i++) {
          $line= sprintf("%s;%s;%s;%s;%s;%s",'',$regelsArr[ 'kop'][$i],$regelsArr[ 'waarde'][$i],'','','');
          $pdf_data[] = explode(';',trim($line));
        }
      } else {
        $line = sprintf("%s;%s;%s;%s;%s;%s",$regelsArr[ 'waarde'][0], $regelsArr[ 'waarde'][1], $regelsArr[ 'waarde'][2], $regelsArr[ 'waarde'][3], $regelsArr[ 'waarde'][4],$regelsArr[ 'waarde'][5]);
        $pdf_data[] = explode(';',trim($line));  
      }
    }
    $pdf->DataTable ($pdf_header, $pdf_data, $regelsArr[ 'cols']);  
	$pdf_data = array();
    if(isset($settingArr['opinfo'])) $pdf_text .="\n".stripslashes(htmlspecialchars($settingArr['opinfo']));
	$pdf_text .= CH_CR.CH_CR.$regelsArr['filename_pdf'].CH_CR ;
    $pdf_text .= CH_CR . "Aantal records : " . $regelsArr ['n'].CH_CR ;
    $pdf_text .= CH_CR . "Document created on : " . str_replace("_", " ",$run ). CH_CR;
    if ( $debug ) $pdf_text .= CH_CR. "Version : " . $regelsArr ['module'].$regelsArr ['versie'] . CH_CR;
	if (strlen($regelsArr[ 'search' ]) >1) $pdf_text .= CH_CR."Selection : " . $regelsArr[ 'search' ]; 
    $pdf_text .= $regelsArr['filename_pdf'].CH_CR ;
// pdf output
    $pdf->ChapterBody($pdf_text);
    $pdf->Output($place['pdf'].$regelsArr['filename_pdf'], 'F');
    $msg[ 'inf' ] .= ' report created</br>';
  }
// read records and loop through the records
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` " . $regelsArr[ 'search_mysql' ] . " ORDER BY `".$regelsArr['volgorde']."` LIMIT " . $regelsArr[ 'seq' ] . ", " . $regelsArr[ 'qty' ];
}
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ($debug) $msg[ 'bug' ] .= 'DB : mode ' . $regelsArr[ 'mode'] . '</br>';
if ($debug) $msg[ 'bug' ] .= 'DB : query </br>' . $query . '</br>';
// at this point the database query for the relevant records prepared
/*
 * display preparation
 */
switch ( $regelsArr[ 'mode'] ) {
  case 5: // detail
    $results = $database->query( $query );
    if ( $results && $results->numRows() > 0 ) {
      $regelsArr[ 'kop']=array( 0=>"Rekening",1=>"Nummer",2=>"Budget",3=>"Nieuw Budget",4=>"type");  
      $regelsArr[ 'select' ]  .= sprintf( $LINETEMP[ 2 ],$MOD_GSMOFF['line_color'] [4], ucfirst($regelsArr[ 'kop'][0]), ucfirst($regelsArr[ 'kop'][1]), ucfirst($regelsArr[ 'kop'][2]), ucfirst($regelsArr[ 'kop'][3]), ucfirst($regelsArr[ 'kop'][4]));
      $tint=false;
      while ( $row = $results->fetchRow() ) {
        $i=0;
        foreach ($fieldArr as $key => $value ){ $regelsArr[ 'waarde'][$i]=$row[$key]; $i++;}
        if ($tint) {$hulp = $MOD_GSMOFF['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
        $regelsArr[ 'select' ] .= sprintf( $LINETEMP[ 3 ],
		  $hulp, $row['id'], 
		  $regelsArr[ 'waarde'][0],
		  $regelsArr[ 'waarde'][1],
		  $regelsArr[ 'waarde'][6],
          sprintf( $ICONTEMP[ 21 ],$row['id'],$regelsArr[ 'waarde'][7]),
		  $regelsArr[ 'waarde'][2]);
      }  
    } else {
      $regelsArr[ 'select' ] .= $MOD_GSMOFF[ 'nodata' ];
    }
    break; 
  default: // default list 
    $results = $database->query( $query );
    if ( $results && $results->numRows() > 0 ) {
      $regelsArr[ 'kop']=array( 0=>"",1=>"",2=>"",3=>"",4=>"");  $i=0;
      foreach ($fieldArr as $key => $value ){ $regelsArr[ 'kop'][$i]=$key; $i++;}
      $regelsArr[ 'head' ]  .= sprintf( $LINETEMP[ 2 ],$MOD_GSMOFF['line_color'] [4], ucfirst($regelsArr[ 'kop'][0]), ucfirst($regelsArr[ 'kop'][1]), ucfirst($regelsArr[ 'kop'][2]), ucfirst($regelsArr[ 'kop'][3]), ucfirst($regelsArr[ 'kop'][4]));
      $tint=false;
      while ( $row = $results->fetchRow() ) {
        $i=0;
        foreach ($fieldArr as $key => $value ){ $regelsArr[ 'waarde'][$i]=$row[$key]; $i++;}
        if ($tint) {$hulp = $MOD_GSMOFF['line_color'] [2]; $tint=false;} else { $hulp=""; $tint=true;}
        $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 3 ],$hulp, $row['id'], $regelsArr[ 'waarde'][0],$regelsArr[ 'waarde'][1],$regelsArr[ 'waarde'][2],$regelsArr[ 'waarde'][3],$regelsArr[ 'waarde'][4]);
      }  
    } else {
      $regelsArr[ 'descr' ] .= $MOD_GSMOFF[ 'nodata' ];
    }
    break;
}
switch ( $regelsArr[ 'mode'] ) {
  case 5: // detail
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo']))).CH_CR;
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 2 ], "", $ICONTEMP[ 4 ], $ICONTEMP[ 2 ],"","", $ICONTEMP[ 16 ]);
    break;
      break;  
  case 6: // detail
    $results = $database->query( $query );
    $row = $results->fetchRow();
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];  
    $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], "Id"), sprintf($LINETEMP[ 73 ], $row['id']), "");  
    foreach ($fieldArr as $key => $value ) { $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], $MOD_GSMOFF['line_color'] [2], sprintf($LINETEMP[ 72 ], ucfirst($key)), sprintf($LINETEMP[ 73 ], $row[$key]), "");}        
    $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], "Zoek"), sprintf($LINETEMP[ 73 ], $row['zoek']), "");      
    $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], "Updated"), sprintf($LINETEMP[ 73 ], $row['updated']), "");          
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 2 ], "", $ICONTEMP[ 1 ], $ICONTEMP[ 2 ], $ICONTEMP[ 6  ],$ICONTEMP[ 19 ] , $ICONTEMP[ 19 ]);
    break;  
  case 7: // Update
    $results = $database->query( $query );
    $row = $results->fetchRow();
    $regelsArr[ 'update' ] =$row['updated'];
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];  
    foreach ($fieldArr as $key=>$value) {  
      switch ( $MOD_GSMOFF['file_type'][$value] ) {
        case 1: //Text veld varchar(255)
          $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], ucfirst($key)), sprintf($LINETEMP[ 74 ], $key, 255, $row[$key]), "");  
          break;
        case 4: //Veld met E-mail address varchar(63)
          $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], ucfirst($key)), sprintf($LINETEMP[ 74 ], $key, 63, $row[$key]), "");  
          break;
        case 5: //Veld met een URL varchar(127)
          $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], ucfirst($key)), sprintf($LINETEMP[ 74 ], $key, 127, $row[$key]), "");  
          break;
        case 7: //Veld met Ja/Nee flag int(7)
          $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], ucfirst($key)), sprintf($LINETEMP[ 74 ], $key, 12, $row[$key]), "");  
          break;
        default: // new list
          $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], ucfirst($key)), sprintf($LINETEMP[ 74 ], $key, 12, $row[$key]), "");  
          break;
      }
    }
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 2 ], "", $ICONTEMP[ 4 ], $ICONTEMP[ 2 ],$ICONTEMP[ 5  ],$ICONTEMP[ 19 ], $ICONTEMP[ 19 ]);
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));
    break;
  case 8: // Nieuw
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];  
    foreach ($fieldArr as $key=>$value) {  $regelsArr[ 'select' ] .=sprintf($LINETEMP[ 4 ], "", sprintf($LINETEMP[ 72 ], ucfirst($key)), sprintf($LINETEMP[ 74 ], $key, 64, ""), "");}
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", stripslashes(htmlspecialchars($settingArr['opinfo'])));
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 2 ], "", $ICONTEMP[ 4 ], $ICONTEMP[ 2 ],$ICONTEMP[ 19 ],$ICONTEMP[ 19 ], $ICONTEMP[ 19 ]);
    break;  
  default: // new list
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 6 ], "", (isset($settingArr['opinfo'])) ? stripslashes(htmlspecialchars($settingArr['opinfo'])) :"");
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 5 ], "", "", Gsm_next ($regelsArr[ 'search' ], $regelsArr[ 'n' ] ,$regelsArr[ 'seq' ], $regelsArr[ 'qty' ] ), "", "");
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 2 ], 
	  "", 
	  $ICONTEMP[ 3  ],
          $ICONTEMP[ 19 ],	  
	  $ICONTEMP[ 11 ],
	  (isset($regelsArr['filename_pdf'])) ? sprintf($ICONTEMP[18], "", $regelsArr['filename_pdf']) : "",
	  $ICONTEMP[ 13 ]);
    break;
}  
/*
 * the output to the screen
 */
$regelsArr[ 'hash' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
$_SESSION[ 'page_h' ] = $regelsArr[ 'hash' ];
switch ( $regelsArr[ 'mode'] ) {
  default: //list
    $parseViewArray = array(
      'header' => strtoupper ($regelsArr ['project']),
      'page_id' => 0,
      'section_id' => $section_id,
      'kopregels' => $regelsArr[ 'head' ],
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ),
      'selection' => $regelsArr[ 'select' ],
      'return' => CH_RETURN,
      'parameter' => $regelsArr[ 'search' ],
      'hash'  => $regelsArr[ 'hash' ],
      'update' => $regelsArr[ 'update' ],
      'recid' => $regelsArr[ 'recid' ],
 //     'search'  => $regelsArr[ 'search' ],
      'mod' => $regelsArr ['module'],
      'sel' => '');
    $prout .= $TEMPLATE[ 1 ];
    foreach ( $parseViewArray as $key => $value ) { $prout = str_replace( "{" . $key . "}", $value, $prout );}
    break;  
}
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?>