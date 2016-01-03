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
/* change history
 * 20151229  aanpassing toevoeging van meer regels
 */
/*
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'somrente',
// voor versie display
  'modulen' => 'vsomrente',
  'versie' => '  v20151229 ', 
// general parameters  
  'app' => 'wettelijke rente ',
  'module2' => '',
// file en directory parameters 
  'table' => TABLE_PREFIX.'users',
  'adres' => CH_DBBASE.'_adres',
  'file_1' => "sv_sommatie",
  'file' => CH_DBBASE.'_sv_sommatie',
  'file0' => 'media/legal',  // the directory for the documents
  'file1' => 'sommatie',  // subdirectory with the administered documents
  'file_pre' => ( isset( $settingArr[ 'prefix' ] ) ) ? $settingArr[ 'prefix' ] : 'XX',
  'allow_ext' => array ( "pdf", "jpg", "zip" ),   
// Output en processing
  'carriage_return' => 'X',
  'carriage_return2' => '</br>',
  'head' => '',
  'rapport' => '',
  'select' => '',
  'update' => '',
  'descr' => '',
  'toegift' => '',
  'hash' => '', 
  'recid' => '',
// default values/ create fields
  'email' => "info@personeelsadviseur.eu",
  'info_mail' => "incasso@contracthulp.nl",
  'max' => 200,
  'memored_mutaties' => 1, 
  'wrcalcdate'=> date( "Y-m-d" ),
//  'memored_recid' =>0, 
  'memory' => 1,
  'today' => date( "Y-m-d" ),
  'wrref1comp' => 3,
  'wrref2comp' => 3,
  'gap'=> 30,
  'wrrente' => 0,
  'wrrentepct' =>0,
  'wrink' => 1,
  'comment' => '',
  'wrsummary' => 1,  
  'show_existing' => false,
  'wrref1dienst'=> "",
  'comment' => '',
  'wrrekdt' => date( "Y-m-d" ),
  'wrremind' => 30,
  'e_mail_switch' => false,
  'selection_block' => false,
);
$regelsArr['project'] = $regelsArr[ 'app' ] . ' - Berekening';
$regelsArr['login'] = (!isset($_SESSION[ 'USER_ID' ]) || $_SESSION[ 'USER_ID' ] <1) ? false : true;

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
$MOD_GSMOFF ['tbl_icon'][7] = 'Compleet';
$MOD_GSMOFF ['tbl_icon'][8] = 'Bijwerken';
// extend text strings
$MOD_GSMOFF [ 'OK_UPLOAD' ] = 'upload ok';
$MOD_GSMOFF [ 'som_mail1' ] = 'ls,</br>U heeft gevraagd om een berekening van de wettelijke rente / incasso kosten met de door U verstrekte gegevens van het dossier {dossier}.<br/>Deze berekening kunt U downloaden from <a href="{url}{place}{filename}" title="pdf">{filename}</a>.<br/>Indien U wenst dat AV advocatuur in deze Uw belangen behartigd dient U gebruik te maken van de volgende activatie link <a href="{link}" title="link">{link}</a>.<br/><br/>met vriendelijke groet<br/>{fromname}';
$MOD_GSMOFF [ 'som_mail1c' ] = 'ls,</br></br>{to} heeft gevraagd om een berekening van de wettelijke rente / incasso kosten met verstrekte de aangeleverde gegevens onder dossier ref {dossier}/{volgnummer}.<br/><br/>met vriendelijke groet<br/>{fromname}';
$MOD_GSMOFF [ 'som_mail2' ] = "</br></br>Een mail is gestuurd naar het opgegeven e-mail adres: {to}. Deze mail bevat een link naar het document {filename} en een activatie link.</br></br>";  
$MOD_GSMOFF [ 'som_details' ]  = array ( '1' => 'Basic', '2' => 'Dossier aanleggen');
$MOD_GSMOFF [ 'som_cont' ] = array ( '0' => 'Nee, wettelijke rente', '1' => 'Ja, in plaats van de wettelijke rente', '2' => 'Ja, bovenop de wettelijke rente' );
$MOD_GSMOFF [ 'som_trans' ] = array ( '0' => 'Nee', '1' => 'Ja' );
$MOD_GSMOFF [ 'som_verval' ] = array( '7' => '7 dagen', '14' => '14 dagen', '30' => '30 dagen', '45' => '45 dagen', '60' => '60 dagen');
// overrule standard function
$ICONTEMP[ 7 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][7].'" style="width: 100%;" />'.CH_CR; 
$ICONTEMP[ 8 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][2].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][8].'" style="width: 100%;" />'.CH_CR;
// extend standard function
$LINETEMP[ 80 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>'.CH_CR;
$LINETEMP[ 81 ] = '<tr %1$s><td>%2$s</td><td colspan="4">%3$s</td></tr>'.CH_CR;
$LINETEMP[ 82 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td colspan="3">%4$s</td></tr>'.CH_CR;
$LINETEMP[ 83 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td colspan="2">%5$s</td></tr>'.CH_CR;
$ICONTEMP[ 21 ] ='<a href="' . CH_RETURN . '&command=%2$s&module=%3$s&recid=%4$s">%1$s</a>'.CH_CR;
$ICONTEMP[ 22 ] ='<a href="' . CH_RETURN . '&command=%2$s&module=%3$s">%1$s</a>'.CH_CR;
$ICONTEMP[ 23 ] = '<input type="text" name="%1$s" size="%2$s"  value="%3$s" placeholder="%4$s" autocomplete="off" />'.CH_CR;
$ICONTEMP[ 24 ] = '<input type="date" name="%1$s" size="%2$s" value="%3$s" placeholder="%4$s" autocomplete="off" />'.CH_CR;
$ICONTEMP[ 25 ] = '<select name="%1$s">%3$s</select>'.CH_CR;
$ICONTEMP[ 26 ] = '<textarea rows="%2$s" cols="35" name="%1$s" placeholder="%4$s" >%3$s</textarea>'.CH_CR;
$ICONTEMP[ 27 ] = '<p><center><embed height="200" width="600" src="%1$s%2$s#toolbar=1&navpanes=0&scrollbar=1"></embed></center></p>'.CH_CR;
$ICONTEMP[ 28 ] = '<input type="checkbox" name="vink[]" value="%2$s">&nbsp;%1$s'.CH_CR;
$ICONTEMP[ 29 ] = '<input type="file" name="%1$s" />'.CH_CR;
$ICONTEMP[ 30 ] = '<a href="../%1s/%2$s">%3$s</a> <a href="' . CH_RETURN . '&command=rm&module={module}&recid=%4$s&file=%5$s"><img src="' . $place['imgm'] . 'delete_16.png"></a></br>'.CH_CR;
$ICONTEMP[ 31 ] = '<a href="' . CH_RETURN . '&command=kies&module={module}&recid=%1$s">%2$s</a><a href="' . CH_RETURN . '&command=weg&module={module}&recid=%1$s"><img src="' . $place['imgm'] . 'delete_16.png"></a></br>'.CH_CR;
$ICONTEMP[ 32 ] = '<a href="' . CH_RETURN . '&command=show&module={module}&recid=%1$s">%2$s</a>'.CH_CR;
/*
 * various functions
 */
// empty
/*
 * Location includes met rates, functions
 */
$calc = array( array( ) );
// places of the includes
// load includes rente
require_once( $place_incl . 'vsomrente.inc' );
// load includes with specific functions
require_once( $place_incl . 'vsomrente_incl.inc' );
/*
 * Determine person and entity and preload address
 * deze data van de login persoon overschrijft de default values
 */
if ($regelsArr[ 'login' ]) $regelsArr = array_merge($regelsArr, Gsm_login_person ($regelsArr, $_SESSION[ 'USER_ID' ])); 
/*
 * some job to do 
 */
if ( isset( $_POST[ 'command' ] ) ) {
  // deze form data overschrijft de default values welke eventueel al overschreven zijn door opgeslagen data
  $regelsArr = array_merge($regelsArr, Gsm_post_data ($regelsArr)); 
  if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
  // is er ook een bijlage bij zo ja sla die op 
  Gsm_attachment ($regelsArr['allow_ext' ], $regelsArr['file0'], $regelsArr['file1'], $regelsArr[ 'memored_recid' ], $MOD_GSMOFF[ 'som_att' ], $allow_size=6000000);  
  switch ( $_POST[ 'command' ] ) {
    case $MOD_GSMOFF[ 'tbl_icon' ][10]: // one entry more
      $regelsArr[ 'memored_mutaties' ]++; // 20151229 toegevoegd
      $regelsArr[ 'memored_mutaties' ]++; // 20151229 toegevoegd
      $regelsArr[ 'memored_mutaties' ]++; // 20151229 toegevoegd
      $regelsArr[ 'memored_mutaties' ]++;
      $regelsArr[ 'memory' ] = $regelsArr[ 'memored_mutaties' ];
      $regelsArr[ 'mode' ] = 9;
      break;
    case $MOD_GSMOFF[ 'tbl_icon' ][7]: // set status op 2
	  Gsm_set_status ($regelsArr[ 'file' ], $regelsArr[ 'memored_recid' ] , 2 );
      $regelsArr[ 'mode' ] = 8;
      break;
    case $MOD_GSMOFF[ 'tbl_icon' ][8]:
      $regelsArr[ 'mode' ] = 9;
      break;
    default:
      if ( $debug ) $msg[ 'bug' ] .= __LINE__ . ' access'.NL;
      $regelsArr[ 'mode' ] = 9;
      break;
  } //$_POST[ 'command' ]
} elseif ( isset( $_GET[ 'command' ] ) ) {
  switch ( $_GET[ 'command' ] ){
    case 'rm':  // remove file/attachment
	  Gsm_get_files( WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] , $_GET[ 'file' ], 3 );
	  $regelsArr = array_merge($regelsArr, Gsm_file_data ($regelsArr[ 'file' ] , $_GET ['recid'] ) );
      $regelsArr[ 'mode' ] = 9;
      break;
    case 'weg': // set status to 0
	  Gsm_set_status ($regelsArr[ 'file' ], $_GET ['recid'] , 0 );
	  $regelsArr[ 'mode' ] = 9;
      break;
    case 'acc': // set status to 3
	  $regelsArr[ 'memored_recid' ] = substr(trim($_GET['ref']), 0, strlen(trim($_GET['ref']))-5);
	  Gsm_set_status ($regelsArr[ 'file' ], $regelsArr[ 'memored_recid' ] , 3 );
	  $regelsArr[ 'mode' ] = 6;
      break;
    case 'show': 
    case 'kies': // pick up data from file 
	  $regelsArr = array_merge($regelsArr, Gsm_file_data ($regelsArr[ 'file' ] , $_GET ['recid'] ) );
	  $regelsArr[ 'mode' ] = 9;
	  if ($regelsArr[ 'wrstatus' ] >2 ) $regelsArr[ 'mode' ] = 7;
      break;
    default:
      if ( $debug ) $msg[ 'bug' ] .= __LINE__ . ' access'.NL;
      $regelsArr[ 'mode' ] = 9;
      break;
  } //$_GET[ 'command' ]
} else { // so standard display 
// if ( $debug ) $msg[ 'bug' ] .= __LINE__ . ' access'.NL;
}
if ( $debug ) $msg[ 'bug' ] .= NL . __LINE__ . ' mode: ' . $regelsArr[ 'mode' ] . ' ' . ( ( isset( $query ) ) ? $query : "" ) . NL.NL;
if ( $debug ) Gsm_debug( $regelsArr, __LINE__);
// is er een e-mail address en een bedrag ?  dan opslaan update of als nieuw
if (isset( $regelsArr['wrfacamt'] ) && isset( $regelsArr[ 'owner_email' ] ) && $regelsArr['wrfacamt'][1]>0  && strlen($regelsArr[ 'owner_email'])>12 ) {
  if ($regelsArr[ 'mode' ] == 9) $regelsArr = array_merge($regelsArr, Gsm_record_data ($regelsArr[ 'file' ], $regelsArr , $status=0 ));
} else {
  // als er geen bedrag is zorg dat opgeslagen data geselecteerd kan worden
  if (isset( $regelsArr[ 'owner_email' ] ) && strlen($regelsArr[ 'owner_email'])>12 && $regelsArr[ 'login' ] ) $regelsArr[ 'selection_block' ] = true;  //20151103:added && $regelsArr[ 'login' ]
}  
if ($regelsArr[ 'mode' ] == 9 || $regelsArr[ 'mode' ] == 7 ) {
/* ----------------------------------------------------invoer formulier-------------------------------------------------------
 * Input form start 
 *
 * parameters
 * wrsummary (1) summary / (2) details / (3) dossier / 
 * login true false
 * id= record id
 * name e-mail adres contact persoon schuldeiser
 * wrstatus  1 editing by customer 2 procedd by legal comp 9 finished
 * wrref0 project referentie: text
 * wrdata will contain status text
 * wrmodel procedure text selection by legal company
 * wrsupport reference to legal company involved
 * wrcalcdate rekendatum voor wettelijke rente
 * wrremind standaard periode voor betalingstermijn
 * wrref1id id van schuldeiser
 * wrref1name naam contact persoon schuldeiser
 * wrref1adres address schuldeiser
 * wrref1comp bedrijfsvorm schuldeiser
 * wrref1dienst geleverde dienst product.
 * wrref2adres address debiteur
 * wrref2comp bedrijfsvorm debiteur
 * wrref2reg registratie nummer kvkxx|vatxx debiteur
 * wrrente rente methode van toepassing
 * wrrentepct rente percentage passend bij methode 
 * wrink inkasso kosten methode
 * array: wrfacref
 *        wrfacdat
 *        wrfacamt
 *        wrfacverv
 * comment extra informatie
 */
/*
 * Algemeen
 */ 
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 3 ], ucfirst( 'Details' ), sprintf( $ICONTEMP[ 25 ], "wrsummary", '', Gsm_option( $MOD_GSMOFF[ 'som_details' ], $regelsArr[ 'wrsummary' ] ) ), '', '','' );	
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'Referentie' ), sprintf( $ICONTEMP[ 23 ], 'wrref0', 60, ( isset( $regelsArr[ 'wrref0' ] ) ) ? $regelsArr[ 'wrref0' ] : 'noggeennaam', 'project referentie' ), '', '' );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'Dienst Verleend ' ), sprintf( $ICONTEMP[ 26 ], 'wrref1dienst', 3, $regelsArr[ 'wrref1dienst' ], 'Uw dienst of levering' ), '', '', '' );
/*
 * Schuldeiser gegevens
 */ 
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( '(1) Schuldeiser' ), sprintf( $ICONTEMP[ 26 ], 'wrref1adres', 4, $regelsArr[ 'wrref1adres' ], 'naam en adres' ), '', '' );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'T.a.v' ), sprintf( $ICONTEMP[ 23 ], 'wrref1name', 60, ( isset( $regelsArr[ 'wrref1name' ] ) ) ? $regelsArr[ 'wrref1name' ] : '', 'T.a.v.' ), '', '' );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'e-mail adres' ), sprintf( $ICONTEMP[ 23 ], 'owner_email', 60, ( isset( $regelsArr[ 'owner_email' ] ) ) ? $regelsArr[ 'owner_email' ] : '', 'berekenings resultaat gaat naar dit e-mail adres' ), '', '' );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'Schuldeiser is een' ), sprintf( $ICONTEMP[ 25 ], 'wrref1comp', '', Gsm_option( $MOD_GSMOFF[ 'lgn_vorm' ], $regelsArr[ 'wrref1comp' ] ) ), '', '','' );	
/*
 * Debiteur gegevens
 */
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( '(2) Debiteur' ), sprintf( $ICONTEMP[ 26 ], 'wrref2adres', 4, ( isset( $regelsArr[ 'wrref2adres' ] ) ) ? $regelsArr[ 'wrref2adres' ] :'', 'naam en adres' ), '', '' );
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], '', ucfirst( 'Debiteur is een' ), sprintf( $ICONTEMP[ 25 ], "wrref2comp", '', Gsm_option( $MOD_GSMOFF[ 'lgn_vorm' ], $regelsArr[ 'wrref2comp' ] ) ), '', '','' );	
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'KVK nr Debiteur' ), sprintf( $ICONTEMP[ 23 ], 'wrref2kvk', 60, ( isset( $regelsArr[ 'wrref2kvk' ] ) ) ? $regelsArr[ 'wrref2kvk' ] : '', 'KVK nummer' ), '', '' );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'VAT nr Debiteur' ), sprintf( $ICONTEMP[ 23 ], 'wrref2vat', 60, ( isset( $regelsArr[ 'wrref2vat' ] ) ) ? $regelsArr[ 'wrref2vat' ] : '', 'VAT nummer' ), '', '' );	
/*
 * Facturen overdue en eventuele deelbetalingen
 */
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 80 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], 
    ucfirst( 'Document</br>kenmerk' ), 
    ucfirst( 'datum</br>(yyyy-mm-dd)' ), 
    ucfirst( 'bedrag' ), 
    ( $regelsArr[ 'wrsummary' ] > 1 ) ? ucfirst( 'Verval datum</br>(yyyy-mm-dd)' ) : '', 
    $ICONTEMP[ 10 ] );
  for ( $i = 1; $i <= $regelsArr[ 'memored_mutaties' ]; $i++ ) {
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 80 ], '', 
      sprintf( $ICONTEMP[ 23 ], '1|' . $i, 16, ( isset( $regelsArr['wrfacref'][ $i ] ) ) ? $regelsArr['wrfacref'][ $i ] : '', 'doc ' . $i . ' ref.' ), 
	  sprintf( $ICONTEMP[ 24 ], '2|' . $i, 12, ( isset( $regelsArr['wrfacdat'][ $i ] ) ) ? $regelsArr['wrfacdat'][ $i ] : '', 'Datum' ), 
      sprintf( $ICONTEMP[ 23 ], '3|' . $i, 12, ( isset( $regelsArr['wrfacamt'][ $i ] ) ) ? $regelsArr['wrfacamt'][ $i ] : '', 'Bedrag' ), 
	  ( $regelsArr[ 'wrsummary' ] > 1 ) ? sprintf( $ICONTEMP[ 24 ], '4|' . $i, 12, ( isset( $regelsArr['wrfacverv'][ $i ] ) ) ? $regelsArr['wrfacverv'][ $i ] : '', 'Datum' ): '', 
	  '' );
  } //$i = 1; $i <= $regelsArr[ 'memored_mutaties' ]; $i++
  /*
   * Reken parameters
   */
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], '', ucfirst( 'reken</br>parameters' ), ucfirst( 'waarde' ), ucfirst( '' ), ucfirst( '  ' ) );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', ucfirst( 'Berekenen tot</br>(yyyy-mm-dd)' ), sprintf( $ICONTEMP[ 24 ], 'wrcalcdate', 19, ( isset( $regelsArr[ 'wrcalcdate' ] ) ) ? $regelsArr[ 'wrcalcdate' ] : $regelsArr[ 'today'] , 'Datum' ), '', '' );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 83 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', 
    ucfirst( 'Contractuele rente' ) , 
    sprintf( $ICONTEMP[ 25 ], "wrrente", '', Gsm_option( $MOD_GSMOFF[ 'som_cont' ], $regelsArr[ 'wrrente' ] )), 
   ( $regelsArr[ 'wrrente' ] != 0 ) ? sprintf( $ICONTEMP[ 24 ], "wrrentepct", 6, (isset( $regelsArr[ 'wrrentepct' ] ) ) ? $regelsArr[ 'wrrentepct' ] : '', '%' ).' % ' : '',
    '');
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'incasso kosten ' ), sprintf( $ICONTEMP[ 25 ], "wrink", '', Gsm_option( $MOD_GSMOFF[ 'som_trans' ], $regelsArr[ 'wrink' ] )), '', '' );
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '',ucfirst( 'Vervaltermijn ' ), sprintf( $ICONTEMP[ 25 ], "wrremind", '', Gsm_option( $MOD_GSMOFF[ 'som_verval' ], $regelsArr[ 'wrremind' ] )), '', '');	
  if ( $regelsArr[ 'wrsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'Opmerking ' ), sprintf( $ICONTEMP[ 26 ], 'comment', 3, $regelsArr[ 'comment' ], 'Aanvullende gegevens' ), '', '', '' );
/*
 * bijlages
 */
  if ( $regelsArr[ 'wrsummary' ] > 1 && isset($regelsArr[ 'memored_recid' ]) && $regelsArr[ 'memored_recid' ]>0 ) {
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], '', ucfirst( 'Bijlages' ), '', '', '' ) ;
    $arr_files = Gsm_get_files( WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] , $regelsArr[ 'memored_recid' ]."_",1 );
    foreach ( $arr_files as $key => $value ) {
        $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', 
	    sprintf( $ICONTEMP[ 30 ], 
	    $regelsArr['file0']. "/" . $regelsArr['file1'], 
        $value, 
	    $value, 
	    $regelsArr[ 'memored_recid' ], 
	    $value ));
      if ($debug) $msg[ 'bug' ] .= __LINE__.' EN: ' . $key . "|" . $value . '</br>';
    } //$arr_files as $key => $value   
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'Bijlage type' ), sprintf( $ICONTEMP[ 25 ], "atttype", '', Gsm_option( $MOD_GSMOFF[ 'som_att' ], 6 ) ), '', '','' );	
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'Extra omschrijving' ), sprintf( $ICONTEMP[ 23 ], 'attoms', 20, '', 'Bijlage Omschrijving' ), '', '' );
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( '' ), sprintf( $ICONTEMP[ 29 ], 'Attdoc'), '', '', '' );
  }
  /*
   * selections of existing entries 
   */
  if ( $regelsArr[ 'selection_block' ]) {
  /*
   * Bestaande entries zoeken
   */
    $query = "SELECT * FROM `" . $regelsArr[ 'file' ] . "` WHERE `name`= '".$regelsArr[ 'owner_email' ]."' ORDER BY `wrstatus`, `updated` DESC"; 
    $results = $database->query( $query );  
    if ( $results && $results->numRows() > 0 ) {
      $regelsArr[ 'toegift' ] .= '<table class="container" width="100%">';
      $regelsArr[ 'toegift' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], '', ucfirst( 'bestaande cases' ), '', '', '' ) ;
      while ( $row = $results->fetchRow() ) { 
        $hulp_adres2 = explode (CH_CR, $row[ 'wrref2adres' ]);
        switch ( $row[ 'wrstatus' ] ) {
          case '1': // edited by schuldeiser
          case '2': // waiting on starting action by legal party
            $regelsArr[ 'toegift' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], $MOD_GSMOFF[ 'som_status' ][ $row[ 'wrstatus' ] ], str_replace ("<br />", "", sprintf( $ICONTEMP[ 31 ], $row['id'], $row[ 'wrref0' ]."_".$hulp_adres2 [0]."_(".$row['id'].")_".$row[ 'wrref1dienst' ], '' )), '', '', '' );
            break;
	      case '0': // being processed by legal party		
          case '3': // being processed by legal party
          case '4': // being processed by legal party
          case '5': // being processed by legal party
          case '6': // being processed by legal party
          case '7': // being processed by legal party
          case '8': // being processed by legal party
		  case '9': // completed
		    $regelsArr[ 'toegift' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], $MOD_GSMOFF[ 'som_status' ][ $row[ 'wrstatus' ] ], str_replace ("<br />", "", sprintf( $ICONTEMP[ 32 ], $row['id'], $row[ 'wrref0' ]."_".$hulp_adres2 [0].'_'.$row['updated']."_(".$row['id'].")", '' )), '', '', '' );   	   
		   break;
          default: // unknown status
		    break;
	    }
	  }
    }
    $regelsArr[ 'toegift' ] .= '</table>';   	
  }
/*
 * selection
 */
  if ($regelsArr[ 'mode' ]==9) {
    $regelsArr[ 'select' ] .=$LINETEMP[ 1 ];
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 2 ], 
      "", 
	  "", 
      $ICONTEMP[ 8 ],
	  "",
	  "",
     (isset( $regelsArr['wrfacamt'] ) && isset( $regelsArr[ 'owner_email' ] ) && $regelsArr['wrfacamt']>0  && strlen($regelsArr[ 'owner_email'])>12 ) ? $ICONTEMP[ 7 ] :"",
      "",
      "");
	}
} //$regelsArr[ 'mode' ]
switch ( $regelsArr[ 'mode' ] ){
  case 6: // 
      $regelsArr[ 'toegift' ] .= '</br></br>Activatie verwerkt.</br></br>';
    break;
  default:
/* -----------------------------------------------------------------------------------------------------------
 * Input form einde /
 */
/*
 * Berekenen 
 */
    if (isset( $regelsArr['wrfacamt'] ) && $regelsArr['wrfacamt'][1]>0 ) {
      // consument of business rates
      // welke parameters zijn relevant
      $calcArr = array (
        'max' => $regelsArr[ 'max' ],
	    'wrrente' => $regelsArr[ 'wrrente' ], 
        'wrrentepct' => $regelsArr[ 'wrrentepct' ],
	    'wrfacref' => $regelsArr['wrfacref'],
	    'wrfacdat' => $regelsArr['wrfacdat'],
	    'wrfacamt' => $regelsArr['wrfacamt'],
	    'wrfacverv' => $regelsArr['wrfacverv'],
	    'wrref1comp' => $regelsArr[ 'wrref1comp' ],
	    'wrref2comp' => $regelsArr[ 'wrref2comp' ],
	    'wrink' => $regelsArr['wrink'],
        'ink_tar' => $inkasso_tarieven,
        'ink_tar_min'=> $inkasso_tarieven_min,
        'ink_tar_max'=>	$inkasso_tarieven_max,	
        'ink_tar_btw'=>	$inkasso_tarieven_btwcompensatie,
	    'toegift' => $regelsArr[ 'toegift' ]
      );
      switch ($regelsArr[ 'wrref2comp' ]) {
      // de cases hangen af van BTW plichtig bedrijven
        case 1:
	    case 2:
          $datum_list = Gsm_som_datum( $regelsArr['wrfacverv'], $regelsArr[ 'wrcalcdate' ], $consument_rente_percentages );	
          $calc = Gsm_som_bereken( $calcArr, $datum_list , $consument_rente_percentages, 1);
	      break;
	    default:
	      $datum_list = Gsm_som_datum( $regelsArr['wrfacverv'], $regelsArr[ 'wrcalcdate' ], $handels_rente_percentages );	
	      $calc = Gsm_som_bereken( $calcArr, $datum_list, $handels_rente_percentages, 1);
          break;
      }
      if ( $debug ) {
        Gsm_debug( $regelsArr['wrfacverv'], __LINE__);
        Gsm_debug( $regelsArr[ 'wrcalcdate' ], __LINE__);
        Gsm_debug( $consument_rente_percentages, __LINE__);
        Gsm_debug( $handels_rente_percentages, __LINE__);
        Gsm_debug( $datum_list, __LINE__);
        Gsm_debug( $calc, __LINE__);
      }
      $regelsArr = array_merge($regelsArr, Gsm_som_bereken2 ($calc, $calcArr));
      $regelsArr['toegift'] = Gsm_som_report( $regelsArr, $calcArr, $MOD_GSMOFF, $func = 1 );
      if (isset( $regelsArr[ 'owner_email' ]) && strlen($regelsArr[ 'owner_email'])>12  && $regelsArr[ 'mode' ] == 8) {
      /*
       *  PDF file condities zijn er
       */ 
       // load include with specific layout
        require_once( $place_incl . 'vsomrente_pdf.inc' );  
        $mailArr = array(
          'from' => $regelsArr[ 'email' ],
          'to' => $regelsArr[ 'owner_email' ],
          'cc' => $regelsArr[ 'info_mail'],
          'volgnummer' => $regelsArr[ 'memored_recid' ], 
         'dossier' => $regelsArr[ 'wrref0' ],
         'subject' => $regelsArr[ 'wrref0' ] . ' / ' . $regelsArr[ 'memored_recid' ],
         'link' => LEPTON_URL.PAGES_DIRECTORY.'/incasso.php?section_id='.$section_id.'&command=acc&ref=' . $regelsArr[ 'memored_recid' ]. substr(md5($regelsArr[ 'memored_recid' ]),0,5) ,
         'fromname' => "Contracthulp",
         'toname' => $regelsArr[ 'owner_email' ],
	     'place' => $regelsArr['file0'].'/'.$regelsArr['file1'].'/',
	     'filename' => $regelsArr[ 'memored_recid' ].'_berekening_'.$regelsArr[ 'wrcalcdate' ].'.pdf',
         'url' => LEPTON_URL."/"
        );	
        $mailArr ['file'] = Gsm_out( $regelsArr, $calc, $MOD_GSMOFF, 98, $mailArr ['place'], $mailArr ['filename'], ''); //create pdf file
        if ($debug) Gsm_debug($mailArr, __LINE__);
	    $mailArr[ 'body' ] = Gsm_prout ($MOD_GSMOFF[ 'som_mail1' ], $mailArr); // format the message
	    $mailArr[ 'body2' ] = Gsm_prout ($MOD_GSMOFF[ 'som_mail2' ], $mailArr); // format the message
        if ( $wb->mail( $mailArr[ 'from' ], $mailArr[ 'to' ], $mailArr[ 'subject' ], $mailArr[ 'body' ], $mailArr[ 'fromname' ] ) ) $regelsArr[ 'toegift' ] .= $mailArr[ 'body2' ];
	    $mailArr[ 'bodyc' ] = Gsm_prout ($MOD_GSMOFF[ 'som_mail1c' ], $mailArr); // format the message
	    $help = $wb->mail( $mailArr[ 'from' ], $mailArr[ 'cc' ], $mailArr[ 'subject' ], $mailArr[ 'bodyc' ], $mailArr[ 'fromname' ] ); 
      }
    }
    break;	
}
/*
 * display
 */
switch ( $regelsArr[ 'mode' ] ){
  case 9: // 
  default: // default
    $_SESSION[ 'page_h' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
    $parseViewArray = array(
      'header' => strtoupper( $regelsArr[ 'project' ] ),
      'page_id' => $page_id,
      'section_id' => $section_id,
      'kopregels' => $regelsArr[ 'head' ], 
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ),
      'selection' => $regelsArr[ 'select' ],
      'toegift' => $regelsArr[ 'toegift' ],   
      'return' => CH_RETURN,
      'module' => $regelsArr[ 'module' ],
	  'memory' => $regelsArr[ 'memory' ] . "|",
 //     'memory' => ($regelsArr[ 'mode' ] == 8) ? "clear|" : $regelsArr[ 'memory' ] . "|",
      'recid' => '',
      'update' => '',
      'rapportage' => $regelsArr[ 'rapport' ],
      'hash' => $_SESSION[ 'page_h' ] 
    );
    $prout .= Gsm_prout ($TEMPLATE[ 2 ], $parseViewArray);
    break;
} //$regelsArr[ 'mode' ]
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?> 