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
 * 20160121 toevoegen van webmaster/website ref
 */
/*
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'login',
// voor versie display
  'modulen' => 'vlogin',
  'versie' => ' v20160121 ',
//database
  'table' => TABLE_PREFIX . 'users',
  'settings' => TABLE_PREFIX . 'settings',
  'standen' => CH_DBBASE . '_standen',
  'stand' => 'standen',
  'adressen' => CH_DBBASE . '_adres',
  'adr'=> 'adres', 
  'webmaster' => '', // add 20160121
//
  'owner' => (isset($settingArr[ 'logo'])) ? $settingArr[ 'logo'] : '',  // ** This is the logo on the pdf **
  'self change' => ( strstr( $set_mode, "admin" ) ) ? false : true,
  'syndic' => 'Admin.',
  'name' => 'login',
  'app' => 'login ', 
  'meterstanden' => ( strstr( $set_mode, "stand" ) ) ? true : false,
  'documents' => ( strstr( $set_mode, "docu" ) ) ? true : false,
// Output en processing
  'recid' => '',
  'descr' => '',
  'head' => '',
  'naam' => array( ),
  'nwnaam' => array( ),
  'adres' => array( ),
  'nwadres' => array( ),
  'contact' => array( ),
  'nwcontact' => array( ),
  'email' => '',
  'nwemail' => '',
  'password' => '',
  'loginname' => '',
  'note' => '',
  'bank' => '',  
  'toegift' => '',
  'info' => '',
  'username' => '',
  'aant' => '',
  'refer' => '',
  'comment' => '',
  'select' => '' ,
  'comp' => 1
);
/*
 * debug used data
 */
if ( $debug ) {
  Gsm_debug ( $regelsArr, __LINE__ );
  Gsm_debug ( $settingArr, __LINE__);
  Gsm_debug ( $_POST, __LINE__ );
  Gsm_debug ( $_GET, __LINE__ );
  Gsm_debug ( $place, __LINE__ );
} //$debug
/*
 * Functions
 */
// get files of recursive subdirectories of certain file types 
function scanDirectories( $rootDir, $allowext, $allowdir, $allData = array( ) ) {
  $dirContent = scandir( $rootDir );
  foreach ( $dirContent as $key => $content ) {
    $path = $rootDir . '/' . $content;
    $ext = substr( $content, strrpos( $content, '.' ) + 1 );
    if ( $content == "." || $content == ".." ) {
    } elseif ( is_dir( $path ) && is_readable( $path ) && in_array( $content, $allowdir ) ) { // recursive callback to open new directory
      $allData = scanDirectories( $path, $allowext, $allowdir, $allData );
    } elseif ( in_array( $ext, $allowext ) ) {
      $allData[ $content ] = $path;
    } //in_array( $ext, $allowext )
  } //$dirContent as $key => $content
  return $allData;
}
/*
 * Lay-out strings
 */
$TEMPLATE[ 3 ] = '
  <h2>{header}</h2>
    {message}
  <div class="container">
    <form name="view" method="post" action="{return}">
      <table>
        <colgroup><col width="20%"><col width="50%"><col width="30%"></colgroup>
        <thead><tr><th> </th><th >{titel8}</th><th> </th></tr></thead>
        <tr><td>{naam}:</td><td>{naam1} {naam2} {naam3} {naam4}</td><td></td></tr>
        <tr><td>{adres}:</td><td>{adres2}</td><td></td></tr>
		<tr><td></td><td>{adres3}</td><td></td></tr>
        <tr><td></td><td>{adres4}</td><td></td></tr>
        <tr><td>{e-mail}</td><td>{email1}</td><td></td></tr>
        <tr><td>{telefoon}</td><td>{tel1}&nbsp;&nbsp;{mob1}</td><td></td></tr>
		  {toegift}
        <tr><td>{opmerking}</td><td>{opm1}</td><td></td></tr>
      </table>
      <table>
        <colgroup><col width="20%"><col width="50%"><col width="30%"></colgroup> 
        <tr><td>{menu1}</td><td></td><td></td><tr> 
      </table>
    </form>
    <form name="logout" action="{logouturl}" method="post">
      <table>
        <colgroup><col width="20%"><col width="50%"><col width="30%"></colgroup>
        <tr><td>{menu2}</td><td></td><td></td><tr>
      </table>
    </form>
  </div>
  <div class="container">
    {description} 
  </div>';
$TEMPLATE[ 4 ] = '
  <h2>{header}</h2>
    {message}
  <div class="container">
    {description}
  </div>
  <div class="container">
    <form name="view" method="post" action="{return}">
      <input type="hidden" name="sh" value="{hash}" />
      <input type="hidden" name="page_id" value="{page_id}" />
      <input type="hidden" name="section_id" value="{section_id}" />
      <table>
        <colgroup><col width="16%"><col width="42%"><col width="42%"></colgroup>
        <thead><tr><th> </th><th>{titel4a}</th><th>{titel4b}</th></tr></thead>
        <tr><td>{recid}</td><td></td><td></td></tr>
        <tr><td>{naam}:</td><td>{naam1}</td><td><input type="text" name="naam1" size="15" value="{naam1n}" placeholder="{naam1c}" autocomplete="off" /></td></tr>
        <tr><td></td><td>{naam2}</td><td><input type="text" name="naam2" size="45" value="{naam2n}" placeholder="{naam2c}" autocomplete="off" /></td></tr>
        <tr><td></td><td>{naam3}</td><td><input type="text" name="naam3" size="15" value="{naam3n}" placeholder="{naam3c}" autocomplete="off" /></td></tr>
        <tr><td></td><td>{naam4}</td><td><input type="text" name="naam4" size="45" value="{naam4n}" placeholder="{naam4c}" autocomplete="off" /></td></tr>
        <tr><td>{adres} :</td><td>{adres1}</td><td><input type="text" name="adres1" size="45" value="{adres1n}" placeholder="{adres1c}" autocomplete="off" /></td></tr>
        <tr><td></td><td>{adres2}</td><td><input type="text" name="adres2" size="45" value="{adres2n}" placeholder="{adres2c}" autocomplete="off" /></td></tr>
        <tr><td></td><td>{adres3}</td><td><input type="text" name="adres3" size="45" value="{adres3n}" placeholder="{adres3c}" autocomplete="off" /></td></tr>
        <tr><td></td><td>{adres4}</td><td><input type="text" name="adres4" size="45" value="{adres4n}" placeholder="{adres4c}" autocomplete="off" /></td></tr>
        <tr><td>{e-mail} :</td><td>{email1}</td><td><input type="text" name="email1" size="45" style="text-transform: lowercase;"value="{email1n}" placeholder="{email1c}" autocomplete="off" /></td></tr>
        <tr><td>{mobiel} :</td><td>{mob1}</td><td><input type="text" name="mob1" size="20" value="{mob1n}" placeholder="{mob1c}" autocomplete="off" /></td></tr>
        <tr><td>{telefoon} :</td><td>{tel1}</td><td><input type="text" name="tel1" size="20" value="{tel1n}" placeholder="{tel1c}" autocomplete="off" /></td></tr>
        <tr><td>{info} :</td><td colspan="2"><textarea rows="3" cols="60" name="info" placeholder="{infoc}" >{infon}</textarea></td><tr>
        <tr><td></td><td>'.$ICONTEMP[ 1 ].'</td><td>'.$ICONTEMP[ 2 ].'</td><tr>
      </table>
    </form>
  </div>';
$TEMPLATE[ 5 ] = '
  <h2>{header}</h2>
    {message}
  <div class="container">
    {description}
  </div>
  <div class="container">
    <form name="login" action="{loginurl}" method="post">
      <input type="hidden" name="redirect" value="{return}" />
      <table>
        <colgroup><col width="20%"><col width="40%"><col width="40%"></colgroup>
        <thead><tr><th> </th><th>{titel1}</th> </th><th></tr></thead>
        <tr><td>{username} :</td><td><input type="text" name="username" size="45" style="text-transform: lowercase;" placeholder="{username}" /></td><td></td><tr>
        <tr><td>{password} :</td><td><input type="password" name="password" placeholder="{passwordL}"/></td><td></td><tr>
        <tr><td> </td><td><input type="submit" name="submit" value="{aanmelden}" class="dbutton" /></td><td></td><tr>
        <tr><td></td><td>{toegift}</td><td></td><tr>
      </table>
    </form>
  </div>';
$TEMPLATE[ 6 ] = '
  <h2>{header}</h2>
   {message}
  <div class="container">
   {description}
  </div>
  <div class="container">
    <form name="view" method="post" action="{return}">
      <input type="hidden" name="sh" value="{hash}" />
      <input type="hidden" name="page_id" value="{page_id}" />
      <input type="hidden" name="section_id" value="{section_id}" />
      <table>
        <colgroup><col width="20%"><col width="40%"><col width="40%"></colgroup>
        <thead><tr><th> </th><th colspan="2">{titel2}</th></tr> </thead>
        <tr><td>{username}</br>(Username) :</td><td><input type="text" name="username" size="45" style="text-transform: lowercase;" placeholder="{username}" autocomplete="off" /></td><td></td><tr>
        <tr><td>{password} :</td><td><input type="password" name="password" placeholder="{passwordL}" autocomplete="off" /></td><td></td><tr>
        <tr><td></td><td><input type="submit" name="command" value="{her_aanmelding}" class="dbutton" /></td><td></td><tr>
        <tr><td></td><td>{toegift}</td><td></td><tr>
      </table>
    </form> 
  </div>';
$TEMPLATE[ 9 ] = '
  <h2>{header}</h2>
    {message}
  <div class="container">
    {description}
  </div>';
$TEMPLATE[ 31 ] = '<table><colgroup><col width="%s%%"><col width="%s%%"><col width="%s%%"><col width="%s%%"></colgroup><thead><tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr></thead>';
$TEMPLATE[ 32 ] = '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>';
$TEMPLATE[ 36 ] = '<tr><td>%s</td><td colspan="2">
  <form name="view" method="post" action="{return}">
    <input type="hidden" name="page_id" value="{page_id}" />
    <input type="hidden" name="section_id" value="{section_id}" />
    <input type="hidden" name="recid" value="{recid}" />
    <input type="text" name="ist1" size="12" value="{ist1new}" placeholder="{ist1c}" autocomplete="off" /> {ist1c}<br/>
    <input type="text" name="ist2" size="12" value="{ist2new}" placeholder="{ist2c}" autocomplete="off" /> {ist2c} <br/>
    <input type="submit" name="command" value="{opgave}" />
    </td><td>%s%s%s</td></tr>';
$TEMPLATE[ 37 ] = '<tr><td>%s</td><td>%s</td><td>%01.2f</td><td>%s</td></tr><tr><td>%s</td><td>%s</td><td>%01.2f</td><td>%s</td></tr>';
$TEMPLATE[ 38 ] = '<tr><td>%s</td><td>%s</td><td>%01.2f</td><td>%s</td></tr><tr><td>%s</td><td>%s</td><td>%01.2f</td><td>%s</td></tr> </table>';
$TEMPLATE[ 39 ] = '</table>';


$MOD_GSMOFF[ 'lgn_aan' ] = 'Uw Gegevens';
$MOD_GSMOFF[ 'lgn_adres' ] = 'Adres';
$MOD_GSMOFF[ 'lgn_add' ] = 'Hallo {toname},<br/><br/>Op Uw verzoek wordt Uw wachtwoord aangepast<br/>Als U de navolgende link activeert kunt U direct inloggen met Uw nieuwe wachtwoord.<br/><a href="{link}">{link}</a><br/><br/>Indien U niet gevraagd heeft om het wachtwoord te wijzigen of als deze melding niet voor U bestemd is,<br/>verwijder deze dan onmiddellijk. <br/><br/><i>webmaster {fromname}</i>';
$MOD_GSMOFF[ 'lgn_adres' ] = 'Adres';
$MOD_GSMOFF[ 'lgn_adres1' ] = 'Firma naam';
$MOD_GSMOFF[ 'lgn_adres2' ] = 'Straat / numer';
$MOD_GSMOFF[ 'lgn_adres3' ] = 'Postcode/woonplaats';
$MOD_GSMOFF[ 'lgn_adres4' ] = 'Land';
$MOD_GSMOFF[ 'lgn_bank' ] = 'Bank (IBAN) nummer';
$MOD_GSMOFF[ 'lgn_bedrijfsvorm' ] = "Bedrijfsvorm";
$MOD_GSMOFF[ 'lgn_com' ] = array ( '1' => 'per post', '2' => 'via mail', '3' => 'via mail/sepa');
$MOD_GSMOFF[ 'lgn_comm' ] = 'Berichten/Rekening/Betaling';
$MOD_GSMOFF[ 'lgn_crea' ] = 'table creation attempt';
$MOD_GSMOFF[ 'lgn_edit' ] = 'Gegevens wijzigen';
$MOD_GSMOFF[ 'lgn_eind' ] = 'Deelname tot';
$MOD_GSMOFF[ 'lgn_email' ] = 'Email';
$MOD_GSMOFF[ 'lgn_ext' ] = 'table extention attempt';
$MOD_GSMOFF[ 'lgn_geb' ] = 'Geboortedatum';
$MOD_GSMOFF[ 'lgn_gsm' ] = 'Mobiel';
$MOD_GSMOFF[ 'lgn_herlogin' ] = 'Wachtwoord vergeten / Wachtwoord aanmaken';
$MOD_GSMOFF[ 'lgn_id' ] = 'id'; 
$MOD_GSMOFF[ 'lgn_ist' ] = 'Huidige waarde';
$MOD_GSMOFF[ 'lgn_kvkref' ] = "KVK nummer";
$MOD_GSMOFF[ 'lgn_login' ] = 'Aanmelden';
$MOD_GSMOFF[ 'lgn_login1' ] = 'E-mail adres en Wachtwoord';
$MOD_GSMOFF[ 'lgn_login2' ] = 'Uw E-mail adres en een wachtwoord naar keuze';
$MOD_GSMOFF[ 'lgn_macht_dat' ] = 'Machtigings Datum';
$MOD_GSMOFF[ 'lgn_macht_ref' ] = 'Machtiging Referentie';
$MOD_GSMOFF[ 'lgn_mess0' ] = 'U heeft nu de mail nodig met de activatie link. Kijk daarvoor in Uw inbox';
$MOD_GSMOFF[ 'lgn_mess1' ] = 'Reeds ingelogd';
$MOD_GSMOFF[ 'lgn_mess2' ] = '(1) Not allowed, (2) link incorrect or (3) expired ';
$MOD_GSMOFF[ 'lgn_mess3' ] = 'U kunt nu inloggen met Uw nieuwe wachtwoord';
$MOD_GSMOFF[ 'lgn_mess4' ] = 'Geen of incorrect mail adres';
$MOD_GSMOFF[ 'lgn_mess4' ] = 'Geen of incorrect mail adres';
$MOD_GSMOFF[ 'lgn_mess5' ] = 'Geen wachtwoord';
$MOD_GSMOFF[ 'lgn_mess7' ] = 'Om de wijziging door te voeren heeft nu de mail nodig met de activatie link. Kijk daarvoor in Uw inbox';
$MOD_GSMOFF[ 'lgn_messa1' ] = 'Hallo {toname},<br/><br/>U heeft verzocht om Uw gegevens te wijzigen. Na deze wijzigingen zijn deze als volgt: <br/>Naam: {uwnaam}<br/>Adres: {uwadres}<br/>E-mail adres: {uwemail}<br/><br/>Over officiele berichten en/of rekeningen wenst {uwnaam} geinformeerd te worden: {uwnote}<br/><br/>Activatielink om de wijziging direct door te voeren:<br/><a href = "{link}">{link}</a><br/><br/><br/>Indien U niet gevraagd heeft om Uw gegevens te wijzigen, gebruik de activatielink niet en<br/>neem onmiddelijk contact op met de {syndic}.<br/><br/><i>webmaster {fromname}</i>';
$MOD_GSMOFF[ 'lgn_messa2' ] = 'Copie {syndic}<br/><br/> Hallo {toname},<br/><br/>U heeft verzocht om Uw gegevens te wijzigen. Na deze wijzigingen zijn deze als volgt: <br/>Naam: {uwnaam}<br/>Adres: {uwadres}<br/>E-mail adres: {uwemail}<br/><br/>Over officiele berichten en/of rekeningen wenst {uwnaam} geinformeerd te worden: {uwnote}<br/><br/>Activatielink om de wijziging direct door te voeren:<br/><a href = "{link}">{link}</a><br/><br/><br/>Indien U niet gevraagd heeft om Uw gegevens te wijzigen, gebruik de activatielink niet en<br/>neem onmiddelijk contact op met de {syndic}.<br/><br/><i>webmaster {fromname}</i>';
$MOD_GSMOFF[ 'lgn_messb' ] = 'De administrateur zal de wijzigingen doorvoeren na controle.';
$MOD_GSMOFF[ 'lgn_messb1' ] = 'Hallo {syndic},<br/><br/>{uwnaam} heeft verzocht om zijn/haar gegevens te wijzigen. Na deze wijzigingen zijn deze als volgt: <br/>Naam: {uwnaam}<br/>Adres: {uwadres}<br/>E-mail adres: {uwemail}<br/><br/>Over officiele berichten en/of rekeningen wenst {uwnaam} geinformeerd te worden: {uwnote}<br/><br/>Activatie link:<br/><a href = "{link}">{link}</a><br/><br/><i>webmaster {fromname}</i>';
$MOD_GSMOFF[ 'lgn_messb2' ] = 'Hallo {toname},<br/><br/>Wij hebben de {syndic} als volgt geinformeerd:<br/><br/>{uwnaam} heeft verzocht om zijn/haar gegevens te wijzigen. Na deze wijzigingen zijn deze als volgt: <br/>Naam: {uwnaam}<br/>Adres: {uwadres}<br/>E-mail adres: {uwemail}<br/><br/>Over officiele berichten en/of rekeningen wenst {uwnaam} geinformeerd te worden: {uwnote}<br/><br/>Indien U niet gevraagd heeft om Uw gegevens te wijzigen of als deze melding niet voor U bestemd is,<br/>neem dan onmiddelijk contact op met de {syndic}.<br/><br/><i>webmaster {fromname}</i>';
$MOD_GSMOFF[ 'lgn_name' ] = 'Name';
$MOD_GSMOFF[ 'lgn_name1' ] = 'Voorvoegsel';
$MOD_GSMOFF[ 'lgn_name2' ] = 'Voornaam';
$MOD_GSMOFF[ 'lgn_name3' ] = 'Tussenvoegsel';
$MOD_GSMOFF[ 'lgn_name4' ] = 'Achternaam';
$MOD_GSMOFF[ 'lgn_nn' ] = 'Onbekend';
$MOD_GSMOFF[ 'lgn_nodata' ] = "No data";
$MOD_GSMOFF[ 'lgn_notitie' ] = 'Notitie';
$MOD_GSMOFF[ 'lgn_num' ] = 'Uw nummer'; 
$MOD_GSMOFF[ 'lgn_num' ] = 'Uw nummer'; 
$MOD_GSMOFF[ 'lgn_opm' ] = 'Opmerking';
$MOD_GSMOFF[ 'lgn_rem' ] = 'Aanvullende gegevens';
$MOD_GSMOFF[ 'lgn_sinds' ] = 'deelnemer sinds';
$MOD_GSMOFF[ 'lgn_soll' ] = 'Wijziging'; 
$MOD_GSMOFF[ 'lgn_tel' ] = 'Telefoon';
$MOD_GSMOFF[ 'lgn_toev' ] = 'Gegevens toevoegen';
$MOD_GSMOFF[ 'lgn_uit' ] = 'Afmelden';
$MOD_GSMOFF[ 'lgn_upd' ] =  'Hallo {toname},<br/><br/>Wij hebben Uw registratie verwerkt.<br/>Als U de navolgende link activeert kunt U direct inloggen.<br/><a href="{link}">{link}</a><br/><br/>Indien U niet om een registratie gevraagd heeft of als deze melding niet voor U bestemd is,<br/>verwijder deze dan onmiddellijk. <br/><br/><i>webmaster {fromname}</i>';
$MOD_GSMOFF[ 'lgn_user' ] = 'E-mail adres';
$MOD_GSMOFF[ 'lgn_vatref' ] = "BTW nummer";
$MOD_GSMOFF[ 'lgn_vatverif' ] = "Verificatie gegevens";
$MOD_GSMOFF[ 'lgn_wacht' ] = 'Wachtwoord';

  
/*
 * Ophalen van reference data
 */
// get lepton settings data
$query = "SELECT * FROM `" . $regelsArr[ 'settings' ] . "`";
$message = $MOD_GSMOFF[ 'error0' ] . $query . "</br>";
$results = $database->query( $query );
$settingsArray = array( );
if ( !$results || $results->numRows() == 0 ) die( $message );
while ( $row = $results->fetchRow() ) {$settingsArray[ $row[ 'name' ] ] = $row[ 'value' ];} //$row = $results->fetchRow()
if ( $debug ) Gsm_debug( $settingsArray, __LINE__ );
$regelsArr['webmaster'] = (isset($settingArr[ 'webmaster'])) ? $settingArr[ 'webmaster'] : $settingsArray[ 'website_title' ]; // add 20160121
/*
 * some job to do
 */
if ( isset( $_GET[ 'command' ] ) && $_GET[ 'command' ] == 'notice' ) {
  // processing data van mail link 
  // id van positie 8 upwards
  $query = "SELECT 
    `" . $regelsArr[ 'adressen' ] . "`.`name`,
    `" . $regelsArr[ 'adressen' ] . "`.`adres`,
    `" . $regelsArr[ 'table' ] . "`.`email`,
    `" . $regelsArr[ 'adressen' ] . "`.`nwname`,
    `" . $regelsArr[ 'adressen' ] . "`.`nwadres`,
    `" . $regelsArr[ 'adressen' ] . "`.`nwemail`,
     `" . $regelsArr[ 'adressen' ] . "`.`id`,
     `" . $regelsArr[ 'table' ] . "`.`username`,
     `" . $regelsArr[ 'table' ] . "`.`user_id`,
     `" . $regelsArr[ 'table' ] . "`.`password`
    FROM `" . $regelsArr[ 'adressen' ] . "`
    LEFT JOIN `" . $regelsArr[ 'table' ] . "`
    ON `" . $regelsArr[ 'adressen' ] . "`.`email` = `" . $regelsArr[ 'table' ] . "`.`email`
    WHERE `user_id` = '" . substr( $_GET[ 'ref' ], 8 ) . "'";
  if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
  $results = $database->query( $query );
  if ( $results->numRows() == 0 ) {
    $msg[ 'err' ] .= $MOD_GSMOFF[ 'LGN_MESS2' ] . "1<br />";
    $regelsArr[  'mode'  ] = 2;
  } //$results->numRows() == 0
  $row = $results->fetchRow();
  // eerste 3 characters van het wachtwoord worden gecontroleerd
  if ( substr( $_GET[ 'ref' ], 0, 3 ) != substr( $row[ 'password' ], 0, 3 ) ) {
    $msg[ 'err' ] .= $MOD_GSMOFF[ 'LGN_MESS2' ] . "2<br />";
    $regelsArr[  'mode'  ] = 2;
  } //substr( $_GET[ 'ref' ], 0, 3 ) != substr( $row[ 'password' ], 0, 3 )
  // eerste 6 characters van de userid worden gecontroleerd
  if ( substr( $_GET[ 'ref' ], 3, 5 ) != substr( $row[ 'username' ], 0, 5 ) ) {
    $msg[ 'err' ] .= $MOD_GSMOFF[ 'LGN_MESS2' ] . "3<br />";
    $regelsArr[  'mode'  ] = 2;
  } //substr( $_GET[ 'ref' ], 3, 5 ) != substr( $row[ 'username' ], 0, 5 )
  if ( $regelsArr[  'mode'  ] != 2 ) {
    $hulpArr = array( );
    if ( $row[ 'email' ] != $row[ 'nwemail' ] ) { $hulpArr[ 'email' ] = $row[ 'nwemail' ]; } 
    if ( $row[ 'name' ] != $row[ 'nwname' ] ) { $hulpArr[ 'display_name' ] = str_replace( "|", " ", $row[ 'nwname' ] ); } 
    if ( $row[ 'email' ] == $row[ 'username' ] && $row[ 'email' ] != $row[ 'nwemail' ] ) { $hulpArr[ 'username' ] = $row[ 'nwemail' ]; } 
    if ( count( $hulpArr ) > 0 ) {
      $query = "UPDATE `" . $regelsArr[ 'table' ] . "` SET " . Gsm_parse( 2, $hulpArr ) . " WHERE `user_id` = '" . $row[ 'user_id' ] . "'";
      if ($debug) $msg[ 'bug' ] .= '<br/>id ' . __LINE__ . ' ' . $query . '<br/>';
      $results = $database->query( $query );
    } //count( $hulpArr ) > 0
    $hulp2Arr = array( );
    // adres
    if ( $row[ 'email' ] != $row[ 'nwemail' ] ) { $hulp2Arr[ 'email' ] = $row[ 'nwemail' ]; } 
    if ( $row[ 'name' ] != $row[ 'nwname' ] ) { $hulp2Arr[ 'name' ] = $row[ 'nwname' ]; }  
    if ( $row[ 'adres' ] != $row[ 'nwadres' ] ) { $hulp2Arr[ 'adres' ] = $row[ 'nwadres' ]; } 
    if ( count( $hulp2Arr ) > 0 ) {
      $query = "UPDATE `" . $regelsArr[ 'adressen' ] . "` SET " . Gsm_parse( 2, $hulp2Arr ) . " WHERE `id` = '" . $row[ 'id' ] . "'";
      if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
      $results = $database->query( $query );
    } //count( $hulp2Arr ) > 0
    $regelsArr[ 'descr' ] .= '<h5>' . $MOD_GSMOFF[ 'lgn_mess3' ] . '</h5><br />';
    $regelsArr[  'mode'  ] = 1;
  } //$regelsArr[  'mode'  ] != 2
} elseif ( isset( $_GET[ 'command' ] ) && $_GET[ 'command' ] == 'reset' ) {
  // processing data van mail link 
  // id van positie 8 upwards
  //  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE `user_id` = '" . substr( $_GET[ 'ref' ], 8 ) . "'";
  $query = "SELECT 
    `" . $regelsArr[ 'adressen' ] . "`.`nwpass`,
    `" . $regelsArr[ 'table' ] . "`.`username`,
    `" . $regelsArr[ 'table' ] . "`.`user_id`,
    `" . $regelsArr[ 'table' ] . "`.`password`
  FROM `" . $regelsArr[ 'adressen' ] . "`
  LEFT JOIN `" . $regelsArr[ 'table' ] . "`
  ON `" . $regelsArr[ 'adressen' ] . "`.`email` = `" . $regelsArr[ 'table' ] . "`.`email`
  WHERE `user_id` = '" . substr( $_GET[ 'ref' ], 8 ) . "'";
  if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
  $results = $database->query( $query );
  if ( $results->numRows() == 0 ) {
    $msg[ 'err' ] .= $MOD_GSMOFF[ 'LGN_MESS2' ] . "1<br />";
    $regelsArr[  'mode'  ] = 2;
  } //$results->numRows() == 0
  $row = $results->fetchRow();
  // eerste 3 characters van het wachtwoord worden gecontroleerd
  if ( substr( $_GET[ 'ref' ], 0, 3 ) != substr( $row[ 'password' ], 0, 3 ) ) {
    $msg[ 'err' ] .= $MOD_GSMOFF[ 'lgn_mess2' ] . "2<br />";
    $regelsArr[  'mode'  ] = 2;
  } //substr( $_GET[ 'ref' ], 0, 3 ) != substr( $row[ 'password' ], 0, 3 )
  // eerste 6 characters van de userid worden gecontroleerd
  if ( substr( $_GET[ 'ref' ], 3, 5 ) != substr( $row[ 'username' ], 0, 5 ) ) {
    $msg[ 'err' ] .= $MOD_GSMOFF[ 'LGN_MESS2' ] . "3<br />";
    $regelsArr[  'mode'  ] = 2;
  } //substr( $_GET[ 'ref' ], 3, 5 ) != substr( $row[ 'username' ], 0, 5 )
  if ( $regelsArr[  'mode'  ] != 2 ) {
    $hulpArr = array( 'password' => $row[ 'nwpass' ]  );
    $query = "UPDATE `" . $regelsArr[ 'table' ] . "` SET " . Gsm_parse( 2, $hulpArr ) . " WHERE `user_id` = '" . $row[ 'user_id' ] . "'";
    if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
    $results = $database->query( $query );
    $regelsArr[ 'descr' ] .= '<h5>' . $MOD_GSMOFF[ 'lgn_mess3' ] . '</h5><br />';
    $regelsArr[  'mode'  ] = 1;
  // bypass GET section ID 
  }
} elseif ( FRONTEND_LOGIN == 'enabled' && VISIBILITY != 'private' && $wb->get_session( 'USER_ID' ) == '' ) {
  // not logged in
  if ( isset( $_GET[ 'section_id' ] ) ) {
    if ( isset( $_POST[ 'command' ] ) && $_POST[ 'command' ] == $MOD_GSMOFF[ 'lgn_herlogin' ] ) {
    // processing data voor wijzigen wachtwoord of nieuw account 
      $regelsArr[ 'loginname' ] = ( isset( $_POST[ 'username' ] ) ) ? Gsm_eval( $_POST[ 'username' ], 3 ) : "";
      if ( $regelsArr[ 'loginname' ] == '' ) {
        $msg[ 'err' ] .= $MOD_GSMOFF[ 'lgn_mess4' ] . "<br />";
        $regelsArr[  'mode'  ] = 2;
      } //$regelsArr[ 'loginname' ] == ''
      $regelsArr[ 'password' ] = ( isset( $_POST[ 'password' ] ) ) ? md5( $_POST[ 'password' ] ) : "";
      if ( $regelsArr[ 'password' ] == '' ) {
        $msg[ 'err' ] .= $MOD_GSMOFF[ 'lgn_mess5' ] . "<br />";
        $regelsArr[  'mode'  ] = 2;
      } //$regelsArr[ 'password' ] == ''
      $hulp = explode( '@', $regelsArr[ 'loginname' ] );
      // check for add or update
      if ( $regelsArr[  'mode'  ] != 2 ) {
        $query = "SELECT 
          `" . $regelsArr[ 'adressen' ] . "`.`name`,
          `" . $regelsArr[ 'adressen' ] . "`.`adres`,
          `" . $regelsArr[ 'adressen' ] . "`.`nwpass`,
          `" . $regelsArr[ 'table' ] . "`.`email`,
          `" . $regelsArr[ 'table' ] . "`.`password`,
          `" . $regelsArr[ 'table' ] . "`.`username`,
          `" . $regelsArr[ 'table' ] . "`.`user_id`
          FROM `" . $regelsArr[ 'table' ] . "`
          LEFT JOIN `" . $regelsArr[ 'adressen' ] . "`
          ON `" . $regelsArr[ 'table' ] . "`.`email` = `" . $regelsArr[ 'adressen' ] . "`.`email`
          WHERE `" . $regelsArr[ 'table' ] . "`.`username` = '" . $regelsArr[ 'loginname' ] . "'";
        if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
        $results = $database->query( $query );
        if ( $results->numRows() == 0 ) {
          // add record
          $hulpArr = array(
            'group_id' => $settingsArray[ 'frontend_signup' ],
            'groups_id' => $settingsArray[ 'frontend_signup' ],
            'active' => 1,
            'statusflags' => 6,
            'username' => $regelsArr[ 'loginname' ],
            'password' => MD5( TABLE_PREFIX . $regelsArr[ 'loginname' ] ),
            'display_name' => $hulp[ 0 ],
            'email' => $regelsArr[ 'loginname' ] 
          );
          $query = "INSERT INTO `" . $regelsArr[ 'table' ] . "` " . Gsm_parse( 1, $hulpArr );
          if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
          $results = $database->query( $query );
          $hulp2Arr = array(
            'email' => $regelsArr[ 'loginname' ],
            'name' => $hulp[ 0 ],
            'nwpass' => $regelsArr[ 'password' ] 
          );
          $query = "SELECT * FROM `" . $regelsArr[ 'adressen' ] . "` WHERE `email` = '" . $regelsArr[ 'loginname' ] . "'";
          if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
          $results = $database->query( $query );
          if ( $results->numRows() == 0 ) {
            // insert
            $query = "INSERT INTO `" . $regelsArr[ 'adressen' ] . "` " . Gsm_parse( 1, $hulp2Arr );
            if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
            $results = $database->query( $query );
          } else {
            //update
            $query = "UPDATE `" . $regelsArr[ 'adressen' ] . "` SET " . Gsm_parse( 2, $hulp2Arr ) . " WHERE `email` = '" . $regelsArr[ 'loginname' ] . "'";
            if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
            $results = $database->query( $query );
          }
           // readback
          $query = "SELECT 
            `" . $regelsArr[ 'adressen' ] . "`.`name`,
            `" . $regelsArr[ 'adressen' ] . "`.`adres`,
            `" . $regelsArr[ 'table' ] . "`.`email`,
            `" . $regelsArr[ 'table' ] . "`.`password`,
            `" . $regelsArr[ 'table' ] . "`.`username`,
            `" . $regelsArr[ 'table' ] . "`.`user_id`
            FROM `" . $regelsArr[ 'table' ] . "`
            LEFT JOIN `" . $regelsArr[ 'adressen' ] . "`
            ON `" . $regelsArr[ 'table' ] . "`.`email` = `" . $regelsArr[ 'adressen' ] . "`.`email`
            WHERE `" . $regelsArr[ 'table' ] . "`.`username` = '" . $regelsArr[ 'loginname' ] . "'";
          if ($debug) $msg[ 'bug' ] .= '<br/>id ' . __LINE__ . ' ' . $query . '<br/>';
          $results = $database->query( $query );
          $row = $results->fetchRow();
          $mailArr = array(
            'from' => $settingsArray[ 'server_email' ],
            'to' => $regelsArr[ 'loginname' ],
            'subject' => $regelsArr['webmaster'] . ' ' . $MOD_GSMOFF[ 'lgn_login' ],
            'body' => $MOD_GSMOFF[ 'lgn_add' ],
            'link' => CH_LOGIN . '&command=reset&ref=' . substr( $row[ 'password' ], 0, 3 ) . substr( $row[ 'username' ], 0, 5 ) . $row[ 'user_id' ],
            'fromname' => $regelsArr['webmaster'],
            'toname' => $hulp[ 0 ] 
           );
        } else {
          // update record
          $row = $results->fetchRow();
          $hulp2Arr = array(
            'email' => $regelsArr[ 'loginname' ],
            'name' => $hulp[ 0 ],
            'nwpass' => $regelsArr[ 'password' ] 
           );
          $query = "SELECT * FROM `" . $regelsArr[ 'adressen' ] . "` WHERE `email` = '" . $regelsArr[ 'loginname' ] . "'";
          if ($debug) $msg[ 'bug' ] .= '<br/>id ' . __LINE__ . ' ' . $query . '<br/>';
          $results = $database->query( $query );
          if ( $results->numRows() == 0 ) {
            // insert
            $query = "INSERT INTO `" . $regelsArr[ 'adressen' ] . "` " . Gsm_parse( 1, $hulp2Arr );
            if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
            $results = $database->query( $query );
          } else {
            //update
            $query = "UPDATE `" . $regelsArr[ 'adressen' ] . "` SET " . Gsm_parse( 2, $hulp2Arr ) . " WHERE `email` = '" . $regelsArr[ 'loginname' ] . "'";
            if ($debug) $msg[ 'bug' ] .= '<br/>id ' . __LINE__ . ' ' . $query . '<br/>';
            $results = $database->query( $query );
          }
          // readback
          $query = "SELECT 
            `" . $regelsArr[ 'adressen' ] . "`.`name`,
            `" . $regelsArr[ 'adressen' ] . "`.`adres`,
            `" . $regelsArr[ 'table' ] . "`.`email`,
            `" . $regelsArr[ 'table' ] . "`.`password`,
            `" . $regelsArr[ 'table' ] . "`.`username`,
            `" . $regelsArr[ 'table' ] . "`.`user_id`
            FROM `" . $regelsArr[ 'table' ] . "`
            LEFT JOIN `" . $regelsArr[ 'adressen' ] . "`
            ON `" . $regelsArr[ 'table' ] . "`.`email` = `" . $regelsArr[ 'adressen' ] . "`.`email`
            WHERE `" . $regelsArr[ 'table' ] . "`.`username` = '" . $regelsArr[ 'loginname' ] . "'";
          if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
          $results = $database->query( $query );
          $row = $results->fetchRow();
          $mailArr = array(
            'from' => $settingsArray[ 'server_email' ],
            'to' => $regelsArr[ 'loginname' ],
            'subject' => $regelsArr['webmaster'] . ' ' . $MOD_GSMOFF[ 'lgn_login' ],
            'body' => $MOD_GSMOFF[ 'lgn_upd' ],
            'link' => CH_LOGIN . '&command=reset&ref=' . substr( $row[ 'password' ], 0, 3 ) . substr( $row[ 'username' ], 0, 5 ) . $row[ 'user_id' ],
            'fromname' => $regelsArr['webmaster'],
            'toname' => str_replace( "|", " ", $row[ 'name' ] ) 
          );
        }
        foreach ( $mailArr as $key => $value ) { $mailArr[ 'body' ] = str_replace( "{" . $key . "}", $value, $mailArr[ 'body' ] ); } 
        if ( $wb->mail( $mailArr[ 'from' ], $mailArr[ 'to' ], $mailArr[ 'subject' ], $mailArr[ 'body' ], $mailArr[ 'fromname' ] ) ) { $success = true; } 
        $regelsArr[ 'descr' ] .= '<h5>' . $MOD_GSMOFF[ 'lgn_mess0' ] . '</h5>';
      } //$regelsArr[  'mode'  ] != 2
    } else { $regelsArr[  'mode'  ] = 2; }
  } else { $regelsArr[  'mode'  ] = 1; }
} elseif ( FRONTEND_LOGIN == 'enabled' && is_numeric( $wb->get_session( 'USER_ID' ) ) ) {
  // logged in 
  if ( isset( $_POST[ 'command' ] ) && $_POST[ 'command' ] == $MOD_GSMOFF[ 'lgn_edit' ] ) {
    $query = "SELECT 
      `" . $regelsArr[ 'adressen' ] . "`.`name`,
      `" . $regelsArr[ 'adressen' ] . "`.`adres`,
      `" . $regelsArr[ 'table' ] . "`.`email`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwname`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwadres`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwemail`,
      `" . $regelsArr[ 'adressen' ] . "`.`note`,
      `" . $regelsArr[ 'adressen' ] . "`.`contact`,
      `" . $regelsArr[ 'adressen' ] . "`.`referlist`,
      `" . $regelsArr[ 'adressen' ] . "`.`info`,
      `" . $regelsArr[ 'adressen' ] . "`.`comp`, 
      `" . $regelsArr[ 'adressen' ] . "`.`comp_kvk`, 
      `" . $regelsArr[ 'adressen' ] . "`.`comp_vat`, 
      `" . $regelsArr[ 'adressen' ] . "`.`bank`,
      `" . $regelsArr[ 'adressen' ] . "`.`aant`,
      `" . $regelsArr[ 'adressen' ] . "`.`id`,
      `" . $regelsArr[ 'table' ] . "`.`user_id`,
      `" . $regelsArr[ 'table' ] . "`.`username`
      FROM `" . $regelsArr[ 'table' ] . "`
      LEFT JOIN `" . $regelsArr[ 'adressen' ] . "`
      ON `" . $regelsArr[ 'table' ] . "`.`email` = `" . $regelsArr[ 'adressen' ] . "`.`email`
      WHERE `" . $regelsArr[ 'table' ] . "`.`user_id` = '" . $_SESSION[ 'USER_ID' ] . "'";
    if ($debug) $msg[ 'bug' ] .=  __LINE__ . ' ' . $query . '<br/>';
    $results = $database->query( $query );
    if ( $results->numRows() == 0 ) { $msg[ 'inf' ] .= __LINE__ . ' missing data !!'; 
    } else {
      $row = $results->fetchRow();
      if ( $debug ) Gsm_debug( $row, __LINE__ );
      $regelsArr[ 'recid' ] = $row[ 'id' ];
      $regelsArr[ 'naam' ] = explode( "|", $row[ 'name' ] );
      $regelsArr[ 'nwnaam' ] = explode( "|", $row[ 'nwname' ] );
      $regelsArr[ 'adres' ] = explode( "|", $row[ 'adres' ] );
      $regelsArr[ 'nwadres' ] = explode( "|", $row[ 'nwadres' ] );
      $regelsArr[ 'email' ] = $row[ 'email' ];
      $regelsArr[ 'nwemail' ] = $row[ 'nwemail' ];
      $regelsArr[ 'note' ] = $row[ 'note' ];
      $regelsArr[ 'bank' ] = $row[ 'bank' ];
      $regelsArr[ 'contact' ] = explode( "|", $row[ 'contact' ] );
      $regelsArr[ 'refer' ] = $row[ 'referlist' ];
      $regelsArr[ 'info' ] = $row[ 'info' ];
      $regelsArr[ 'aant' ] = $row[ 'aant' ];
      $regelsArr[ 'username' ] = $row[ 'username' ];
      if ( $debug )  Gsm_debug( $regelsArr, __LINE__ );
      $regelsArr[  'mode'  ] = 4;
    }
  } elseif ( isset( $_POST[ 'command' ] ) && $_POST[ 'command' ] == $MOD_GSMOFF[ 'tbl_icon' ][1] ) {
    $query = "SELECT 
      `" . $regelsArr[ 'adressen' ] . "`.`name`,
      `" . $regelsArr[ 'adressen' ] . "`.`adres`,
      `" . $regelsArr[ 'table' ] . "`.`email`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwname`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwadres`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwemail`,
      `" . $regelsArr[ 'adressen' ] . "`.`note`,
      `" . $regelsArr[ 'adressen' ] . "`.`contact`,
      `" . $regelsArr[ 'adressen' ] . "`.`referlist`,
      `" . $regelsArr[ 'adressen' ] . "`.`info`,
      `" . $regelsArr[ 'adressen' ] . "`.`bank`,
      `" . $regelsArr[ 'adressen' ] . "`.`aant`,
      `" . $regelsArr[ 'adressen' ] . "`.`id`,
      `" . $regelsArr[ 'table' ] . "`.`user_id`,
      `" . $regelsArr[ 'table' ] . "`.`username`,
      `" . $regelsArr[ 'table' ] . "`.`display_name`, 
      `" . $regelsArr[ 'table' ] . "`.`password`
      FROM `" . $regelsArr[ 'table' ] . "`
      LEFT JOIN `" . $regelsArr[ 'adressen' ] . "`
      ON `" . $regelsArr[ 'table' ] . "`.`email` = `" . $regelsArr[ 'adressen' ] . "`.`email`
      WHERE `" . $regelsArr[ 'table' ] . "`.`user_id` = '" . $_SESSION[ 'USER_ID' ] . "'";
    if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
    $results = $database->query( $query );
    $row = $results->fetchRow();
    if ( $results->numRows() == 0 ) {  $msg[ 'err' ] .= __LINE__ . 'Missing data<br/>'; 
    } else {
      $regelsArr[ 'recid' ] = $row[ 'id' ];
      $regelsArr[ 'naam' ] = explode( "|", $row[ 'name' ] );
      $regelsArr[ 'adres' ] = explode( "|", $row[ 'adres' ] );
      $regelsArr[ 'email' ] = $row[ 'email' ];
      $regelsArr[ 'note' ] = $row[ 'note' ];
      $regelsArr[ 'contact' ] = explode( "|", $row[ 'contact' ] );
      $regelsArr[ 'aant' ] = $row[ 'aant' ];
      $regelsArr[ 'username' ] = $row[ 'username' ];
      $regelsArr[ 'nwnaam' ] = explode( "|", $_POST[ 'naam1' ] . "|" . $_POST[ 'naam2' ] . "|" . $_POST[ 'naam3' ] . "|" . $_POST[ 'naam4' ] );
      $regelsArr[ 'nwadres' ] = explode( "|", $_POST[ 'adres1' ] . "|" . $_POST[ 'adres2' ] . "|" . $_POST[ 'adres3' ] . "|" . $_POST[ 'adres4' ] );
      $regelsArr[ 'nwcontact' ] = explode( "|", $_POST[ 'mob1' ] . "|" . $_POST[ 'tel1' ] );
      $regelsArr[ 'contact' ] = $regelsArr[ 'nwcontact' ];
      $regelsArr[ 'nwemail' ] = $_POST[ 'email1' ];
      $regelsArr[ 'info' ] = $_POST[ 'info' ];
      $hulpArr = array(
        'nwname' => implode( '|', $regelsArr[ 'nwnaam' ] ),
        'nwadres' => implode( '|', $regelsArr[ 'nwadres' ] ),
        'contact' => implode( '|', $regelsArr[ 'nwcontact' ] ),
        'nwemail' => $regelsArr[ 'nwemail' ],
        'info' => $regelsArr[ 'info' ] 
      );
      $regelsArr[  'mode'  ] = 4;
      $mailArr = array(
        'from' => $settingsArray[ 'server_email' ],
        'cc' => $settingsArray[ 'server_email' ],
        'to' => $regelsArr[ 'email' ],
        'subject' => $regelsArr['webmaster'] . ' ' . $MOD_GSMOFF[ 'lgn_rem' ],
        'body' => '',
        'link' => CH_LOGIN . '&command=notice&ref=' . substr( $row[ 'password' ], 0, 3 ) . substr( $row[ 'username' ], 0, 5 ) . $row[ 'user_id' ],
        'fromname' => $regelsArr['webmaster'],
        'toname' => str_replace( "|", " ", $row[ 'display_name' ] ),
        'syndic' => $regelsArr[ 'syndic' ],
        'uwnaam' => $regelsArr[ 'nwnaam' ][ 0 ] . ' ' . $regelsArr[ 'nwnaam' ][ 1 ] . ' ' . $regelsArr[ 'nwnaam' ][ 2 ] . ' ' . $regelsArr[ 'nwnaam' ][ 3 ],
        'uwadres' => $regelsArr[ 'nwadres' ][ 0 ] . ', ' . $regelsArr[ 'nwadres' ][ 1 ] . ' ' . $regelsArr[ 'nwadres' ][ 2 ] . ', ' . $regelsArr[ 'nwadres' ][ 3 ],
        'uwemail' => $regelsArr[ 'nwemail' ],
        'uwnote' => ( isset( $MOD_GSMOFF['lgn_com'][ $regelsArr[ 'note' ] ] ) ) ? $MOD_GSMOFF['lgn_com'][ $regelsArr[ 'note' ] ] : $MOD_GSMOFF[ 'lgn_nn' ],    
      );
      if ($debug) Gsm_debug($mailArr, __LINE__);
      $query = "UPDATE `" . $regelsArr[ 'adressen' ] . "` SET " . Gsm_parse( 2, $hulpArr ) . " WHERE `id` = '" . $row[ 'id' ] . "'";
      if ($debug) $msg[ 'bug' ] .= __LINE__ . ' ' . $query . '<br/>';
      $results = $database->query( $query );
      $menuArr = array( "Wijzig Uw gegevens" );
      // mail ter bevestiging
      if ( $regelsArr[ 'self change' ] ) {
        // wijziging door bezoeker naar bezoeker mail met link en copie naar 
      if ($debug) $msg[ 'bug' ] .= __LINE__ . 'access<br/>';
        $mailArr[ 'body' ] = $MOD_GSMOFF[ 'lgn_messa1' ];
        foreach ( $mailArr as $key => $value ) { $mailArr[ 'body' ] = str_replace( "{" . $key . "}", $value, $mailArr[ 'body' ] ); } //$mailArr as $key => $value
        if ( $wb->mail( $mailArr[ 'from' ], $mailArr[ 'to' ], $mailArr[ 'subject' ], $mailArr[ 'body' ], $mailArr[ 'fromname' ] ) ) { $success = true; } 
        $mailArr[ 'body' ] = $MOD_GSMOFF[ 'lgn_messa2' ];
        foreach ( $mailArr as $key => $value ) { $mailArr[ 'body' ] = str_replace( "{" . $key . "}", $value, $mailArr[ 'body' ] );} 
        if ( $wb->mail( $mailArr[ 'from' ], $mailArr[ 'cc' ], $mailArr[ 'subject' ], $mailArr[ 'body' ], $mailArr[ 'fromname' ] ) ) { $success = true; } 
        $regelsArr[ 'descr' ] .= '<h5>' . $MOD_GSMOFF[ 'lgn_mess7' ] . '</h5>';
      } else {
        // wijziging door syndicus
       if ($debug) $msg[ 'bug' ] .= __LINE__ . 'access<br/>';       
        $mailArr[ 'body' ] = $MOD_GSMOFF[ 'lgn_messb1' ];
        foreach ( $mailArr as $key => $value ) { $mailArr[ 'body' ] = str_replace( "{" . $key . "}", $value, $mailArr[ 'body' ] ); } 
        if ( $wb->mail( $mailArr[ 'from' ], $mailArr[ 'cc' ], $mailArr[ 'subject' ], $mailArr[ 'body' ], $mailArr[ 'fromname' ] ) ) { $success = true; } 
        $mailArr[ 'body' ] = $MOD_GSMOFF[ 'lgn_messb2' ];
        foreach ( $mailArr as $key => $value ) { $mailArr[ 'body' ] = str_replace( "{" . $key . "}", $value, $mailArr[ 'body' ] ); } 
        if ( $wb->mail( $mailArr[ 'from' ], $mailArr[ 'cc' ], $mailArr[ 'subject' ], $mailArr[ 'body' ], $mailArr[ 'fromname' ] ) ) { $success = true; } 
        $regelsArr[ 'descr' ] .= '<h5>' . $MOD_GSMOFF[ 'lgn_messb' ] . '</h5>';
      }
    }
  } else {
    $regelsArr[  'mode'  ] = 3;
    $query = "SELECT 
      `" . $regelsArr[ 'adressen' ] . "`.`name`,
      `" . $regelsArr[ 'adressen' ] . "`.`adres`,
      `" . $regelsArr[ 'table' ] . "`.`email`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwname`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwadres`,
      `" . $regelsArr[ 'adressen' ] . "`.`nwemail`,
      `" . $regelsArr[ 'adressen' ] . "`.`note`,
      `" . $regelsArr[ 'adressen' ] . "`.`bank`,
      `" . $regelsArr[ 'adressen' ] . "`.`contact`,
      `" . $regelsArr[ 'adressen' ] . "`.`referlist`,
      `" . $regelsArr[ 'adressen' ] . "`.`info`,
      `" . $regelsArr[ 'adressen' ] . "`.`comp`, 
      `" . $regelsArr[ 'adressen' ] . "`.`comp_kvk`, 
      `" . $regelsArr[ 'adressen' ] . "`.`comp_vat`, 
      `" . $regelsArr[ 'adressen' ] . "`.`aant`,
      `" . $regelsArr[ 'adressen' ] . "`.`id`,
      `" . $regelsArr[ 'table' ] . "`.`username`
      FROM `" . $regelsArr[ 'table' ] . "`
      LEFT JOIN `" . $regelsArr[ 'adressen' ] . "`
      ON `" . $regelsArr[ 'table' ] . "`.`email` = `" . $regelsArr[ 'adressen' ] . "`.`email`
      WHERE `" . $regelsArr[ 'table' ] . "`.`user_id` = '" . $_SESSION[ 'USER_ID' ] . "'";
    if ($debug) $msg[ 'bug' ] .= '<br/>id ' . __LINE__ . ' ' . $query . '<br/>';
    $results = $database->query( $query );
    if ($debug) $msg[ 'bug' ] .= '<br/>id ' . __LINE__ . ' ' . $query . '<br/>';
    if ( $results->numRows() == 0 ) {
      $msg[ 'err' ] .= __LINE__ . 'Missing data<br/>';
      $regelsArr[  'mode'  ] = 9;
      exit( );
    } else {
      $row = $results->fetchRow();
      if ( $debug ) Gsm_debug( $row, __LINE__ );
      $regelsArr[ 'naam' ] = explode( "|", $row[ 'name' ] );
      $regelsArr[ 'adres' ] = explode( "|", $row[ 'adres' ] );
      $regelsArr[ 'contact' ] = explode( "|", $row[ 'contact' ] );
      $regelsArr[ 'email' ] = $row[ 'email' ];
//bedrijfsgegevens
      $regelsArr[ 'comp' ] = $row[ 'comp' ];
      $regelsArr[ 'comp_vat' ] = $row[ 'comp_vat' ];
      $regelsArr[ 'comp_kvk' ] = $row[ 'comp_kvk' ];
//bank gegevens	 communicatie	   
      $regelsArr[ 'note' ] = $row[ 'note' ];	  
      $regelsArr[ 'bank' ] = $row[ 'bank' ];
// communicatie	  
      $regelsArr[ 'opm' ] = $row[ 'info' ];
      $regelsArr[ 'opm' ] = $row[ 'info' ];	
// Referenties	  
      $referArr = explode( "|", $row[ 'referlist' ] );
      if ( $regelsArr[ 'meterstanden' ] || $regelsArr[ 'documents' ] ) {
        foreach ( $referArr as $key => $value ) {
          $stripped_refer = ( strpos( $value, '/' ) ) ? substr( $value, 0, strpos( $value, '/' ) ) : $value;
          $stripped_refer2 = str_replace( '/', '-', $value );
          if ( $debug ) $msg[ 'bug' ] .= $stripped_refer . ">" . $stripped_refer2 . " key:" . $key . " value: " . $value . '</br>';
          if ( $regelsArr[ 'documents' ] ) {
            $regelsArr[ 'descr' ] .= '<h3>Documenten : ' . $stripped_refer . '</h3>';
            $fileArray = scanDirectories( substr( $place[ 'document' ], 0, -1 ), array( "pdf", "html" ), array( $stripped_refer, $stripped_refer2 ) );
            ksort( $fileArray );
            foreach ( $fileArray as $key => $value ) {
              if ( stripos( $key, ".pdf" ) === false ) {
                  $keyh = str_replace( ".html", "", $key );
                  $regelsArr[ 'descr' ] .= '<p><a href="' . str_replace( $place[ 'document' ], $place[ 'document1' ], $value ) . '">&nbsp;' . $keyh . '</a></p>';
              } else {
                $keyh = str_replace( ".pdf", "", $key );
                $regelsArr[ 'descr' ] .= '<p><a class="pdf" href="' . str_replace( $place[ 'document' ], $place[ 'document1' ], $value ) . '">&nbsp;' . $keyh . '</a></p>';
              }
            } //$fileArray as $key => $value
          } //$regelsArr[ 'documents' ]
          if ( $regelsArr[ 'meterstanden' ] ) {
            $regelsArr[ 'descr' ] .= '<h3>Standen : ' . $stripped_refer . '</h3>';
            $query = " SELECT * FROM `" . $regelsArr[ 'standen' ] . "` 
              WHERE `refer` = '" . $stripped_refer . "' 
              OR `refer` = '" . $stripped_refer2 . "' 
              AND `active` > '3' 
              ORDER BY `name` , `datumsoll`, `reference`";
            if ($debug) $msg[ 'bug' ] .= '<br/>id ' . __LINE__ . ' ' . $query . '<br/>';
            $results = $database->query( $query );
            $bucket = '';
            $first_done = false;
            while ( $row = $results->fetchRow() ) {
              if ( $bucket != $row[ 'name' ] ) {
                if ( $first_done ) {
                  if ( $processArr[ 'process' ] == 1 && $processArr[ 'count1' ] - $processArr[ 'count3' ] != 0 ) {
                    $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 38 ], '', $MOD_GSMOFF[ 'betaald' ], $processArr[ 'count2' ], '', '', $MOD_GSMOFF[ 'open' ], $processArr[ 'count1' ] - $processArr[ 'count3' ], '', 9 );
                  } elseif ( $processArr[ 'process' ] == 2 ) {
                    $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 38 ], '', $MOD_GSMOFF[ 'verbruik' ], $processArr[ 'count0' ] - $processArr[ 'count-1' ], $MOD_GSMOFF[ 'periode' ], '', '', ( $processArr[ 'count0' ] - $processArr[ 'count-1' ] ) / $processArr[ 'countddate-1' ] * 365, $MOD_GSMOFF[ 'jaar' ], 9 );
                  } else {
                    $regelsArr[ 'descr' ] .= $TEMPLATE[ 39 ];
                  }
                } //$first_done
                $first_done = true;
                $bucket = $row[ 'name' ];
                // get processing
                $set_bucket = ( isset( $standenArr[ strtolower( $bucket ) ] ) ) ? $standenArr[ strtolower( $bucket ) ] : "";
                $set_part = explode( "|", $set_bucket );
                foreach ( $set_part as $key => $value ) {  } //$set_part as $key => $value
                // check if complete and provide defaults
                  $processArr = array(
                    'width1' => ( isset( $set_part[ 8 ] ) ) ? Gsm_eval( $set_part[ 8 ], $func = 8, $upper = 80, $lower = 5 ) : 25,
                    'width2' => ( isset( $set_part[ 9 ] ) ) ? Gsm_eval( $set_part[ 9 ], $func = 8, $upper = 80, $lower = 5 ) : 25,
                    'width3' => ( isset( $set_part[ 10 ] ) ) ? Gsm_eval( $set_part[ 10 ], $func = 8, $upper = 80, $lower = 5 ) : 25,
                    'width4' => ( isset( $set_part[ 11 ] ) ) ? Gsm_eval( $set_part[ 11 ], $func = 8, $upper = 80, $lower = 5 ) : 25,
                    'head1' => $row[ 'name' ],
                    'head2' => ( isset( $set_part[ 0 ] ) ) ? $set_part[ 0 ] : '',
                    'head3' => ( isset( $set_part[ 1 ] ) ) ? $set_part[ 1 ] : '',
                    'head4' => ( isset( $set_part[ 2 ] ) ) ? $set_part[ 2 ] : '',
                    'field1' => ( isset( $set_part[ 3 ] ) ) ? $set_part[ 3 ] : 'reference',
                    'field2' => ( isset( $set_part[ 4 ] ) ) ? $set_part[ 4 ] : 'datumist',
                    'field3' => ( isset( $set_part[ 5 ] ) ) ? $set_part[ 5 ] : 'standist',
                    'field4' => ( isset( $set_part[ 6 ] ) ) ? $set_part[ 6 ] : 'omschrijving',
                    'process' => ( isset( $set_part[ 7 ] ) ) ? $set_part[ 7 ] : 5,
                    'count-1' => 0,
                    'count0' => 0,
                    'count1' => 0,
                    'count2' => 0,
                    'count3' => 0,
                    'countn' => 0,
                    'countddate-1' => 0,
                    'countddate0' => 0 
                  );
                  $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 31 ], $processArr[ 'width1' ], $processArr[ 'width2' ], $processArr[ 'width3' ], $processArr[ 'width4' ], $processArr[ 'head1' ], $processArr[ 'head2' ], $processArr[ 'head3' ], $processArr[ 'head4' ] );
                } //$bucket != $row[ 'name' ]
                if ( $row[ 'datumist' ] == '0000-00-00' && $row[ 'active' ] == 9 ) {
                  $help = sprintf( $TEMPLATE[ 36 ], $row[ $processArr[ 'field1' ] ], '', '', $MOD_GSMOFF[ 'opgave' ] . $row[ 'datumsoll' ] );
                  $help2 = '';
                  if ( $processArr[ 'countn' ] > 1 ) {
                    $help2 = sprintf( "%1.2f", $processArr[ 'count0' ] + ( $processArr[ 'count0' ] - $processArr[ 'count-1' ] ) / $processArr[ 'countddate-1' ] * ( strtotime( date( "Y-m-d" ) ) - $processArr[ 'countddate0' ] ) / 86400 );
                  } //$processArr[ 'countn' ] > 1
                  $parseViewArray = array(
                    'recid' => $row[ 'id' ],
                    'ist1c' => $MOD_GSMOFF[ 'LGN_ST_DAT' ],
                    'ist1new' => date( "Y-m-d" ),
                    'ist2c' => $MOD_GSMOFF[ 'LGN_ST_STND' ],
                    'ist2new' => $help2,
                    'opgave' => $MOD_GSMOFF[ 'LGN_OPGAVE' ],
                    'page_id' => $page_id,
                    'section_id' => $section_id,
                    'return' => CH_RETURN 
                  );
                  foreach ( $parseViewArray as $key => $value ) { $help = str_replace( "{" . $key . "}", $value, $help ); } 
                $regelsArr[ 'descr' ] .= $help;
              } elseif ( $row[ 'datumist' ] == '0000-00-00' && $processArr[ 'process' ] == 2 ) {
                $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 37 ], 'test2', '', '', $row[ 'datumist' ], '', '', '', strtotime( $row[ 'datumist' ] ) );
              } else {
                $processArr[ 'countn' ]++;
                $processArr[ 'count-1' ] = $processArr[ 'count0' ];
                $processArr[ 'count0' ] = $row[ 'standist' ];
                $processArr[ 'count1' ] = $processArr[ 'count1' ] + $row[ 'standsoll' ];
                $processArr[ 'count2' ] = $processArr[ 'count2' ] + $row[ 'standist' ];
                $processArr[ 'count3' ] = ( $processArr[ 'count2' ] > $processArr[ 'count1' ] ) ? $processArr[ 'count3' ] + $processArr[ 'count1' ] : $processArr[ 'count3' ] + $processArr[ 'count2' ];
                $processArr[ 'countddate-1' ] = ( $processArr[ 'countddate0' ] > 0 ) ? ( strtotime( $row[ 'datumist' ] ) - $processArr[ 'countddate0' ] ) / 86400 : 0;
                $processArr[ 'countddate0' ] = strtotime( $row[ 'datumist' ] );
                $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 32 ], $row[ $processArr[ 'field1' ] ], $row[ $processArr[ 'field2' ] ], $row[ $processArr[ 'field3' ] ], $row[ $processArr[ 'field4' ] ] );
              }
            } //$row = $results->fetchRow()
            if ( $first_done ) {
              if ( $processArr[ 'process' ] == 1 && $processArr[ 'count1' ] - $processArr[ 'count3' ] != 0 ) {
                $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 38 ], '', $MOD_GSMOFF[ 'betaald' ], $processArr[ 'count2' ], '', '', $MOD_GSMOFF[ 'open' ], $processArr[ 'count1' ] - $processArr[ 'count3' ], '', 9 );
              } elseif ( $processArr[ 'process' ] == 2 ) {
                $regelsArr[ 'descr' ] .= sprintf( $TEMPLATE[ 38 ], '', $MOD_GSMOFF[ 'verbruik' ], $processArr[ 'count0' ] - $processArr[ 'count-1' ], $MOD_GSMOFF[ 'periode' ], '', '', ( $processArr[ 'count0' ] - $processArr[ 'count-1' ] ) / $processArr[ 'countddate-1' ] * 365, $MOD_GSMOFF[ 'jaar' ], 9 );
              } else {
                $regelsArr[ 'descr' ] .= $TEMPLATE[ 39 ];
              }
            } //$first_done
          } //$regelsArr[ 'meterstanden' ]
        } //$referArr as $key => $value
      } //$regelsArr[ 'meterstanden' ] || $regelsArr[ 'documents' ]
    }
  }
} //FRONTEND_LOGIN == 'enabled' && is_numeric( $wb->get_session( 'USER_ID' ) )
if ( $debug ) $msg[ 'bug' ] .= 'Mode : ' . $regelsArr[  'mode'  ] . '</br>';
// at this point the database query for the relevant records prepared
/*
 * display preparation
 */
/* 
switch ( $regelsArr[  'mode'  ] ) {
  case 1:
    break;
  case 2:
    break;
  default: // default list 
    break;
} //$regelsArr[  'mode'  ]
*/
/*
 * next function preparation
 */
switch ( $regelsArr[  'mode'  ] ) {
  default: // default list 
    break;
} //$regelsArr[  'mode'  ]
/*
 * the output to the screen
 */
$regelsArr[ 'hash' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
$_SESSION[ 'page_h' ] = $regelsArr[ 'hash' ];
if ( $debug ) Gsm_debug( $regelsArr, __LINE__ );
switch ( $regelsArr[  'mode'  ] ) {
  case 1:
    $parseViewArray = array(
      'header' => $MOD_GSMOFF[ 'lgn_login' ],
      'titel1' => $MOD_GSMOFF[ 'lgn_login1' ],
      'loginurl' => LOGIN_URL,
      'aanmelden' => $TEXT[ 'LOGIN' ],
      'username' => $MOD_GSMOFF[ 'lgn_user' ],
      'password' => $MOD_GSMOFF[ 'lgn_wacht' ],
      'passwordL' => strtolower( $MOD_GSMOFF[ 'lgn_wacht' ] ),
      'thispage' => CH_RETURN,
      'page_id' => $page_id,
      'section_id' => $section_id,
      'description' => $regelsArr[ 'descr' ],
      'toegift' => '<a href="' . CH_LOGIN . '">'.$MOD_GSMOFF[ 'lgn_herlogin'].'</a>',
      'message' => message( $msg, $debug ),
      'hash' => $regelsArr[ 'hash' ],
      'return' => CH_LOGIN 
    );
	$prout .= Gsm_prout ($TEMPLATE[ 5 ], $parseViewArray);
    break;
  case 2: // not logged in: show form
    $parseViewArray = array(
      'header' => $MOD_GSMOFF[ 'lgn_herlogin' ],
      'titel2' => $MOD_GSMOFF[ 'lgn_login2' ],
      'username' => $MOD_GSMOFF[ 'lgn_user' ],
      'password' => $MOD_GSMOFF[ 'lgn_wacht' ],
      'passwordL' => strtolower( $MOD_GSMOFF[ 'lgn_wacht' ] ),
      'her_aanmelding' => $MOD_GSMOFF[ 'lgn_herlogin' ],
      'page_id' => $page_id,
      'section_id' => $section_id,
      'description' => $regelsArr[ 'descr' ],
      'toegift' => '',
      'message' => message( $msg, $debug ),
      'hash' => $regelsArr[ 'hash' ],
      'return' => CH_RETURN 
    );
	$prout .= Gsm_prout ($TEMPLATE[ 6 ], $parseViewArray);
    break;
 case 3:
    $regelsArr[ 'toegift' ]="";
	if( isset( $MOD_GSMOFF['lgn_com'][ $regelsArr[ 'note' ] ] ) ) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], "Communicatie", $MOD_GSMOFF['lgn_com'][ $regelsArr[ 'note' ] ] );	
    if  (strlen($regelsArr[ 'bank' ]) >5) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], "IBAN", $regelsArr[ 'bank' ] );
	if( $regelsArr[ 'comp' ]>1 ) {
      $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], "Bedrijfsvorm", $MOD_GSMOFF['lgn_vorm'][ $regelsArr[ 'comp' ] ] );
      if  (strlen($regelsArr[ 'adres' ][ 0 ]) >3) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", $regelsArr[ 'adres' ][ 0 ] );		  
      if  (strlen($regelsArr[ 'comp_kvk' ]) >3) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], "Kvk", $regelsArr[ 'comp_kvk' ] );	
      if  (strlen($regelsArr[ 'comp_vat' ]) >3) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], "BTW", $regelsArr[ 'comp_vat' ] );	
    }	
// adres label
		if( $regelsArr[ 'comp' ] ==1 ) {
		// prive label
		  $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], "Post adres", str_replace ("  ", " ", sprintf("%s %s %s %s",
		    $regelsArr[ 'naam' ][ 0 ],
		    ( isset( $regelsArr[ 'naam' ][ 1 ] ) ) ? $regelsArr[ 'naam' ][ 1 ] : '',
		    ( isset( $regelsArr[ 'naam' ][ 2 ] ) ) ? $regelsArr[ 'naam' ][ 2 ] : '',
		    ( isset( $regelsArr[ 'naam' ][ 3 ] ) ) ? $regelsArr[ 'naam' ][ 3 ] : '')));	  
			if (isset( $regelsArr[ 'adres' ][ 1 ] ) && strlen( $regelsArr[ 'adres' ][ 1 ] ) >5 ) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", $regelsArr[ 'adres' ][ 1 ]);
			if (isset( $regelsArr[ 'adres' ][ 2 ] ) && strlen( $regelsArr[ 'adres' ][ 2 ] ) >5 ) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", $regelsArr[ 'adres' ][ 2 ]);
			if (isset( $regelsArr[ 'adres' ][ 3 ] ) && strlen( $regelsArr[ 'adres' ][ 3 ] ) >5 ) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", $regelsArr[ 'adres' ][ 3 ]);	
		} else {
		// bedrijfslabel label
		  $tel = ( isset( $regelsArr[ 'contact' ][ 0 ] ) && strlen($regelsArr[ 'contact' ][ 0 ]) >5 ) ? $regelsArr[ 'contact' ][ 0 ] : ' ';
		  $tel = ( isset( $regelsArr[ 'contact' ][ 1 ] ) && strlen($regelsArr[ 'contact' ][ 1 ]) >5 ) ? $regelsArr[ 'contact' ][ 1 ] : $tel;
		  $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], "Post adres", $regelsArr[ 'adres' ][ 0 ] );	  
		  $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", str_replace ( "  ", " ", sprintf("f.a.o. %s %s %s %s (%s)",
		    $regelsArr[ 'naam' ][ 0 ],
		    ( isset( $regelsArr[ 'naam' ][ 1 ] ) ) ? $regelsArr[ 'naam' ][ 1 ] : '',
		    ( isset( $regelsArr[ 'naam' ][ 2 ] ) ) ? $regelsArr[ 'naam' ][ 2 ] : '',
		    ( isset( $regelsArr[ 'naam' ][ 3 ] ) ) ? $regelsArr[ 'naam' ][ 3 ] : '',
		    ( strlen($tel) >5 ) ? "Tel:".str_replace ( "  ", " ", $tel) : "" )));	
			if (isset( $regelsArr[ 'adres' ][ 1 ] ) && strlen( $regelsArr[ 'adres' ][ 1 ] ) >5 ) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", $regelsArr[ 'adres' ][ 1 ]);
			if (isset( $regelsArr[ 'adres' ][ 2 ] ) && strlen( $regelsArr[ 'adres' ][ 2 ] ) >5 ) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", $regelsArr[ 'adres' ][ 2 ]);
			if (isset( $regelsArr[ 'adres' ][ 3 ] ) && strlen( $regelsArr[ 'adres' ][ 3 ] ) >5 ) $regelsArr[ 'toegift' ] .= sprintf($LINETEMP[ 11 ], " ", $regelsArr[ 'adres' ][ 3 ]);	
		}
	
	
	$parseViewArray = array(
      'header' => $MOD_GSMOFF[ 'lgn_aan' ],
      'titel8' => $MOD_GSMOFF[ 'lgn_aan' ], 
      'afmelden' => $MOD_GSMOFF[ 'lgn_uit' ],
// naam/adres
      'naam' => $MOD_GSMOFF[ 'lgn_name' ],
      'naam1' => $regelsArr[ 'naam' ][ 0 ],
      'naam2' => ( isset( $regelsArr[ 'naam' ][ 1 ] ) ) ? $regelsArr[ 'naam' ][ 1 ] : '',
      'naam3' => ( isset( $regelsArr[ 'naam' ][ 2 ] ) ) ? $regelsArr[ 'naam' ][ 2 ] : '',
      'naam4' => ( isset( $regelsArr[ 'naam' ][ 3 ] ) ) ? $regelsArr[ 'naam' ][ 3 ] : '',
      'adres' => $MOD_GSMOFF[ 'lgn_adres' ],
      'adres1' => $regelsArr[ 'adres' ][ 0 ],
      'adres2' => ( isset( $regelsArr[ 'adres' ][ 1 ] ) ) ? $regelsArr[ 'adres' ][ 1 ] : '',
      'adres3' => ( isset( $regelsArr[ 'adres' ][ 2 ] ) ) ? $regelsArr[ 'adres' ][ 2 ] : '',
      'adres4' => ( isset( $regelsArr[ 'adres' ][ 3 ] ) ) ? $regelsArr[ 'adres' ][ 3 ] : '',
      'e-mail' => $MOD_GSMOFF[ 'lgn_email' ],
      'email1' => $regelsArr[ 'email' ],
//telefoon
      'telefoon' => 'Telefoon',
      'mob1' => ( isset( $regelsArr[ 'contact' ][ 0 ] ) ) ? $regelsArr[ 'contact' ][ 0 ] : ' ',
      'tel1' => ( isset( $regelsArr[ 'contact' ][ 1 ] ) ) ? $regelsArr[ 'contact' ][ 1 ] : ' ',
// opmerkingen
      'opmerking' => ( isset( $regelsArr[ 'opm' ] ) && strlen( $regelsArr[ 'opm' ] ) > 1 ) ? 'Opmerking' : '',
      'opm1' => ( isset( $regelsArr[ 'opm' ] ) ) ? $regelsArr[ 'opm' ] : '',
//	overige  
	  'toegift' => $regelsArr[ 'toegift' ],
      'menu1' => sprintf($ICONTEMP[ 13 ], $MOD_GSMOFF[ 'lgn_edit' ], $MOD_GSMOFF[ 'tbl_icon2' ][21]),
      'menu2' => sprintf($ICONTEMP[ 13 ], $MOD_GSMOFF[ 'lgn_uit' ], $MOD_GSMOFF[ 'tbl_icon2' ][20]),
//      'page_id' => $page_id,
//      'section_id' => $section_id,
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ),
//      'hash' => $regelsArr[ 'hash' ],
      'logouturl' => LOGOUT_URL.'?redirect='.$_SERVER['SCRIPT_NAME'],
      'return' => CH_RETURN 
    );
   	$prout .= Gsm_prout ($TEMPLATE[ 3 ], $parseViewArray);
    break;
  case 4:
    if ( !isset( $regelsArr[ 'naam' ][ 0 ] ) ) $regelsArr[ 'naam' ][ 0 ] = '';
    if ( !isset( $regelsArr[ 'naam' ][ 1 ] ) ) $regelsArr[ 'naam' ][ 1 ] = '';
    if ( !isset( $regelsArr[ 'naam' ][ 2 ] ) ) $regelsArr[ 'naam' ][ 2 ] = '';
    if ( !isset( $regelsArr[ 'naam' ][ 3 ] ) ) $regelsArr[ 'naam' ][ 3 ] = '';
    if ( !isset( $regelsArr[ 'adres' ][ 0 ] ) ) $regelsArr[ 'adres' ][ 0 ] = '';
    if ( !isset( $regelsArr[ 'adres' ][ 1 ] ) ) $regelsArr[ 'adres' ][ 1 ] = '';
    if ( !isset( $regelsArr[ 'adres' ][ 2 ] ) ) $regelsArr[ 'adres' ][ 2 ] = '';
    if ( !isset( $regelsArr[ 'adres' ][ 3 ] ) ) $regelsArr[ 'adres' ][ 3 ] = '';
    $parseViewArray = array(
      'header' => $MOD_GSMOFF[ 'lgn_edit' ],
      'titel4a' => $MOD_GSMOFF[ 'lgn_ist' ],
      'titel4b' => $MOD_GSMOFF[ 'lgn_soll' ],
// algemeene data	  
      'id' => ( $regelsArr[ 'recid' ] != "" ) ? $MOD_GSMOFF[ 'lgn_id' ] : '',
      'recid' => ( isset( $regelsArr[ 'recid' ] ) ) ? $regelsArr[ 'recid' ] : '',
      'naam' => $MOD_GSMOFF[ 'lgn_name' ],
      'naam1' => $regelsArr[ 'naam' ][ 0 ],
      'naam2' => $regelsArr[ 'naam' ][ 1 ],
      'naam3' => $regelsArr[ 'naam' ][ 2 ],
      'naam4' => $regelsArr[ 'naam' ][ 3 ],	  
      'naam1n' => ( isset( $regelsArr[ 'nwnaam' ][ 0 ] ) && strlen( $regelsArr[ 'nwnaam' ][ 0 ] ) > 1 ) ? $regelsArr[ 'nwnaam' ][ 0 ] : $regelsArr[ 'naam' ][ 0 ],
      'naam2n' => ( isset( $regelsArr[ 'nwnaam' ][ 1 ] ) && strlen( $regelsArr[ 'nwnaam' ][ 1 ] ) > 1 ) ? $regelsArr[ 'nwnaam' ][ 1 ] : $regelsArr[ 'naam' ][ 1 ],
      'naam3n' => ( isset( $regelsArr[ 'nwnaam' ][ 2 ] ) && strlen( $regelsArr[ 'nwnaam' ][ 2 ] ) > 1 ) ? $regelsArr[ 'nwnaam' ][ 2 ] : $regelsArr[ 'naam' ][ 2 ],
      'naam4n' => ( isset( $regelsArr[ 'nwnaam' ][ 3 ] ) && strlen( $regelsArr[ 'nwnaam' ][ 3 ] ) > 1 ) ? $regelsArr[ 'nwnaam' ][ 3 ] : $regelsArr[ 'naam' ][ 3 ],
      'naam1c' => $MOD_GSMOFF[ 'lgn_name1' ],
      'naam2c' => $MOD_GSMOFF[ 'lgn_name2' ],
      'naam3c' => $MOD_GSMOFF[ 'lgn_name3' ],
      'naam4c' => $MOD_GSMOFF[ 'lgn_name4' ],
	  
      'adres' => $MOD_GSMOFF[ 'lgn_adres' ],
      'adres1' => $regelsArr[ 'adres' ][ 0 ],
      'adres2' => $regelsArr[ 'adres' ][ 1 ],
      'adres3' => $regelsArr[ 'adres' ][ 2 ],
      'adres4' => $regelsArr[ 'adres' ][ 3 ],	  
      'adres1n' => ( isset( $regelsArr[ 'nwadres' ][ 0 ] ) && strlen( $regelsArr[ 'nwadres' ][ 0 ] ) > 1 ) ? $regelsArr[ 'nwadres' ][ 0 ] : $regelsArr[ 'adres' ][ 0 ],
      'adres2n' => ( isset( $regelsArr[ 'nwadres' ][ 1 ] ) && strlen( $regelsArr[ 'nwadres' ][ 1 ] ) > 1 ) ? $regelsArr[ 'nwadres' ][ 1 ] : $regelsArr[ 'adres' ][ 1 ],
      'adres3n' => ( isset( $regelsArr[ 'nwadres' ][ 2 ] ) && strlen( $regelsArr[ 'nwadres' ][ 2 ] ) > 1 ) ? $regelsArr[ 'nwadres' ][ 2 ] : $regelsArr[ 'adres' ][ 2 ],
      'adres4n' => ( isset( $regelsArr[ 'nwadres' ][ 3 ] ) && strlen( $regelsArr[ 'nwadres' ][ 3 ] ) > 1 ) ? $regelsArr[ 'nwadres' ][ 3 ] : $regelsArr[ 'adres' ][ 3 ],
      'adres1c' => $MOD_GSMOFF[ 'lgn_adres1' ],
      'adres2c' => $MOD_GSMOFF[ 'lgn_adres2' ],
      'adres3c' => $MOD_GSMOFF[ 'lgn_adres3' ],
      'adres4c' => $MOD_GSMOFF[ 'lgn_adres4' ],	  	  
	  
      'e-mail' => $MOD_GSMOFF[ 'lgn_email' ],
      'email1' => $regelsArr[ 'email' ],	  
      'email1n' => ( isset( $regelsArr[ 'nwemail' ] ) && strlen( $regelsArr[ 'nwemail' ] ) > 1 ) ? $regelsArr[ 'nwemail' ] : $regelsArr[ 'email' ],
      'email1c' => $MOD_GSMOFF[ 'lgn_user' ],	  
	  
      'info' => $MOD_GSMOFF[ 'lgn_opm' ],
      'infon' => ( isset( $regelsArr[ 'info' ] ) ) ? $regelsArr[ 'info' ] : '',
      'infoc' => $MOD_GSMOFF[ 'lgn_rem' ],
// telefoonnummer blok	  
      'mobiel' => $MOD_GSMOFF[ 'lgn_gsm' ],
      'mob1' => ( isset( $regelsArr[ 'contact' ][ 0 ] ) && strlen( $regelsArr[ 'contact' ][ 0 ] ) ) ? $regelsArr[ 'contact' ][ 0 ] : '',
      'mob1n' => ( isset( $regelsArr[ 'contact' ][ 0 ] ) ) ? $regelsArr[ 'contact' ][ 0 ] : '',	  
      'mob1c' => $MOD_GSMOFF[ 'lgn_num' ],	  

      'telefoon' => $MOD_GSMOFF[ 'lgn_tel' ],
      'tel1' => ( isset( $regelsArr[ 'contact' ][ 1 ] ) && strlen( $regelsArr[ 'contact' ][ 1 ] ) ) ? $regelsArr[ 'contact' ][ 1 ] : '',
      'tel1n' => ( isset( $regelsArr[ 'contact' ][ 1 ] ) ) ? $regelsArr[ 'contact' ][ 1 ] : '',
      'tel1c' => $MOD_GSMOFF[ 'lgn_num' ],
//	overige  
      'page_id' => $page_id,
      'section_id' => $section_id,

      'kopregels' => $regelsArr[ 'head' ],
      'description' => $regelsArr[ 'descr' ],
      'selection' => $regelsArr[ 'select' ],
      'message' => message( $msg, $debug ),
      'module' => $module,	  

      'return' => CH_RETURN 
    );
	$prout .= Gsm_prout ($TEMPLATE[ 4 ], $parseViewArray);
    break;
  default: //logged in met data
    $parseViewArray = array(
      'header' => PAGE_TITLE,
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ) 
    );
	$prout .= Gsm_prout ($TEMPLATE[ 9 ], $parseViewArray);    break;
} //$regelsArr[  'mode'  ]
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?> 