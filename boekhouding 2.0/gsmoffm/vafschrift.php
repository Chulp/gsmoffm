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
/*
 * variables
 */
$regelsArr = array(
//module
  'mode' => 9,
  'module' => 'afschrift', 
// voor versie display
  'modulen' => 'vafschrift',
  'versie' => ' v20160122',
 // table
  'app' => "Rekening afschrift", 
  'table' => CH_DBBASE . "_booking",
  'table_rek' => CH_DBBASE . "_rekening",
  'owner' => (isset($settingArr['logo'])) ? $settingArr['logo'] : '',
  'company' => $settingArr['company'], 
  'file' => "booking",
// search
  'search_mysql' => '', 
//display
  'descr' => '',
  'head' => '',
  'select' => '',
  'memory' => '',
  'rek' => array(),	
  'recid' => array(),
// application    
  'rekening_size' => 4,
  'fyear' => ( isset( $settingArr[ 'fjaar' ] )) ? date( "Y", strtotime( $settingArr[ 'fjaar' ]))."-01-01" : date("Y")."-01-01",
  'tyear' => ( isset( $settingArr[ 'tjaar' ] )) ? date( "Y", strtotime( $settingArr[ 'tjaar' ]))."-12-31" : "2020-12-31",
  'cum_rek' => 0,
  'found' => false,
  'block' => '0',
  'select_rek' => ( isset( $settingArr[ 'prefrek' ] )) ? $settingArr[ 'prefrek' ] : '5600',
  'select_rek_id' => 0,
  'consolidate' => ( isset( $settingArr[ 'consolidate' ] )) ? $settingArr[ 'consolidate' ] : '',
  'bedragvan' => '0',	
  'bedragtot' => '0',	
  'vanaf' => (date("m")>1) ? date( "Y-m-d" , mktime(0, 0, 0, date("m")-1, 1, date("Y"))) : date( "Y-m-d" , mktime(0, 0, 0, date("m"), 1, date("Y"))), 
  'totenmet' => date( "Y-m-d" )
);
$regelsArr['xyear'] = date( "Y-m", strtotime( $regelsArr[ 'tyear' ]))."-1" ;
/*
 * Initial file data
 */
/*
 * Lay-out strings
 */
$MOD_GSMOFFM [ 'LAB_OMS' ] = 'Rekening afschrift van';
$MOD_GSMOFFM [ 'LAB_DATUMB' ] = 'Boek datum';
$MOD_GSMOFFM [ 'LAB_VORIG' ] = 'Vorig afschrift';
$MOD_GSMOFFM [ 'SUR_MUT' ] = 'Mutatie';
$MOD_GSMOFFM [ 'SUR_BEDRAG' ] = 'Bedrag';
$MOD_GSMOFFM [ 'LAB_DATUM' ] = 'Datum'; 
$MOD_GSMOFFM [ 'LAB_PROJECT' ] = 'Project';
$MOD_GSMOFFM [ 'LAB_REK' ] = 'rekening';
$MOD_GSMOFFM [ 'SUR_SALDO' ] = 'Saldo';
$MOD_GSMOFFM [ 'LAB_REF' ] = 'Omschrijving'; 
// icontemp 1-19 is defined in language module
// linetemp 1-19 is defined in language module
// template 0 is in scheduler module
// template 1 is in language module
  
$LINETEMP[ 1 ] = '<colgroup><col width="20%"><col width="35%"><col width="15%"><col width="15%"><col width="15%"></colgroup>';
$LINETEMP[ 2 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td align="right">%5$s</td><td align="right">%6$s</td></tr>';
$ICONTEMP[ 20 ] = '%2$s';
$ICONTEMP[ 21 ] = '%2$s ==>';
$ICONTEMP[ 22 ] = '<input maxlength="10" size="10" type="text" name="a|%1$s|%2$s" value="%2$s" width="18" /><input type="checkbox" name="vink0[]" value="b%1$s">';
$ICONTEMP[ 23 ] = '<input maxlength="10" size="10" type="text" name="a|%1$s|%2$s" value="%2$s" width="18" />';
$ICONTEMP[ 24 ] = '%2$s&nbsp;&nbsp;<input type="checkbox" name="vink[]" value="%1$s">copy';
$ICONTEMP[ 25 ] = '%2$s&nbsp;&nbsp;<input type="checkbox" name="vink1[]" value="%1$s">paid';
$ICONTEMP[ 26 ] = '%2$s&nbsp;&nbsp;<input type="checkbox" name="vink2[]" value="%1$s">paid';

$TEMPLATE[ 3 ] = 
  '<h2>{header}</h2>
    {message}
    <div class="container">
      <form name="menu" method="post" action="{return}">
      <input type="hidden" name="module" value="{module}" />
      {menu}
    </form>
  </div>';
$TEMPLATE[ 4 ]= '<br/>' . $MOD_GSMOFFM[ 'LAB_OMS' ] . ' : &nbsp;
  <select name="bkid">{rekeningen}</select>&nbsp;&nbsp;
  <br/><br/>'.$MOD_GSMOFFM['LAB_DATUMB'].' : &nbsp;&nbsp;<input maxlength="12" size="12" type="text" name="select_tot" value="{totenmet}" width="30" />
  <br/><br/>'.$MOD_GSMOFFM['LAB_VORIG'].' :&nbsp;&nbsp;'.$MOD_GSMOFFM['LAB_DATUM'].' : &nbsp;&nbsp;
  <input maxlength="12" size="12" type="text" name="select_van" value="{vanaf}" width="30" />&nbsp;&nbsp;'.$MOD_GSMOFFM['SUR_BEDRAG'].' : &nbsp;&nbsp;
  <input maxlength="12" size="12" type="text" name="bedrag_van" value="{bedragvan}" width="30" />
  <br/><br/>'.$ICONTEMP[ 9 ];
$TEMPLATE[ 5 ]= '
  <h3>{header}</h3>
    {message}
  <div class="container">
  <table class="inhoud" width="100%">
    {kopregels}
    {description}
  </table>
  </div>';
$TEMPLATE[ 6 ]= '
  <h3>{header}</h3>
    {message}
  <div class="container">
  <form name="menu" method="post" action="{return}">
    <input type="hidden" name="module" value="{module}" />
    <input type="hidden" name="page_id" value="{page_id}" />
    <input type="hidden" name="section_id" value="{section_id}" />
    <input type="hidden" name="memory" value="{bkid}|{totenmet}|{bedragtot}|{vanaf}|{bedragvan}|{rek}" />    
    <table class="inhoud" width="100%">
    {kopregels}
    {description}
    </table>
    <input class="modules" type="submit" value="' . $MOD_GSMOFFM[ 'change' ] . ': {header} " />
  </form>
  </div>';
/*
 * Collect rekening data  
 */
$query = "SELECT * FROM `" . $regelsArr[ 'table_rek' ]."` ORDER BY `rekeningnummer`";
$message = __LINE__ ." func ". $MOD_GSMOFFM[ 'error0' ] . $regelsArr[ 'table_rek' ] . " missing</br>";
$results = $database->query( $query ); 
if ( !$results || $results->numRows() == 0 ) die( $message );
$rekeningArr = array ();  // id/rekening nummer-naam
$rekeningArr[0]= $MOD_GSMOFFM[ 'to_select' ]; //initiele waarde
$rekeningtypeArr = array (); // id/rekening type
$activaArr = array (); // id /rekeningnummer-naam activa
$activaArr[0]= $MOD_GSMOFFM[ 'to_select' ]; // initiele waarde
$offdateArr = array (); // id/balans
$offsetArr = array (); // id/balansdatum
$consolidateArr = array (); 
while ( $row = $results->fetchRow() ) { 
 // if ( $debug ) Gsm_debug( $row, __LINE__ ,2);
  $rekeningArr[$row['id']]= $row['rekeningnummer']." - ".$row['name']; 
  $rekeningtypeArr[$row['id']]= $row['rekening_type']; 
  if ($row['rekening_type']=='1') { $activaArr[$row['id']]= $row['rekeningnummer']." - ".$row['name']; }
  $offsetArr[$row['id']]= $row['balans'];
  $offdatArr[$row['id']]= $row['balans_date'];
//  process the settings
  $pos = (strpos($regelsArr ['consolidate'] , $row['rekeningnummer']));
  if ($pos !==  FALSE ) $consolidateArr[$row['rekeningnummer']]= $row['id'];
  if ( $regelsArr ['select_rek'] ==  $row['rekeningnummer'] ) $regelsArr ['select_rek_id']= $row['id'];  
}
if ( isset( $settingArr[ 'consolidate' ] )) {
  $rek= explode ('|', $regelsArr ['consolidate']);
  foreach ($rek as $keyc => $valuec ) { 
    $regelsArr['rek'][$keyc] = explode ('_', $valuec);
    if (isset($regelsArr['rek'][$keyc][1])) { 
      $regelsArr['recid'][$keyc] [1]= $consolidateArr[ $regelsArr['rek'][$keyc][1]];
      $regelsArr['recid'][$keyc] [0]= $consolidateArr[ $regelsArr['rek'][$keyc][0]];
    }
  }
}  
// rekening data collected
if ( $debug ) {
  Gsm_debug( $regelsArr, __LINE__);
  Gsm_debug( $rekeningArr, __LINE__);
  Gsm_debug( $rekeningtypeArr, __LINE__);
  Gsm_debug( $activaArr, __LINE__);
  Gsm_debug( $offdatArr, __LINE__);
  Gsm_debug( $offsetArr, __LINE__);
  Gsm_debug( $consolidateArr, __LINE__);
}
//successive screens 
if (isset( $_POST[ 'memory' ] )) { 
  $hulp= explode ("|", $_POST[ 'memory' ]);
  $regelsArr ['select_rek_id'] = (isset ($hulp[0])) ? $hulp[0] : '0';
  $regelsArr ['totenmet'] = (isset ($hulp[1])) ? $hulp[1] : date( "Y-m-d" );
  $regelsArr ['bedragtot'] = (isset ($hulp[2])) ? $hulp[2] : '0';
  $regelsArr ['vanaf'] = (isset ($hulp[3])) ? $hulp[3] : $regelsArr ['vanaf'];
  $regelsArr ['bedragvan'] = (isset ($hulp[4])) ? Gsm_eval ($hulp[4], 8, 999999, -999999) : '0'; 
  $regelsArr ['block'] = (isset ($hulp[5])) ? $hulp[5] : '0';
} else {
// input screen    
  $regelsArr ['select_rek_id'] = ( isset( $_POST[ 'bkid' ] ) ) ? $_POST[ 'bkid' ] : $regelsArr ['select_rek_id'];
  $regelsArr ['vanaf'] = ( isset( $_POST[ 'select_van' ] ) ) ? Gsm_eval ($_POST[ 'select_van' ], 9, date( "Y-m-d" ),$regelsArr['fyear']) : $regelsArr ['vanaf'];
  $regelsArr ['totenmet'] = ( isset( $_POST[ 'select_tot' ] ) ) ? Gsm_eval ($_POST[ 'select_tot' ],9, $regelsArr['xyear'],$regelsArr['vanaf']) : date( "Y-m-d" );
  $regelsArr ['bedragtot'] = ( isset( $_POST[ 'bedrag_tot' ] ) ) ? Gsm_eval ($_POST[ 'bedrag_tot' ], 8, 999999, -999999): '0';
  $regelsArr ['bedragvan'] = ( isset( $_POST[ 'bedrag_van' ] ) ) ? Gsm_eval ($_POST[ 'bedrag_van' ], 8, 999999, -999999) : '0';
  $regelsArr ['block'] = '0';
}
// remove input to simplify processing
if (isset( $_POST[ 'memory' ] ))  unset ( $_POST[ 'memory' ] );
if (isset( $_POST[ 'module' ] ))  unset ( $_POST[ 'module' ] );
if (isset( $_POST[ 'page_id' ] ))  unset ( $_POST[ 'page_id' ] );
if (isset( $_POST[ 'section_id' ] ))  unset ( $_POST[ 'section_id' ] );
if (isset( $_POST[ 'bkid' ] ))  unset ( $_POST[ 'bkid' ] );
if (isset( $_POST[ 'select_rek_id' ] ))  unset ( $_POST[ 'select_rek_id' ] );
if (isset( $_POST[ 'bedrag_van' ] ))  unset ( $_POST[ 'bedrag_van' ] );
if (isset( $_POST[ 'bedrag_tot' ] ))  unset ( $_POST[ 'bedrag_tot' ] );
if (isset( $_POST[ 'select_van' ] ))  unset ( $_POST[ 'select_van' ] );
if (isset( $_POST[ 'select_tot' ] ))  unset ( $_POST[ 'select_tot' ] );
if ( $debug ) Gsm_debug( $_POST, __LINE__ );
if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
// controle rekening
if ($regelsArr ['select_rek_id'] == '0' ) {
  $regelsArr ['mode'] = '9';
  $msg[ 'err' ] .= $MOD_GSMOFFM [ 'LAB_OMS' ]." : ".$MOD_GSMOFFM[ 'error2' ].'</br>'; 
} else {
  $regelsArr ['mode'] = '1';
}
/*
 * Menu  
 */
$parseViewArr = array(
  'menu' => $TEMPLATE[4],
  'return' => CH_RETURN,
  'module' => $regelsArr[ 'module' ], 
  'header' => $regelsArr[ 'app' ], 
  'page_id' => $page_id,	 // onnodig
  'section_id' => $section_id,  // onnodig
  'message' => message( $msg, $debug ), 
  'rekeningen' => Gsm_option($activaArr, $regelsArr[ 'select_rek_id' ]),
  'vanaf' =>$regelsArr[ 'vanaf' ],
  'totenmet' =>$regelsArr[ 'totenmet' ], // ok tot hier
  'bedragvan' =>Gsm_opmaak($regelsArr[ 'bedragvan' ], 8),
);
$print .= $TEMPLATE[ 3 ];
foreach ( $parseViewArr as $key => $value ) { $print = str_replace( "{" . $key . "}", $value, $print );}
if (isset($query)) unset( $query );
$msg[ 'bug' ] ="";
/*
 * some job to do
 */
if ( isset( $_POST[ 'vink' ] ) ) {
  $n0=1;
  foreach ($_POST[ 'vink' ] as $key => $value ) { 
    $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $value . "'";
    $results = $database->query( $query );
    $row = $results->fetchRow();
    $hulpArr= array(
      'name' => $row['name'],
      'booking_date' => $regelsArr[ 'totenmet' ],
      'project' => $row['project' ], 
      'debet_amount' => $row['debet_amount'], 
      'debet_id' => $row['debet_id'],
      'debet_rekening' => $row['debet_rekening'],
      'tegen1_amount' => $row['tegen1_amount'],
      'tegen1_id' => $row['tegen1_id' ], 
      'tegen1_rekening' => $row['tegen1_rekening'],
      'tegen2_amount' => $row['tegen2_amount'],
      'tegen2_id' => $row['tegen2_id'],
      'tegen2_rekening' => $row['tegen2_rekening'],
      'boekstuk' => '', 
      'zoek' => '', 
    );
    $query = "INSERT INTO `". $regelsArr[ 'table' ] . "` ". Gsm_parse (1,$hulpArr);
    $results = $database->query( $query );
    $msg[ 'inf' ] .= ' Rekening '.$n0.' toegevoegd</br>'; 
    $n0++;
  }
}
/*
 * Afhandeling te ontvangen
 */
//change rekening and add rekening to consolidate
if( isset( $_POST[ 'vink1' ] ) ) {
  $n0=1;
  foreach ($_POST[ 'vink1' ] as $key => $value ) { 
    $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $value . "'";
    $results = $database->query( $query );
    $row = $results->fetchRow();
	if( $regelsArr['recid'][0][0] == $row['debet_id'] ||  $regelsArr['recid'][0][0] == $row['tegen1_id' ] ||$regelsArr['recid'][0][0] == $row['tegen2_id' ] ) {
      $hulpArrUpd= array(
        'debet_id' => ($regelsArr['recid'][0][0] == $row['debet_id']) ? $regelsArr['recid'][0][1] : $row['debet_id'],
        'tegen1_id' => ($regelsArr['recid'][0][0] == $row['tegen1_id' ]) ? $regelsArr['recid'][0][1] : $row['tegen1_id' ],
        'tegen2_id' => ($regelsArr['recid'][0][0] == $row['tegen2_id' ]) ? $regelsArr['recid'][0][1] : $row['tegen2_id' ],
        'boekstuk' => $row['boekstuk'].'|paid:'.$regelsArr[ 'totenmet' ], 
        'zoek' => str_replace("_", "", str_replace( substr( $rekeningArr[$regelsArr['recid'][0][0]], 0, $regelsArr ['rekening_size']), substr( $rekeningArr[$regelsArr['recid'][0][1]], 0, $regelsArr ['rekening_size']), $row['zoek'] )).'|'.$value, 
      ); 
      $hulpArrUpd['debet_rekening' ] = substr( $rekeningArr[$hulpArrUpd['debet_id']],0,$regelsArr ['rekening_size']);
      $hulpArrUpd['tegen1_rekening' ] = substr( $rekeningArr[$hulpArrUpd['tegen1_id']],0,$regelsArr ['rekening_size']);
      $hulpArrUpd['tegen2_rekening' ] = ($row['tegen2_id']==0)? '0': substr( $rekeningArr[$hulpArrUpd['tegen2_id']],0,$regelsArr ['rekening_size']);
      if ($regelsArr['recid'][0][0] == $row['debet_id']) { 
        $hulpamt=$row['debet_amount'];
      } elseif ($regelsArr['recid'][0][0] == $row['tegen1_id' ]) {
        $hulpamt=$row['tegen1_amount'];
      } else {
        $hulpamt=$row['tegen2_amount'];
      }
      $hulpArr= array(
        'name' => $row['name'],
        'booking_date' => $regelsArr[ 'totenmet' ],
        'project' => $row['project' ],
        'debet_id' => $regelsArr ['select_rek_id'],
        'tegen1_id' => $regelsArr['recid'][0][1], 
        'debet_amount' => $hulpamt, 
        'tegen1_amount' => $hulpamt,
        'tegen2_id' => '0',
        'tegen2_amount' => '0',
        'tegen2_rekening' => '',
        'boekstuk' => $row['boekstuk'].'|inv:'.$row['booking_date'], 
        'zoek' => $row['project' ].'|'.substr($rekeningArr[$regelsArr ['select_rek_id']], 0, $regelsArr ['rekening_size']).'|'.substr($rekeningArr[$regelsArr['recid'][0][1]], 0, $regelsArr ['rekening_size']).'|'.$hulpamt, 
      );
      $hulpArr ['debet_rekening'] = substr( $rekeningArr[$hulpArr['debet_id']],0,$regelsArr ['rekening_size']);
      $hulpArr ['tegen1_rekening'] = substr( $rekeningArr[$hulpArr['tegen1_id']],0,$regelsArr ['rekening_size']);
      $query = "UPDATE `" . $regelsArr[ 'table' ] . "` SET ".Gsm_parse (2,$hulpArrUpd)."  WHERE `id`='" . $value . "'"; 
      if ($debug) {if (isset($query)) $msg[ 'bug' ] .=  __LINE__ . $query . ' case 4001</br>';} 
	  $results = $database->query( $query ); 
      $query = "INSERT INTO `". $regelsArr[ 'table' ] . "` ". Gsm_parse (1,$hulpArr);
	  if ($debug) {if (isset($query)) $msg[ 'bug' ] .=  __LINE__ . $query . ' case 4001</br>';} 
      $results = $database->query( $query );
      $msg[ 'inf' ] .= ' Ontvangst  '.$n0.' geboekt</br>'; // read record
      $n0++;
	}
  }
}
/*
 * Afhandeling te betalen
 */
 //change rekening and add rekening to consolidate
if( isset( $_POST[ 'vink2' ] ) ) {
  $n0=1;
  foreach ($_POST[ 'vink2' ] as $key => $value ) { 
    $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $value . "'";
    $results = $database->query( $query );
    $row = $results->fetchRow();
    if( $regelsArr['recid'][1][0] == $row['debet_id'] ||  $regelsArr['recid'][1][0] == $row['tegen1_id' ] ||$regelsArr['recid'][1][0] == $row['tegen2_id' ] ) {
      $hulpArrUpd= array(
        'debet_id' => ($regelsArr['recid'][1][0] == $row['debet_id']) ? $regelsArr['recid'][1][1] : $row['debet_id'],
        'tegen1_id' => ($regelsArr['recid'][1][0] == $row['tegen1_id' ]) ? $regelsArr['recid'][1][1] : $row['tegen1_id' ],
        'tegen2_id' => ($regelsArr['recid'][1][0] == $row['tegen2_id' ]) ? $regelsArr['recid'][1][1] : $row['tegen2_id' ],
        'boekstuk' => $row['boekstuk'].'|paid:'.$regelsArr[ 'totenmet' ], 
        'zoek' => str_replace("_", "", str_replace( substr( $rekeningArr[$regelsArr['recid'][1][0]], 0, $regelsArr ['rekening_size']), substr( $rekeningArr[$regelsArr['recid'][1][1]], 0, $regelsArr ['rekening_size']), $row['zoek'] )).'|'.$value, 
      ); 
      $hulpArrUpd['debet_rekening' ] = substr( $rekeningArr[$hulpArrUpd['debet_id']],0,$regelsArr ['rekening_size']);
      $hulpArrUpd['tegen1_rekening' ] = substr( $rekeningArr[$hulpArrUpd['tegen1_id']],0,$regelsArr ['rekening_size']);
      $hulpArrUpd['tegen2_rekening' ] = ($row['tegen2_id']==0)? '0': substr( $rekeningArr[$hulpArrUpd['tegen2_id']],0,$regelsArr ['rekening_size']);
      if ($regelsArr['recid'][1][0] == $row['debet_id']) { 
        $hulpamt=$row['debet_amount']*-1;
      } elseif ($regelsArr['recid'][1][0] == $row['tegen1_id' ]) {
        $hulpamt=$row['tegen1_amount']*-1;
      } else {
        $hulpamt=$row['tegen2_amount']*-1;
      }
      $hulpArr= array(
        'name' => $row['name'],
        'booking_date' => $regelsArr[ 'totenmet' ],
        'project' => $row['project'],
        'debet_id' => $regelsArr ['select_rek_id'],
        'tegen1_id' => $regelsArr['recid'][1][1], 
        'debet_amount' => $hulpamt, 
        'tegen1_amount' => $hulpamt,
        'tegen2_id' => '0',
        'tegen2_amount' => '0',
        'tegen2_rekening' => '',
        'boekstuk' => $row['boekstuk'].'|inv:'.$row['booking_date'], 
        'zoek' => $row['project' ].'|'.substr($rekeningArr[$regelsArr ['select_rek_id']], 0, $regelsArr ['rekening_size']).'|'.substr($rekeningArr[$regelsArr['recid'][1][1]], 0, $regelsArr ['rekening_size']).'|'.$hulpamt, 
      );
      $hulpArr ['debet_rekening'] = substr( $rekeningArr[$hulpArr['debet_id']],0,$regelsArr ['rekening_size']);
      $hulpArr ['tegen1_rekening'] = substr( $rekeningArr[$hulpArr['tegen1_id']],0,$regelsArr ['rekening_size']);
      $query = "UPDATE `" . $regelsArr[ 'table' ] . "` SET ".Gsm_parse (2,$hulpArrUpd)."  WHERE `id`='" . $value . "'"; 
	  if ($debug) {if (isset($query)) $msg[ 'bug' ] .=  __LINE__ . $query . ' case 4111</br>';} 
      $results = $database->query( $query ); 
      $query = "INSERT INTO `". $regelsArr[ 'table' ] . "` ". Gsm_parse (1,$hulpArr);
	  if ($debug) {if (isset($query)) $msg[ 'bug' ] .=  __LINE__ . $query . ' case 4111</br>';} 
      $results = $database->query( $query );
      $msg[ 'inf' ] .= ' Betaling  '.$n0.' geboekt</br>'; // read record
      $n0++;
    }
  }
}
foreach ($_POST as $key => $value ) { 
  $posta= explode ("|", $key);
  if ( isset($posta[2]) && $posta[2]!=$value) { 
    $hulpdate= Gsm_eval ($value, 9, $regelsArr ['totenmet'],$regelsArr['vanaf']);
    if ($hulpdate > $regelsArr[ 'vanaf' ]) {
      $hulpArr= array ('booking_date' => $hulpdate);
      $query = "UPDATE `" . $regelsArr[ 'table' ] . "` SET ".Gsm_parse (2,$hulpArr)."  WHERE `id`='" . $posta[1] . "'"; 
      $results = $database->query( $query ); 
      $msg[ 'inf' ] .= $posta[2].' => '.$hulpdate.' Boekdatum aangepast</br>'; 
    } else {
      $msg[ 'inf' ] .= $posta[2].' => '.$value.' Datum --out of range--</br>';
    }
  }
}
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ( $debug ) {
  Gsm_debug( $regelsArr, __LINE__);
//  Gsm_debug( $rekeningArr, __LINE__);
//  Gsm_debug( $rekeningtypeArr, __LINE__);
 // Gsm_debug( $activaArr, __LINE__);
//  Gsm_debug( $offdatArr, __LINE__);
 // Gsm_debug( $offsetArr, __LINE__);
 // Gsm_debug( $consolidateArr, __LINE__);
}
if ( $debug ) $msg[ 'bug' ] .= __LINE__ . " mode: " . $regelsArr[ 'mode' ] . ' block ' . $regelsArr ['block' ]. '</br>';


/*
 * display preparation
 */
if ($regelsArr ['mode']==1 ) {
  $regelsArr ['app' ]= $rekeningArr [$regelsArr ['select_rek_id']];
  $regelsArr[ 'head' ] .= $LINETEMP[ 1 ];
  $regelsArr[ 'head' ] .= sprintf( $LINETEMP[ 2 ], $MOD_GSMOFFM['line_color'][3], strtoupper($MOD_GSMOFFM['LAB_DATUM']), strtoupper ($MOD_GSMOFFM['LAB_REK']).' / '.strtoupper ($MOD_GSMOFFM['LAB_REF']),strtoupper ($MOD_GSMOFFM['LAB_PROJECT']), strtoupper($MOD_GSMOFFM['SUR_MUT']),strtoupper($MOD_GSMOFFM['SUR_SALDO']) );
  $regelsArr[ 'descr' ] .= $LINETEMP[ 1 ];
  $pagingArr[ 'm' ] = 0; // voor regel om regel andere kleur
  $regelsArr[ 'cum_rek' ] = $offsetArr [$regelsArr ['select_rek_id']];
  if ( $debug ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col = $MOD_GSMOFFM['line_color'][ 2 ], $offdatArr [$regelsArr ['select_rek_id']], ' - Openings Balans','', '--', sprintf( '%01.2f', $regelsArr[ 'cum_rek' ] ), '' );
  $regelsArr ['search_mysql'] = "`booking_date` >= '" . $offdatArr [$regelsArr ['select_rek_id']] . "' AND `booking_date` <= '" . $regelsArr[ 'totenmet' ] . "' ";
  $regelsArr ['search_mysql'] .= " AND ( `debet_id`= '" . $regelsArr ['select_rek_id'] . "' OR `tegen1_id`= '" . $regelsArr ['select_rek_id'] . "' OR  `tegen2_id`= '" . $regelsArr ['select_rek_id'] . "' )";
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE " . $regelsArr ['search_mysql'] . " ORDER BY `booking_date`, `project`";
  if ($debug) {if (isset($query)) $msg[ 'bug' ] .=  __LINE__ . $query . '</br>';}  
  $results = $database->query( $query );
  if ( $results && $results->numRows() > 0 ) {
    while ( $row = $results->fetchRow() ) {
      if($regelsArr ['found' ]) { 
        $hulptemp=$ICONTEMP[ 23 ]; 
      } else {
        $hulptemp=$ICONTEMP[ 24 ]; 
        if ( isset( $settingArr[ 'consolidate' ] )) {		
          if (count($regelsArr,1) >7) {
            if ($row['debet_id'] == $regelsArr['recid'][0][1] ) $hulptemp=$ICONTEMP[ 20 ];
            if ($row['tegen1_id'] == $regelsArr['recid'][0][1] ) $hulptemp=$ICONTEMP[ 20 ];
            if ($row['tegen2_id'] != '0' && $row['tegen2_id'] == $regelsArr['recid'][0][1] ) $hulptemp=$ICONTEMP[ 20 ];
            if (count($regelsArr,1) >13) {
              if ($row['debet_id'] == $regelsArr['recid'][1][1] ) $hulptemp=$ICONTEMP[ 20 ];
              if ($row['tegen1_id'] == $regelsArr['recid'][1][1] ) $hulptemp=$ICONTEMP[ 20 ];
              if ($row['tegen2_id'] != '0' && $row['tegen2_id'] == $regelsArr['recid'][1][1] ) $hulptemp=$ICONTEMP[ 20 ];
            }
          }
		}
      }
      $hulp1 = ( strlen( $row[ 'name' ] ) > 2 ) ?  str_replace ('_', ' ',$row[ 'name' ]) : '';
      $hulp1 .= ( strlen( $row[ 'boekstuk' ] ) > 2 ) ?  ' ==> ' .str_replace ('_', ' ',$row[ 'boekstuk' ]). ' ' : '';
      $hulp2 = ( strlen( $row[ 'project' ] ) > 2 ) ? $row[ 'project' ]: '';
      $col = ( $pagingArr[ 'm' ] % 2 == 0 ) ? $col = $MOD_GSMOFFM['line_color'][ 2 ] : "";
      if ( $row[ 'debet_id' ] == $regelsArr ['select_rek_id'] ) {
        $regelsArr[ 'cum_rek' ] += $row[ 'debet_amount' ];
        if ($regelsArr[ 'vanaf' ] <= $row['booking_date'] ) { 
          if (abs($regelsArr[ 'cum_rek' ] - $regelsArr[ 'bedragvan' ] ) < 0.001) { 
            $hulptemp = $ICONTEMP[ 21 ]; 
            $regelsArr ['found' ] = true ;
          } 
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
            sprintf($hulptemp, $row['id'],$row['booking_date']),
            $hulp1, $hulp2, 
            Gsm_opmaak($row[ 'debet_amount' ], 8), 
            '<i>'.Gsm_opmaak($regelsArr[ 'cum_rek' ], 1).'</i>' );
			$pagingArr[ 'm' ]++;
        }
      }
      if ( $row[ 'tegen1_id' ] == $regelsArr ['select_rek_id'] ) {
        $hulp_amt = $row[ 'tegen1_amount' ]  * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['debet_id']]]  * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['tegen1_id']]] * -1;
        $regelsArr[ 'cum_rek' ] += $hulp_amt;
        if ($regelsArr[ 'vanaf' ] <= $row['booking_date'] ) { 
          if (abs($regelsArr[ 'cum_rek' ] - $regelsArr[ 'bedragvan' ] ) < 0.001) { 
            $hulptemp = $ICONTEMP[ 21 ]; 
            $regelsArr ['found' ] = true ;
          } 
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
            sprintf($hulptemp, $row['id'],$row['booking_date']),
            $hulp1, $hulp2, 
            Gsm_opmaak($hulp_amt, 8), 
            '<i>'.Gsm_opmaak($regelsArr[ 'cum_rek' ], 1).'</i>' );
			$pagingArr[ 'm' ]++;
        }
      }
      if ( $row[ 'tegen2_id' ] == $regelsArr ['select_rek_id'] ) {
        $hulp_amt=0;
        if ($row[ 'tegen2_amount' ]<>0) { 
          $hulp_amt = $row[ 'tegen2_amount' ] * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['debet_id']]] * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['tegen2_id']]] * -1;
          $regelsArr[ 'cum_rek' ] += $hulp_amt;
          if ($regelsArr[ 'vanaf' ] <= $row['booking_date'] ) {
            if (abs($regelsArr[ 'cum_rek' ] - $regelsArr[ 'bedragvan' ] ) < 0.001) { 
              $hulptemp = $ICONTEMP[ 21 ]; 
              $regelsArr ['found' ] = true ;
            } 
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
            sprintf($hulptemp, $row['id'],$row['booking_date']),
            $hulp1, $hulp2, 
            Gsm_opmaak($hulp_amt, 8), 
            '<i>'.Gsm_opmaak($regelsArr[ 'cum_rek' ], 1).'</i>' );
			$pagingArr[ 'm' ]++;
          } 
        }
      }          
    }
    $parseViewArr = array(
	  'header' => $regelsArr[ 'app' ],
      'message' => message( $msg, $debug ),
      'return' => CH_RETURN,
      'module' => $regelsArr[ 'module' ],
      'page_id' => $page_id,	
      'section_id' => $section_id, 
	  'bkid' => $regelsArr[ 'select_rek_id' ],
      'bedragvan' =>Gsm_opmaak($regelsArr[ 'bedragvan' ], 8),
      'bedragtot' =>Gsm_opmaak($regelsArr[ 'bedragtot' ], 8),
      'vanaf' =>$regelsArr[ 'vanaf' ],
      'totenmet' =>$regelsArr[ 'totenmet' ],	  
      'rek' => '1',
      'kopregels' => $regelsArr[ 'head' ],
      'description' => $regelsArr[ 'descr' ],
    );
    $print .= $TEMPLATE[ 6 ];
    foreach ( $parseViewArr as $key => $value ) { $print = str_replace( "{" . $key . "}", $value, $print );}  
	$msg[ 'bug' ] ="";
  }
/*
 * Display te ontvangen
 */
  if ( isset($regelsArr['recid'][0][0] ) && $regelsArr['recid'][0][0] != '0') {
    $regelsArr ['app' ]= $rekeningArr [$regelsArr['recid'][0][0]];
    $regelsArr[ 'head' ] = $LINETEMP[ 1 ];
    $regelsArr[ 'head' ] .= sprintf( $LINETEMP[ 2 ], $MOD_GSMOFFM['line_color'][3], strtoupper($MOD_GSMOFFM['LAB_DATUM']), strtoupper ($MOD_GSMOFFM['LAB_REK']).' / '.strtoupper ($MOD_GSMOFFM['LAB_REF']),strtoupper ($MOD_GSMOFFM['LAB_PROJECT']), strtoupper($MOD_GSMOFFM['SUR_MUT']),'' );
    $regelsArr[ 'descr' ] = $LINETEMP[ 1 ];
    $pagingArr[ 'm' ] = 0; // voor regel om regel andere kleur
    $regelsArr ['search_mysql'] = "`booking_date` >= '" . $offdatArr [$regelsArr['recid'][0][0]] . "' AND `booking_date` <= '" . $regelsArr[ 'totenmet' ] . "' ";
    $regelsArr ['search_mysql'] .= " AND ( `debet_id`= '" . $regelsArr['recid'][0][0] . "' OR `tegen1_id`= '" . $regelsArr['recid'][0][0] . "' OR  `tegen2_id`= '" . $regelsArr['recid'][0][0] . "' )";
    $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE " . $regelsArr ['search_mysql'] . " ORDER BY `booking_date`, `project`";
    $results = $database->query( $query );
    if ( $results && $results->numRows() > 0 ) {
      while ( $row = $results->fetchRow() ) {
        $hulp1 = ( strlen( $row[ 'name' ] ) > 2 ) ?  str_replace ('_', ' ',$row[ 'name' ]) : '';
        $hulp1 .= ( strlen( $row[ 'boekstuk' ] ) > 2 ) ?  ' ==> ' .str_replace ('_', ' ',$row[ 'boekstuk' ]). ' ' : '';
        $hulp2 = ( strlen( $row[ 'project' ] ) > 2 ) ? $row[ 'project' ]: '';

        $col = ( $pagingArr[ 'm' ] % 2 == 0 ) ? $col = $MOD_GSMOFFM['line_color'][ 2 ] : "";
        if ( $row[ 'debet_id' ] == $regelsArr['recid'][0][0] ) {
          $regelsArr[ 'cum_rek' ] += $row[ 'debet_amount' ];
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
            sprintf($ICONTEMP[25], $row['id'],$row['booking_date']),
            $hulp1, $hulp2, 
            Gsm_opmaak($row [ 'debet_amount' ], 8), 
            '' );
        }
        if ( $row[ 'tegen1_id' ] == $regelsArr['recid'][0][0] ) {
          $hulp_amt = $row[ 'tegen1_amount' ]  * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['debet_id']]]  * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['tegen1_id']]] * -1;
          $regelsArr[ 'cum_rek' ] += $hulp_amt;
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
            sprintf($ICONTEMP[25], $row['id'],$row['booking_date']),
            $hulp1, $hulp2, 
            Gsm_opmaak( $hulp_amt, 8), 
            '' );
        }
        if ( $row[ 'tegen2_id' ] == $regelsArr['recid'][0][0] ) {
          $hulp_amt=0;
          if ($row[ 'tegen2_amount' ]<>0) { 
            $hulp_amt = $row[ 'tegen2_amount' ] * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['debet_id']]] * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['tegen2_id']]] * -1;
            $regelsArr[ 'cum_rek' ] += $hulp_amt;
            $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
              sprintf($ICONTEMP[25], $row['id'],$row['booking_date']),
              $hulp1, $hulp2, 
              Gsm_opmaak( $hulp_amt, 8), 
              '' );
          }
        }          
      }
      $parseViewArr = array(
	    'header' => $regelsArr[ 'app' ],
        'message' => message( $msg, $debug ),
        'return' => CH_RETURN,
        'module' => $regelsArr[ 'module' ],
        'page_id' => $page_id,	
        'section_id' => $section_id, 
	    'bkid' => $regelsArr[ 'select_rek_id' ],
        'bedragvan' =>Gsm_opmaak($regelsArr[ 'bedragvan' ], 8),
        'bedragtot' =>Gsm_opmaak($regelsArr[ 'bedragtot' ], 8),
        'vanaf' =>$regelsArr[ 'vanaf' ],
        'totenmet' =>$regelsArr[ 'totenmet' ],	  
        'rek' => '2',
        'kopregels' => $regelsArr[ 'head' ],
        'description' => $regelsArr[ 'descr' ],
      );
      $print .= $TEMPLATE[ 6 ];
      foreach ( $parseViewArr as $key => $value ) { $print = str_replace( "{" . $key . "}", $value, $print );} 
      $msg[ 'bug' ] ="";	  
    } 
  }
/*
 * Display te ontvangen
 */
  if ( isset($regelsArr['recid'][1][0]) && $regelsArr['recid'][1][0] != '0') {
    $regelsArr ['app' ]= $rekeningArr [$regelsArr['recid'][1][0]];
    $regelsArr[ 'head' ] = $LINETEMP[ 1 ];
    $regelsArr[ 'head' ] .= sprintf( $LINETEMP[ 2 ], $MOD_GSMOFFM['line_color'][3], strtoupper($MOD_GSMOFFM['LAB_DATUM']), strtoupper ($MOD_GSMOFFM['LAB_REK']).' / '.strtoupper ($MOD_GSMOFFM['LAB_REF']),strtoupper ($MOD_GSMOFFM['LAB_PROJECT']), strtoupper($MOD_GSMOFFM['SUR_MUT']),'' );
    $regelsArr[ 'descr' ] = $LINETEMP[ 1 ];
    $pagingArr[ 'm' ] = 0; // voor regel om regel andere kleur
    $regelsArr ['search_mysql'] = "`booking_date` >= '" . $offdatArr [$regelsArr['recid'][1][0]] . "' AND `booking_date` <= '" . $regelsArr[ 'totenmet' ] . "' ";
    $regelsArr ['search_mysql'] .= " AND ( `debet_id`= '" . $regelsArr['recid'][1][0] . "' OR `tegen1_id`= '" . $regelsArr['recid'][1][0] . "' OR  `tegen2_id`= '" . $regelsArr['recid'][1][0] . "' )";
    $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE " . $regelsArr ['search_mysql'] . " ORDER BY `booking_date`, `project`";
    $results = $database->query( $query );
    if ( $results && $results->numRows() > 0 ) {
      while ( $row = $results->fetchRow() ) {
        $hulp1 = ( strlen( $row[ 'name' ] ) > 2 ) ?  str_replace ('_', ' ',$row[ 'name' ]) : '';
        $hulp1 .= ( strlen( $row[ 'boekstuk' ] ) > 2 ) ?  ' ==> ' .str_replace ('_', ' ',$row[ 'boekstuk' ]). ' ' : '';
        $hulp2 = ( strlen( $row[ 'project' ] ) > 2 ) ? $row[ 'project' ]: '';
        $col = ( $pagingArr[ 'm' ] % 2 == 0 ) ? $col = $MOD_GSMOFFM['line_color'][ 2 ] : "";
        if ( $row[ 'debet_id' ] == $regelsArr['recid'][1][0] ) {
          $regelsArr[ 'cum_rek' ] += $row[ 'debet_amount' ];
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
            sprintf($ICONTEMP[26], $row['id'],$row['booking_date']),
            $hulp1, $hulp2, 
            Gsm_opmaak($row[ 'debet_amount' ], 8), 
            '' );
        }
        if ( $row[ 'tegen1_id' ] == $regelsArr['recid'][1][0] ) {
          $hulp_amt = $row[ 'tegen1_amount' ]  * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['debet_id']]]  * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['tegen1_id']]] * -1;
          $regelsArr[ 'cum_rek' ] += $hulp_amt;
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
            sprintf($ICONTEMP[26], $row['id'],$row['booking_date']),
            $hulp1, $hulp2, 
            Gsm_opmaak( $hulp_amt, 8),  
            '' );
        }
        if ( $row[ 'tegen2_id' ] == $regelsArr['recid'][1][0] ) {
          $hulp_amt=0;
          if ($row[ 'tegen2_amount' ]<>0) { 
            $hulp_amt = $row[ 'tegen2_amount' ] * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['debet_id']]] * $MOD_GSMOFFM ['rek_type_sign'][ $rekeningtypeArr[$row['tegen2_id']]] * -1;
            $regelsArr[ 'cum_rek' ] += $hulp_amt;
            $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 2 ], $col, 
              sprintf($ICONTEMP[26], $row['id'],$row['booking_date']),
              $hulp1, $hulp2, 
              Gsm_opmaak( $hulp_amt, 8), 
              '' );
          }
        }          
      }
      $parseViewArr = array(
	    'header' => $regelsArr[ 'app' ],
        'message' => message( $msg, $debug ),
        'return' => CH_RETURN,
        'module' => $regelsArr[ 'module' ],
        'page_id' => $page_id,	
        'section_id' => $section_id, 
	    'bkid' => $regelsArr[ 'select_rek_id' ],
        'bedragvan' =>Gsm_opmaak($regelsArr[ 'bedragvan' ], 8),
        'bedragtot' =>Gsm_opmaak($regelsArr[ 'bedragtot' ], 8),
        'vanaf' =>$regelsArr[ 'vanaf' ],
        'totenmet' =>$regelsArr[ 'totenmet' ],	  
        'rek' => '3',
        'kopregels' => $regelsArr[ 'head' ],
        'description' => $regelsArr[ 'descr' ],
      );
      $print .= $TEMPLATE[ 6 ];
      foreach ( $parseViewArr as $key => $value ) { $print = str_replace( "{" . $key . "}", $value, $print );}  
	  $msg[ 'bug' ] ="";
    } 
  }
}
?>