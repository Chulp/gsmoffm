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
if (defined('WB_PATH')) {
  include(WB_PATH . '/framework/class.secure.php');
} //defined( 'WB_PATH' )
else {
  $oneback = "../";
  $root = $oneback;
  $level = 1;
  while (($level < 10) && (!file_exists($root . '/framework/class.secure.php'))) {
    $root .= $oneback;
    $level += 1;
  } //( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) )
  if (file_exists($root . '/framework/class.secure.php')) {
    include($root . '/framework/class.secure.php');
  } //file_exists( $root . '/framework/class.secure.php' )
  else {
    trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
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
  'module' => 'afgesloten',
// voor versie display
  'modulen' => 'tafgesloten',
  'versie' => ' v20160116 ',
// table
  'app' => 'Jaar',
  'table' => CH_DBBASE . "_booking",
  'table_copy' => CH_DBBASE . "_booking",
  'table_rek' => CH_DBBASE . "_rekening",
  'owner' => (isset($settingArr['logo'])) ? $settingArr['logo'] : '',
  'company' => $settingArr['company'], 
  'file' => "booking",
  'file0' => 'media/booking',  // the directory for the documents
  'file1' => 'current',  // subdirectory with the administered documents
  'dir_to' => $place['pdf'],
  'dir_to_orig' =>'',
  'file_pre' => ( isset( $settingArr[ 'prefix' ] ) ) ? $settingArr[ 'prefix' ] : 'XX',
// for display  
// search
  'search' => '',
// display
  'descr' => '',
  'head' => '',
  'select' => '',
  'update' => '',
  'recid' => '',
// application  
  'document' => 'Jaar',
  'show_type' => array(),
  'vanaf' => "1970-01-01",
  'totenmet' => date("Y-m-d"),
  'details' => false,
  'budget_exist' => false,
  'result' => true
);
$regelsArr['project'] = $regelsArr['app'] . ' - Overzicht';
if (isset($_GET['date'])) $regelsArr['project'] .= '  '.$_GET['date'];
/*
 * Initial file data
 */
/*
 * Layout template
 *
 * in language module
 * $template[0-3] in language module
 * $icontemp [0-19]
 * $linetemp [0-19]
 */
// extend text strings
$MOD_GSMOFF['SUR_VENW'] = 'V en W';
$MOD_GSMOFF['SUR_BAL'] = 'Balans';
$MOD_GSMOFF['DETAILS'] = 'Rekeningen';
$MOD_GSMOFF['SUR_BEDRAG'] = 'Bedrag'; 
$MOD_GSMOFF['SUR_OMS'] = 'Omschrijving';
$MOD_GSMOFF['SUR_PER'] = 'Periode : ';
$MOD_GSMOFF['SUR_REK'] = 'rek';
$MOD_GSMOFF['SUR_TOT'] = 'Totaal';
$MOD_GSMOFF['SUR_RES'] = 'Resultaat';

// icontemp 1-19 is defined in language module
// linetemp 1-19 is defined in language module
// template 0 is in scheduler module
// template 1 is in language module
// extend standard function
$LINETEMP[ 20 ] = '<colgroup><col width="6%"><col width="8%"><col width="50%"><col width="12%"><col width="12%"><col width="12%"></colgroup>';
// extend standard function
$ICONTEMP[ 31 ] = '<a href="' . CH_RETURN . '&command=balans&date=%1$s&archive=%2$s"><img src="' . $place['imgm'] . 'clock_16.png">Balans </a>'.CH_CR;
$ICONTEMP[ 32 ] = '<a href="' . CH_RETURN . '&command=resultaat&date=%1$s&archive=%2$s"><img src="' . $place['imgm'] . 'clock_red_16.png">Resultaat </a>'.CH_CR;
$ICONTEMP[ 33 ] = '<a href="' . CH_RETURN . '&command=details&date=%1$s&archive=%2$s"><img src="' . $place['imgm'] . 'sections_16.png">Details</a>'.CH_CR;
$ICONTEMP[ 34 ] = '<a href="' . CH_RETURN . '&command=archive&date=%1$s&archive=%2$s"><img src="' . $place['imgm'] . 'clock_del_16.png">Archive</a>'.CH_CR;
/*
 * various functions
 */
function func_table_preload ( $table, $func=1, $first='') {  // Check table precense or preload parts of it
/*
 * preloading certain data into a table
 *
 * table is the relevant database tabel
 * func is the relevant function
 * first is a  n initla value of the table
 * func 1 create a table with years present in the file 
 * func 2 create a list of jear files
 * func 3 create a list of rekening nummers
 * func 4 crate a list or rekening types
 */
  global $database;
  global $MOD_GSMOFF; 
  $oke = true;
  $returnvalue = '';
  switch ( $func ) {
    case 1: 
	  $yearArr = array( );
	  $check_query  = "SELECT * FROM `" . $table . "`";
      $results = $database->query( $check_query ); 
      while ( $result = $results->fetchRow() ) { 
		$hlp=substr($result['booking_date'],0,4);
		if (!isset( $yearArr [$hlp]))  $yearArr [$hlp]=$result['booking_date'].'|'.$result['id'];
      }
      $returnvalue=$yearArr;
	  break;
    case 2: 
	  $fileArr = array( );
      $message = __LINE__ ." func ".  $table . " missing</br>";
	  $check_query   = "SHOW TABLES LIKE '" . str_replace ("_go_" , "_%_" , $table) . "'";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 ) die( $message ); 
      while ( $result = $check_results->fetchRow() ) {
	    foreach($result as $key=>$value) $fileArr[  ] = $value;
	  }
      $returnvalue=$fileArr;
	  break;
    case 3: 
	  $rekArr = array( );
	  (is_array($first)) ?$rekArr = $first : $rekArr = array( );
      $check_query   = "SELECT * FROM `" . $table . "` ORDER BY `rekeningnummer`";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error2' ] . $table . " br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
      while ( $result = $check_results->fetchRow() ) $rekArr[ $result[ 'id' ] ] = $result[ 'rekeningnummer' ] . " - " . $result[ 'name' ];
      $returnvalue=$rekArr;
	  break;
    case 4: 	  
	  (is_array($first)) ?$typeArr = $first : $typeArr = array( );
      $check_query = "SELECT * FROM `" . $table . "` ORDER BY `rekeningnummer`";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error2' ] . $table . " br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message );
      while ( $result = $check_results->fetchRow() )$typeArr[$result['id']] = $result['rekening_type'];
      $returnvalue=$typeArr;
	  break;	
    default:
      break;
  }
  return $returnvalue;
}
function func_rekening ( $Arr_in ) {  // processing
/*
 * creating balans/ resultaat relkening or een detail overzicht
 *
 * $Arr_in['mode'] 1= balans
 * $Arr_in['mode'] 2= resultaat
 * $Arr_in['mode'] 3= details
 * $Arr_in['vanaf']  vanaf
 * $Arr_in['totenmet'] tot en met
 */
  require_once( $place_incl . 'pdf.inc' );   
  global $database;
  global $MOD_GSMOFF; 
  global $LINETEMP; 
  global $ICONTEMP;   
  global $place;  
  global $msg;    
  $returnvalue="";
  $pdf_text = '';  
  $pdf_data = array();  
  $subtotals = array();
  $Arr_local = array(
    'n' =>0,
    'm' =>0,
	'text_budget' => '',
    'rekeningnummer' => '',
    'cum_rek' => 0,
    'cum_group' => 0,
    'cum_srt' => 0,
    'cum_activa' => 0,
    'cum_resultaat' => 0,
	'cum_rek_previous' => 0,
    'rekening_type' => '');   
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
  $returnvalue .= $LINETEMP[ 20 ];  // kolombreedte instelling
  // end  initiate pdf file
  //***************************
  // Heading
  switch ($Arr_in['mode']) { 
    case 1: // balans
      $returnvalue .= sprintf($LINETEMP[7], '', 3, 3,
		'<strong>' . $Arr_in['document'] . '</strong>'.'&nbsp;&nbsp;&nbsp;'.'<strong>' . $Arr_in['company'] . '</strong>', 
		'Datum : '.$Arr_in['totenmet']);
      $returnvalue .= sprintf($LINETEMP[11], $MOD_GSMOFF['line_color'][4], '', $MOD_GSMOFF['SUR_REK'], $MOD_GSMOFF['SUR_OMS'], '','', $MOD_GSMOFF['SUR_BEDRAG']);
      $title .= sprintf("   %s  %s, datum: %s", $Arr_in['document'], $Arr_in['company'], $Arr_in['totenmet']);
      $pdf->ChapterTitle(1, $title);
      $pdf->SetFont('Arial', '', 8);
      $pdf_text .= CH_CR . $title . CH_CR;
      $pdf_header = array( $MOD_GSMOFF['SUR_REK'], 
	    $MOD_GSMOFF['SUR_OMS'], 
		'',
        '',
        '',
        $MOD_GSMOFF['SUR_BEDRAG']
      );
	  break;
	case 2: // resultaat
	  $Arr_local ['text_budget'] = ($Arr_in['budget_exist']) ? "Budget" : "";
      $returnvalue .= sprintf($LINETEMP[7], '', 3, 3,
	 	'<strong>' . $Arr_in['document'] . '</strong>'.'&nbsp;&nbsp;&nbsp;'.'<strong>' . $Arr_in['company'] . '</strong>',
		$MOD_GSMOFF['SUR_PER'].  $Arr_in['vanaf'].' - '. $Arr_in['totenmet']);
      $returnvalue .= sprintf($LINETEMP[11], $MOD_GSMOFF['line_color'][4], '' ,  $MOD_GSMOFF['SUR_REK'], $MOD_GSMOFF['SUR_OMS'], $Arr_local ['text_budget'] ,'', $MOD_GSMOFF['SUR_BEDRAG']);
      $title = sprintf("%s  %s, periode: %s  -  %s", $Arr_in['document'], $Arr_in['company'], $Arr_in['vanaf'], $Arr_in['totenmet']);
      $pdf->ChapterTitle(1, $title);
      $pdf->SetFont('Arial', '', 8);
      $pdf_text .= CH_CR . $title . CH_CR;
      $pdf_header = array(
        $MOD_GSMOFF['SUR_REK'],
        $MOD_GSMOFF['SUR_OMS'],
        $Arr_local ['text_budget'],
        '',
        '',
        $MOD_GSMOFF['SUR_BEDRAG']
      );
	  break;
	case 3: // details
      $returnvalue .= sprintf($LINETEMP[7], '', 3, 3,
	 	'<strong>' . $Arr_in['document'] . '</strong>'.'&nbsp;&nbsp;&nbsp;'.'<strong>' . $Arr_in['company'] . '</strong>',
		$MOD_GSMOFF['SUR_PER'].  $Arr_in['vanaf'].' - '. $Arr_in['totenmet']);
      $returnvalue .= sprintf($LINETEMP[11], $MOD_GSMOFF['line_color'][4], ' ', $MOD_GSMOFF['SUR_REK'], $MOD_GSMOFF['SUR_OMS'], '','', $MOD_GSMOFF['SUR_BEDRAG']);
      $title = sprintf("%s  %s, periode: %s  -  %s", $Arr_in['document'], $Arr_in['company'], $Arr_in['vanaf'], $Arr_in['totenmet']);
      $pdf->ChapterTitle(1, $title);
      $pdf->SetFont('Arial', '', 8);
      $pdf_text .= CH_CR . $title . CH_CR;
      $pdf_header = array(
        $MOD_GSMOFF['SUR_REK'],
        $MOD_GSMOFF['SUR_OMS'],
        '',
        '',
        '',
        $MOD_GSMOFF['SUR_BEDRAG']
      );	
	  break;
    default: // default list
      break;
  }
  // end of heading
  //***************************
  // preload rekening details
  $rektypeArr = func_table_preload ( $Arr_in['table_rek'], 4 ); // preload data
  //$row = $results->fetchRow() 
  // start processing
  // loop through rekening nummers
  // find associated booking records
  // level1 on balans en resultaten rekening
  // level2 op 1e positie van rekening nummer
  // level3 op rekening nummer
  
 //***************************
  $Arr_level_L1 = array ('vorig'=>0, 'count'=>0); // for levelbreak on type
  $Arr_level_L2 = array ('vorig'=>0, 'count'=>0); // for levelbreak on rekening groep
  $Arr_level_L3 = array ('vorig'=>0, 'count'=>0); // for levelbreak on rekening nummer
  $query = "SELECT * FROM `" . $Arr_in['table_rek'] . "` ORDER BY `rekening_type`, `rekeningnummer`";
  $r_results = $database->query($query);
  if ($r_results && $r_results->numRows() > 0) { // there are records
    while ($r_row = $r_results->fetchRow()) {  // loop through the records
	  // alleen de volgende rekening nummers doen mee	
      if (in_array($r_row['rekening_type'], $Arr_in['show_type'] )) {	// rekening nummer is to be processed the type matches
        if ($Arr_level_L2['vorig']!= substr($r_row['rekeningnummer'], 0, 1) || $Arr_level_L1['vorig']!= $r_row['rekening_type'] ) { // type of group chnage
        // afsluiten L2 vorige group afsluiten
          if ($Arr_level_L2['count'] !=0) { // alleen als er records geweest zijn in deze groep
            $Arr_local ['cum_srt'] += $Arr_local['cum_group'];  // bij type totaal voegen
			if ($Arr_in['result']) {	 // output onder bepaalde conditie 
              $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '','' , '<b>' . $MOD_GSMOFF['grootboek'][$Arr_level_L2['vorig']] . '</b>', '', '', Gsm_opmaak($Arr_local['cum_group'], 1));
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['grootboek'][$Arr_level_L2['vorig']], '', '', '', Gsm_opmaak($Arr_local['cum_group'], 2));
              $pdf_data[] = explode(';', trim($pdf_line));
			  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', '', '', '', '');
              $pdf_data[] = explode(';', trim($pdf_line));
			}
            // resetten cumul values            
            $Arr_local ['cum_group'] = 0; // groep totaal op nul
            $Arr_local ['cum_rek'] = 0;  // een onderliggende rekening nummer totaal op nul zetten
			if (!$Arr_in['details']) {	// blanco regel onder bepaalde conditie
              // blanco regel volgt
              $returnvalue .=  sprintf($LINETEMP[11], '', '&nbsp;', '', '', '', '', '');
              $Arr_local ['m'] = 0;// reset kleur indicatie
			}
          }
          if ($Arr_level_L1['vorig']!= $r_row['rekening_type'] ) {  // moet er rekening type totaal worden gemaakt
          // afsluiten L1
            $subtotals[ $Arr_level_L1['vorig']] = $Arr_local ['cum_srt'];  // totaal van het type vasthouden
            if ($Arr_level_L1['count'] !=0) { // acties nodig // zijn er records geweest
			  if ($Arr_in['result']) {  // output onder bepaalde conditie 
                if ( $Arr_level_L1['vorig'] == 2 ) {  // type 2 processing
			      $Arr_local['cum_resultaat'] = $subtotals[ 1 ] - $subtotals[ 2 ];  // bereken resultaat
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '', '<b>' . $MOD_GSMOFF['SUR_RES'] . '</b>', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_RES'], '', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 1));
                  $pdf_data[] = explode(';', trim($pdf_line));
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][$Arr_local['rekening_type']] . '</strong>', '', '', Gsm_opmaak($Arr_local['cum_activa'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$Arr_local['rekening_type']], '', '', '', Gsm_opmaak($subtotals[ 1 ], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                } elseif ($Arr_level_L1['vorig'] == 5 ) { // type 5 processing
                  $Arr_local['cum_resultaat'] = $subtotals [5] - $subtotals [4];  // bereken resultaat
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][$Arr_local['rekening_type']] . '</strong>', '', '', Gsm_opmaak($Arr_local['cum_srt'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$Arr_local['rekening_type']], '', '', '', Gsm_opmaak($subtotals[ 4 ], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '', '<b>' . $MOD_GSMOFF['SUR_RES'] . '</b>', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_RES'], '', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                } else {   // niet 2 of 5  2 = einde balans 5 = einde resultaat overzicht
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][ $Arr_level_L1['vorig']] . '</strong>', '', '', 
				  Gsm_opmaak($subtotals[ $Arr_level_L1['vorig']], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$Arr_level_L1['vorig']], '', '', '', Gsm_opmaak($subtotals[ $Arr_level_L1['vorig']], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                  // blanco regel volgt
                  $returnvalue .=  sprintf($LINETEMP[11], '', '&nbsp;', '', '', '', '', '');
				  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', '', '', '', '');
                  $pdf_data[] = explode(';', trim($pdf_line));
				  $Arr_local ['m'] = 0; // reset kleur indicatie
                }	
              }
			  $Arr_local ['cum_srt']=0;   // soort totaal op nul voor de volgende
			}
            //openen L1
            $Arr_level_L1['vorig']= $r_row['rekening_type']; 			 // rekening type
            $Arr_level_L1['count']=0; // totaal voor level break uitzetten
          }
          //openen L2
          $Arr_level_L2['vorig']= substr($r_row['rekeningnummer'], 0, 1);   // rekening groep
          $Arr_level_L2['count']=0; // totaal voor level break uitzetten
        }   
		// process details L3
		// selection depending on balans rekening
		$Arr_level_L3['vorig'] = (isset ($row['debet_id']) ) ? $row['debet_id']: ' '; // waarde key volgende cycle	
        $Arr_level_L3['count']=0; // totaal voor level break uitzetten	
		// pick up openings balans van rekening rek
		$Arr_local['cum_rek'] =0;
		if (abs($r_row['balans']) >0.001 &&  $Arr_in['totenmet'] >= $r_row['balans_date'] ) {
          $Arr_local['cum_rek'] = $r_row['balans']; 
		  $Arr_level_L3['count']++;  // om een openings balans te krijgen als er geen verdere boekingen zijn
		//dit kan er later uit 
		  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][0], '', '', $r_row['balans_date'] . ' >> Openings Balans', '--', Gsm_opmaak($Arr_local['cum_rek'], 1),"");
		// tot hier			
		}
		$msql_search = ($r_row['rekening_type']==1 || $r_row['rekening_type']==2) ?  "`booking_date` <= '" . $Arr_in['totenmet'] . "' " :  $msql_search = "`booking_date` >= '" . $Arr_in['vanaf'] . "' AND `booking_date` <= '" . $Arr_in['totenmet'] . "' "; 
        $msql_search .= " AND ( `debet_id`= '" . $r_row['id'] . "' OR `tegen1_id`= '" . $r_row['id'] . "' OR  `tegen2_id`= '" . $r_row['id'] . "' )";
        $query = "SELECT * FROM `" . $Arr_in['table'] . "` WHERE " . $msql_search . " ORDER BY `booking_date`, `project`"; 
	    $results = $database->query($query);
        if ($results && $results->numRows() > 0) {
          while ($row = $results->fetchRow()) {	 
            $hulp = sprintf ('%s - %s%s',
			$row['booking_date'],
              (strlen($row['name']) > 2) ? $row['name'] . ' - ' : '',
              (strlen($row['project']) > 2) ? $row['project'] : '');
               $col = ($Arr_local['m'] % 2 == 0) ? $MOD_GSMOFF['line_color'][2] : "";	
            // data for display detail
            if ($row['debet_id'] == $r_row['id']) {
              $hulp_amt = $row['debet_amount'];
			  $Arr_local['cum_rek'] += $hulp_amt;
			  // display detailed lines
			  if ($row['booking_date']< $Arr_in['vanaf'] ) $Arr_local['cum_rek_previous'] = $Arr_local['cum_rek']; // voor een eventuele 0penings balans
              if ($hulp_amt <> 0 && ($Arr_in['details'] || $Arr_in['search'] == $r_row['rekeningnummer'])) {          
			    if ($row['booking_date'] >= $Arr_in['vanaf'] && $row['booking_date']<= $Arr_in['totenmet']) {
                  if (abs($Arr_local['cum_rek_previous']) >0.001 ) { 
                    // er is nog een openings balans af te drukken 
                    $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][0], '', '', ' - Openings Balans', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 1),"");
                    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', ' - Openings Balans', '', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 2),"");
                    $pdf_data[] = explode(';', trim($pdf_line)); 
                    $Arr_local ['m']= 0;  // reset kleur                 
                    $col = ($Arr_local['m'] % 2 == 0) ? $MOD_GSMOFF['line_color'][2] : "";   
                    $Arr_local['cum_rek_previous']=0; //vergeet openingsbalans		  
                  }
                  // detailed line
                  $returnvalue .= sprintf($LINETEMP[12], $col, '<small>'.$row['id'].'</small>', '', $hulp, Gsm_opmaak($hulp_amt, 1), Gsm_opmaak($Arr_local['cum_rek'], 1), '');
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $hulp, '', Gsm_opmaak($hulp_amt, 2), Gsm_opmaak($Arr_local['cum_rek'], 2), '');
                  $pdf_data[] = explode(';', trim($pdf_line));
			    } // ($row['booking_date']>= $Arr_in['vanaf'] & $row['booking_date']<= $Arr_in['totenmet'])
              } //($hulp_amt <> 0 && ($Arr_in['details'] || $Arr_in['search'] == $r_row['rekeningnummer']))
			} 
			if ($row['tegen1_id'] == $r_row['id']) { 
              $hulp_amt= $row['tegen1_amount'] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$row['debet_id']]] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$row['tegen1_id']]] * -1;
              $Arr_local['cum_rek'] += $hulp_amt;	
			  // display detailed lines
			  if ($row['booking_date']< $Arr_in['vanaf'] ) $Arr_local['cum_rek_previous'] = $Arr_local['cum_rek']; // voor een eventuele 0penings balans
              if ($hulp_amt <> 0 && ($Arr_in['details'] || $Arr_in['search'] == $r_row['rekeningnummer'])) {          
			    if ($row['booking_date']>= $Arr_in['vanaf'] && $row['booking_date']<= $Arr_in['totenmet']) {
                  if (abs($Arr_local['cum_rek_previous']) >0.001 ) { 
                    // er is nog een openings balans af te drukken 
                    $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][0], '', '', $Arr_in['vanaf'] . ' - Openings Balans', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 1),"");
                    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $Arr_in['vanaf'] . ' - Openings Balans', '', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 2),"");
                    $pdf_data[] = explode(';', trim($pdf_line)); 
                    $Arr_local ['m']= 0;  // reset kleur                 
                    $col = ($Arr_local['m'] % 2 == 0) ? $MOD_GSMOFF['line_color'][2] : "";   
                    $Arr_local['cum_rek_previous']=0; //vergeet openingsbalans		  
                  }
                  // detailed line
                  $returnvalue .= sprintf($LINETEMP[12], $col, '<small>'.$row['id'].'</small>', '', $hulp, Gsm_opmaak($hulp_amt, 1), Gsm_opmaak($Arr_local['cum_rek'], 1), '');
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $hulp, '', Gsm_opmaak($hulp_amt, 2), Gsm_opmaak($Arr_local['cum_rek'], 2), '');
                  $pdf_data[] = explode(';', trim($pdf_line));
			    } // ($row['booking_date']>= $Arr_in['vanaf'] & $row['booking_date']<= $Arr_in['totenmet'])
              } //($hulp_amt <> 0 && ($Arr_in['details'] || $Arr_in['search'] == $r_row['rekeningnummer']))			  
			} 
			if ($row['tegen2_id'] == $r_row['id']) {
              $hulp_amt = $row['tegen2_amount'] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$row['debet_id']]] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$row['tegen2_id']]] * -1;
              $Arr_local['cum_rek'] += $hulp_amt;
			  // display detailed lines
			  if ($row['booking_date']< $Arr_in['vanaf'] ) $Arr_local['cum_rek_previous'] = $Arr_local['cum_rek']; // voor een eventuele 0penings balans
              if ($hulp_amt <> 0 && ($Arr_in['details'] || $Arr_in['search'] == $r_row['rekeningnummer'])) {          
			    if ($row['booking_date']>= $Arr_in['vanaf'] && $row['booking_date']<= $Arr_in['totenmet']) {
                  if (abs($Arr_local['cum_rek_previous']) >0.001 ) { 
                    // er is nog een openings balans af te drukken 
                    $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][0], '', '', $Arr_in['vanaf'] . ' - Openings Balans', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 1),"");
                    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', $r_row['balans_date'] . ' - Openings Balans', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 2),"");
                    $pdf_data[] = explode(';', trim($pdf_line)); 
                    $Arr_local ['m']= 0;  // reset kleur                 
                    $col = ($Arr_local['m'] % 2 == 0) ? $MOD_GSMOFF['line_color'][2] : "";   
                    $Arr_local['cum_rek_previous']=0; //vergeet openingsbalans		  
                  }
                  // detailed line
                  $returnvalue .= sprintf($LINETEMP[12], $col, '<small>'.$row['id'].'</small>', '', $hulp, Gsm_opmaak($hulp_amt, 1), Gsm_opmaak($Arr_local['cum_rek'], 1), '');
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $hulp, '', Gsm_opmaak($hulp_amt, 2), Gsm_opmaak($Arr_local['cum_rek'], 2), '');
                  $pdf_data[] = explode(';', trim($pdf_line));
			    } // ($row['booking_date']>= $Arr_in['vanaf'] & $row['booking_date']<= $Arr_in['totenmet'])
              } //($hulp_amt <> 0 && ($Arr_in['details'] || $Arr_in['search'] == $r_row['rekeningnummer']))
            }           
			// display line
		    // set details for totals and levelbreak
            $Arr_local ['m']++;
			if ($row['booking_date']>= $Arr_in['vanaf'] && $row['booking_date']<= $Arr_in['totenmet']) $Arr_level_L3['count']++;	
		  } // $row = $results->fetchRow()
        } //($results && $results->numRows() > 0)  
        // totaal
        if ($Arr_level_L3['count'] != 0 || abs($Arr_local['cum_rek_previous']) >0.001 ) { 
		  if (abs($Arr_local['cum_rek_previous']) >0.001 ) { 
            // er is nog een openings balans af te drukken 
            $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][0], '', '', 'Openings Balans', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 1),"");
            $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', 'Openings Balans', '', '--', Gsm_opmaak($Arr_local['cum_rek_previous'], 2),"");
            $pdf_data[] = explode(';', trim($pdf_line)); 
            $Arr_local ['m']= 0;  // reset kleur                 
            $col = ($Arr_local['m'] % 2 == 0) ? $MOD_GSMOFF['line_color'][2] : "";   
            $Arr_local['cum_rek_previous']=0; //vergeet openingsbalans				  
          }
		  if ($Arr_in['budget_exist'] && $r_row['budget_a'] > 0) {
            $help_b1 = Gsm_opmaak($r_row['budget_a'], 3);
            $help_b2 = ($Arr_local['cum_rek']==0) ? "" : Gsm_opmaak((100 * $Arr_local['cum_rek'] / $r_row['budget_a']), 9); 
          } else {
            $help_b1 = "";
            $help_b2 = "";
          }
          $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][2], '', '<b>' . $r_row['rekeningnummer'] . '</b>', '<b>' . $r_row['name'] . '</b>',$help_b1, $help_b2, Gsm_opmaak($Arr_local['cum_rek'], 1));
          $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", $r_row['rekeningnummer'], $r_row['name'], $help_b1, $help_b2, '', Gsm_opmaak($Arr_local['cum_rek'], 2));
          $pdf_data[] = explode(';', trim($pdf_line));
          // blanco regel volgt
          if ($Arr_in['details']) {			
            $returnvalue .=  sprintf($LINETEMP[11], '', '&nbsp;', '', '', '', '', '');
		    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', '', '', '', '');
            $pdf_data[] = explode(';', trim($pdf_line));
            $Arr_local ['m'] = 0;
          }
		  $Arr_local['cum_group'] += $Arr_local['cum_rek'];
		  $Arr_local['cum_rek'] =0;
		  $Arr_level_L3['count'] = 0;
		  $Arr_level_L2['count'] ++;
		  $Arr_level_L1['count'] ++;
        } //($Arr_level_L3['count'] != 0)     
      } // (in_array($r_row['rekening_type'], $Arr_in['show_type'] ))  
    } //($r_row = $r_results->fetchRow())
    // afsluiten L2
    if ($Arr_level_L2['count'] !=0) { // acties nodig
		    $Arr_local['cum_group'] += $Arr_local['cum_rek'];
            $Arr_local ['cum_srt'] += $Arr_local['cum_group'];
			if ($Arr_in['result']) {	  
              $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '..', '', '<b>' . $MOD_GSMOFF['grootboek'][$Arr_level_L2['vorig']] . '</b>', '', '', Gsm_opmaak($Arr_local['cum_group'], 1));
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '..', $MOD_GSMOFF['grootboek'][$Arr_level_L2['vorig']], '', '', '', Gsm_opmaak($Arr_local['cum_group'], 2));
              $pdf_data[] = explode(';', trim($pdf_line));
			  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', '', '', '', '');
              $pdf_data[] = explode(';', trim($pdf_line));
			}
            // resetten cumul values            
            $Arr_local ['cum_group'] = 0;
            $Arr_local ['cum_rek'] = 0;
			if (!$Arr_in['details']) {	
              // blanco regel volgt
              $returnvalue .=  sprintf($LINETEMP[11], '', '&nbsp;', '', '', '', '', '');
              $Arr_local ['m'] = 0;
			}
    }
    // afsluiten L1
    if ($Arr_level_L1['count'] !=0) { // acties nodig
			  if ($Arr_in['result']) {
                if ( $Arr_level_L1['vorig'] == 2 ) {
				  $subtotals[ 2 ]= $Arr_local ['cum_srt'];
			      $Arr_local['cum_resultaat'] = $subtotals[ 1 ] - $subtotals[ 2 ];
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '', '<b>' . $MOD_GSMOFF['SUR_RES'] . '</b>', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_RES'], '', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][1] . '</strong>', '', '', Gsm_opmaak($subtotals[ 1 ], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][1], '', '', '', Gsm_opmaak($subtotals[ 1 ], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                } elseif ($Arr_level_L1['vorig'] == 5 ) {
				  $subtotals[ 5 ]= $Arr_local ['cum_srt'];
                  $Arr_local['cum_resultaat'] = $subtotals [5] - $subtotals [4];
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][5] . '</strong>', '', '', Gsm_opmaak($Arr_local['cum_srt'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][4], '', '', '', Gsm_opmaak($subtotals[ 4 ], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                  $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '', '<b>' . $MOD_GSMOFF['SUR_RES'] . '</b>', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_RES'], '', '', '', Gsm_opmaak($Arr_local['cum_resultaat'], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                } else {
                    $returnvalue .= sprintf($LINETEMP[12], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][$Arr_local['rekening_type']] . '</strong>', '1', '', Gsm_opmaak($subtotals[ $Arr_level_L1['vorig']], 1));
                    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$Arr_local['rekening_type']], '', '', '', Gsm_opmaak($subtotals[ $Arr_level_L1['vorig']], 2));
                    $pdf_data[] = explode(';', trim($pdf_line));
                }	
              }
			  if (!$Arr_in['details']) {	
                // blanco regel volgt
                $returnvalue .=  sprintf($LINETEMP[11], '', '&nbsp;', '', '', '', '', '');
				$pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', '', '', '', '');
                $pdf_data[] = explode(';', trim($pdf_line));
                $Arr_local ['m'] = 0;
			  }
    }     
  } // ($r_results && $r_results->numRows() > 0)
    else {
      $returnvalue .= $MOD_GSMOFF['SUR_NDATA'];
      $pdf_text .= $MOD_GSMOFF['SUR_NDATA'] . CH_CR;
  }
  if (isset($Arr_in['filename_pdf'])) {
    /*
     * the output to the pdf
     */
    $pdf_cols = array(
      12,
      85,
      15,
      25,
      25,
      25 );
    $pdf_text .= CH_CR . $Arr_in['company'];
	$pdf_text .= CH_CR.CH_CR.$regelsArr['filename_pdf'].CH_CR ;
    $pdf_text .= "Document created on : " . str_replace("_", " ",$run ). CH_CR;
    if ( $debug ) $pdf_text .= CH_CR. "Version : " . $regelsArr ['module'].$regelsArr ['versie'] . CH_CR;
    if (strlen($regelsArr[ 'search' ]) >1) $pdf_text .= CH_CR."Selection : " . $regelsArr[ 'search' ]; 	
    $pdf->DataTable($pdf_header, $pdf_data, $pdf_cols);
    $pdf->ChapterBody($pdf_text);
    $pdf->Output($Arr_in['dir_to'] .'/'. $Arr_in['filename_pdf'], 'F');
    $msg['inf'] .= ' report created</br>';
  } //isset( $Arr_local[ 'filename_pdf' ] )   
  return $returnvalue;
} // end procedure
function func_rek_update ($Arr_in, $year) {
  /*
   * Update rekening tabel with one year balans data from bookings
   * als resultaat 0 true terug gegeven anders false 
   */
  global $database;
  global $MOD_GSMOFF; 
  $oke = false;
  $subtotals = array();
  $Arr_local = array(
    'cum_rek' => 0,
	'cum_group1' => 0,
    'cum_group2' => 0); 
  $rektypeArr = func_table_preload ( $Arr_in['table_rek'], 4 ); // preload data 
  $query = "SELECT * FROM `" . $Arr_in ['table_rek']  . "` WHERE  `rekening_type` = '1' OR `rekening_type` = '2' ORDER BY `rekening_type`, `rekeningnummer`";
  $r_results = $database->query($query);
  if ($r_results && $r_results->numRows() > 0) { 
    while ($r_row = $r_results->fetchRow()) { 
	  $Arr_local['cum_rek']= $r_row['balans'];
      $query = "SELECT * FROM `" . $Arr_in['table'] . "` WHERE `booking_date` <= '" . $year."-12-31' AND ( `debet_id`= '" . $r_row['id'] . "' OR `tegen1_id`= '" . $r_row['id'] . "' OR  `tegen2_id`= '" . $r_row['id'] . "' ) ORDER BY `booking_date`, `project`"; 
	  $b_results = $database->query($query);
      if ($b_results && $b_results->numRows() > 0) { 
	    while ($b_row = $b_results->fetchRow()) {
		  if ($b_row['debet_id'] == $r_row['id'])  $Arr_local['cum_rek'] += $b_row['debet_amount'];
		  if ($b_row['tegen1_id'] == $r_row['id']) $Arr_local['cum_rek'] += $b_row['tegen1_amount'] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$b_row['debet_id']]] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$b_row['tegen1_id']]] * -1;
 		  if ($b_row['tegen2_id'] == $r_row['id']) $Arr_local['cum_rek'] += $b_row['tegen2_amount'] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$b_row['debet_id']]] * $MOD_GSMOFF['rek_type_sign'][$rektypeArr[$b_row['tegen2_id']]] * -1;    
        }		  
	  }
	  // opslaan
	  $year_next=$year+1;
	  $hulpArr = array(
	    'balans_date'=> $year_next.'-01-01',
		'balans'=> $Arr_local['cum_rek']);
      $query   = "UPDATE `" . $Arr_in[ 'table_rek' ] . "` SET " . Gsm_parse( 2, $hulpArr ) . "  WHERE `id`='" . $r_row[ 'id' ] . "'";
	  $u_results = $database->query( $query );
      if ($r_row ['rekening_type'] == 1) $Arr_local['cum_group1'] += $Arr_local['cum_rek'];
	  if ($r_row ['rekening_type'] == 2) $Arr_local['cum_group2'] += $Arr_local['cum_rek'];
      $Arr_local['cum_rek']= 0;
	}
	// test resultaat
	if (abs($Arr_local['cum_group1'] - $Arr_local['cum_group2'] )<0.001) $oke=true;
  } // ($r_results && $r_results->numRows() > 0)
    else {
      $returnvalue .= $MOD_GSMOFF['SUR_NDATA'];
  }
  return $oke;
} // end procedure
function func_rek_moveatt ($Arr_in, $year) {
  /*
   * Move attachments
   */
  global $database;
  $oke = true;
  $returnvalue="";
  $subtotals = array();
  $idArr=array();
  $query = "SELECT * FROM `" . $Arr_in ['table']  . "`";
  $r_results = $database->query($query);
  if ($r_results && $r_results->numRows() > 0) { 
    while ($r_row = $r_results->fetchRow()) { 
	  $idArr[$r_row['id']] = $r_row['booking_date'];
	}
  	$fileArr=Gsm_get_files( $Arr_in['dir_to_orig'], $prefix="_".$Arr_in['file_pre']."_");
    foreach($fileArr as $key=>$value) {
	  $help = explode('_',$value);
	  if($help[1] == $Arr_in['file_pre']) {
	    if (isset( $idArr[$help[2]])) {
	      $help[0] = $idArr[$help[2]];
		  $newname = implode ("_", $help);
		  rename ($Arr_in['dir_to_orig'] .'/'. $value ,$Arr_in['dir_to'] .'/'. $newname);
	    }
	  }
    }	
  } // ($r_results && $r_results->numRows() > 0)
  return $oke;
} // end procedure
function func_restore_zoek ($Arr_in) {
  /*
   * Move restore the zoek parameter
   */
  global $database;
  $oke = true;
  $returnvalue="";
  $idArr=array();
  $idArr[0] = '';
  $query = "SELECT * FROM `" . $Arr_in ['table_rek']  . "`";
  $r_results = $database->query($query);
  if ($r_results && $r_results->numRows() > 0) { 
    while ($r_row = $r_results->fetchRow()) { 
	  $idArr[$r_row['id']] = $r_row['rekeningnummer'];
	}
  }
  $query = "SELECT * FROM `" . $Arr_in ['table']  . "`";
  $r_results = $database->query($query);
  if ($r_results && $r_results->numRows() > 0) { 
    while ($r_row = $r_results->fetchRow()) { 
	  $hulpArr = array();
	  $zoek=sprintf('%s|%s|%s|%s|%s|%s', 
	    $r_row['project'], 
	    $idArr[$r_row['debet_id']], 
	    $idArr[$r_row['tegen1_id']], 
	    $idArr[$r_row['tegen2_id']], 
	    $r_row['debet_amount'], 
	    $r_row['id'] );
	  if ($zoek != $r_row['zoek']) $hulpArr['zoek']=$zoek;
	  if ($idArr[$r_row['debet_id']] != $r_row['debet_rekening']) $hulpArr['debet_rekening']=$idArr[$r_row['debet_id']];
	  if ($idArr[$r_row['tegen1_id']] != $r_row['tegen1_rekening']) $hulpArr['tegen1_rekening']=$idArr[$r_row['tegen1_id']];
	  if ($idArr[$r_row['tegen2_id']] != $r_row['tegen2_rekening']) $hulpArr['tegen2_rekening']=$idArr[$r_row['tegen2_id']];
	  if (abs($r_row['tegen2_amount'])<0.001) $hulpArr['tegen2_rekening']=$idArr[0];
      if (count($hulpArr) >0) {
        $query   = "UPDATE `" . $Arr_in[ 'table' ] . "` SET " . Gsm_parse( 2, $hulpArr ) . "  WHERE `id`='" . $r_row[ 'id' ] . "'";
	    $u_results = $database->query( $query );	  
	  }
	}
  } // ($r_results && $r_results->numRows() > 0)
  return $oke;
} // end procedure
/*
 * debug
 */
if ($debug) {
  Gsm_debug($settingArr, __LINE__);
  Gsm_debug($regelsArr, __LINE__);
  Gsm_debug($_POST, __LINE__);
  Gsm_debug($_GET, __LINE__);
  Gsm_debug($place, __LINE__);
} //$debug
/*
 * some job to do
 */
if (isset($_POST['command'])) {
  switch ($_POST['command']) {
     default:
      $msg['inf'] .= __LINE__ . " post: " . $_POST['command'] . '<br/>';
      $regelsArr['mode'] = 9;
      break;
  } //$_POST[ 'command' ]
} //isset( $_POST[ 'command' ] )
elseif (isset($_GET['command'])) {
	$regelsArr['dir_to'] = WB_PATH. "/" . $regelsArr['file0'] ;
	if (!file_exists($regelsArr['dir_to']. "/" )) { mkdir($regelsArr['dir_to'], 0777); } // create if not exist 
	$regelsArr['dir_to'] = WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] ;  
	if (!file_exists($regelsArr['dir_to']. "/" )) { mkdir($regelsArr['dir_to'], 0777); } // create if not exist 
    if (isset($_GET['archive']) && $_GET['archive']=='yes') {
	  $regelsArr['dir_to_orig']= $regelsArr['dir_to'];
	  $regelsArr['dir_to'] = WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file_pre'].$_GET['date'] ;  
	  if (!file_exists($regelsArr['dir_to']. "/" )) { mkdir($regelsArr['dir_to'], 0777); } // create if not exist 
    }	  
  switch ($_GET['command']) {
    case "balans": // begin
      $regelsArr['document'] = $MOD_GSMOFF['SUR_BAL'];
      $regelsArr['totenmet'] = $_GET['date'] . "-01-01";
      $regelsArr['show_type'] = array( '1','2');
      $regelsArr['filename_pdf'] = sprintf('%s %s %s.pdf', str_replace('.', ' ', $settingArr['company']), $MOD_GSMOFF['SUR_BAL'], $regelsArr['totenmet'] );
      if (isset($_GET['archive']) && $_GET['archive']=='yes') {
 	  	$regelsArr['table'] = str_replace ('_go_', '_'.$_GET['date'].'_', $regelsArr['table']);
	    $regelsArr['table_rek']= str_replace ('_go_', '_'.$_GET['date'].'_', $regelsArr['table_rek']);
	  }
	  $regelsArr['mode'] = 1;
      if ($debug) Gsm_debug($regelsArr, __LINE__);
	  $regelsArr['descr'] .= func_rekening ( $regelsArr );
      if ($debug) Gsm_debug($regelsArr, __LINE__);
       // balans eind
      $regelsArr['document'] = $MOD_GSMOFF['SUR_BAL'];
      $regelsArr['totenmet'] = $_GET['date'] . "-12-31";
      $regelsArr['show_type'] = array( '1','2');
      $regelsArr['filename_pdf'] = sprintf('%s %s %s.pdf', str_replace('.', ' ', $settingArr['company']), $MOD_GSMOFF['SUR_BAL'], $regelsArr['totenmet'] );
      if ($debug) Gsm_debug($regelsArr, __LINE__);
	  $regelsArr['descr'] .= func_rekening ( $regelsArr );
      if ($debug) Gsm_debug($regelsArr, __LINE__);
      $regelsArr['mode'] = 8;	
      if ($debug) Gsm_debug($regelsArr, __LINE__);
      break;
	case "resultaat":
      $regelsArr['document'] = $MOD_GSMOFF['SUR_VENW'];
      $regelsArr['vanaf'] = $_GET['date'] . "-01-01";
      $regelsArr['totenmet'] = $_GET['date'] . "-12-31";
      $regelsArr['show_type'] = array( '4','5');
      $regelsArr['filename_pdf'] = sprintf('%s %s %s.pdf', str_replace('.', ' ', $settingArr['company']), $MOD_GSMOFF['SUR_VENW'], $regelsArr['totenmet'] );
      if (isset($_GET['archive']) && $_GET['archive']=='yes') {
	  	$regelsArr['table'] = str_replace ('_go_', '_'.$_GET['date'].'_', $regelsArr['table']);
	    $regelsArr['table_rek']= str_replace ('_go_', '_'.$_GET['date'].'_', $regelsArr['table_rek']);
	  }
	  $regelsArr['mode'] = 2;
      if ($debug) Gsm_debug($regelsArr, __LINE__);
	  $regelsArr['descr'] .= func_rekening ( $regelsArr );
      if ($debug) Gsm_debug($regelsArr, __LINE__);
      $regelsArr['mode'] = 8;	
	  break;
	case "details":
      $regelsArr['document'] = $MOD_GSMOFF['DETAILS'];
      $regelsArr['vanaf'] = $_GET['date'] . "-01-01";
      $regelsArr['totenmet'] = $_GET['date'] . "-12-31";
	  if ( isset( $_GET[ 'selection' ] ) && strlen( $_GET[ 'selection' ] ) >= 2 ) { $regelsArr[ 'search' ] = trim( $_GET[ 'selection' ] );}
      $regelsArr['show_type'] = array( '1', '2', '4','5');
      $regelsArr['details'] = true;
      $regelsArr['filename_pdf'] = sprintf('%s %s %s.pdf', str_replace('.', ' ', $settingArr['company']), $MOD_GSMOFF['DETAILS'], $regelsArr['totenmet'] );
      if (isset($_GET['archive']) && $_GET['archive']=='yes') {
	  	$regelsArr['table'] = str_replace ('_go_', '_'.$_GET['date'].'_', $regelsArr['table']);
	    $regelsArr['table_rek']= str_replace ('_go_', '_'.$_GET['date'].'_', $regelsArr['table_rek']);
		func_rek_moveatt ($regelsArr, $_GET['date']); // move data
	  } else {
	    func_restore_zoek ($regelsArr);
	  }
	  $regelsArr['result']= false;
	  $regelsArr['mode'] = 3;
      if ($debug) Gsm_debug($regelsArr, __LINE__);
	  $regelsArr['descr'] .= func_rekening ( $regelsArr );
      if ($debug) Gsm_debug($regelsArr, __LINE__);
      $regelsArr['mode'] = 8;	
      break;	  
	case "archive":
      $regelsArr['mode'] = 9;
	  $table=func_table_preload ( $regelsArr[ 'table' ], 1); 
      ksort ($table);
	  $archive_year=substr(array_shift($table),0,4);
	  $jobs = array(); 
	  // Remove old backup of rekening table if existing 
	  // Make backup of rekening table
	  // copy rekening tabel
	  // Modify originnal rekening tabel
	  $query = "SHOW TABLES LIKE '" . $regelsArr[ 'table_rek' ]."_bck'";
	  $results = $database->query( $query );
	  if ($results && $results->numRows() != 0 ) $jobs[] = "DROP TABLE ".$regelsArr[ 'table_rek' ]."_bck"; // remove if existing
	  $jobs[] = "RENAME TABLE  `" . $regelsArr ['table_rek'] . "` TO  `" . $regelsArr ['table_rek'] . "_bck`";
	  $jobs[] = "CREATE TABLE IF NOT EXISTS `" . $regelsArr ['table_rek'] . "` LIKE `" . $regelsArr ['table_rek'] . "_bck`"; 
	  $jobs[] = "CREATE TABLE IF NOT EXISTS `" . str_replace ('_go_', '_'.$archive_year.'_', $regelsArr ['table_rek']) . "` LIKE `" . $regelsArr ['table_rek'] . "_bck`"; 
	  $jobs[] = "INSERT `" . $regelsArr ['table_rek'] . "` SELECT * FROM `" . $regelsArr ['table_rek'] . "_bck`";	  
	  $jobs[] = "INSERT `" . str_replace ('_go_', '_'.$archive_year.'_', $regelsArr ['table_rek']) . "` SELECT * FROM `" . $regelsArr ['table_rek'] . "_bck`";
      $errors = array();
      foreach($jobs as $query) {
	  $database->query( $query );
	  if ( $database->is_error() ) $errors[] = $database->get_error();}
      if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
      if ($debug) Gsm_debug($regelsArr, __LINE__);
      $jobs = array();
	  $test_ok_result=func_rek_update ($regelsArr, $archive_year);
	  if (!$test_ok_result) { 
	    $jobs = array();
		$jobs[] = "DROP TABLE ".$regelsArr[ 'table_rek' ]; // remove if existing
        $jobs[] = "DROP TABLE ".str_replace ('_go_', '_'.$archive_year.'_', $regelsArr ['table_rek']); // remove if existing		
		$jobs[] = "RENAME TABLE  `" . $regelsArr ['table_rek'] . "_bck` TO  `" . $regelsArr ['table_rek'] . "`";
		foreach($jobs as $query) {
	    $database->query( $query );
	    if ( $database->is_error() ) $errors[] = $database->get_error();}
        if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
	  } else  {
	  $query = "SHOW TABLES LIKE '" . $regelsArr[ 'table' ]."_bck'";
		$results = $database->query( $query );
		if ( $results && $results->numRows() != 0 ) {
// remove if existing	
		  $query ="DROP TABLE ".$regelsArr[ 'table' ]."_bck";
			$results = $database->query($query);
			}
	  $jobs[] = "RENAME TABLE  `" . $regelsArr ['table'] . "` TO  `" . $regelsArr ['table'] . "_bck`";
	  $jobs[] = "CREATE TABLE IF NOT EXISTS `" . $regelsArr ['table'] . "` LIKE `" . $regelsArr ['table'] . "_bck`"; 
	  $jobs[] = "CREATE TABLE IF NOT EXISTS `" . str_replace ('_go_', '_'.$archive_year.'_', $regelsArr ['table']) . "` LIKE `" . $regelsArr ['table'] . "_bck`"; 
	  $jobs[] = "INSERT `" . $regelsArr ['table'] . "` SELECT * FROM `" . $regelsArr ['table'] . "_bck` WHERE `booking_date` > '" . $archive_year. "-12-31'";	  
	  $jobs[] = "INSERT `" . str_replace ('_go_', '_'.$archive_year.'_', $regelsArr ['table']) . "` SELECT * FROM `" . $regelsArr ['table'] . "_bck` WHERE `booking_date` <= '" . $archive_year. "-12-31'";	  
      $errors = array();
      foreach($jobs as $query) {$database->query( $query ); if ( $database->is_error() ) $errors[] = $database->get_error();}
      if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
	  }
      if ($debug) Gsm_debug($regelsArr, __LINE__);
      break;
    default:
      $msg['inf'] .= __LINE__ . " post: " . $_GET['command'] . '<br/>';
      $regelsArr['mode'] = 9;
      break;
  } //$_GET[ 'command' ]
} //isset( $_GET[ 'command' ] )
  elseif ( isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 2 ) {
  $regelsArr[ 'search' ] = trim( $_POST[ 'selection' ] );
} 
if ( $debug ) $msg[ 'bug' ] .= sprintf('%s mode: %s %s', __LINE__ , $regelsArr[ 'mode' ], (isset($query)) ? $query. '</br>' : 'no query</br>');
 
switch ($regelsArr['mode']) { 
/*
 * for the preparation
 */ 
  case 8:
  default: // default list
    $treshold =  date("Y");
	$regelsArr[ 'select' ] .= sprintf( $LINETEMP[ 2 ], $MOD_GSMOFF['line_color'][4], "jaartal", "type", "action", "", "");
	// bepalen welke jaren er zijn  in de current file
	$table=func_table_preload ( $regelsArr[ 'table_copy' ], 1); 
    krsort ($table);
	foreach ($table as $key=> $value) {
	  if ($treshold>= $key) 
	    $regelsArr[ 'select' ] .= sprintf( $LINETEMP[ 2 ], 
		  "", $key, 
		  "in current file", 
		  sprintf( $ICONTEMP[ 31 ], "$key", "no")."&nbsp;".sprintf( $ICONTEMP[ 32 ], "$key", "no")."&nbsp;".sprintf( $ICONTEMP[ 33 ], "$key", "no")."&nbsp;".sprintf( $ICONTEMP[ 34 ], "$key", "no"), 
		  "", 
		  "");
	}
	// bepalen welke jaren er zijn  in closed data
    $table=func_table_preload ( $regelsArr[ 'table_copy' ], 2);
	foreach ($table as $key=> $value) {
      $hulp=explode("_", $value); 
	  if ($hulp[2]!="go") $hulpArr[$hulp[2]]= $value;
	}	
    if(isset($hulpArr) && is_array($hulpArr)) {
	  krsort ($hulpArr);
	  foreach ($hulpArr as $key=> $value) {
        $regelsArr[ 'select' ] .= sprintf( $LINETEMP[ 2 ], 
	      "", 
		  $key, 
		  "in closed archive", 
		  sprintf( $ICONTEMP[ 31 ], "$key", "yes")."&nbsp;".sprintf( $ICONTEMP[ 32 ], "$key", "yes")."&nbsp;".sprintf( $ICONTEMP[ 33 ], "$key", "yes") , 
		  "", 
		  "");
      }
	}
    break;
} 
switch ($regelsArr['mode']) { 
/*
 * for the details
 */ 
  default: // default list
    break;
} 
switch ($regelsArr['mode']) { 
/*
 * for the footer
 */ 
  default: // default list
    break;
} 
/*
 * the output to the screen
 */
$_SESSION['page_h'] = sha1(MICROTIME() . $_SERVER['HTTP_USER_AGENT']); 
switch ($regelsArr['mode']) {
  case 8:
  default: // default list
    $parseViewArray = array(
      'header' => $regelsArr['project'],
      'message' => message($msg, $debug),
      'kopregels' => $regelsArr['head'],
      'description' => $regelsArr['descr'],
      'return' => CH_RETURN,
      'hash' => $_SESSION['page_h'],
      'page_id' => $page_id,
      'section_id' => $section_id,
      'update' => $regelsArr[ 'update' ],
      'recid' => $regelsArr['recid'],
      'selection' => $regelsArr['select'],
// for selection icons
      'sel' => $regelsArr['search'],
      'mod' => $regelsArr['module'],	  
    );
    $prout .= $TEMPLATE[1];
    foreach ($parseViewArray as $key => $value) {
      $prout = str_replace("{" . $key . "}", $value, $prout);
    } //$parseViewArray as $key => $value
    break;
} //$regelsArr[ 'mode' ]
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?> 