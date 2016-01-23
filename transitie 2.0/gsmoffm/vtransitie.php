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
 * 20151207  aanpassing naar nieuw maximum (vtrantable wordt ook aangepast)
 * 20151229  aanpassing mail addressen
 * 20151229  edit tekst
 * 20160105  ter voorkoming van errors
 */
/*
 * variable setting
 */
 $regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'transitie',
// voor versie display
  'modulen' => 'vtransitie',
  'versie' => '  v20151229 ', 
// general parameters  
  'app' => 'transitie vergoeding ',
  'table' => TABLE_PREFIX.'users',
  'adres' => CH_DBBASE.'_adres',
  'file_1' => "sv_transitie",
  'file' => CH_DBBASE.'_sv_transitie',
  'file0' => 'media/legal',  // the directory for the documents
  'file1' => 'transitie',  // subdirectory with the administered documents
  'file_pre' => ( isset( $settingArr[ 'prefix' ] ) ) ? $settingArr[ 'prefix' ] : 'XX',
  'allow_ext' => array ( "pdf", "jpg", "zip" ),   
// Output en processing
//  'carriage_return' => 'X',
//  'carriage_return2' => '</br>',
//   vervalt 20151229  'email' => "info@personeelsadviseur.eu", 
//   vervalt 20151229  'info_mail' => "incasso@contracthulp.nl",
  'email' => "info@hrmgemak.nl", //   toegevoegd 20151229  
  'info_mail' => "info@hrmgemak.nl", //   toegevoegd 20151229
  'head' => '',
  'rapport' => '',
  'select' => '',
  'update' => '',
  'descr' => '',
  'toegift' => '',
  'hash' => '', 
  'recid' => '',
// default values/ create fields
  'memory' => 1,
  'memored_mutaties' => 1,
  'today' => date( "Y-m-d" ),
  'tvsummary' => 1,
  'name' => "",  
  'tvstatus' => 1, 
  'tvref0' => "noggeennaam", 
  'tvdata' => "", 
  'tvmodel' => "", 
  'tvsupport' => "", 
  'tvcalcdate' => date( "Y-m-d" ), 
  'tvremind' => 30,
  'tvref0id' => 0, 
  'tvref0name' => '', 
  'tvref0adres' => '', 
  'tvref1name' => '', 
  'tvref1adres' => '', 
  'tvref2name' => '', 
  'tvref2adres' => '', 
  'tvref2comp' => 7,   
  'tvref2reg' => "",    
  'tvgebdat' => '1970-01-01',  
  'tvdatin' => '1990-01-01',  
  'tvdatout' => date( "Y-m-d" ), 
  'tvbtoamt' => 0, 
  'tvbtoper' => 12, 
  'tvbtoext' => 0,   
  'tvvakpct' => 8,   
  'tvprest' => 0,    
  'tvgevaar' => 0,  
  'tvorkw' => 2,  
  'tvperc' => 0,   
  'tvamtc' => 0,  
  'comment' => '',  
  'tvref2kvk' => '', //   toegevoegd 20160105  
  'tvref2vat' => '', //   toegevoegd 20160105  
  'owner_email' => '', //   toegevoegd 20160105  
  'selection_block' => 0, 
);
$regelsArr['project'] = $regelsArr[ 'app' ] . ' - Berekening';
$regelsArr['login'] = (!isset($_SESSION[ 'USER_ID' ]) || $_SESSION[ 'USER_ID' ] <1) ? false : true;
// $regelsArr['filename_pdf'] = strtolower( $regelsArr[ 'project' ] . $regelsArr[ 'today_pf' ] ) . '.pdf';
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
$MOD_GSMOFF [ 'trans_mail1' ] = 'Geachte heer / mevr., </br>U heeft gevraagd om een berekening van de transitie vergoeding rente met de door U verstrekte gegevens van het dossier {dossier}.<br/>Deze berekening kunt U downloaden : <a href="{url}{place}{filename}" title="pdf">{filename}</a>.<br/>Indien U wenst dat AV advocatuur in deze Uw belangen behartigd dient U gebruik te maken van de volgende activatie link <a href="{link}" title="link">{link}</a>.<br/><br/>met vriendelijke groet<br/>{fromname}';
$MOD_GSMOFF [ 'trans_mail1c' ] = 'Ls, </br></br>{to} heeft gevraagd om een berekening van de transitie vergoeding met de verstrekte gegevens onder dossier ref {dossier}/{volgnummer}.<br/><br/>met vriendelijke groet<br/>{fromname}'; //   edit 20151229
$MOD_GSMOFF [ 'trans_mail2' ] = "</br></br>Een mail is gestuurd naar het opgegeven e-mail adres: {to}. Deze mail bevat een link naar het document {filename} en een activatie link.</br></br>";  
$MOD_GSMOFF [ 'trans_details' ]  = array ( '1' => 'Basic', '2' => 'Dossier aanleggen');
$MOD_GSMOFF [ 'trans_loon_periode' ] = array( '13' => '4 weken', '12' => 'maand', '4' => 'kwartaal', '1' => 'jaar',  );
$MOD_GSMOFF [ 'trans_kleintjes' ] = array( '1' => 'tot 25 werknemers', '2' => 'meer als 25 werknemers' );
$MOD_GSMOFF [ 'trans_att' ] = array (
    '1' => 'recent loonstrookje',
    '9' => 'overige correspondentie');
// overrule standard function
$ICONTEMP[ 7 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][7].'" style="width: 100%;" />'.CH_CR; 
$ICONTEMP[ 8 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][2].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][8].'" style="width: 100%;" />'.CH_CR;
// extend standard function
$LINETEMP[ 80 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>'.CH_CR;
$LINETEMP[ 81 ] = '<tr %1$s><td>%2$s</td><td colspan="4">%3$s</td></tr>'.CH_CR;
$LINETEMP[ 82 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td colspan="3">%4$s</td></tr>'.CH_CR;
//$LINETEMP[ 83 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td colspan="2">%5$s</td></tr>'.CH_CR;
//$ICONTEMP[ 21 ] ='<a href="' . CH_RETURN . '&command=%2$s&module=%3$s&recid=%4$s">%1$s</a>'.CH_CR;
//$ICONTEMP[ 22 ] ='<a href="' . CH_RETURN . '&command=%2$s&module=%3$s">%1$s</a>'.CH_CR;
$ICONTEMP[ 23 ] = '<input type="text" name="%1$s" size="%2$s"  value="%3$s" placeholder="%4$s" autocomplete="off" />'.CH_CR;
$ICONTEMP[ 24 ] = '<input type="date" name="%1$s" size="%2$s" value="%3$s" placeholder="%4$s" autocomplete="off" />'.CH_CR;
$ICONTEMP[ 25 ] = '<select name="%1$s">%3$s</select>'.CH_CR;
$ICONTEMP[ 26 ] = '<textarea rows="%2$s" cols="35" name="%1$s" placeholder="%4$s" >%3$s</textarea>'.CH_CR;
//$ICONTEMP[ 27 ] = '<p><center><embed height="200" width="600" src="%1$s%2$s#toolbar=1&navpanes=0&scrollbar=1"></embed></center></p>'.CH_CR;
//$ICONTEMP[ 28 ] = '<input type="checkbox" name="vink[]" value="%2$s">&nbsp;%1$s'.CH_CR;
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
// extend text strings
//$MOD_GSMOFF [ 'OK_UPLOAD' ] = 'upload ok';
// overrule standard function
//$ICONTEMP[ 7 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][7].'" style="width: 100%;" />'.CH_CR; 
// extend standard function
$LINETEMP[ 90 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>';
//$LINETEMP[ 91 ] = '<tr %1$s><td>%2$s</td><td colspan="3">%3$s</td><td>%4$s</td></tr>';
//$LINETEMP[ 92 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td colspan="2">%4$s</td><td>%5$s</td></tr>';
//$LINETEMP[ 93 ] = '<input type="text" name="%1$s" size="%2$s"  value="%3$s" placeholder="%4$s" autocomplete="off" />';
//$LINETEMP[ 94 ] = '<input type="date" name="%1$s" size="%2$s" value="%3$s" placeholder="%4$s" autocomplete="off" />';
//$LINETEMP[ 95 ] = '<select name="%1$s">%3$s</select>';
//$LINETEMP[ 96 ] = '<textarea rows="%2$s" cols="35" name="%1$s" placeholder="%4$s" >%3$s</textarea>';
//$LINETEMP[ 97 ] = '<p><center><embed height="200" width="600" src="%1$s%2$s#toolbar=1&navpanes=0&scrollbar=1"></embed></center></p>';
//$LINETEMP[ 98 ] = '<input type="checkbox" name="vink[]" value="%2$s">&nbsp;%1$s';
$LINETEMP[ 99 ] = '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>';
//$ICONTEMP[ 20 ] ='<a href="' . CH_RETURN . '&command=%2$s&module=%3$s">%1$s</a>';
//$ICONTEMP[ 21 ] ='<a href="' . CH_RETURN . '&command=%2$s&module=%3$s&recid=%4$s"">%1$s</a>';
/*
 * various functions
 */
// empty
/*
 * Location includes met rates, functions
 */
$calc = array( array( ) );
// places of the includes
 // load includes tables
require_once( $place_incl . 'vtrantable.inc' );

// load includes with specific functions
require_once( $place_incl . 'vtransitie_incl.inc' );
/*
 * Determine person and entity to load specific partner function
 */
 /*
if (file_exists($place_incl . 'vt_own'.$regelsArr[ 'owner_short' ].'.inc')) { 
  require_once($place_incl . 'vt_own'.$regelsArr[ 'owner_short' ].'.inc');
} else { 
  require_once($place_incl . 'vt_owner.inc' );
}
*/
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
  $regelsArr = array_merge($regelsArr, Gsm_post_data ()); 
  // is er ook een bijlage bij zo ja sla die op 
  Gsm_attachment ($regelsArr['allow_ext' ], $regelsArr['file0'], $regelsArr['file1'], $regelsArr[ 'memored_recid' ], $MOD_GSMOFF[ 'trans_att' ], $allow_size=6000000);  
 switch ( $_POST[ 'command' ] ) {
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
} elseif ( isset( $_GET[ 'command' ] )) {
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
    case 'kies': // pick up data from file 
	  $regelsArr = array_merge($regelsArr, Gsm_file_data ($regelsArr[ 'file' ] , $_GET ['recid'] ) );
	  $regelsArr[ 'mode' ] = 9;
	 if ($regelsArr[ 'tvstatus' ] >2 ) $regelsArr[ 'mode' ] = 7;
      break;
    default:
      if ( $debug ) $msg[ 'bug' ] .= __LINE__ . ' access'.NL;
      $regelsArr[ 'mode' ] = 9;
      break;
  } //$_GET[ 'command' ]
} else { // so standard display
  /*
   * standard display job with or without search
   */
}
if ( $debug ) $msg[ 'bug' ] .= NL . __LINE__ . ' mode: ' . $regelsArr[ 'mode' ] . ' ' . ( ( isset( $query ) ) ? $query : "" ) . NL.NL;
if ( $debug ) Gsm_debug( $regelsArr, __LINE__);

// is er een e-mail address en een bruto bedrag ?  dan opslaan update of als nieuw
if (isset( $regelsArr['tvbtoamt'] ) && isset( $regelsArr[ 'owner_email' ] ) && $regelsArr['tvbtoamt']>0  && strlen($regelsArr[ 'owner_email'])>12 ) {
  if ($regelsArr[ 'mode' ] == 9) $regelsArr = array_merge($regelsArr, Gsm_record_data ($regelsArr[ 'file' ], $regelsArr , $status=0 ));
} else {
  // als er geen bedrag is zorg dat opgeslagen data geselecteerd kan worden
  if (isset( $regelsArr[ 'owner_email' ] ) && strlen($regelsArr[ 'owner_email'])>12 && $regelsArr[ 'login']) $regelsArr[ 'selection_block' ] = true;
}  

if ($regelsArr[ 'mode' ] == 9  || $regelsArr[ 'mode' ] == 7) {
/* ----------------------------------------------------invoer formulier-------------------------------------------------------
 * Input form start 
 *
 * parameters
 * tvsummary (1) summary / (2) details 
 * login true false
 * id= record id
 * name e-mail adres contact person

 * tvsummary (1) summary / (2) details 
 * login true false
 * id = record id
 * name e-mail adres contact persoon werknemer
 * tvstatus  1 editing by customer 2 proceed by legal comp 9 finished
 * tvref0 project referentie: 
 * tvdata will contain status text
 * tvmodel procedure text selection by legal company
 * tvsupport reference to legal company involved
 
 * tvcalcdate rekendatum voor wettelijke rente
 
 * tvremind standaard periode voor betalingstermijn
 
 * tvref0id id van aanvrager
 * tvref0name naam aanvrager 
 * tvref0adres addres aanvrager
 
 * tvref1name naam werknemer
 * tvref1adres naam werknemer 
 
 * tvref2adres addres werkgever
 * tvref2comp bedrijfsvorm werkgever
 * tvref2name contact persoon wekgever
 * tvref2reg registratie nummer kvkxx|vatxx werkgever
 
 * tvgebdat geboortedatum werknemer
 
 * tvdatin datum in dienst
 * tvdatout datum uit dienst
 
 * tvbtoamt bruto loon per periode excl vakantiegeld, prestatie beloning en gevarengeld
 * tvbtoper periode bij bruto loon
 * tvbtoext 13e maand
 * tvvakpct vakantie geld percentage
 * tvprest prestatie beloning per jaar
 * tvgevaar gevarengeld per jaar  
 
 * tvorkw vergangsregeling kleine werknemers 
 * tvperc` periode correctie
 * tvamtc` amount correctie
 * comment extra informatie
 */
/*
 * Algemeen
 */ 
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 3 ], ucfirst( 'Details' ), sprintf( $ICONTEMP[ 25 ], "tvsummary", '', Gsm_option( $MOD_GSMOFF[ 'trans_details' ], $regelsArr[ 'tvsummary' ] ) ), '', '','' );	
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( 'Referentie' ), sprintf( $ICONTEMP[ 23 ], 'tvref0', 60, ( isset( $regelsArr[ 'tvref0' ] ) ) ? $regelsArr[ 'tvref0' ] : 'noggeennaam', 'project referentie' ), '', '' );
/*
 * werknemer aanvrager
 */ 
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( '(0)Aanvrager '), sprintf( $ICONTEMP[ 23 ], 'tvref0name', 60, $regelsArr[ 'tvref0name' ] , 'T.a.v.' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '',ucfirst( 'Adres aanvrager' ), sprintf( $ICONTEMP[ 26 ], 'tvref0adres', 4, $regelsArr[ 'tvref0adres' ], 'naam en adres' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '',ucfirst( 'e-mail adres' ), sprintf( $ICONTEMP[ 23 ], 'owner_email', 60, ( isset( $regelsArr[ 'owner_email' ] ) ) ? $regelsArr[ 'owner_email' ] :'', 'berekenings resultaat gaat naar dit e-mail adres' ), '', '' );

/*
 * werknemer gegevens
 */ 
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( '(1) Werknemer '), sprintf( $ICONTEMP[ 23 ], 'tvref1name', 60, $regelsArr[ 'tvref1name' ], 'Naam Werknemer' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Adres werknemer' ), sprintf( $ICONTEMP[ 26 ], 'tvref1adres', 4, $regelsArr[ 'tvref1adres' ], 'naam en adres' ), '', '' );
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', ucfirst( 'Geboortedatum </br>(yyyy-mm-dd)' ), sprintf( $ICONTEMP[ 23 ], 'tvgebdat', 19, $regelsArr[ 'tvgebdat' ] , 'Datum' ), '', '' );
/*
 * werkgever details
 */
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], ucfirst( '(2) Werkgever' ), sprintf( $ICONTEMP[ 26 ], 'tvref2adres', 4, $regelsArr[ 'tvref2adres' ] , 'naam en adres' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Contact persoon'), sprintf( $ICONTEMP[ 23 ], 'tvref2name', 60,  $regelsArr[ 'tvref2name' ] , 'T.a.v.' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Werkgever is een' ), sprintf( $ICONTEMP[ 25 ], "tvref2comp", '', Gsm_option( $MOD_GSMOFF[ 'lgn_vorm' ], $regelsArr[ 'tvref2comp' ] ) ), '', '','' );	
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'KVK nr Werkgever' ), sprintf( $ICONTEMP[ 23 ], 'tvref2kvk', 60, ( isset( $regelsArr[ 'tvref2kvk' ] ) ) ? $regelsArr[ 'tvref2kvk' ] : '', 'KVK nummer' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'VAT nr Werkgever' ), sprintf( $ICONTEMP[ 23 ], 'tvref2vat', 60, ( isset( $regelsArr[ 'tvref2vat' ] ) ) ? $regelsArr[ 'tvref2vat' ] : '', 'VAT nummer' ), '', '' );
/*
 * arbeidsduur
 */
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], ucfirst( 'Arbeidsduur' ), '', ucfirst( 'datum (yyyy-mm-dd)' ), '', '', '' ); 
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', ucfirst( 'In-Dienst ' ), sprintf( $ICONTEMP[ 23 ], 'tvdatin', 19, $regelsArr[ 'tvdatin' ] , 'Datum' ), '', '' );
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', ucfirst( 'Uit-Dienst' ), sprintf( $ICONTEMP[ 23 ], 'tvdatout', 19, $regelsArr[ 'tvdatout' ]  , 'Datum' ), '', '' );
/*
 * bruto loon
 */
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], ucfirst( 'Bruto loon' ), '', '', '', '' );
  $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', ucfirst( 'Bruto loon' ), sprintf( $ICONTEMP[ 23 ], 'tvbtoamt', 12, $regelsArr['tvbtoamt'] , 'Bedrag' )." per ".sprintf( $ICONTEMP[ 25 ], 'tvbtoper', '', Gsm_option( $MOD_GSMOFF[ 'trans_loon_periode' ], $regelsArr[ 'tvbtoper' ] ) ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( '13e maand' ), sprintf( $ICONTEMP[ 23 ], 'tvbtoext', 12, $regelsArr['tvbtoext'], 'Bedrag' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Vakantie geld' ), sprintf( $ICONTEMP[ 23 ], 'tvvakpct', 6, $regelsArr['tvvakpct'], '%' )." %", '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Prestatie beloning' ), sprintf( $ICONTEMP[ 23 ], 'tvprest', 12, $regelsArr['tvprest'], 'Bedrag' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Gevarengeld' ), sprintf( $ICONTEMP[ 23 ], 'tvgevaar', 12,  $regelsArr['tvgevaar'], 'Bedrag' ), '', '' );
 /*
 * specifieke situaties
 */ 
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], ucfirst( 'Specifieke Situaties' ), '', '', '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], '', ucfirst( 'Reken datum </br>(yyyy-mm-dd)' ), sprintf( $ICONTEMP[ 24 ], 'tvcalcdate', 19, $regelsArr[ 'tvcalcdate' ] , 'Datum' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Overgangs regeling' ), "Kleine Werknemers  : ".sprintf( $ICONTEMP[ 25 ], 'tvorkw', '', Gsm_option( $MOD_GSMOFF[ 'trans_kleintjes' ], $regelsArr[ 'tvorkw' ] ) ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Correctie arbeidsduur' ), sprintf( $ICONTEMP[ 23 ], 'tvperc', 10, $regelsArr['tvperc'], 'maanden' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Correctie transactievergoeding' ), sprintf( $ICONTEMP[ 23 ], 'tvamtc', 12,  $regelsArr['tvamtc'], 'Bedrag' ), '', '' );
  if ( $regelsArr[ 'tvsummary' ] > 1 ) $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Opmerking' ), sprintf( $ICONTEMP[ 26 ], 'comment', 4,  $regelsArr[ 'comment' ] , 'aanvullende gegevens' ), '', '' );
/*
 * bijlages
 */
  if ( $regelsArr[ 'tvsummary' ] > 1 && isset($regelsArr[ 'memored_recid' ]) && $regelsArr[ 'memored_recid' ]>0 ) { 
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], ucfirst( 'Bijlages' ), '', '', '' ) ;
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
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ],'', ucfirst( 'Bijlage type' ), sprintf( $ICONTEMP[ 25 ], "atttype", '', Gsm_option( $MOD_GSMOFF[ 'trans_att' ], 6 ) ), '', '','' );	
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Extra omschrijving' ), sprintf( $ICONTEMP[ 23 ], 'attoms', 20, '', 'Bijlage Omschrijving' ), '', '' );
    $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 82 ], $MOD_GSMOFF[ 'line_color' ][ 2 ], '', ucfirst( 'Opmerking ' ), sprintf( $ICONTEMP[ 29 ], 'Attdoc'), '', '', '' );
  }
  /*
   * selections of existing entries 
   */
  if ( $regelsArr[ 'selection_block' ]) {
  /*
   * Bestaande entries zoeken
   */
    $query = "SELECT * FROM `" . $regelsArr[ 'file' ] . "` WHERE `name`= '".$regelsArr[ 'owner_email' ]."' ORDER BY `tvstatus`, `updated` DESC"; 
//	echo __LINE__.$query;
    $results = $database->query( $query );  
    if ( $results && $results->numRows() > 0 ) {
      $regelsArr[ 'toegift' ] .= '<table class="container" width="100%">';
      $regelsArr[ 'toegift' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 4 ], '', ucfirst( 'bestaande cases' ), '', '', '' ) ;
      while ( $row = $results->fetchRow() ) { 
        switch ( $row[ 'tvstatus' ] ) {
          case '1': // edited by schuldeiser
          case '2': // waiting on starting action by legal party
            $regelsArr[ 'toegift' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], $MOD_GSMOFF[ 'som_status' ][ $row[ 'tvstatus' ] ], str_replace ("<br />", "", sprintf( $ICONTEMP[ 31 ], $row['id'], $row[ 'tvref0' ]."_". $row[ 'tvref1name' ].'_'.$row['updated']."_(".$row['id'].")_", '' )), '', '', '' );
            break;
	      case '0': // being processed by legal party		
          case '3': // being processed by legal party
          case '4': // being processed by legal party
          case '5': // being processed by legal party
          case '6': // being processed by legal party
          case '7': // being processed by legal party
          case '8': // being processed by legal party
		  case '9': // completed
		    $regelsArr[ 'toegift' ] .= sprintf( $LINETEMP[ 81 ], $MOD_GSMOFF[ 'line_color' ][ 1 ], $MOD_GSMOFF[ 'som_status' ][ $row[ 'tvstatus' ] ], str_replace ("<br />", "", sprintf( $ICONTEMP[ 32 ], $row['id'], $row[ 'tvref0' ]."_". $row[ 'tvref1name' ].'_'.$row['updated']."_(".$row['id'].")", '' )), '', '', '' );   	   
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
     (isset( $regelsArr['tvbtoamt'] ) && isset( $regelsArr[ 'owner_email' ] ) && $regelsArr['tvbtoamt']>0  && strlen($regelsArr[ 'owner_email'])>12 ) ? $ICONTEMP[ 7 ] :"",
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
    if (isset( $regelsArr['tvbtoamt'] ) && $regelsArr['tvbtoamt']>0  ) {
	  $calc = array ();
      $calcArr = array (
	  	'project' => $regelsArr[ 'project' ],
        'tvgebdat' => $regelsArr[ 'tvgebdat' ], 
        'tvdatin' => $regelsArr[ 'tvdatin' ],
	    'tvdatout' => $regelsArr['tvdatout'],
	    'overgangsregeling' => $regelsArr[ 'overgangsregeling' ],
	    'tvorkw' => $regelsArr['tvorkw'],
	    'tvperc' => $regelsArr['tvperc'],
	    'tvbtoamt' => $regelsArr[ 'tvbtoamt' ],
	    'tvbtoper' => $regelsArr[ 'tvbtoper' ],
	    'tvbtoext' => $regelsArr['tvbtoext'],
	    'tvvakpct' => $regelsArr[ 'tvvakpct' ],
	    'tvprest' => $regelsArr[ 'tvprest' ],
	    'tvgevaar' => $regelsArr['tvgevaar'],
	    'pensioen_offset' => $regelsArr['pensioen_offset'],
	    'pensioen_base' => $regelsArr['pensioen_base'],
	    'trans_loon_periode' => $MOD_GSMOFF [ 'trans_loon_periode' ],
	    'max_vakantie_over' => $regelsArr[ 'max_vakantie_over' ],
	    'max_transitie' => $regelsArr['max_transitie'], //depricated 20151207 
		'maxima_transitie' => $regelsArr['maxima_transitie'],  // toegevoegd 20151207
	    'tvamtc' => $regelsArr[ 'tvamtc' ],
		'tvref0adres' => $regelsArr[ 'tvref0adres' ],
		'tvref1adres' => $regelsArr[ 'tvref1adres' ],
		'tvref2adres' => $regelsArr[ 'tvref2adres' ],
		'tvref0name' => $regelsArr[ 'tvref0name' ],
		'tvref1name' => $regelsArr[ 'tvref1name' ],
		'tvref2name' => $regelsArr[ 'tvref2name' ],
		'tvref2kvk' => $regelsArr[ 'tvref2kvk' ],	
		'tvref2vat' => $regelsArr[ 'tvref2vat' ],
		'tvref2comp' => $regelsArr[ 'tvref2comp' ],
		'tvorkw' => $regelsArr[ 'tvorkw' ],
		'owner_email' => $regelsArr[ 'owner_email' ],
		'tvcalcdate' => $regelsArr[ 'tvcalcdate' ],
		'comment' => $regelsArr[ 'comment' ],
		'file0' => $regelsArr[ 'file0' ],
		'file1' => $regelsArr[ 'file1' ],
		'memored_recid' => $regelsArr[ 'memored_recid' ],
		'tvref0' => $regelsArr[ 'tvref0'  ],		
      );
      $calcArr = array_merge ($calcArr, Gsm_trans_bereken($calcArr, $calc, 1));
      if ( $debug ) {
        Gsm_debug( $calcArr, __LINE__);
      }
      $regelsArr['toegift'] = Gsm_trans_report( $regelsArr, $calcArr, $MOD_GSMOFF, $func = 1 );
      if (isset( $regelsArr[ 'owner_email' ]) && strlen($regelsArr[ 'owner_email'])>12  && $regelsArr[ 'mode' ] == 8) {
      /*
       *  PDF file condities zijn er
       */ 
       // load include with specific layout
        require_once( $place_incl . 'vtransitie_pdf.inc' );  
        $mailArr = array(
          'from' => $regelsArr[ 'email' ],
          'to' => $regelsArr[ 'owner_email' ],
          'cc' => $regelsArr[ 'info_mail'],
          'volgnummer' => $regelsArr[ 'memored_recid' ], 
          'dossier' => $regelsArr[ 'tvref0' ],
          'subject' => $regelsArr[ 'tvref0' ] . ' / ' . $regelsArr[ 'memored_recid' ],
          'link' => LEPTON_URL.PAGES_DIRECTORY.'/transitie.php?section_id='.$section_id.'&command=acc&ref=' . $regelsArr[ 'memored_recid' ]. substr(md5($regelsArr[ 'memored_recid' ]),0,5) ,
          'fromname' => "Contracthulp",
          'toname' => $regelsArr[ 'owner_email' ],
	      'place' => $regelsArr['file0'].'/'.$regelsArr['file1'].'/',
	      'filename' => $regelsArr[ 'memored_recid' ].'_berekening_'.$regelsArr[ 'tvcalcdate' ].'.pdf',
          'url' => LEPTON_URL."/"
        );	
        if ($debug) Gsm_debug($calcArr, __LINE__);
        $mailArr ['file'] = Gsm_out($calcArr, $MOD_GSMOFF, 98, $mailArr ['place'], $mailArr ['filename'], ''); //create pdf file
        if ($debug) Gsm_debug($mailArr, __LINE__);
	    $mailArr[ 'body' ] = Gsm_prout ($MOD_GSMOFF[ 'trans_mail1' ], $mailArr); // format the message
	    $mailArr[ 'body2' ] = Gsm_prout ($MOD_GSMOFF[ 'trans_mail2' ], $mailArr); // format the message
        if ( $wb->mail( $mailArr[ 'from' ], $mailArr[ 'to' ], $mailArr[ 'subject' ], $mailArr[ 'body' ], $mailArr[ 'fromname' ] ) ) $regelsArr[ 'toegift' ] .= $mailArr[ 'body2' ];
	    $mailArr[ 'bodyc' ] = Gsm_prout ($MOD_GSMOFF[ 'trans_mail1c' ], $mailArr); // format the message
	    $help = $wb->mail( $mailArr[ 'from' ], $mailArr[ 'cc' ], $mailArr[ 'subject' ], $mailArr[ 'bodyc' ], $mailArr[ 'fromname' ] ); 
      }
    }
    break;	
}
/*
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