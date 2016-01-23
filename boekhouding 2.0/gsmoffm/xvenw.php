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
  'mode' => 5,
  'module' => 'venw',
  // voor versie display
  'modulen' => 'xvenw',
  'versie' => ' v20160114 ',
  // table
  'app' => 'overzicht',
  'table' => CH_DBBASE . "_booking",
  'table_rek' => CH_DBBASE . "_rekening",
  'owner' => (isset($settingArr['logo'])) ? $settingArr['logo'] : '',
  // for display  
  'seq' => (isset($_POST['next'])) ? $regelsArr['seq'] = $_POST['next'] : 0,
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
  'toegift' => '',
  'rapport' => '',
  'memory' => '',
  'hash' => '',
  'recid' => '',
  //  'record_update' => false,
  //  'pdf_ok' => false,
  //  'edit_ok' => false,
  //  'add_ok' => false,
  // pdf
  //  'print_regels' => 1,  // ** relevance for layout
  // 'cols'=> array(55, 35, 35, 20, 20, 20),
  //  'leeg' => array( 1=>"",2=>"",3=>"",4=>"",5=>"",6=>"" ),
  'today_pf' => date("_Ymd_His"),
  // this document
  'Document' => 'Jaar Overzicht',
  'rekeningnummer' => '',
  'edit_run' => false,
  'cum_rek' => 0,
  'cum_group' => 0,
  'cum_srt' => 0,
  'cum_activa' => 0,
  'cum_resultaat' => 0,
  'rekening_type' => '',
  'show1_type' => '1',
  'show2_type' => '2',
  'period_begin' => '',
  'period_end' => '',
  'period_start' => '',
  //    'today'=> date( "Y-m-d" ),
  'Document' => 'Balans',
  'result' => true,
  'details' => false,
  'budget_exist' => true,
  'project' => false,
  'proj_ref' => '',
  'show_num' => '',
  'amt_line' => 0,
  'cum_line' => 0,
  'Vanaf' => (isset($settingArr['fjaar'])) ? date("Y", strtotime($settingArr['fjaar'])) . "-01-01" : date("Y") . "-01-01",
  'Totenmet' => date("Y-m-d")
);
$regelsArr['project'] = $regelsArr['app'] . ' - Overzicht';
//$regelsArr['filename_pdf'] = strtolower( $regelsArr[ 'project' ]) . '.pdf';
/*
 * Initial file data
 */
/*
 * Lay-out strings
 */
$MOD_GSMOFF['SUR_VENW'] = 'V en W';
$MOD_GSMOFF['SUR_COMPRESS_PERIOD'] = 'Overzicht periode';
$MOD_GSMOFF['TH_COMPRESS_PERIOD'] = 'Financieel verslag';
$MOD_GSMOFF['SUR_BAL'] = 'Balans';
$MOD_GSMOFF['SUR_BEDRAG'] = 'Bedrag';
$MOD_GSMOFF['SUR_DET'] = 'details : ';
$MOD_GSMOFF['SUR_NOTOK'] = 'Incorrect: Vanaf datum ';
$MOD_GSMOFF['SUR_OMS'] = 'Omschrijving';
$MOD_GSMOFF['SUR_PER'] = 'Periode : ';
$MOD_GSMOFF['SUR_REK'] = 'rek';
$MOD_GSMOFF['SUR_RES'] = 'Resultaat';
$MOD_GSMOFF['SUR_TOT'] = 'Totaal';
$MOD_GSMOFF['SUR_CUMM'] = 'Sub';
$MOD_GSMOFF['SUR_TOTENMET'] = 'tot';
$MOD_GSMOFF['SUR_TUS'] = 'Tussen rekening';
$MOD_GSMOFF['SUR_VAN'] = 'van';
$MOD_GSMOFF['SUR_VANAF'] = 'vanaf : ';
$MOD_GSMOFF['SUR_VENW'] = 'V en W';
$MOD_GSMOFF['SUR_NDATA'] = 'no records found';
$MOD_GSMOFF['INF_MESSAGE1'] = 'Een periode is verwijderd save the backup en de pdf file';
$MOD_GSMOFF['INF_WARNING1'] = 'Maak eerst een backup';
// icontemp 1-19 is defined in language module
// linetemp 1-19 is defined in language module
// template 0 is in scheduler module
// template 1 is in language module
$ICONTEMP[14] = '<input maxlength="10" size="10" type="text" name="select_van" value="%1$s" width="10" />%2$s' . CH_CR;
$ICONTEMP[15] = '<input maxlength="10" size="10" type="text" name="select_tot" value="%1$s" width="10" />' . CH_CR;
$LINETEMP[21] = '<colgroup><col width="6%"><col width="8%"><col width="50%"><col width="12%"><col width="12%"><col width="12%"></colgroup>';
$LINETEMP[22] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td><td>%7$s</td></tr>';
$LINETEMP[23] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td align="right">%5$s</td><td align="right">%6$s</td><td align="right">%7$s</td></tr>';
$ICONTEMP[21] = '<input class="' . $MOD_GSMOFF['tbl_icon2'][13] . '" name="command" type="submit" value="' . $MOD_GSMOFF['SUR_TUS'] . '" style="width: 100%;" />';
$ICONTEMP[22] = '<input class="' . $MOD_GSMOFF['tbl_icon2'][13] . '" name="command" type="submit" value="' . $MOD_GSMOFF['SUR_BAL'] . '" style="width: 100%;" />';
$ICONTEMP[23] = '<input class="' . $MOD_GSMOFF['tbl_icon2'][13] . '" name="command" type="submit" value="' . $MOD_GSMOFF['SUR_VENW'] . '" style="width: 100%;" />';
$ICONTEMP[26] = '<input type="checkbox" name="%2$s" value="yes" />%1$s';
/*
 *  boekingen, rekening data needed
 */
$query = "SELECT * FROM `" . $regelsArr['table_rek'] . "` ORDER BY `rekeningnummer`";
$message = __LINE__ . $MOD_GSMOFF['error2'] . $query . "</br>";
$results = $database->query($query);
if (!$results || $results->numRows() == 0)
  die($message);
$rekeningArray = array();
$rekeningtypeArray = array();
$budgetArray = array();
$offsetArray = array();
// select rekening naam, type budget and balans
while ($row = $results->fetchRow()) {
  $rekeningArray[$row['id']] = $row['rekeningnummer'] . " - " . $row['name'];
  $rekeningtypeArray[$row['id']] = $row['rekening_type'];
  $budgetArray[$row['id']] = $row['budget_a'];
  $offsetArray[$row['id']] = $row['balans'];
} //$row = $results->fetchRow()
$query = "SELECT * FROM `" . $regelsArr['table'] . "`";
$message = __LINE__ . $MOD_GSMOFF['error2'] . $query . "</br>";
$results = $database->query($query);
if (!$results || $results->numRows() == 0)
  die($message);
$first = true;
// find ealiest booking data
while ($row = $results->fetchRow()) {
  if ($first) {
    $first = false;
    $regelsArr['period_start'] = $row['booking_date'];
  } //$first
  if ($regelsArr['period_start'] > $row['booking_date'])
    $regelsArr['period_start'] = $row['booking_date'];
} //$row = $results->fetchRow()
// beyond this point: booking data is available 
// laagste datum gevonden bepaal nu 1-1 van dat jaar of ingegeven datum en de laatste dag van het jaar
$regelsArr['period_begin'] = (isset($_POST['select_van'])) ? $regelsArr['period_begin'] = $_POST['select_van'] : date("Y", strtotime($regelsArr['period_start'])) . "-01-01";
$regelsArr['period_end'] = (isset($_POST['select_tot'])) ? $regelsArr['period_end'] = $_POST['select_tot'] : date("Y", strtotime($regelsArr['period_begin'])) . "-12-31";
unset($query);
/*
 * debug
 */
if ($debug) {
  Gsm_debug($settingArr, __LINE__);
  Gsm_debug($regelsArr, __LINE__);
  Gsm_debug($_POST, __LINE__);
  Gsm_debug($_GET, __LINE__);
  Gsm_debug($place, __LINE__);
  Gsm_debug($budgetArray, __LINE__);
  Gsm_debug($offsetArray, __LINE__);
  Gsm_debug($rekeningArray, __LINE__);
  Gsm_debug($rekeningtypeArray, __LINE__);
} //$debug
/*
 * some job to do
 */
if (isset($_POST['command'])) {
  switch ($_POST['command']) {
    case $MOD_GSMOFF['SUR_VENW']:
      $regelsArr['Document'] = $MOD_GSMOFF['SUR_VENW'];
      $regelsArr['Vanaf'] = $_POST['select_van'];
      $regelsArr['Totenmet'] = $_POST['select_tot'];;
      $regelsArr['show1_type'] = '4';
      $regelsArr['show2_type'] = '5';
      if (isset($_POST['details']) && $_POST['details'] == 'yes')
        $regelsArr['details'] = true;
      if (isset($_POST['pdf']) && $_POST['pdf'] == 'yes')
        $regelsArr['filename_pdf'] = sprintf('%s %s %s.pdf', str_replace('.', ' ', $settingArr['company']), "Resultaat", $regelsArr['Totenmet'] );
      $regelsArr['mode'] = 6;
      break;
    case $MOD_GSMOFF['SUR_BAL']:
      $regelsArr['Document'] = $MOD_GSMOFF['SUR_BAL'];
      $regelsArr['Totenmet'] = $_POST['select_tot'];;
      $regelsArr['show1_type'] = '1';
      $regelsArr['show2_type'] = '2';
      if (isset($_POST['details']) && $_POST['details'] == 'yes')
        $regelsArr['details'] = true;
      if (isset($_POST['pdf']) && $_POST['pdf'] == 'yes')
        $regelsArr['filename_pdf'] = sprintf('%s %s %s.pdf', str_replace('.', ' ', $settingArr['company']), "Balans" ,$regelsArr['Totenmet']);
      $regelsArr['mode'] = 7;
      break;
    case $MOD_GSMOFF['SUR_TUS']:
      $regelsArr['Document'] = $MOD_GSMOFF['SUR_TUS'];
      $regelsArr['Vanaf'] = $_POST['select_van'];
      $regelsArr['Totenmet'] = $_POST['select_tot'];;
      $regelsArr['show1_type'] = '7';
      $regelsArr['show2_type'] = '7';
      if (isset($_POST['details']) && $_POST['details'] == 'yes')
        $regelsArr['details'] = true;
      if (isset($_POST['pdf']) && $_POST['pdf'] == 'yes')
        $regelsArr['filename_pdf'] = sprintf('%s %s %s.pdf', str_replace('.', ' ', $settingArr['company']), "Tussen" , $regelsArr['Totenmet']);
      $regelsArr['mode'] = 8;
      break;
    default:
      $msg['inf'] .= __LINE__ . " get: " . $_POST['command'] . '<br/>';
      $regelsArr['mode'] = 9;
      break;
  } //$_POST[ 'command' ]
} //isset( $_POST[ 'command' ] )
elseif (isset($_GET['command'])) {
  switch ($_GET['command']) {
    default:
      $regelsArr['mode'] = 9;
      break;
  } //$_GET[ 'command' ]
} //isset( $_GET[ 'command' ] )
  elseif (isset($_POST['selection']) && strlen($_POST['selection']) >= 1) {
  $regelsArr['search'] = trim($_POST['selection']);
  $query = "SELECT * FROM `" . $regelsArr['table_rek'] . "` WHERE  `rekeningnummer`= '" . $regelsArr['search'] . "'";
  $r_results = $database->query($query);
  if ($r_results && $r_results->numRows() > 0) {
    $r_row = $r_results->fetchRow();
    $regelsArr['details'] = false;
    $regelsArr['result'] = false;
    $regelsArr['Document'] = $regelsArr['search'];
    $regelsArr['show1_type'] = $r_row['rekening_type'];
    $regelsArr['show2_type'] = $r_row['rekening_type'];
  } //$r_results && $r_results->numRows() > 0
} //isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 1
if ($regelsArr['details']) {
  $msg['bug'] .= 'EN: details=yes' . '</br>';
} //$regelsArr[ 'details' ]
/*
 * initiatie pdf before starting the normal process
 */
$pdf_text = '';
$pdf_data = array();
if (isset($regelsArr['filename_pdf'])) {
  require_once( $place_incl . 'pdf.inc' ); 
  $pdf = new PDF();
  global $title;
  global $owner;
  $owner = $regelsArr['owner'];
  $title = $regelsArr['project'];
  $run = date("Ymd_His");
  $pdf->AliasNbPages();
  $pdf->AddPage();
} //isset( $regelsArr[ 'filename_pdf' ] )
/*
 * display  Header
 */
switch ($regelsArr['mode']) {
  case 5:
    $regelsArr['head'] .= $LINETEMP[21];
    $regelsArr['head'] .= sprintf($LINETEMP[22], "", "", '<strong>' . $regelsArr['Document'] . '</strong>', '<strong>' . $settingArr['company'] . '</strong>', "Datum :", $regelsArr['Totenmet'], '');
    $regelsArr['head'] .= sprintf($LINETEMP[22], $MOD_GSMOFF['line_color'][4], '', $MOD_GSMOFF['SUR_REK'], $MOD_GSMOFF['SUR_OMS'], '', '', $MOD_GSMOFF['SUR_BEDRAG']);
    $title = sprintf("%s  %s, periode: %s-%s", $regelsArr['Document'], $settingArr['company'], $regelsArr['Vanaf'], $regelsArr['Totenmet']);
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
  case 6:
    if ($regelsArr['budget_exist']) {
      $help_b = "Budget";
    } //$regelsArr[ 'budget_exist' ]
    else {
      $help_b = "";
    }
    $regelsArr['head'] .= $LINETEMP[21];
    $regelsArr['head'] .= sprintf($LINETEMP[22], "", "", '<strong>' . $regelsArr['Document'] . '</strong>', '<strong>' . $settingArr['company'] . '</strong>', $MOD_GSMOFF['SUR_PER'], $regelsArr['Vanaf'], $regelsArr['Totenmet']);
    $regelsArr['head'] .= sprintf($LINETEMP[22], $MOD_GSMOFF['line_color'][4], '', $MOD_GSMOFF['SUR_REK'], $MOD_GSMOFF['SUR_OMS'], $help_b, '', $MOD_GSMOFF['SUR_BEDRAG']);
    $title = sprintf("%s  %s, periode: %s  -  %s", $regelsArr['Document'], $settingArr['company'], $regelsArr['Vanaf'], $regelsArr['Totenmet']);
    $pdf_text .= CH_CR . $title . CH_CR . CH_CR;
    $pdf_header = array(
      $MOD_GSMOFF['SUR_REK'],
      $MOD_GSMOFF['SUR_OMS'],
      $help_b,
      '',
      '',
      $MOD_GSMOFF['SUR_BEDRAG']
    );
    break;
  case 7:
    $regelsArr['head'] .= $LINETEMP[21];
    $regelsArr['head'] .= sprintf($LINETEMP[22], "", "", '<strong>' . $regelsArr['Document'] . '</strong>', '<strong>' . $settingArr['company'] . '</strong>', "Datum :", $regelsArr['Totenmet'], '');
    $regelsArr['head'] .= sprintf($LINETEMP[22], $MOD_GSMOFF['line_color'][4], '', $MOD_GSMOFF['SUR_REK'], $MOD_GSMOFF['SUR_OMS'], '', '', $MOD_GSMOFF['SUR_BEDRAG']);
    $title = sprintf("%s %s, datum: %s", $regelsArr['Document'], $settingArr['company'], $regelsArr['Totenmet']);
    $pdf_text .= CH_CR . $title . CH_CR . CH_CR;
    $pdf_header = array(
      $MOD_GSMOFF['SUR_REK'],
      $MOD_GSMOFF['SUR_OMS'],
      '',
      '',
      '',
      $MOD_GSMOFF['SUR_BEDRAG']
    );
    break;
  case 8:
    $regelsArr['head'] .= $LINETEMP[21];
    $regelsArr['head'] .= sprintf($LINETEMP[22], "", "", '<strong>' . $regelsArr['Document'] . '</strong>', '<strong>' . $settingArr['company'] . '</strong>', $MOD_GSMOFF['SUR_PER'], $regelsArr['Vanaf'], $regelsArr['Totenmet']);
    $regelsArr['head'] .= sprintf($LINETEMP[22], $MOD_GSMOFF['line_color'][4], '', $MOD_GSMOFF['SUR_REK'], $MOD_GSMOFF['SUR_OMS'], '', '', $MOD_GSMOFF['SUR_BEDRAG']);
    $title = sprintf("%s  %s, periode: %s-%s", $regelsArr['Document'], $settingArr['company'], $regelsArr['Vanaf'], $regelsArr['Totenmet']);
    $pdf_text .= CH_CR . $title . CH_CR . CH_CR;
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
    $pdf_text .= '--';
    $pdf_header = array(
      '',
      '',
      '',
      '',
      '',
      ''
    );
    break;
} //$regelsArr[ 'mode' ]
if (isset($regelsArr['filename_pdf'])) {
  $pdf->ChapterTitle(1, $title);
  $pdf->SetFont('Arial', '', 10);
  $pdf_text .= CH_CR . $settingArr['company'];
  $pdf_text .= CH_CR . "Document created on : " . str_replace("_", " ",$run ). CH_CR;
  if ( $debug ) $pdf_text .= CH_CR. "Version : " . $regelsArr ['module'].$regelsArr ['versie'] . CH_CR;
  if (strlen($regelsArr[ 'search' ]) >1) $pdf_text .= CH_CR."Selection : " . $regelsArr[ 'search' ]; 
  $pdf_text .= CH_CR . $regelsArr['filename_pdf'];
} //isset( $regelsArr[ 'filename_pdf' ] )
switch ($regelsArr['mode']) {
  default: // default list
    $regelsArr['descr'] .= $LINETEMP[21];
    $regelsArr['n'] = 0; // of de staart verwerkt met worden
    $regelsArr['m'] = 0; // voor regel om regel andere kleur
    // rekeningen in de juiste volgorde inlezen
    $query = "SELECT * FROM `" . $regelsArr['table_rek'] . "` ORDER BY `rekening_type`, `rekeningnummer`";
    $r_results = $database->query($query);
    if ($r_results && $r_results->numRows() > 0) {
      while ($r_row = $r_results->fetchRow()) {
        if ($r_row['rekening_type'] == $regelsArr['show1_type'] || $r_row['rekening_type'] == $regelsArr['show2_type']) {
          if ($debug)
            $msg['bug'] .= __LINE__ . "|" . $r_row['rekeningnummer'] . '</br>';
          // wijzigt 1e positie rekening nummer 
          if (substr($regelsArr['rekeningnummer'], 0, 1) != substr($r_row['rekeningnummer'], 0, 1)) {
            // sluit voorgaande af als er regels zijn
            // regel e.g. * Kl 4: Kortlopende schulden en vorderingen 
            if ($regelsArr['n'] != 0) {
              $regelsArr['cum_group'] += $regelsArr['cum_rek'];
              $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '..', '', '<b>' . $MOD_GSMOFF['grootboek'][substr($regelsArr['rekeningnummer'], 0, 1)] . '</b>', '', '', Gsm_opmaak($regelsArr['cum_group'], 1));
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '..', $MOD_GSMOFF['grootboek'][substr($regelsArr['rekeningnummer'], 0, 1)], '', '', '', Gsm_opmaak($regelsArr['cum_group'], 2));
              $pdf_data[] = explode(';', trim($pdf_line));
              $regelsArr['cum_srt'] += $regelsArr['cum_group'];
              // resetten cumul values            
              $regelsArr['cum_group'] = 0;
              $regelsArr['cum_rek'] = 0;
              // blanco regel volgt
              $regelsArr['descr'] .= sprintf($LINETEMP[22], '', '&nbsp;', '', '', '', '', '');
              $regelsArr['m'] = 0;
            } //$regelsArr[ 'n' ] != 0
            // wijzigt het rekening type 
            if ($regelsArr['rekening_type'] != $r_row['rekening_type']) {
              // sluit voorgaande af als er regels zijn
              if ($regelsArr['n'] != 0) {
                $regelsArr['cum_srt'] += $regelsArr['cum_group'];
                if ($regelsArr['rekening_type'] == 2) {
                  $regelsArr['cum_resultaat'] = $regelsArr['cum_srt'] - $regelsArr['cum_activa'];
                  $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '', '1', '<b>' . $MOD_GSMOFF['SUR_RES'] . '</b>', '', '', Gsm_opmaak($regelsArr['cum_resultaat'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_RES'], '', '', '', Gsm_opmaak($regelsArr['cum_resultaat'], 1));
                  $pdf_data[] = explode(';', trim($pdf_line));
                  $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '2', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF_TYPE[$regelsArr['rekening_type']] . '</strong>', '', '', Gsm_opmaak($regelsArr['cum_activa'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']], '', '', '', Gsm_opmaak($regelsArr['cum_activa'], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                } //$regelsArr[ 'rekening_type' ] == 2
                else {
                  // regel Totaal activa
                  $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']] . '</strong>', '', '', Gsm_opmaak($regelsArr['cum_srt'], 1));
                  $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']], '', '', '', Gsm_opmaak($regelsArr['cum_srt'], 2));
                  $pdf_data[] = explode(';', trim($pdf_line));
                }
                if ($regelsArr['rekening_type'] == 1)
                  $regelsArr['cum_activa'] = $regelsArr['cum_srt'];
                $regelsArr['cum_resultaat'] += $regelsArr['cum_srt'];
                $regelsArr['cum_srt'] = 0;
                $regelsArr['descr'] .= sprintf($LINETEMP[22], '', '&nbsp;', '', '', '', '', '');
                $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', '', '', '', '');
                $pdf_data[] = explode(';', trim($pdf_line));
                $regelsArr['n'] = 0;
              } //$regelsArr[ 'n' ] != 0
              $regelsArr['rekening_type'] = $r_row['rekening_type'];
              $regelsArr['descr'] .= sprintf($LINETEMP[22], $MOD_GSMOFF['line_color'][4], '', '', '<strong>' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']] . '</strong>', '', '', '');
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']], '', '', '', '');
              $pdf_data[] = explode(';', trim($pdf_line));
              $regelsArr['m'] = 0;
            } //$regelsArr[ 'rekening_type' ] != $r_row[ 'rekening_type' ]
            $regelsArr['rekeningnummer'] = $r_row['rekeningnummer'];
          } //substr( $regelsArr[ 'rekeningnummer' ], 0, 1 ) != substr( $r_row[ 'rekeningnummer' ], 0, 1 )
          $regelsArr['cum_group'] += $regelsArr['cum_rek'];
          $regelsArr['cum_rek'] = (isset($r_row['balans'])) ? $r_row['balans'] : 0;
          if ($regelsArr['cum_rek'] != 0) {
            if ($regelsArr['details'] || $regelsArr['search'] == $r_row['rekeningnummer']) {
              $regelsArr['descr'] .= sprintf($LINETEMP[23], $col = $MOD_GSMOFF['line_color'][2], '4', '', $r_row['balans_date'] . ' - Openings Balans', '--', Gsm_opmaak($regelsArr['cum_rek'], 1), "");
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', '', $r_row['balans_date'] . ' - Openings Balans', '--', Gsm_opmaak($regelsArr['cum_rek'], 2),"");
              $pdf_data[] = explode(';', trim($pdf_line));
            } //$regelsArr[ 'details' ] || $regelsArr[ 'search' ] == $r_row[ 'rekeningnummer' ]
          } //$regelsArr[ 'cum_rek' ] != 0
          $col = ($regelsArr['m'] % 2 == 0) ? $col = $MOD_GSMOFF['line_color'][2] : "";
          $msql_search = "`booking_date` >= '" . $regelsArr['Vanaf'] . "' AND `booking_date` <= '" . $regelsArr['Totenmet'] . "' ";
          $msql_search .= " AND ( `debet_id`= '" . $r_row['id'] . "' OR `tegen1_id`= '" . $r_row['id'] . "' OR  `tegen2_id`= '" . $r_row['id'] . "' )";
          $query = "SELECT * FROM `" . $regelsArr['table'] . "` WHERE " . $msql_search . " ORDER BY `booking_date`, `project`";
          $results = $database->query($query);
          if ($results && $results->numRows() > 0) {
            $regelsArr['p'] = 0;
            while ($row = $results->fetchRow()) {
              $hulp = $row['booking_date'] . ' - ';
              $hulp .= (strlen($row['name']) > 2) ? $row['name'] . ' - ' : '';
              $hulp .= (strlen($row['project']) > 2) ? $row['project'] : '';
              $regelsArr['p']++;
              $col = ($regelsArr['m'] % 2 == 0) ? $col = $MOD_GSMOFF['line_color'][2] : "";
              if ($row['debet_id'] == $r_row['id']) {
                $regelsArr['cum_rek'] += $row['debet_amount'];
                if ($regelsArr['details'] || $regelsArr['search'] == $r_row['rekeningnummer']) {
                  if ($row['debet_amount'] <> 0) {
                    $regelsArr['descr'] .= sprintf($LINETEMP[23], $col, '', '', $hulp, Gsm_opmaak($row['debet_amount'], 1), Gsm_opmaak($regelsArr['cum_rek'], 1), '');
                    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $hulp, '', Gsm_opmaak($row['debet_amount'], 2), Gsm_opmaak($regelsArr['cum_rek'],2), '');
                    $pdf_data[] = explode(';', trim($pdf_line));
                  } //$row[ 'debet_amount' ] <> 0
                } //$regelsArr[ 'details' ] || $regelsArr[ 'search' ] == $r_row[ 'rekeningnummer' ]
              } //$row[ 'debet_id' ] == $r_row[ 'id' ]
              if ($row['tegen1_id'] == $r_row['id']) {
                $hulp_amt = $row['tegen1_amount'] * $MOD_GSMOFF['rek_type_sign'][$rekeningtypeArray[$row['debet_id']]] * $MOD_GSMOFF['rek_type_sign'][$rekeningtypeArray[$row['tegen1_id']]] * -1;
                $regelsArr['cum_rek'] += $hulp_amt;
                if ($regelsArr['details'] || $regelsArr['search'] == $r_row['rekeningnummer']) {
                  if ($hulp_amt <> 0) { // 20140423 onderdruk 0-regels
                    $regelsArr['descr'] .= sprintf($LINETEMP[23], $col, '', '', $hulp, Gsm_opmaak($hulp_amt, 1), Gsm_opmaak($regelsArr['cum_rek'], 1), '');
                    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $hulp, '', Gsm_opmaak($hulp_amt, 2), Gsm_opmaak($regelsArr['cum_rek'], 2), '');
                    $pdf_data[] = explode(';', trim($pdf_line));
                  } // 20140425 onderdruk 0-regels                                        
                } //$regelsArr[ 'details' ] || $regelsArr[ 'search' ] == $r_row[ 'rekeningnummer' ]
              } //$row[ 'tegen1_id' ] == $r_row[ 'id' ]
              if ($row['tegen2_id'] == $r_row['id']) {
                $hulp_amt = $row['tegen2_amount'] * $MOD_GSMOFF['rek_type_sign'][$rekeningtypeArray[$row['debet_id']]] * $MOD_GSMOFF['rek_type_sign'][$rekeningtypeArray[$row['tegen2_id']]] * -1;
                $regelsArr['cum_rek'] += $hulp_amt;
                if ($regelsArr['details'] || $regelsArr['search'] == $r_row['rekeningnummer']) {
                  if ($hulp_amt <> 0) {
                    $regelsArr['descr'] .= sprintf($LINETEMP[23], $col, '', '', $hulp, Gsm_opmaak($hulp_amt, 1), Gsm_opmaak($regelsArr['cum_rek'], 1), '');
                    $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $hulp, '', Gsm_opmaak($hulp_amt, 2), Gsm_opmaak($regelsArr['cum_rek'], 2), '');
                    $pdf_data[] = explode(';', trim($pdf_line));
                  } //$hulp_amt <> 0
                } //$regelsArr[ 'details' ] || $regelsArr[ 'search' ] == $r_row[ 'rekeningnummer' ]
              } //$row[ 'tegen2_id' ] == $r_row[ 'id' ]
            } //$row = $results->fetchRow()
          } //$results && $results->numRows() > 0
          $regelsArr['n']++;
          $regelsArr['m']++;
          if ($regelsArr['budget_exist']) {
            if ($regelsArr['cum_rek'] != 0) {
              if ($regelsArr['show1_type'] == '4' || $regelsArr['show1_type'] == '5') {
                $help_b1 = Gsm_opmaak($budgetArray[$r_row['id']], 3);
                if ($budgetArray[$r_row['id']] == 0 && $regelsArr['cum_rek'] <> 0) {
                  $help_b2 = "***";
                } //$budgetArray[ $r_row[ 'id' ] ] == 0 && $regelsArr[ 'cum_rek' ] <> 0
                elseif ($budgetArray[$r_row['id']] == 0 && $regelsArr['cum_rek'] == 0) {
                  $help_b2 = '';
                } //$budgetArray[ $r_row[ 'id' ] ] == 0 && $regelsArr[ 'cum_rek' ] == 0
                else {
                  $help_b2 = Gsm_opmaak((100 * $regelsArr['cum_rek'] / $budgetArray[$r_row['id']]), 9);
                }
              } //$regelsArr[ 'show1_type' ] == '4' || $regelsArr[ 'show1_type' ] == '5'
              else {
                $help_b1 = "";
                $help_b2 = "";
              }
              $regelsArr['descr'] .= sprintf($LINETEMP[23], $col = $MOD_GSMOFF['line_color'][2], '', '<b>' . $r_row['rekeningnummer'] . '</b>', '<b>' . $r_row['name'] . '</b>', $help_b1, $help_b2, Gsm_opmaak($regelsArr['cum_rek'], 1));
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", $r_row['rekeningnummer'], $r_row['name'], $help_b1, $help_b2, '', Gsm_opmaak($regelsArr['cum_rek'], 2));
              $pdf_data[] = explode(';', trim($pdf_line));
            } //$regelsArr[ 'cum_rek' ] != 0
          } //$regelsArr[ 'budget_exist' ]
          else {
            if ($regelsArr['cum_rek'] != 0) {
              $regelsArr['descr'] .= sprintf($LINETEMP[23], $col = $MOD_GSMOFF['line_color'][2], '', '<b>' . $r_row['rek_num'] . '</b>', '<b>' . $r_row['rek_name'] . '</b>', '', '', Gsm_opmaak($regelsArr['cum_rek'], 1));
              $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", $r_row['rekekeningnummer'], $r_row['name'], '', '', '', Gsm_opmaak($regelsArr['cum_rek'], 2));
              $pdf_data[] = explode(';', trim($pdf_line));
            } //$regelsArr[ 'cum_rek' ] != 0
          }
        } //$r_row[ 'rekening_type' ] == $regelsArr[ 'show1_type' ] || $r_row[ 'rekening_type' ] == $regelsArr[ 'show2_type' ]
        else {
        }
      } //$r_row = $r_results->fetchRow()
      if ($regelsArr['n'] != 0) {
        // regel e.g. Kl 4: Kortlopende schulden en vorderingen 
        $regelsArr['cum_group'] += $regelsArr['cum_rek'];
        $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '..', '', '<b>' . $MOD_GSMOFF['grootboek'][substr($regelsArr['rekeningnummer'], 0, 1)] . '</b>', '', '', Gsm_opmaak($regelsArr['cum_group'], 1));
        $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '..', $MOD_GSMOFF['grootboek'][substr($regelsArr['rekeningnummer'], 0, 1)], '', '', '', Gsm_opmaak($regelsArr['cum_group'], 2));
        $pdf_data[] = explode(';', trim($pdf_line));
        $regelsArr['descr'] .= sprintf($LINETEMP[22], '', '&nbsp;', '', '', '', '', '');
        $regelsArr['cum_srt'] += $regelsArr['cum_group'];
        $regelsArr['m'] = 0;
      } //$regelsArr[ 'n' ] != 0
      if ($regelsArr['rekening_type'] == 2) {
        // regel Resultaat Balans
        // regel Totaal passiva
        $regelsArr['cum_resultaat'] = $regelsArr['cum_activa'] - $regelsArr['cum_srt'];
        if ($regelsArr['result']) {
          $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '', '', '<b>' . $MOD_GSMOFF['SUR_RES'] . '</b>', '', '', Gsm_opmaak($regelsArr['cum_resultaat'], 1));
          $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_RES'], '', '', '', Gsm_opmaak($regelsArr['cum_resultaat'], 2));
          $pdf_data[] = explode(';', trim($pdf_line));
          $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']] . '</strong>', '', '', Gsm_opmaak($regelsArr['cum_activa'], 1));
          $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']], '', '', '', Gsm_opmaak($regelsArr['cum_activa'], 2));
          $pdf_data[] = explode(';', trim($pdf_line));
        } //$regelsArr[ 'result' ]
      } //$regelsArr[ 'rekening_type' ] == 2
      elseif ($regelsArr['rekening_type'] == 5) {
        // regel Totaal Inkomsten
        // regel Resultaat V en W
        $regelsArr['cum_resultaat'] = $regelsArr['cum_srt'] - $regelsArr['cum_resultaat'];
        if ($regelsArr['result']) {
          $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']] . '</strong>', '', '', Gsm_opmaak($regelsArr['cum_srt'], 1));
          $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']], '', '', '', Gsm_opmaak($regelsArr['cum_srt'], 2));
          $pdf_data[] = explode(';', trim($pdf_line));
          $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '', '', '<b>' . $MOD_GSMOFF['SUR_RES'] . '</b>', '', '', Gsm_opmaak($regelsArr['cum_resultaat'], 1));
          $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_RES'], '', '', '', Gsm_opmaak($regelsArr['cum_resultaat'], 2));
          $pdf_data[] = explode(';', trim($pdf_line));
        } //$regelsArr[ 'result' ]
      } //$regelsArr[ 'rekening_type' ] == 5
      else {
        // regel Totaal Tussenrek
        if (!isset($MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']])) {
          $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']] = '';
        } //!isset( $MOD_GSMOFF[ 'rek_type' ][ $regelsArr[ 'rekening_type' ] ] )
        $regelsArr['descr'] .= sprintf($LINETEMP[23], $MOD_GSMOFF['line_color'][3], '', '<b>' . $MOD_GSMOFF['SUR_TOT'] . '</b>', '<strong>' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']] . '</strong>', '', '', Gsm_opmaak($regelsArr['cum_srt'], 1));
        $pdf_line = sprintf("%s;%s;%s;%s;%s;%s", '', $MOD_GSMOFF['SUR_TOT'] . ' ' . $MOD_GSMOFF['rek_type'][$regelsArr['rekening_type']], '', '', '', Gsm_opmaak($regelsArr['cum_srt'], 2));
        $pdf_data[] = explode(';', trim($pdf_line));
      }
    } //$r_results && $r_results->numRows() > 0
    else {
      $regelsArr['descr'] .= $MOD_GSMOFF['SUR_NDATA'];
      $pdf_text .= $MOD_GSMOFF['SUR_NDATA'] . "n";
    }
    break;
} //$regelsArr[ 'mode' ]
if (isset($regelsArr['filename_pdf'])) {
  /*
   * the output to the pdf
   */
  $pdf_cols = array(
    12,
    85,
    15,
    25,
    25,
    25
  );
  $pdf->DataTable($pdf_header, $pdf_data, $pdf_cols);
  $pdf->ChapterBody($pdf_text);
  $pdf->Output($place['pdf'] . $regelsArr['filename_pdf'], 'F');
  $msg['inf'] .= ' report created</br>';
} //isset( $regelsArr[ 'filename_pdf' ] )
/*
 * display selection footer
 */
$regelsArr['select'] .= $LINETEMP[1];
$regelsArr['select'] .= sprintf($LINETEMP[10], '', $ICONTEMP[21], $ICONTEMP[22], $ICONTEMP[23], sprintf(' %1$s %2$s %3$s&nbsp;%4$s%5$s%6$s%7$s', $MOD_GSMOFF['SUR_VAN'], sprintf($ICONTEMP[14], $regelsArr['Vanaf'], ''), $MOD_GSMOFF['SUR_TOTENMET'], sprintf($ICONTEMP[15], $regelsArr['Totenmet']), sprintf($ICONTEMP[26], ' det ', 'details'), sprintf($ICONTEMP[26], ' pdf ', 'pdf'), (isset($regelsArr['filename_pdf'])) ? sprintf($ICONTEMP[18], "", $regelsArr['filename_pdf']) : ""));
/*
 * the output to the screen
 */
switch ($regelsArr['mode']) {
  default: // default list
    $_SESSION['page_h'] = sha1(MICROTIME() . $_SERVER['HTTP_USER_AGENT']);
    $parseViewArray = array(
      'header' => $regelsArr['Document'],
      'page_id' => $page_id,
      'section_id' => $section_id,
      'kopregels' => $regelsArr['head'],
      'description' => $regelsArr['descr'],
      'message' => message($msg, $debug),
      'selection' => $regelsArr['select'],
      'return' => CH_RETURN,
      'parameter' => $regelsArr['search'],
      'sel' => $regelsArr['search'],
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