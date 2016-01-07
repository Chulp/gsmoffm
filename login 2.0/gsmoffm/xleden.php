<?php
/**
 *  @module         Office_files
 *  @version        see info.php of this module
 *  @author         Gerard Smelt
 *  @copyright      2013, ContractHulp B.V.
 *  @system         Developped and tested under Lepton 1.2.2
 *  @license        All rights reserved
 *  @license terms  see info.php of this module
 *  @platform       see info.php of this module
 *  @requirements   PHP 5.2.x and higher
 *
 *     select.php:     invoked in the front
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
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'leden',
// voor versie display
  'modulen' => 'xleden',  
  'versie' => ' vv20151121',
// datastructures 
  'table_adres' => CH_DBBASE . '_adres',
  'standen' => CH_DBBASE.'_standen',
  'standtype' => '1',
// general parameters
  'owner' => (isset($settingArr[ 'logo'])) ? $settingArr[ 'logo'] : '', // ** This is the logo on the pdf **
  'app' => 'Leden',
// for display en pdf output 
  'seq' => (isset($_POST['next'])) ? $regelsArr['seq'] = $_POST['next'] : 0,
  'n' => 0,
  'qty' => (isset($settingArr['oplines'])) ? $settingArr['oplines'] : 60,
  'project' => '',
// search
  'search' => '',
  'search_mysql' => '', 
// display fields
  'recid' => '',
  'memory' => '', 
  'head' => '',
  'descr' => '',
  'select' => '',
  'toegift' => '',        
  'rapport' => '',
  'today' => date( "Y-m-d" ),
//display
  'update' => '',
  'hash' => '',
// search
  'search' => '',
  'search_mysql' => '',
  'volgorde' => 'name',
  'opzoek' => 'name',
  'record_update' => false,
  'pdf_ok' => false,
  'edit_ok' => false,
  'add_ok' => false,
  'xdatumist' => date("Y-m-d"),
// pdf
  'fields' => array(
    1 => 'id',
    2 => 'name',
    3 => 'refer',
    4 => 'datumist',
    5 => 'standist'
  ),
  'print_regels' => 1, // ** relevance for layout
  'cols' => array( 55, 35, 35, 20, 20, 20 ),
  'leeg' => array( 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "" ),
  'today_pf' => date("_Ymd_His")
);
$regelsArr['project'] = $regelsArr['app'] . ' - Administratie';
/*
 * debug used data
 */
if ( $debug ) {
  Gsm_debug( $place, __LINE__);
  Gsm_debug( $settingArr, __LINE__);
  Gsm_debug( $_POST, __LINE__);
  Gsm_debug( $_GET, __LINE__);
  Gsm_debug( $regelsArr, __LINE__);
}
/*
 * Layout template
 *
 * in language module
 * $template[0-3] in language module
 * $icontemp [0-19]
 * $linetemp [0-19]
 */
 // overrule standaard  strings
$MOD_GSMOFF[ 'tbl_icon' ][13] = "Betaald";
// extend text strings
$MOD_GSMOFF [ 'nodata' ] = 'Geen data/informatie';
// overrule standard function
$ICONTEMP[ 7 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][7].'" style="width: 100%;" />'.CH_CR; 
$ICONTEMP[ 8 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][2].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][8].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 13 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][13].'" style="width: 100%;" />'.CH_CR;
// extend standard function
$LINETEMP[70] = '<input type="hidden" name="recid" value="%1$s" >';
$LINETEMP[86] = '<textarea rows="%2$s" cols="35" name="%1$s" placeholder="%4$s" >%3$s</textarea>';
$LINETEMP[87] = '<tr %1$s><td>%2$s</td><td colspan="2">%3$s</td><td>%4$s</td><td>%5$s</td></tr>';
$LINETEMP[88] = '<input type="text" name="%1$s" size="%2$s"  value="%3$s" placeholder="%4$s" autocomplete="off" />';
$LINETEMP[89] = '<input type="date" name="%1$s" size="%2$s" value="%3$s" placeholder="%4$s" autocomplete="off" />';
$LINETEMP[90] = '<tr %1$s><td colspan="2">%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td></tr>';
$LINETEMP[91] = '<colgroup><col width="28%%"><col width="7%%"><col width="25%%"><col width="35%%"><col width="5%%"></colgroup><thead><tr><th>%1$s</th><th>%2$s</th><th>%3$s</th><th>%4$s</th><th>%5$s</th></tr></thead>';
$LINETEMP[92] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>';
$LINETEMP[93] = '<input type="checkbox" name="vink[]" value="%2$s">&nbsp;%1$s';
$LINETEMP[95] = '<input type="checkbox" name="vink[]" value="%2$s" checked>&nbsp;%1$s';
$LINETEMP[94] = '<a href="' . CH_RETURN . '&command=view&module={module}&recid=%2$s">%1$s</a>';
$LINETEMP[98] = '<tr><td colspan="2">%1$s</td><td colspan="3">%2$s</td></tr>';
$LINETEMP[99] = '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>';
$ICONTEMP[23] = '<input maxlength="10" size="10" type="text" name="a|%1$s|%2$s" value="%2$s" width="18" />';
/*
 * create details file if not existing file if not existing
 */
$query = "DESCRIBE " . $regelsArr ['standen'];
$message = $MOD_GSMOFF['error0'] . $query . "</br>";
$results = $database->query($query);
if (!$results || $results->numRows() == 0) die($message);
$fieldArr = array();
$defaultcontent = array(
  'varchar' => '',
  'date' => '',
  'decimal' => 0,
  'int' => 0
);
// Add fields automatically to the array 
while ($row = $results->fetchRow()) {
  $fieldArr[$row['Field']] = $row['Type'];
  $regelsArr['x' . $row['Field']] = '';
  foreach ($defaultcontent as $key => $value) {
    if (strstr($row['Type'], $key))
      $regelsArr['x' . $row['Field']] = $value;
  } //$defaultcontent as $key => $value
} //$row = $results->fetchRow()
// Modified default values 
$regelsArr['xdatumist'] = date("Y-m-d");
$regelsArr['xstandtype'] = 1; //registratie van een open betaling en voldaan;
// all fields collected now remove the standard fields except name
unset($fieldArr['id']);
unset($fieldArr['zoek']);
unset($fieldArr['updated']);
if ($debug) {
  Gsm_debug($fieldArr, __LINE__);
  Gsm_debug($regelsArr, __LINE__);
}
unset($query);
/*
 * some job to do ?
 */
if (isset($_POST['selection']) && strlen($_POST['selection']) >= 2) {
  $regelsArr['search'] = trim($_POST['selection']);
  $help = "%" . str_replace(' ', '%', $regelsArr['search']) . "%";
  $regelsArr['search_mysql'] = " WHERE  `zoek` LIKE '" . $help . "' AND `standtype`= '".$regelsArr['standtype']."'";
} else {  
	$regelsArr['search_mysql'] = " WHERE `standtype`= '".$regelsArr['standtype']."'";
} //isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 2
/*
 * are records selected
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
  // if data is available get it from the database
  $query = "SELECT 
      `" . $regelsArr['table_adres'] . "`.`adres`,
      `" . $regelsArr['table_adres'] . "`.`email`,
      `" . $regelsArr['standen'] . "`.`id`,
      `" . $regelsArr['standen'] . "`.`name`,
      `" . $regelsArr['standen'] . "`.`adresid`,
      `" . $regelsArr['standen'] . "`.`standtype`,		  
      `" . $regelsArr['standen'] . "`.`refer`,
      `" . $regelsArr['standen'] . "`.`datumsoll`,
      `" . $regelsArr['standen'] . "`.`datumist`,
      `" . $regelsArr['standen'] . "`.`standsoll`,
      `" . $regelsArr['standen'] . "`.`standist`,
      `" . $regelsArr['standen'] . "`.`reference`,
      `" . $regelsArr['standen'] . "`.`comment`,
      `" . $regelsArr['standen'] . "`.`omschrijving`,
      `" . $regelsArr['table_adres'] . "`.`info`,
      `" . $regelsArr['table_adres'] . "`.`bank`,
      `" . $regelsArr['table_adres'] . "`.`sinds`,
      `" . $regelsArr['table_adres'] . "`.`contact`,
      `" . $regelsArr['table_adres'] . "`.`eind`,
      `" . $regelsArr['table_adres'] . "`.`aant`
      FROM `" . $regelsArr['standen'] . "`
      LEFT JOIN `" . $regelsArr['table_adres'] . "`
      ON `" . $regelsArr['standen'] . "`.`adresid` = `" . $regelsArr['table_adres'] . "`.`id`
      WHERE `" . $regelsArr['standen'] . "`.`id`= '". $regelsArr['recid'] ."' AND `". $regelsArr['standen']."`.`standtype`= '".$regelsArr['standtype']."'";
  if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
  $results = $database->query($query);
  if (!$results || $results->numRows() >= 1) {
    $row = $results->fetchRow();
    foreach ($fieldArr as $key => $value) {
      $regelsArr['x' . $key] = $row[$key];
    } //$fieldArr as $key => $value
  } //!$results || $results->numRows() >= 1
  if ($debug) Gsm_debug($regelsArr, __LINE__);
} //$regelsArr['recid'] >0
/*
 * some job to do ?
 */
if (isset($_POST['command'])) {
/*
 * process the input 
 */  
  if (isset($_POST['adresid'])) $regelsArr['xadresid'] = $_POST['adresid'];
  if (isset($_POST['omschrijving'])) $regelsArr['xomschrijving'] = Gsm_eval($_POST['omschrijving'], 1, 255);
  if (isset($_POST['refer'])) $regelsArr['xrefer'] = Gsm_eval($_POST['refer'], 1, 20);
  if (isset($_POST['datumsoll'])) $regelsArr['xdatumsoll'] = Gsm_eval($_POST['datumsoll'], 9, '2020-01-01', '2000-01-01');
  if (isset($_POST['datumist'])) $regelsArr['xdatumist'] = Gsm_eval($_POST['datumist'], 9, '2020-01-01', '0000-00-00');
  if (isset($_POST['standsoll'])) $regelsArr['xstandsoll'] = Gsm_eval($_POST['standsoll'], 8, 200, 0);
  if (isset($_POST['standist'])) $regelsArr['xstandist'] = Gsm_eval($_POST['standist'], 8, 200, 0);
  if (isset($_POST['reference'])) $regelsArr['xreference'] = Gsm_eval($_POST['reference'], 3);
  if (isset($_POST['comment'])) $regelsArr['xcomment'] = Gsm_eval($_POST['comment'], 1, 255);
  if ($debug) Gsm_debug($regelsArr, __LINE__);
  switch ($_POST['command']) {
    case $MOD_GSMOFF['tbl_icon'][5]: //opslaan als nieuw
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      if (isset($_POST['vink'][0])) {
        foreach ($_POST['vink'] as $key => $value) {
          // ophalen adresrecord
          $query = "SELECT * FROM `" . $regelsArr['table_adres'] . "` WHERE `id`= '" . $value . "'";
          if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
          $results = $database->query($query);
          if (isset($results) && $results->numRows() >= 1) {
            $row = $results->fetchRow();
            $hulpArr = array(
              'name' => $row['name'],
              'zoek' => $row['name'] . "|" . $regelsArr['xrefer'],
              'adresid' => $row['id'],
			        'standtype' => $regelsArr['standtype'],
              'omschrijving' => $regelsArr['xomschrijving'],
              'refer' => $regelsArr['xrefer'],
              'datumsoll' => $regelsArr['xdatumsoll'],
              'standsoll' => $regelsArr['xstandsoll']
            );
            $query2 = "SELECT * FROM `" . $regelsArr['standen'] . "` WHERE 'adresid'= '" . $hulpArr['adresid'] . "' AND `refer`= '" . $hulpArr['refer'] . "' AND `standtype`= '".$regelsArr['standtype']."'";
            if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query2 . ' <br/>';
            $result2 = $database->query($query2);
            if (isset($result2) && $result2->numRows() >= 1) {
              $row2 = $result2->fetchRow();
              $query2 = "UPDATE `" . $regelsArr['standen'] . "` SET " . Gsm_parse(2, $hulpArr) . "  WHERE `id` = '" . $row2['id'] . "'";
              if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query2 . ' <br/>';
              $result2 = $database->query($query2);
            } else {
              $query2 = "INSERT INTO `" . $regelsArr['standen'] . "` " . Gsm_parse(1, $hulpArr);
              if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query2 . ' <br/>';
              $result2 = $database->query($query2);
            }
          } //$results && $results->numRows() >= 1
        } //$_POST[ 'vink' ] as $key => $value
      } //isset( $_POST[ 'vink' ][ 0 ] )
      unset($query);
      // verder met toevoegen
    case $MOD_GSMOFF['tbl_icon'][3]: //toevoegen
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['mode'] = 6;
      break;
    case $MOD_GSMOFF['tbl_icon'][13]: //betaald
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      if (isset($_POST['vink'][0])) {
        foreach ($_POST['vink'] as $key => $value) {
          // ophalen adresrecord
          $query = "SELECT * FROM `" . $regelsArr['standen'] . "` WHERE `id`= '" . $value . "'  AND `standtype`= '".$regelsArr['standtype']."'";
          if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
          $results = $database->query($query);
          if (isset($results) && $results->numRows() >= 1) {
            $row = $results->fetchRow();
           $hulpArr = array(        
              'datumist' => ($regelsArr['xdatumist'] > "2010-01-01") ? $regelsArr['xdatumist'] : date("Y-m-d"),
              'standist' => ($regelsArr['xstandist'] > 0) ? $regelsArr['xstandist'] : $row['standsoll']
            );
            $query = "UPDATE `" . $regelsArr['standen'] . "` SET " . Gsm_parse(2, $hulpArr) . "  WHERE `id` = '" . $row['id'] . "'";
            if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
            $result = $database->query($query);
          } //$results && $results->numRows() >= 1
        } //$_POST[ 'vink' ] as $key => $value
      } //isset( $_POST[ 'vink' ][ 0 ] )
      foreach ($_POST as $key => $value) {
        $posta = explode("|", $key);
        if ($posta[0] == "a") {
          // correct type
          if (isset($posta[2]) && $posta[2] != $value) {
            // something changed
            $hulpArr = array(
              'datumist' => Gsm_eval($value, 9, '2020-01-01', '0000-00-00')
            );
            $query = "UPDATE `" . $regelsArr['standen'] . "` SET " . Gsm_parse(2, $hulpArr) . "  WHERE `id` = '" . $posta[1] . "'";
            if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
            $results = $database->query($query);
          } //isset( $posta[ 2 ] ) && $posta[ 2 ] != $value
        } //$posta[ 0 ] == "a"
      } //$_POST as $key => $value 
      unset($query);
      // verder met betaling
    case $MOD_GSMOFF['tbl_icon'][12]: //betaling
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['mode'] = 5;
      break;
    case $MOD_GSMOFF['tbl_icon'][1]: //wijzigen
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      if(isset($_POST['vink'][0])) {
        $regelsArr['mode'] = 8;
      } else { 
        $msg['inf'] .= ' no selection <br/>';
        $regelsArr['mode'] = 9; 
      }
      break;
    case $MOD_GSMOFF['tbl_icon'][6]: //remove
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $query = "DELETE FROM `" . $regelsArr['standen'] . "` WHERE `id`= '" . $regelsArr['recid'] . "'";
      if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
      $results = $database->query($query);
      unset($query);
      $regelsArr['mode'] = 9;
      break;
    case $MOD_GSMOFF['tbl_icon'][2]: //terug
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      unset($query);
      $regelsArr['mode'] = 9;
      break;
    case $MOD_GSMOFF['tbl_icon'][4]: //opslaan
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $hulpArr = array();
      foreach ($fieldArr as $key => $value) { $hulpArr[$key] = $regelsArr['x' . $key];  } 
      if ($debug) Gsm_debug($hulpArr, __LINE__);
      $query = "UPDATE `" . $regelsArr['standen'] . "` SET " . Gsm_parse(2, $hulpArr) . "  WHERE `id` = '" . $regelsArr['recid'] . "'";
      $msg['inf'] .= ' entry updated<br/>';
      if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
      $results = $database->query($query);
      unset($query);
      $regelsArr['mode'] = 9;
      break;
    case $MOD_GSMOFF['tbl_icon'][11]: //print
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['filename_pdf'] = strtolower( $regelsArr[ 'project' ] ) . '.pdf';
      require_once( $place_incl . 'pdf.inc' );   
      $query = "SELECT * FROM `" . $regelsArr['standen'] . "`" . $regelsArr['search_mysql'] . " ORDER BY `refer`, `datumsoll` , `name`";
      if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
      $results = $database->query($query);
      $regelsArr['n'] = $results->numRows();
      /*
       * initiatie pdf before starting the normal process
       */
      $regelsArr['cols'] = array( 10, 60, 35, 20, 20, 20);
      $pdf = new PDF(); 
      global $title;
      global $owner;
      $owner = $regelsArr['owner'];
      $title = $regelsArr['project'];
      $run = date("Ymd_His");
      $pdf->AliasNbPages();
      $pdf->AddPage();
      $pdf->ChapterTitle(1, 'Open');
      $pdf_text='';
      $pdf_data = array();
      $notfirst = false;
      $subtotal0 = 0;
      $subtotal1 = 0;
      $subtotal2 = 0;
      $regelsArr['bucket'] = "";
      $regelsArr['bucket1'] = "";
      $pdf_header = array(
        '',
        'naam',
        'omschrijving',
        'betaald'
      );
      while ($row = $results->fetchRow()) {
        if ($row['datumist']=="0000-00-00" || $row['datumist']=="1970-01-01") {
         if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
          if ($regelsArr['bucket'] != $row['refer']) {
            if ($notfirst) {
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", "", $regelsArr['bucket'], "", "", "", "$subtotal2");
              $pdf_data[] = explode(';', trim($pdf_line));
              $subtotal2 = 0;
            } //$notfirst
            $notfirst = true;
            $regelsArr['bucket'] = $row['refer'];
          } //$regelsArr[ 'bucket' ] != $row[ 'refer' ]
          $namefields = explode('|', $row['name'] . '|||||');
          $h_n = sprintf("%s %s %s ( %s )", $namefields[1], $namefields[2], $namefields[3], $row['id']);
          $h_a = sprintf("%s  : %s", $row['refer'], $row['standsoll']);
          $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", $row['id'], $h_n, $h_a, $row['standist'], "", "");
          if ($debug) $msg['bug'] .=  __LINE__. $pdf_line.  '<br/>';  
          $pdf_data[] = explode(';', trim($pdf_line));
          $subtotal2++;
          $subtotal1++;
          $subtotal0++;
        } //$row[ 'standist' ] < $row[ 'standsoll' ]
      } //$row = $results->fetchRow()
      if ($notfirst) {
        if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
        $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", "", $regelsArr['bucket'], $h_a, "", "", "$subtotal2");
        $pdf_data[] = explode(';', trim($pdf_line));
      } //$notfirst
      $pdf->DataTable($pdf_header, $pdf_data, $regelsArr['cols']);
      $pdf_text .= CH_CR . "Document created on : " . $run . CH_CR;
      $pdf_text .= CH_CR . "Aantal records Open : " . $subtotal0++;
      $pdf_text .= CH_CR . "Aantal records : " . $regelsArr['n'];
      if ($regelsArr['search'] != "") $pdf_text .= CH_CR ."Selection : " . $regelsArr['search'] . CH_CR;
      if ($debug) $pdf_text .= CH_CR . $regelsArr['toegift'] . CH_CR;
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $pdf->ChapterBody($pdf_text);
      $pdf->AddPage();
      $pdf->ChapterTitle(2, 'Betaald');
      $pdf_text = '';
      $pdf_data = array();
      $query = "SELECT * FROM `" . $regelsArr['standen'] . "`" . $regelsArr['search_mysql'] . " ORDER BY `datumist`, `refer`, `name`";
      if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
      $results = $database->query($query);
      $notfirst = false;
      $subtotal0 = 0;
      $subtotal1 = 0;
      $subtotal2 = 0;
      $subtotal3 = 0;
      $regelsArr['bucket'] = "";
      $regelsArr['bucket1'] = "";
      $pdf_header = array(
        '',
        'naam',
        'omschrijving',
        'betaald'
      );
      while ($row = $results->fetchRow()) {  
 //       if ($row['standist'] >= $row['standsoll']) {
          if ($row['datumist'] >= "2010-01-01") {
//          if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';  
          if ($notfirst) {
            if ($regelsArr['bucket1'] != $row['datumist'] || $regelsArr['bucket'] != $row['refer']) {
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", "", $regelsArr['bucket1'], $regelsArr['bucket'], "", "", "$subtotal1");
              $pdf_data[] = explode(';', trim($pdf_line));
              $subtotal2 = 0;
              $subtotal1 = 0;
            } //$regelsArr[ 'bucket1' ] != $row[ 'datumsoll' ] || $regelsArr[ 'bucket' ] != $row[ 'refer' ]
          } //$notfirst
          $notfirst = true;
          $regelsArr['bucket1'] = $row['datumist'];
          $regelsArr['bucket'] = $row['refer'];
          $namefields = explode('|', $row['name'] . '|||||');
          $h_n = sprintf("%s %s %s ( %s )", $namefields[1], $namefields[2], $namefields[3], $row['id']);
          $h_a = sprintf("%s : %s", $row['refer'], $row['standist']);
          if ($row['standist']>0 ) {
            $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", $row['id'], $h_n, $h_a, $row['standist'], "", "");
            if ($debug) $msg['bug'] .=  __LINE__. $pdf_line.  '<br/>';  
            $pdf_data[] = explode(';', trim($pdf_line));
          } else {
            $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", $row['id'], $h_n, $h_a, $row['standist'], "", "");
            if ($debug) $msg['bug'] .=  __LINE__. $pdf_line.  '<br/>';  
            $pdf_data[] = explode(';', trim($pdf_line));
            $subtotal3++;
          }
          $subtotal1 = $subtotal1 + $row['standist'];
          $subtotal2++;
          $subtotal0++;
        } //$row[ 'standist' ] >= $row[ 'standsoll' ]
      } //$row = $results->fetchRow()
      if ($notfirst) {
        $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", "", $regelsArr['bucket1'], $regelsArr['bucket'], "", "", "$subtotal1");
        $pdf_data[] = explode(';', trim($pdf_line));
      } //$notfirst
      $pdf->DataTable($pdf_header, $pdf_data, $regelsArr['cols']);
      $pdf_data = array();
	  $subtotal0 = $subtotal0 - $subtotal3;
	  $pdf_text .= CH_CR.CH_CR.$regelsArr['filename_pdf'].CH_CR ;
      $pdf_text .= "Aantal records Betaald : " . $subtotal0++.CH_CR ;;
      $pdf_text .= "Aantal records Afgeschreven : " . $subtotal3++.CH_CR ;;  
	  $pdf_text .= "Aantal records : " . $regelsArr ['n'].CH_CR;
      $pdf_text .= "Document created on : " . str_replace("_", " ",$run ). CH_CR;
      if ( $debug ) $pdf_text .= CH_CR. "Version : " . $regelsArr ['module'].$regelsArr ['versie'] . CH_CR;
	  if (strlen($regelsArr[ 'search' ]) >1) $pdf_text .= CH_CR."Selection : " . $regelsArr[ 'search' ]; 	
      if ($debug) $pdf_text .= CH_CR . $regelsArr['toegift'] . CH_CR;
      $pdf->ChapterBody($pdf_text);
      $pdf->Output($place['pdf'] . $regelsArr['filename_pdf'], 'F');
      $msg[ 'inf' ] .= ' report created</br>';
      unset($query);
      $regelsArr['mode'] = 9;
      break;
    default:
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['mode'] = 9;
      break;
  } //$_POST[ 'command' ]
} //isset( $_POST[ 'command' ] )
elseif (isset($_GET['command'])) {
  switch ($_GET['command']) {
    case 'select':
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['mode'] = 7;
      break;
    case 'view':
    default:
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['mode'] = 7;
      break;
  } //$_GET[ 'command' ]
} //isset( $_GET[ 'command' ] )
else { // so standard display
  if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
  /******************
   * standard display job with or without search
   */
  if (!isset($query)) {
    // bepaal aantal records
    $query = "SELECT * FROM `" . $regelsArr['standen'] . "`" . $regelsArr['search_mysql'] . " ORDER BY `name`, `datumsoll`";
    if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
    $results = $database->query($query);
    $regelsArr['n'] = $results->numRows();
  } //!isset( $query )
}
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ($debug) $msg['bug'] .= __LINE__ . ' mode ' . $regelsArr['mode'] . ' ' . ((isset($query)) ? $query : "") . '</br></br>';
if (!isset($query)) $query = "SELECT * FROM `" . $regelsArr['standen'] . "`" . $regelsArr['search_mysql'] ." ORDER BY `name`, `datumsoll`";
/*
 * data collection
 */
switch ($regelsArr['mode']) {
  case 5: // betaling
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['descr'] .= $LINETEMP[99];
    $regelsArr['descr'] .= sprintf($LINETEMP[91], 'Betaling ', 'Gemeenschappelijke betaalgegevens', '', '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Betaal datum :'), sprintf($LINETEMP[89], 'datumist', 20, $regelsArr['today'], 'datum'), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Bedrag: '), sprintf($LINETEMP[88], 'standist', 20, $regelsArr['xstandsoll'], 'bedrag'), '', '');
    $select_all=false;
    if ($_POST['selection']== "ALL") {
      $regelsArr['search_mysql'] = "";
      $select_all=true;
    } 
    $query = "SELECT * FROM `" . $regelsArr['standen'] . "` " . $regelsArr['search_mysql'] . " ORDER BY `refer` DESC, `datumist` ASC, `name` ASC";
    if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
    $results = $database->query($query);
    $tint = false;

    while ($row = $results->fetchRow()) {
      if ($tint) {
        $hulp = $MOD_GSMOFF['line_color'][2];
        $tint = false;
      } //$tint
      else {
        $hulp = "";
        $tint = true;
      }
      if ($row['datumist'] < "2010-00-00") {
        $regelsArr['descr'] .= sprintf($LINETEMP[90], $hulp, sprintf($LINETEMP[93], str_replace('|', ' ', $row['name']), $row['id'], '', ''), $row['refer'], $row['datumist'], $row['standsoll'] . " / " . $row['standist'], '');
      } //$row[ 'datumist' ] < "2010-00-00"
      else {
        $regelsArr['descr'] .= sprintf($LINETEMP[90], $hulp, str_replace('|', ' ', $row['name']), $row['refer'], sprintf($ICONTEMP[23], $row['id'], $row['datumist']), $row['standsoll'] . " / " . $row['standist'], '');
      }
    } //$row = $results->fetchRow()
    break;
  case 6: // toevoegen
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['descr'] .= $LINETEMP[99];
    $regelsArr['descr'] .= sprintf($LINETEMP[91], 'Invoer ', 'gemeenschappelijke ', 'gegevens', '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Omschrijving :'), sprintf($LINETEMP[88], 'omschrijving', 45, $regelsArr['xomschrijving'], 'omschrijving'), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Project :'), sprintf($LINETEMP[88], 'refer', 15, $regelsArr['xrefer'], 'project'), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Vervaldatum :'), sprintf($LINETEMP[89], 'datumsoll', 20, $regelsArr['xdatumsoll'], 'datum'), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Bedrag te betalen : '), sprintf($LINETEMP[88], 'standsoll', 20, $regelsArr['xstandsoll'], 'bedrag'), '', '');
    $select_all=false;
    if (isset($_POST['selection']) && strlen($_POST['selection']) >= 2) { 
      if ($_POST['selection']== "ALL") {
        $regelsArr['search_mysql'] = "";
        $select_all=true;
      } else {
        $regelsArr['search_mysql'] = " WHERE  `zoek` LIKE '" . "%" . str_replace(' ', '%', trim($_POST['selection'])) . "%" . "'";
      }
    } else {
      $regelsArr['search_mysql'] = "";
    }
    $query = "SELECT * FROM `" . $regelsArr['table_adres'] . "`" . $regelsArr['search_mysql'] . " ORDER BY `name`";
    if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
    $results = $database->query($query);
    $tint = false;
    while ($row = $results->fetchRow()) {
      //is deze nog lid ?
      $needed = false;
      $project = false;
      if ($row['sinds'] == '1970-01-01') {
        $lid = " -- ";
        $needed = false;
      } //$row[ 'sinds' ] == '1970-01-01'
      elseif ($row['sinds'] != '1970-01-01' && $row['eind'] <= '1970-01-01') {
        $lid = "active";
        $needed = true;
      } //$row[ 'sinds' ] != '1970-01-01' && $row[ 'eind' ] <= '1970-01-01'
      else {
        $lid = "stopped (" . $row['eind'] . ")";
        $needed = false;
      }
      $query2 = "SELECT * FROM `" . $regelsArr['standen'] . "` WHERE `adresid`= '" . $row['id'] . "' AND `refer`= '" . $regelsArr['xrefer'] . "' AND `standtype`= '".$regelsArr['standtype']."'";
      if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query2 . ' <br/>';
      $result2 = $database->query($query2);
      if (isset($result2) && $result2->numRows() >= 1) {
        $project = true;
        if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      } //!$result2 || $result2->numRows() >= 1
      $straat = explode("56", str_replace('|', ' ', $row['adres']));
      if ($tint) {
        $hulp = $MOD_GSMOFF['line_color'][2];
        $tint = false;
      } else {
        $hulp = "";
        $tint = true;
      }
      if ($project) {
        $regelsArr['descr'] .= sprintf($LINETEMP[90], $hulp, str_replace('|', ' ', $row['name']), str_replace('|', ' ', $straat[0]), (strpos($row['email'], "@") === false) ? "" : $row['email'], $lid);
      } elseif ($needed && $select_all) {
        $regelsArr['descr'] .= sprintf($LINETEMP[90], $hulp, sprintf($LINETEMP[95], str_replace('|', ' ', $row['name']), $row['id'], '', ''), str_replace('|', ' ', $straat[0]), (strpos($row['email'], "@") === false) ? "" : $row['email'], $lid);
      } elseif ($needed) {
        $regelsArr['descr'] .= sprintf($LINETEMP[90], $hulp, sprintf($LINETEMP[93], str_replace('|', ' ', $row['name']), $row['id'], '', ''), str_replace('|', ' ', $straat[0]), (strpos($row['email'], "@") === false) ? "" : $row['email'], $lid);
      } else {
        $regelsArr['descr'] .= sprintf($LINETEMP[90], $hulp, str_replace('|', ' ', $row['name']), str_replace('|', ' ', $straat[0]), (strpos($row['email'], "@") === false) ? "" : $row['email'], $lid);
      }
    } //$row = $results->fetchRow()
    break;
  case 7: // view
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['descr'] .= $LINETEMP[99];
    $namefields = explode('|', $row['name'] . '|||||');
    $adresfields = explode('|', $row['adres'] . '|||||');
    $regelsArr['descr'] .= sprintf($LINETEMP[91], 'Algemene gegevens', '', '', '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Naam : '), str_replace('|', " ", $regelsArr['xname']), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Adres : '), $adresfields[0] . '<br>' . $adresfields[1] . '<br>' . $adresfields[2] . '<br>' . $adresfields[3], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Tel nr : '), $row['contact'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('E-mail : '), (strpos($row['email'], "@") === false) ? "" : $row['email'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Add. Info '), $row['info'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Bank ref : '), $row['bank'], '', '');
    if ($row['sinds'] != '2000-01-01')
      $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Start Lid : '), $row['sinds'], '', '');
    if ($row['eind'] != '0000-00-00')
      $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Einde lid : '), $row['eind'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Aantekening '), $row['aant'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[91], 'Deze betaling', '', '', '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Omschrijving :'), $row['omschrijving'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst(' '), $row['refer'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Vervaldatum :'), $row['datumsoll'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Bedrag :'), $row['standsoll'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Betaaldatum :'), ($row['datumist'] == "0000-00-00" || $row['datumist'] == "1970-01-01") ? "open" : $row['datumist'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Betaald :'), $row['standist'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Aantekening :'), $row['comment'], '', '');
    break;
  case 8: // modify
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['descr'] .= $LINETEMP[99];
    $namefields = explode('|', $row['name'] . '|||||');
    $adresfields = explode('|', $row['adres'] . '|||||');
    $regelsArr['descr'] .= sprintf($LINETEMP[91], 'Algemene gegevens', ' ', ' ', ' ', ' ');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Naam : '), str_replace('|', " ", $regelsArr['xname']), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Adres : '), $adresfields[0] . '<br>' . $adresfields[1] . '<br>' . $adresfields[2] . '<br>' . $adresfields[3], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Tel nr : '), $row['contact'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('E-mail : '), (strpos($row['email'], "@") === false) ? "" : $row['email'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Add. Info '), $row['info'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Bank ref : '), $row['bank'], '', '');
    if ($row['sinds'] != '2000-01-01')
      $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Start Lid : '), $row['sinds'], '', '');
    if ($row['eind'] != '0000-00-00')
      $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Einde lid : '), $row['eind'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Aantekening '), $row['aant'], '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[91], 'Rekening gegevens ', '', '', '', '');   
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Omschrijving :'), sprintf($LINETEMP[88], 'omschrijving', 45, $regelsArr['xomschrijving'], 'omschrijving'), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Project :'), sprintf($LINETEMP[88], 'refer', 15, $regelsArr['xrefer'], 'project'), '', '');  
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Vervaldatum : '), sprintf($LINETEMP[89], 'datumsoll', 20, $regelsArr['xdatumsoll'], 'datum'), '', '');    
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Bedrag : '), sprintf($LINETEMP[88], 'standsoll', 20, $regelsArr['xstandsoll'], 'bedrag'), '', '');   
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Betaald op : '), sprintf($LINETEMP[89], 'datumist', 20, $regelsArr['xdatumist'], 'datum'), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Bedrag betaald : '), sprintf($LINETEMP[88], 'standist', 20, $regelsArr['xstandist'], 'bedrag'), '', '');
    $regelsArr['descr'] .= sprintf($LINETEMP[98], ucfirst('Aantekening '), sprintf($LINETEMP[86], 'comment', 3, $regelsArr['xcomment'], 'aantekening'), '', '');
    break;
  case 9: // display
  default: // default list 
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['head'] .= $LINETEMP[99];
    $regelsArr['head'] .= sprintf($LINETEMP[92], $MOD_GSMOFF['line_color'][4], "Naam", '', "Project", "Betaald", "Bedrag");
    if (isset($query)) {
      $results = $database->query($query);
      if ($results && $results->numRows() > 0) {
        $tint = false;
        while ($row = $results->fetchRow()) {
          if ($tint) {
            $hulp = $MOD_GSMOFF['line_color'][2];
            $tint = false;
          } //$tint
          else {
            $hulp = "";
            $tint = true;
          }
          $regelsArr['descr'] .= sprintf($LINETEMP[90], 
            $hulp, 
            ($regelsArr['mode'] == 9) ? sprintf($LINETEMP[93], str_replace('|', ' ', $row[$regelsArr['fields'][2]]), $row['id'], '', '') : str_replace('|', ' ', $row[$regelsArr['fields'][2]]), 
            $row[$regelsArr['fields'][3]], 
            ($row[$regelsArr['fields'][4]] == "0000-00-00" || $row[$regelsArr['fields'][4]] == "1970-01-01") ? "open" : $row[$regelsArr['fields'][4]], 
            $row[$regelsArr['fields'][5]]
            );
        } //$row = $results->fetchRow()
      } //$results && $results->numRows() > 0
      else {
        $regelsArr['descr'] .= sprintf($LINETEMP[91], '', '', $MOD_GSMOFF['nodata'], '' . '', '');
      }
    } //isset( $query )
    break;
} //$regelsArr[ 'mode' ]
/*
 * selection
 */
switch ($regelsArr['mode']) {
  case 5: // betaling
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['select'] .= $LINETEMP[99];
    $regelsArr['select'] .= sprintf($LINETEMP[92], "", $ICONTEMP[13], $ICONTEMP[19], sprintf($LINETEMP[88], "selection", 20, (isset($_POST['selection'])) ? $_POST['selection'] : "", "zoek"), $ICONTEMP[19], $ICONTEMP[2]);
    break;
  case 6: // view
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['select'] .= $LINETEMP[99];
    $regelsArr['select'] .= sprintf($LINETEMP[92], "", $ICONTEMP[19], $ICONTEMP[5], sprintf($LINETEMP[88], "selection", 20, (isset($_POST['selection'])) ? $_POST['selection'] : "", "zoek"), $ICONTEMP[19], $ICONTEMP[2]);
    break;
  case 7: // view
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['select'] .= $LINETEMP[99];
    if ($regelsArr['recid'] > 0)
      $regelsArr['select'] .= sprintf($LINETEMP[70], $regelsArr['recid']);
    $regelsArr['select'] .= sprintf($LINETEMP[92], "", $ICONTEMP[1], $ICONTEMP[19], $ICONTEMP[19], $ICONTEMP[19], $ICONTEMP[2]);
    break;
  case 8: // modify
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['select'] .= $LINETEMP[99];
    $regelsArr['select'] .= sprintf($LINETEMP[92], "", $ICONTEMP[4], $ICONTEMP[19], $ICONTEMP[19], $ICONTEMP[6], $ICONTEMP[2]);
    break;
  case 9: // display
  default: // default
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['select'] .= $LINETEMP[99];
    $regelsArr['select'] .= sprintf($LINETEMP[92], "", $ICONTEMP[12], $ICONTEMP[3], sprintf($LINETEMP[88], "selection", 20, (isset($_POST['selection'])) ? $_POST['selection'] : "", "zoek"), $ICONTEMP[11], (isset($regelsArr['filename_pdf'])) ? sprintf($ICONTEMP[18], "", $regelsArr['filename_pdf']) : $ICONTEMP[1]);
    break;
} //$regelsArr[ 'mode' ]
/*
 * display
 */
switch ($regelsArr['mode']) {
  case 9: // display
  default: // default
    $temp = sha1(MICROTIME() . $_SERVER['HTTP_USER_AGENT']);
    $_SESSION['page_h'] = $temp;
    $parseViewArray = array(
      'header' => strtoupper($regelsArr['project']),
      'page_id' => $page_id,
      'section_id' => $section_id,
      'kopregels' => $regelsArr['head'],
      'description' => $regelsArr['descr'],
      'message' => message($msg, $debug),
      'selection' => $regelsArr['select'],
      'return' => CH_RETURN,
      'module' => $regelsArr['module'],
      'memory' => $regelsArr['memory'] . "|",
      'toegift' => $regelsArr['toegift'],
      'recid' => $regelsArr['recid'],
      'rapportage' => $regelsArr['rapport'],
      'hash' => $_SESSION['page_h']
    );
    $prout .= Gsm_prout ($TEMPLATE[ 2 ], $parseViewArray);
    break;
} //$regelsArr[ 'mode' ]
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?> 