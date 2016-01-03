<?php
/*
 *  @module         Office module
 *  @version        see info.php of this module
 *  @author         Gerard Smelt
 *  @copyright      2015, Gerard Smelt
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
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
 * v20151122  addition of 4 fields in the addressdatabse (comp, comp kvk, comp vat and  compverif)
 *
 */
/*
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'login',
// voor versie display
  'modulen' => 'xlogin',
  'versie' => 'v20151122',
// datastructures 
  'table' => TABLE_PREFIX.'users',
  'settings'=> TABLE_PREFIX.'settings',
  'standen' => CH_DBBASE.'_standen',
  'stand' => 'standen',
  'adressen' => CH_DBBASE.'_adres',
  'adr'=> 'adres',
  'syndic' => 'Admin.', 
  'name' => 'login',
  'app' => 'login ',  
// for display en pdf output 
  'print_regels'=> 1,
  'items' => 1,
  'seq' => (isset($_POST[ 'next' ])) ? $_POST[ 'next' ] : 0,
  'n' => 0,
  'qty' => 30,
  'today_pf' => date( "_Ymd_His" ),
  'owner' => (isset($settingArr[ 'logo'])) ? $settingArr[ 'logo'] : '',
  'cols'=> array(20, 35, 100, 0, 0, 0),
  'kop' =>array( 0=>"ref", 1=>"naam", 2=>"adres", 3=>"e-mail"),
// search
  'search' => '',
  'search_mysql' => '', 
// Output en processing
  'recid' => '',
  'descr' => '',
  'head' => '',
  'naam' => array(),
  'nwnaam' => array(),
  'adres' => array(),
  'nwadres' => array(),
  'note' => '',
  'nwnote' => '',
  'contact' => array(),
  'email' => '',
  'nwemail' => '',
  'info' => '',    
  'comp' => '',  
  'comp_kvk' => '',   
  'comp_vat' => '',   
  'comp_verif' => '',   
  'nwcomp' => '',  
  'nwcomp_kvk' => '',   
  'nwcomp_vat' => '',   
  'nwcomp_verif' => '',   
  'username' => '',
  'bank' => '',
  'macht_dat' => '',
  'macht_ref' => '',
  'geb' => '',
  'sinds' => '',
  'eind' => '',
  'aant' => '',
  'refer' => '',  
  'comment' => '',  
  'select' => '', 
  'update' => '',
  'nieuw' => true,
);
$regelsArr['project'] = $regelsArr[ 'app' ] . ' - Overzicht';

$regelsArr['items']=count($regelsArr ['kop']);
if ( $debug ) {
  Gsm_debug ( $regelsArr, __LINE__ );
  Gsm_debug ( $settingArr, __LINE__);
  Gsm_debug ( $_POST, __LINE__ );
  Gsm_debug ( $_GET, __LINE__ );
  Gsm_debug ( $place, __LINE__ );
} //$debug
/*
 * Layout template
 *
 * in language module
 * $template[0-3] in language module
 * $icontemp [0-19]
 * $linetemp [0-19]
 */
// overrule standaard  strings
//$MOD_GSMOFF ['tbl_icon'][7] = 'Compleet';
// extend text strings
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
// overrule standard function
//$ICONTEMP[ 8 ] = '<input class="modules" name="command" type="submit" value="' . $MOD_GSMOFF[ 'change' ] . '" style="width: 100%;" />';  //1
// extend standard function
//$LINETEMP[ 80 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>'.CH_CR;
$TEMPLATE[8]= '
  <h3>{header}</h3>
    {message}
  <form name="view" method="post" action="{return}">
  <input type="hidden" name="module" value="{module}" />
  <input type="hidden" name="page_id" value="{page_id}" />
  <input type="hidden" name="section_id" value="{section_id}" />
  <input type="hidden" name="recid" value="{recid}" />  
  <div class="container">  
  <table class="inhoud" width="100%">
  <colgroup><col width="16%"><col width="42%"><col width="42%"></colgroup>
  <thead><tr><th> </th><th>{titel4a}</th><th>{titel4b}</th></tr></thead>
  <tr><td>{id}</td><td>{recid}</td><td></td></tr>
  <tr><td>{naam}:</td><td>{naam1}</td><td><input type="text" name="naam1" size="15"  value="{naam1n}" placeholder="{naam1c}" autocomplete="off" /></td></tr>
  <tr><td></td><td>{naam2}</td><td><input type="text" name="naam2" size="45" value="{naam2n}" placeholder="{naam2c}" autocomplete="off" /></td></tr>
  <tr><td></td><td>{naam3}</td><td><input type="text" name="naam3" size="15" value="{naam3n}" placeholder="{naam3c}" autocomplete="off" /></td></tr>
  <tr><td></td><td>{naam4}</td><td><input type="text" name="naam4" size="45" value="{naam4n}" placeholder="{naam4c}" autocomplete="off" /></td></tr>
  <tr><td>{adres} :</td><td>{adres1}</td><td><input type="text" name="adres1" size="45" value="{adres1n}" placeholder="{adres1c}" autocomplete="off" /></td></tr>
  <tr><td></td><td>{adres2}</td><td><input type="text" name="adres2" size="45" value="{adres2n}" placeholder="{adres2c}" autocomplete="off" /></td></tr>
  <tr><td></td><td>{adres3}</td><td><input type="text" name="adres3" size="45" value="{adres3n}" placeholder="{adres3c}" autocomplete="off" /></td></tr>
  <tr><td></td><td>{adres4}</td><td><input type="text" name="adres4" size="45" value="{adres4n}" placeholder="{adres4c}" autocomplete="off" /></td></tr>
  <tr><td>{e-mail} :</td><td>{email1}</td><td><input type="text" name="email1" size="45" style="text-transform: lowercase;" value="{email1n}" placeholder="{email1c}" autocomplete="off" /></td></tr>
  <tr><td>{info} :</td><td colspan="2"><textarea rows="3" cols="60" name="info" placeholder="{infoc}" >{infon}</textarea></td><tr>
  </table>
    </div>
    <div class="container">
  <table class="inhoud" width="100%">  
  <colgroup><col width="16%"><col width="42%"><col width="42%"></colgroup>
  <tr><td>{mobiel} :</td><td><input type="text" name="mob1" size="20" value="{mob1n}" placeholder="{mob1c}" autocomplete="off" /></td><td></td></tr>
  <tr><td>{telefoon} </td><td><input type="text" name="tel1" size="20" value="{tel1n}" placeholder="{tel1c}" autocomplete="off" /></td><td></td></tr>
  </table>
  </div>
  <div class="container">
  <table class="inhoud" width="100%">
  <tr><td>{Userid}</td><td>{userid}</td><td></td></tr>
  <tr><td>{refer} :</td><td><input type="text" name="refer" size="15" value="{refern}" placeholder="{referc}" autocomplete="off" /></td><td></td></tr>
  <tr><td>{communicatie} :</td><td>{wijze1}</td><td><select name="wijze1">{wijze_opt}</select></td></tr>
  </table>
  </div>
  <div class="container">
    <table class="inhoud" width="100%">
	  <colgroup><col width="26%"><col width="52%"><col width="22%"></colgroup>
      <tr><td>{vorm} :</td><td><select name="vorm1">{vorm_opt}</select></td><td.</td></tr>
      <tr><td>{kvk_ref} :</td><td><input type="text" name="kvk_ref" size="24" value="{kvk_refn}" placeholder="{kvk_refc}" autocomplete="off" /></td><td></td></tr>  
      <tr><td>{vat_ref} :</td><td><input type="text" name="vat_ref" size="24" value="{vat_refn}" placeholder="{vat_refc}" autocomplete="off" /></td><td></td></tr>
      <tr><td>{vat_verif} :</td><td colspan="2"><textarea rows="3" cols="60" name="vat_verif" placeholder="{vat_verifc}" >{vat_verifn}</textarea></td><tr>
    </table>
  </div>
  <div class="container">
  <table class="inhoud" width="100%">
  <tr><td>{bank} :</td><td><input type="text" name="bank" size="60" value="{bankn}" placeholder="{bankc}" autocomplete="off" /></td><td></td></tr>
  <tr><td>{macht_ref} :</td><td><input type="text" name="macht_ref" size="60" value="{macht_refn}" placeholder="{macht_refc}" autocomplete="off" /></td><td></td></tr>  
  <tr><td>{macht_dat} :</td><td><input type="text" name="macht_dat" size="60" value="{macht_datn}" placeholder="{macht_datc}" autocomplete="off" /></td><td></td></tr>
  <tr><td>{geb} :</td><td><input type="text" name="geb" size="15" value="{gebn}" placeholder="{gebc}" autocomplete="off" /></td><td></td></tr>
  </table>
  </div>
  <div class="container">
  <table class="inhoud" width="100%">
  <tr><td>{sinds} :</td><td><input type="text" name="sinds" size="15" value="{sindsn}" placeholder="{sindsc}" autocomplete="off" /></td><td></td></tr>
  <tr><td>{eind} :</td><td><input type="text" name="eind" size="15" value="{eindn}" placeholder="{eindc}" autocomplete="off" /></td><td></td></tr>
  <tr><td>{aant} :</td><td colspan="2"><textarea rows="3" cols="60" name="aant" placeholder="{aantc}" >{aantn}</textarea></td><tr>
  </table>
  </div>
  <div class="container">
  <table class="footer" width="100%">
    {selection}
  </table>
  </form>
  <table class="inhoud" width="100%">
     {kopregels}
    {description}
  </table>  
  </div>';
$TEMPLATE[ 9 ] = '
  <h3>{header}</h3>
    {message}
  <div class="container">
  </div>
  <div class="container">
  <form name="view" method="post" action="{return}">
  <input type="hidden" name="module" value="{module}" />
  <input type="hidden" name="page_id" value="{page_id}" />
  <input type="hidden" name="section_id" value="{section_id}" />
  <table class="inhoud" width="100%">
    {kopregels}
    {description}
  </table> 
  <table class="footer" width="100%">
    {selection}
  </table>
  </form>
  </div>';
$LINETEMP[ 70 ] = '<input type="hidden" name="recid" value="%1$s" >';
$LINETEMP[ 72 ] = '<td colspan="2" class="setting_name" align="left" >%1$s&nbsp;:</td>';
$LINETEMP[ 74 ] = '<td colspan="2" class="setting_value" ><input maxlength="%2$s" type="text" name="%1$s" value="%3$s" /></td>';
$LINETEMP[ 91 ] = '<colgroup><col width="28%%"><col width="7%%"><col width="25%%"><col width="35%%"><col width="5%%"></colgroup>
<thead><tr><th>%1$s</th><th>%2$s</th><th>%3$s</th><th>%4$s</th><th>%5$s</th></tr></thead>';
$LINETEMP[ 92 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>';
$LINETEMP[ 93 ] = '<input type="checkbox" name="vink[]" value="%2$s">&nbsp;%1$s';
$LINETEMP[ 95 ] = '<a href="' . CH_RETURN . '&command=select&module={module}&recid=%2$s">%1$s</a>';
$LINETEMP[ 96 ] = '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>';
$LINETEMP[ 97 ] = '<tr %1$s><td>%2$s %3$s<td>%4$s</td></tr>';
$LINETEMP[ 98 ] = '<tr><td colspan="2">%1$s</td><td colspan="3">%2$s</td></tr>';


/*
 * Ophalen van reference when files do not yet exist
 */
$jobs = array();
$jobs[] = "CREATE TABLE IF NOT EXISTS `" .$regelsArr ['standen'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `zoek` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,

  `standtype` int(7) NOT NULL,
  `refer` varchar(255) NOT NULL,
  `adresid` int(11) NOT NULL,
  `omschrijving` varchar(255) NOT NULL,
  `datumsoll` date NOT NULL,
  `datumist` date NOT NULL,
  `standsoll` decimal(9,2) NOT NULL,
  `standist` decimal(9,2) NOT NULL,
  `reference` varchar(255) NOT NULL,  
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
$jobs[] = "CREATE TABLE IF NOT EXISTS `" .$regelsArr ['adressen'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `zoek` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,

  `referlist` VARCHAR( 255 ) NOT NULL,
  `adresid` INT( 11 ) NOT NULL, 
  `adres` VARCHAR( 255 ) NOT NULL,
  `email` VARCHAR( 255 ) NOT NULL,
  `note` int( 11 ) NOT NULL,
  
  `contact` VARCHAR( 255 ) NOT NULL,
  `info` TEXT NOT NULL,
  
  `nwname` VARCHAR( 255 ) NOT NULL,
  `nwadres` VARCHAR( 255 ) NOT NULL,
  
  `nwnote` INT( 11 ) NOT NULL,
  `nwemail` VARCHAR( 255 ) NOT NULL,
  `nwpass` VARCHAR( 255 ) NOT NULL, 
  
  `comp` int( 11 ) NOT NULL,  
  `comp_kvk` varchar(64) NOT NULL,
  `comp_vat` varchar(64) NOT NULL,
  `comp_verif` TEXT NOT NULL,
  
  `bank` varchar(64) NOT NULL,
  `macht_ref` varchar(64) NOT NULL,
  `macht_dat` date NOT NULL,
  
  `geb` date NOT NULL,
  `sinds` date NOT NULL,
  `eind` date NOT NULL,
  `aant` TEXT NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['stand']."','opzoek', 'name|refer')";
$jobs[] = "INSERT INTO `".CH_DBBASE."` (`section`, `table`, `name`, `value`) VALUES( '0', '".$regelsArr ['adr']."','opzoek', 'name|referlist')";
/*
 * Ophalen van reference data and ensure reference data is there
 */
// get lepton settings data
$query = "SELECT * FROM `" . $regelsArr['settings']."`";
$message = $MOD_GSMOFF[ 'error0' ] . $query . "</br>";
$settingsArray = array ();
$results = $database->query( $query ); 
if ( !$results || $results->numRows() == 0 ) die( $message );
while ( $row = $results->fetchRow() ) { $settingsArray[$row['name']]= $row['value']; }
if ($debug) Gsm_debug($settingsArray, __LINE__); 
$login_ok=true;
if ($settingsArray['frontend_login']=='false') {$msg[ 'inf' ] .= __LINE__.' login disabled<br/>';$login_ok=false;}

//check presence of the used tables
$query = "SHOW TABLE STATUS LIKE '" . CH_DBBASE . "%'";
$message = $MOD_GSMOFF[ 'error0'] .__LINE__. $query . "</br>";
$tableArr = array();
$results = $database->query($query);
if (!$results || $results && $results->numRows() == 0) die( $message );
while($row = $results->fetchRow()) { $tableArr[$row['Name']]= $row['Rows']; }
if ($debug) Gsm_debug($tableArr, __LINE__); 
if (!isset($tableArr[$regelsArr ['standen']]) || !isset($tableArr[$regelsArr ['adressen']])) {
  // create if not existing
  $msg[ 'inf' ] .= $MOD_GSMOFF['lgn_crea'].'<br/>';
  $errors = array();
  foreach($jobs as $query) {$database->query( $query ); if ( $database->is_error() ) $errors[] = $database->get_error();}
  if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
  // load adres data
  $query = "SELECT * FROM `" . $regelsArr ['table'] ."` ORDER BY `user_id`";
  $results = $database->query($query);
  while ( $row = $results->fetchRow() ) { 
    $query2 = "SELECT * FROM `" . $regelsArr ['adressen'] ."` WHERE `email`='".$row['email']."'"; 
    $result2 = $database->query($query2);
    if ( $result2 && $result2->numRows() == 0 ) {
      $hulpArr= array();
      $hulpArr ['name']= $row['display_name'];
      $hulpArr ['email']= $row['email'];
      $query3 = "INSERT INTO `".$regelsArr[ 'adressen' ]."` ". Gsm_parse (1,$hulpArr);  
      $result3 = $database->query($query3);
    }
  }  
}
// check presence of extended fields 
$query = "DESCRIBE " . $regelsArr['adressen'];
$message = $MOD_GSMOFF['error0'] . $query . "</br>";
$results = $database->query($query);
if (!$results || $results->numRows() == 0) die($message);
$update1=true; $update2=true;
while ($row = $results->fetchRow()) {
  if($row['Field'] == "macht_ref") $update1=false;
  if($row['Field'] == "comp_kvk") $update2=false;
} //$row = $results->fetchRow()
if ($update1) {
  $msg[ 'inf' ] .= $MOD_GSMOFF['lgn_ext'].'<br/>';
  $extra= array ();
  $extra[]="ALTER TABLE `" .$regelsArr ['adressen'] . "` ADD `macht_ref` VARCHAR(64) NOT NULL AFTER `bank`";
  $extra[]="ALTER TABLE `" .$regelsArr ['adressen'] . "` ADD `macht_dat` date NOT NULL AFTER `macht_ref`";
  // create if not existing
  $errors = array();
  foreach($extra as $query) {$database->query( $query ); if ( $database->is_error() ) $errors[] = $database->get_error();}
  if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
}
if ($update2) {
  $msg[ 'inf' ] .= $MOD_GSMOFF['lgn_ext'].'<br/>';
  $extra= array ();
  $extra[]="ALTER TABLE `" .$regelsArr ['adressen'] . "` ADD `comp` INT( 11 ) NOT NULL AFTER `nwpass`";
  $extra[]="ALTER TABLE `" .$regelsArr ['adressen'] . "` ADD `comp_kvk` varchar(64) NOT NULL AFTER `comp`";
  $extra[]="ALTER TABLE `" .$regelsArr ['adressen'] . "` ADD `comp_vat` varchar(64) NOT NULL AFTER `comp_kvk`";
  $extra[]="ALTER TABLE `" .$regelsArr ['adressen'] . "` ADD `comp_verif` TEXT NOT NULL AFTER `comp_vat`";
  // create if not existing
  $errors = array();
  foreach($extra as $query) {$database->query( $query ); if ( $database->is_error() ) $errors[] = $database->get_error();}
  if (count($errors) > 0) $admin->print_error( implode("<br />n", $errors), 'javascript: history.go(-1);');
}
/*
// settings standen
$query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='".$page_id."' ORDER BY `id`";
$message = $MOD_GSMOFF['error0'] .__LINE__. $query . "</br>";
$standenArr = array();
$results = $database->query( $query ); 
if ( !$results || $results->numRows() == 0 ) die( $message );
while ( $row = $results->fetchRow() ) { $standenArr[$row['name']]= $row['value']; }  
// pick name of entry
if (isset($standenArr[$regelsArr['name']])) { $set_part= explode ("|",$standenArr[$regelsArr['name']]);
  if (isset($set_part[0])) $regelsArr['name']=$set_part[0];
}
if ($debug) Gsm_debug($standenArr, __LINE__); 
*/
if ($debug) Gsm_debug($regelsArr, __LINE__); 
unset( $query );
 /******************
 *
 * some job to do
 * standard parameters 
 * com = command
 * recid = record ID
 * sel + selection string
 */
if ( isset( $_POST[ 'command' ] ) ) {
  switch ( $_POST[ 'command' ] ) {
    case $MOD_GSMOFF[ 'tbl_icon' ][1]:
      if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
      if(!isset($_POST[ 'vink' ][0])) {
        $regelsArr ['mode'] = 9; 
        $msg[ 'inf' ] .= __LINE__.' NO SELECTION <br/>';
        break;}  
      $query = "SELECT 
        `" . $regelsArr[ 'adressen' ] . "`.`name`,
        `" . $regelsArr[ 'adressen' ] . "`.`adres`,
        `" . $regelsArr[ 'adressen' ] . "`.`email`,
        `" . $regelsArr[ 'adressen' ] . "`.`nwname`,
        `" . $regelsArr[ 'adressen' ] . "`.`nwadres`,
        `" . $regelsArr[ 'adressen' ] . "`.`nwemail`,
        `" . $regelsArr[ 'adressen' ] . "`.`note`,
        `" . $regelsArr[ 'adressen' ] . "`.`nwnote`,
        `" . $regelsArr[ 'adressen' ] . "`.`contact`,
        `" . $regelsArr[ 'adressen' ] . "`.`referlist`,
        `" . $regelsArr[ 'adressen' ] . "`.`info`,
	    `" . $regelsArr[ 'adressen' ] . "`.`comp`, 
        `" . $regelsArr[ 'adressen' ] . "`.`comp_kvk`, 
	    `" . $regelsArr[ 'adressen' ] . "`.`comp_vat`, 
	    `" . $regelsArr[ 'adressen' ] . "`.`comp_verif`, 
        `" . $regelsArr[ 'adressen' ] . "`.`bank`,
        `" . $regelsArr[ 'adressen' ] . "`.`macht_ref`,
        `" . $regelsArr[ 'adressen' ] . "`.`macht_dat`,      
        `" . $regelsArr[ 'adressen' ] . "`.`geb`,
        `" . $regelsArr[ 'adressen' ] . "`.`sinds`,
        `" . $regelsArr[ 'adressen' ] . "`.`eind`,
        `" . $regelsArr[ 'adressen' ] . "`.`aant`,
        `" . $regelsArr[ 'adressen' ] . "`.`id`,
        `" . $regelsArr[ 'table' ] . "`.`username`
        FROM `" . $regelsArr[ 'adressen' ] . "`
        LEFT JOIN `" . $regelsArr[ 'table' ] . "`
        ON `" . $regelsArr[ 'adressen' ] . "`.`email` = `" . $regelsArr[ 'table' ] . "`.`email`
        WHERE `" . $regelsArr[ 'adressen' ] . "`.`id`= '".$_POST[ 'vink' ][0]."'"; 
//  case $MOD_GSMOFF['tbl_icon'][3]: // add
        if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
        $regelsArr ['mode'] = 8;
        break;
    case $MOD_GSMOFF['tbl_icon'][11]: //print
        if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
	    $regelsArr['filename_pdf'] = strtolower( $regelsArr[ 'project' ] ) . '.pdf';
		require_once( $place_incl . 'pdf.inc' );  
        $regelsArr ['mode'] = 9;
        break;
	case $MOD_GSMOFF[ 'tbl_icon' ][4]:
    case $MOD_GSMOFF[ 'tbl_icon' ][5]:
        if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>'; 
        // nieuw of update
        if(isset($_POST[ 'recid' ]) && $_POST[ 'recid' ]!="" ) {
          $regelsArr ['nieuw']= false;
          $query = "SELECT 
            `" . $regelsArr[ 'adressen' ] . "`.`name`,
            `" . $regelsArr[ 'adressen' ] . "`.`adres`,
            `" . $regelsArr[ 'adressen' ] . "`.`email`,
            `" . $regelsArr[ 'adressen' ] . "`.`note`,
            `" . $regelsArr[ 'adressen' ] . "`.`contact`,
            `" . $regelsArr[ 'adressen' ] . "`.`referlist`,
            `" . $regelsArr[ 'adressen' ] . "`.`info`,
            `" . $regelsArr[ 'adressen' ] . "`.`comp`, 
            `" . $regelsArr[ 'adressen' ] . "`.`comp_kvk`, 
            `" . $regelsArr[ 'adressen' ] . "`.`comp_vat`, 
            `" . $regelsArr[ 'adressen' ] . "`.`comp_verif`, 
            `" . $regelsArr[ 'adressen' ] . "`.`bank`,
            `" . $regelsArr[ 'adressen' ] . "`.`macht_ref`,
            `" . $regelsArr[ 'adressen' ] . "`.`macht_dat`,              
            `" . $regelsArr[ 'adressen' ] . "`.`geb`,
            `" . $regelsArr[ 'adressen' ] . "`.`sinds`,
            `" . $regelsArr[ 'adressen' ] . "`.`eind`,
            `" . $regelsArr[ 'adressen' ] . "`.`aant`,
            `" . $regelsArr[ 'adressen' ] . "`.`id`
            FROM `" . $regelsArr[ 'adressen' ] . "`
            WHERE `" . $regelsArr[ 'adressen' ] . "`.`id`= '".$_POST[ 'recid' ]."'"; 
          if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>'; 
          $results = $database->query( $query );
          $row = $results->fetchRow(); 
          $regelsArr ['recid'] = $row['id'];    
          $regelsArr ['naam'] = explode ("|", $row['name']);
          $regelsArr ['adres'] = explode ("|", $row['adres']);
          $regelsArr ['email'] = $row['email'];
		  
          $regelsArr ['note'] = $row['note'];
          $regelsArr ['contact'] = explode ("|", $row['contact']);
		  
          $regelsArr ['refer'] = $row ['referlist'];
          $regelsArr ['info'] = $row['info'];
          
          $regelsArr ['comp'] = $row['comp'];
          $regelsArr ['comp_kvk'] = $row['comp_kvk'];
          $regelsArr ['comp_vat'] = $row['comp_vat'];
          $regelsArr ['comp_verif'] = $row['comp_verif'];	
          	  
          $regelsArr ['bank'] = $row['bank'];
          $regelsArr ['macht_ref'] = $row['macht_ref'];
          $regelsArr ['macht_dat'] = $row['macht_dat']; 
		  
          $regelsArr ['geb'] = $row['geb'];
          $regelsArr ['sinds'] = $row['sinds'];
          $regelsArr ['eind'] = $row['eind'];
          $regelsArr ['aant'] = $row['aant'];
        }  else  {
          $regelsArr ['nieuw']= true;
        }      
        $regelsArr ['nwnaam'] = explode ("|",  $_POST[ 'naam1' ]."|".$_POST[ 'naam2' ]."|".$_POST[ 'naam3' ]."|".$_POST[ 'naam4' ]);
        $regelsArr ['nwadres'] = explode ("|",  $_POST[ 'adres1' ]."|".$_POST[ 'adres2' ]."|".$_POST[ 'adres3' ]."|".$_POST[ 'adres4' ]);
        $regelsArr ['nwemail'] = $_POST[ 'email1'];
        $regelsArr ['nwnote'] = $_POST[ 'wijze1'];
        $regelsArr ['nwinfo'] = $_POST['info'];
        $regelsArr ['nwcontact'] = explode ("|",  $_POST[ 'mob1' ]."|".$_POST[ 'tel1' ]);
        $regelsArr ['nwrefer'] = $_POST[ 'refer' ];
        
        $regelsArr ['nwcomp'] = $_POST['vorm1'];	
        $regelsArr ['nwcomp_vat'] = $_POST['vat_ref'];	
        $regelsArr ['nwcomp_kvk'] = $_POST['kvk_ref'];	
        $regelsArr ['nwcomp_verif'] = $_POST['vat_verif'];	
        		
        $regelsArr ['nwbank'] = $_POST['bank'];
        $regelsArr ['nwmacht_ref'] = $_POST['macht_ref'];
        $regelsArr ['nwmacht_dat'] = Gsm_eval( $_POST[ 'macht_dat' ], 9, '2030-01-01', '1900-01-01' );
        $regelsArr ['nwgeb'] = Gsm_eval( $_POST[ 'geb' ], 9, '2030-01-01', '1900-01-01' );
        $regelsArr ['nwsinds'] = Gsm_eval( $_POST['sinds'], 9, '2030-01-01', '1970-01-01' );
        $regelsArr ['nweind'] = Gsm_eval( $_POST['eind'], 9, '2030-01-01', '1970-01-01' );
        $regelsArr ['nwaant'] = $_POST['aant'];
		
        $hulpArr= array(); $hulp2Arr= array();
        $update_ok=false; $mail_dif=false;
        // naam changed
        if ($debug) Gsm_debug($regelsArr ['naam'], __LINE__); 
        if ($debug) Gsm_debug($regelsArr ['nwnaam'], __LINE__); 
        if ( implode('|', $regelsArr ['naam']) != implode('|', $regelsArr ['nwnaam']) ) { 
          $hulpArr ['name']= implode('|', $regelsArr ['nwnaam']);
          $hulpArr ['nwname']= '|||';
          $hulp2Arr ['display_name']= implode(' ', $regelsArr ['nwnaam']);
          $msg[ 'inf' ] .= $MOD_GSMOFF ['TH_NAME'].' : '.str_replace( '|', ' ',$hulpArr ['name']) .'<br/>';
          $update_ok=true;
        }
        // adres changed
        if ( implode('|', $regelsArr ['adres']) != implode('|', $regelsArr ['nwadres']) ) { 
          $hulpArr ['adres']= implode('|', $regelsArr ['nwadres']);
          $hulpArr ['nwadres']= '|||';
          $msg[ 'inf' ] .= $MOD_GSMOFF [ 'lgn_adres' ].' : '.str_replace( '|', ' ',$hulpArr ['adres']).'<br/>';
          $update_ok=true;
        }
        if ($regelsArr ['note']  != $regelsArr ['nwnote'] ) {
          $hulpArr ['note']= $regelsArr ['nwnote'];
          $hulpArr ['nwnote'] = $regelsArr ['nwnote'];
          $msg[ 'inf' ] .= $MOD_GSMOFF ['lgn_comm' ].'<br/>';
          $update_ok=true;
        }   
        if (implode ("|", $regelsArr ['contact']) != implode ("|", $regelsArr ['nwcontact'])) {
          $hulpArr ['contact']= implode ("|", $regelsArr ['nwcontact']);
          $msg[ 'inf' ] .= $MOD_GSMOFF['lgn_num'].'<br/>';
          $update_ok=true;
        }
        if ($regelsArr ['info']  != $regelsArr[ 'nwinfo' ] ) {
          $hulpArr ['info']= $regelsArr ['nwinfo'];
          $regelsArr ['info']= $regelsArr ['nwinfo'];
          $msg[ 'inf' ] .= $MOD_GSMOFF ['lgn_notitie' ].'<br/>';
          $update_ok=true;
        } 
      if ($regelsArr ['comp']  != $regelsArr[ 'nwcomp' ] ) {
          $hulpArr ['comp']= $regelsArr ['nwcomp'];
          $regelsArr ['comp']= $regelsArr ['nwcomp'];
          $update_ok=true;
        } 
        if ($regelsArr ['comp_kvk']  != $regelsArr[ 'nwcomp_kvk' ] ) {
          $hulpArr ['comp_kvk']= $regelsArr ['nwcomp_kvk'];
          $regelsArr ['comp_kvk']= $regelsArr ['nwcomp_kvk'];
          $update_ok=true;
        } 
        if ($regelsArr ['comp_vat']  != $regelsArr[ 'nwcomp_vat' ] ) {
          $hulpArr ['comp_vat']= $regelsArr ['nwcomp_vat'];
          $regelsArr ['comp_vat']= $regelsArr ['nwcomp_vat'];
          $update_ok=true;
		} 
        if ($regelsArr ['comp_verif']  != $regelsArr[ 'nwcomp_verif' ] ) {
          $hulpArr ['comp_verif']= $regelsArr ['nwcomp_verif'];
          $regelsArr ['comp_verif']= $regelsArr ['nwcomp_verif'];
          $update_ok=true;
		}
        if ($regelsArr ['aant']  != $regelsArr[ 'nwaant' ] ) {
          $hulpArr ['aant']= $regelsArr ['nwaant'];
          $regelsArr  ['aant']= $regelsArr ['nwaant'];
          $msg[ 'inf' ] .= $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['bank']  != $regelsArr[ 'nwbank' ] ) {
          $hulpArr ['bank']= $regelsArr ['nwbank'];
          $regelsArr  ['bank']= $regelsArr ['nwbank'];
          $msg[ 'inf' ] .= $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['macht_ref']  != $regelsArr[ 'nwmacht_ref' ] ) {
          $hulpArr ['macht_ref']= $regelsArr ['nwmacht_ref'];
          $regelsArr  ['macht_ref']= $regelsArr ['nwmacht_ref'];
          $msg[ 'inf' ] .= $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['macht_dat']  != $regelsArr[ 'nwmacht_dat' ] ) {
          $hulpArr ['macht_dat']= $regelsArr ['nwmacht_dat'];
          $regelsArr  ['macht_dat']= $regelsArr ['nwmacht_dat'];
          $msg[ 'inf' ] .= $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['geb']  != $regelsArr[ 'nwgeb' ] ) {
          $hulpArr ['geb']= $regelsArr ['nwgeb'];
          $regelsArr  ['geb']= $regelsArr ['nwgeb'];
          $msg[ 'inf' ] .= $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['sinds']  != $regelsArr[ 'nwsinds' ] ) {
          $hulpArr ['sinds']= $regelsArr ['nwsinds'];
          $regelsArr  ['sinds']= $regelsArr ['nwsinds'];
          $msg[ 'inf' ] .= $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['eind']  != $regelsArr[ 'nweind' ] ) {
          $hulpArr ['eind']= $regelsArr ['nweind'];
          $regelsArr  ['eind']= $regelsArr ['nweind'];
          $msg[ 'inf' ] .= $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['refer']  != $regelsArr[ 'nwrefer' ] ) {
          $hulpArr ['referlist']= $regelsArr ['nwrefer'];
          $regelsArr  ['referlist']= $regelsArr ['nwrefer'];
          $msg[ 'inf' ] .= $MOD_GSMOFF['ref'].' : '.$regelsArr[ 'nwrefer' ].'<br/>';
          $update_ok=true;
        }  
        if ($regelsArr ['email'] != $regelsArr ['nwemail'] && strlen($regelsArr ['nwemail']) >5 ) {
          $query = "SELECT * FROM `" . $regelsArr['adressen']. "` WHERE  `email`= '".$regelsArr ['nwemail']."'";
          $c_results = $database->query( $query );
          if ( $c_results->numRows() == 0 ) { 
            $hulpArr ['email']=$regelsArr ['nwemail'];
            $hulp2Arr ['email']=$regelsArr ['nwemail'];
            $mail_dif=true;
            $hulpArr ['nwemail']='';
            $msg[ 'inf' ] .= $MOD_GSMOFF ['TH_EMAIL' ].' : '.$regelsArr ['email'].'<br/>';
            $update_ok=true;
          } else {
            $msg[ 'inf' ] .= $MOD_GSMOFF['lgn_mess4'].$regelsArr ['nwemail'].'<br/>';
            $update_ok=false;
          }       
        }
        if ($debug) Gsm_debug($regelsArr, __LINE__); 
   if ($update_ok && count($hulpArr)>0) {        
          if($regelsArr ['nieuw']) {
            if (strlen($regelsArr ['nwemail']) >5) {
              $query = "INSERT INTO `".$regelsArr[ 'adressen' ]."` ". Gsm_parse (1,$hulpArr);
              if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
              $results = $database->query( $query );
              $msg[ 'inf' ] .= $MOD_GSMOFF['added'].'<br/>';  
            } else {
              $msg[ 'inf' ] .= $MOD_GSMOFF['lgn_mess4'].$regelsArr ['nwemail'].'<br/>';
            }
          } else {
            $query = "UPDATE `".$regelsArr[ 'adressen' ]."` SET ".Gsm_parse (2,$hulpArr)."  WHERE `id` = '".$regelsArr ['recid']."'";
            if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
            $results = $database->query( $query );
            $msg[ 'inf' ] .= $MOD_GSMOFF['changed'].'<br/>';  
            if( $mail_dif) {
              // update login mail address
              $query = "SELECT * FROM `" . $regelsArr ['table'] ."` WHERE  `email`= '".$regelsArr ['email']."'";
              if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>'; 
              $results = $database->query( $query );
              if ( $results->numRows() == 1 ) { 
                $row = $results->fetchRow(); 
                if ($row['email']==$row['username']){
                  $hulp2Arr ['username']=$hulp2Arr ['email'];
                }
                $query = "UPDATE `".$regelsArr ['table']."` SET ".Gsm_parse (2,$hulp2Arr)."  WHERE  `email`= '".$regelsArr ['email']."'";
                if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'<br/>';
                $results = $database->query( $query );
                $msg[ 'inf' ] .= 'Updated'.'<br/>';
              }
            }
          }          
          $query = "SELECT * FROM `" . $regelsArr[ 'adressen' ]. "` WHERE  `email`= '".$regelsArr ['nwemail']."'";
          $regelsArr ['mode'] = 8;  
        } else {
          unset( $query );
          $regelsArr ['mode'] = 9;
        }
      break;
     default:
      if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>'; 
      $regelsArr ['mode'] = 9;
      break;
  }
} elseif ( isset( $_GET[ 'command' ] ) ) {
  switch ( $_GET[ 'command' ] ) {
     default:
      if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
      $regelsArr ['mode'] = 9;
      break;
  }
} else  { // so standard display
  if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
 /******************
 *
 * standard display job with or without search
 *
 */

  if ( isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 1 ) {
    $regelsArr[ 'search' ] = trim( $_POST[ 'selection' ] );
    $help = "%" . str_replace( ' ', '%', $regelsArr[ 'search' ] ) . "%";
    $regelsArr[ 'search_mysql' ] .= "WHERE `" . $regelsArr[ 'adressen' ] . "`.`zoek` LIKE '" . $help . "'";
  }
}

if ($regelsArr ['mode']==9 && !isset($query)) {
  // bepaal aantal records
  $query = "SELECT 
  `" . $regelsArr[ 'adressen' ] . "`.`referlist`,
  `" . $regelsArr[ 'adressen' ] . "`.`name`,  
  `" . $regelsArr[ 'adressen' ] . "`.`adres`,
  `" . $regelsArr[ 'adressen' ] . "`.`email`,
  `" . $regelsArr[ 'adressen' ] . "`.`id`
  FROM `" . $regelsArr[ 'adressen' ] . "` ". $regelsArr[ 'search_mysql' ]. " 
  ORDER BY `" . $regelsArr[ 'adressen' ] . "`.`referlist`";
  if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'</br>';
  $results = $database->query( $query );
  
  if ( $results ) { $regelsArr ['n'] = $results->numRows(); }
  if ( $regelsArr[ 'seq' ] >= $regelsArr ['n'] ) {$regelsArr[ 'seq' ]=0;}
  if (isset($regelsArr['filename_pdf'])) {
  // create print output
/*
 * initiatie pdf before starting the normal process
 */
    $pdf = new PDF();
    $title = ucfirst ($regelsArr ['app']);
    $owner = $regelsArr ['owner'] ;  
	$run= $regelsArr['today_pf'];
    $pdf->AliasNbPages();
    $pdf_text='';
    $pdf_data = array();
    $pdf->AddPage();
    $pdf->ChapterTitle(1,ucfirst($regelsArr ['app']));
    $pdf->SetFont('Arial','',8);
    if ($regelsArr['print_regels'] >1 ) { 
      $pdf_header = array(ucfirst($regelsArr ['kop'][0]), '', '', '', '','');
    } else {
      $pdf_header = array(ucfirst($regelsArr ['kop'][0]), ucfirst($regelsArr ['kop'][1]), ucfirst($regelsArr ['kop'][2]), ucfirst($regelsArr ['kop'][3]));
    }
    while ( $row = $results->fetchRow() ) {
      if ($regelsArr['print_regels'] >1 ) { 
        $line= sprintf("%s;%s;%s;%s;%s;%s",$row[0],$regelsArr ['kop'][1],str_replace( '|', ' ',$row[1]),'','','');
        $pdf_data[] = explode(';',trim($line));
        for ($i=2; $i<=$regelsArr['items']-1; $i++) {
          $line= sprintf("%s;%s;%s;%s;%s;%s",'',$regelsArr ['kop'][$i], str_replace( '|', ' ',$row[$i]),'','','');
          $pdf_data[] = explode(';',trim($line));
        }
      } else {
        $line = sprintf("%s;%s;%s;%s;%s;%s",$row[4].'|'.$row[0], str_replace( '|', ' ',$row[1]), str_replace( '|', ' ',$row[2]), $row[3], '','' );
        $pdf_data[] = explode(';',trim($line));  
      }
    }
    if(isset($settingArr['opinfo'])) $pdf_text .=stripslashes(htmlspecialchars($settingArr['opinfo'])); 
	$pdf_text .= CH_CR.CH_CR.$regelsArr['filename_pdf'].CH_CR ;
	$pdf_text .= "Aantal records : " . $regelsArr ['n'].CH_CR;
    $pdf_text .= "Document created on : " . str_replace("_", " ",$run ). CH_CR;
    if ( $debug ) $pdf_text .= CH_CR. "Version : " . $regelsArr ['module'].$regelsArr ['versie'] . CH_CR;
	if (strlen($regelsArr[ 'search' ]) >1) $pdf_text .= CH_CR."Selection : " . $regelsArr[ 'search' ]; 	
// pdf output
    $pdf->DataTable ($pdf_header, $pdf_data, $regelsArr ['cols']);  
    $pdf->ChapterBody($pdf_text);
    $pdf->Output($place['pdf'].$regelsArr['filename_pdf'], 'F');
    $msg[ 'inf' ] .= ' report created</br>';
} 
  // read records and loop through the records
  $query = "SELECT 
  `" . $regelsArr[ 'adressen' ] . "`.`referlist`,
  `" . $regelsArr[ 'adressen' ] . "`.`name`,  
  `" . $regelsArr[ 'adressen' ] . "`.`adres`,
  `" . $regelsArr[ 'adressen' ] . "`.`email`, 
  `" . $regelsArr[ 'adressen' ] . "`.`id`
  FROM `" . $regelsArr[ 'adressen' ] . "` ". $regelsArr[ 'search_mysql' ]. " 
  ORDER BY `" . $regelsArr[ 'adressen' ] . "`.`referlist`
  LIMIT " . $regelsArr[ 'seq' ] . ", " . $regelsArr[ 'qty' ]; 
  if ($debug) $msg[ 'bug' ] .= '<br/>id '.__LINE__." ".$query.'</br>';
}
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ($debug) $msg[ 'bug' ] .= '</br>DB : query id '.__LINE__.' mode ' . $regelsArr ['mode'] . ' ' .((isset($query)) ? $query :"") . '</br></br>';
// at this point the database query for the relevant records prepared
/******************
 *
 * display preparation
 *
 */
switch ( $regelsArr ['mode'] ) {

  case 8: // wijzigen
    if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
    if (isset($query)) {$results = $database->query( $query ); }
    if (!isset($query) || $results->numRows() == 0 ) { 
        $msg[ 'inf' ] .= $MOD_GSMOFF['lgn_nodata'].'</br>';
    } else {
      $row = $results->fetchRow();  
      $regelsArr ['recid'] = $row['id'];    
      $regelsArr ['naam'] = explode ("|", $row['name']);
      $regelsArr ['nwnaam'] = explode ("|", $row['nwname']);
	  
      $regelsArr ['adres'] = explode ("|", $row['adres']);
      $regelsArr ['nwadres'] = explode ("|", $row['nwadres']);
	  
      $regelsArr ['email'] = $row['email'];
      $regelsArr ['nwemail'] = $row['nwemail'];
	  
      $regelsArr ['note'] = $row['note'];
      $regelsArr ['nwnote'] = $row['nwnote'];
	  
      $regelsArr ['contact'] = explode ("|", $row['contact']);
	  
      $regelsArr ['refer'] = $row ['referlist'];
      $regelsArr ['info'] = $row['info'];
      
      $regelsArr ['comp'] = $row['comp'];
      $regelsArr ['comp_kvk'] = $row['comp_kvk'];
      $regelsArr ['comp_vat'] = $row['comp_vat'];
      $regelsArr ['comp_verif'] = $row['comp_verif'];	
	  
      $regelsArr ['bank'] = $row['bank'];
	        $regelsArr ['macht_ref'] = $row['macht_ref'];
      $regelsArr ['macht_dat'] = $row['macht_dat'];
      $regelsArr ['geb'] = $row['geb'];
	  
      $regelsArr ['sinds'] = $row['sinds'];
      $regelsArr ['eind'] = $row['eind'];
      $regelsArr ['aant'] = $row['aant'];
      
      $regelsArr ['username'] = (isset ($row['username'])) ? $row['username'] : '';
    }
    break;
  default: // default list 
    if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
    $results = $database->query( $query );
    if ( $results && $results->numRows() > 0 ) {
      $regelsArr[ 'head' ]  .= sprintf($LINETEMP[92], $MOD_GSMOFF['line_color'][4], ucfirst($regelsArr ['kop'][0]), ucfirst($regelsArr ['kop'][1]), ucfirst($regelsArr ['kop'][2]), ucfirst($regelsArr ['kop'][3]), '');
      $tint=false;
      while ( $row = $results->fetchRow() ) {
        if ($tint) {$hulp = $MOD_GSMOFF['line_color'][2]; $tint=false;} else { $hulp=""; $tint=true;}
        $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[92],$hulp, 
        ($regelsArr ['mode']==9) ? sprintf($LINETEMP[93], $row[0],$row['id']) : $row[0],
        (isset($row['name'])) ? str_replace( '|', ' ',$row['name']) :$row[1],
        (isset($row['adres'])) ? str_replace( '|', ' ',$row['adres']) :$row[2],
        $row[3],
        '');
      }  
    } else {
      $regelsArr[ 'descr' ] .= $MOD_GSMOFF[ 'lgn_nodata' ];
    }
    break;
}
switch ( $regelsArr ['mode'] ) {

  case 8: // Nieuw
    if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
    $regelsArr[ 'select' ] .=$LINETEMP[ 96 ];  
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 92 ], "", $ICONTEMP[ 4 ], $ICONTEMP[ 2 ],$ICONTEMP[ 19 ],$ICONTEMP[ 19 ], $ICONTEMP[ 19 ]);
     $regelsArr[ 'select' ] .=(isset($standenArr['opinfo'])) ? $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 98 ], "", stripslashes(htmlspecialchars($standenArr['opinfo']))): "";
    break;  
  default: // new list
    if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
    $regelsArr[ 'select' ] .=$LINETEMP[ 96 ];
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 97 ], "", "", Gsm_next ($regelsArr[ 'search' ], $regelsArr[ 'n' ] ,$regelsArr[ 'seq' ], $regelsArr[ 'qty' ] ), "", "");
    $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 92 ], "", $ICONTEMP[ 1 ], $ICONTEMP[ 3 ],$ICONTEMP[11],(isset($regelsArr['filename_pdf'])) ? sprintf($ICONTEMP[18], "", $regelsArr['filename_pdf']) : $ICONTEMP[ 19 ], $ICONTEMP[ 19 ]);
    $regelsArr[ 'select' ] .=(isset($standenArr['opinfo'])) ? $regelsArr[ 'select' ] .=sprintf( $LINETEMP[ 98 ], "", stripslashes(htmlspecialchars($standenArr['opinfo']))): "";
    break;
}  
/******************
 *
 * the output to the screen
 *
 */
switch ( $regelsArr ['mode'] ) {
  case 8: //list
    if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
    if (!isset ($regelsArr ['naam'][0]))  $regelsArr ['naam'][0] ='';
    if (!isset ($regelsArr ['naam'][1]))  $regelsArr ['naam'][1] ='';
    if (!isset ($regelsArr ['naam'][2]))  $regelsArr ['naam'][2] ='';
    if (!isset ($regelsArr ['naam'][3]))  $regelsArr ['naam'][3] ='';
    if (!isset ($regelsArr ['adres'][0])) $regelsArr ['adres'][0]= '';
    if (!isset ($regelsArr ['adres'][1])) $regelsArr ['adres'][1]= '';
    if (!isset ($regelsArr ['adres'][2])) $regelsArr ['adres'][2]= '';
    if (!isset ($regelsArr ['adres'][3])) $regelsArr ['adres'][3]= '';
    $parseViewArray = array(
      'header' =>  ($regelsArr ['recid'] != "")  ? $MOD_GSMOFF ['lgn_edit']  :$MOD_GSMOFF ['lgn_toev'],
      'titel4a' =>  ($regelsArr ['recid'] != "")  ? $MOD_GSMOFF ['lgn_ist']   :'',
      'titel4b' =>  ($regelsArr ['recid'] != "")  ? $MOD_GSMOFF ['lgn_soll']  : $MOD_GSMOFF ['lgn_toev'],  
// algemeen blok	
      'id' =>  ($regelsArr ['recid'] != "")  ? $MOD_GSMOFF ['lgn_id']    :'',  
      'recid' => (isset ($regelsArr ['recid'])) ? $regelsArr ['recid'] : '',	

      'naam' => $MOD_GSMOFF ['lgn_name' ],
      'naam1' => $regelsArr ['naam'][0],
      'naam2' => $regelsArr ['naam'][1],
      'naam3' => $regelsArr ['naam'][2],
      'naam4' => $regelsArr ['naam'][3],
      'naam1n'=> (isset ($regelsArr ['nwnaam'][0]) && strlen($regelsArr ['nwnaam'][0])>1 ) ? $regelsArr ['nwnaam'][0] : $regelsArr ['naam'][0],
      'naam2n'=> (isset ($regelsArr ['nwnaam'][1]) && strlen($regelsArr ['nwnaam'][1])>1 ) ? $regelsArr ['nwnaam'][1] : $regelsArr ['naam'][1], 
      'naam3n'=> (isset ($regelsArr ['nwnaam'][2]) && strlen($regelsArr ['nwnaam'][2])>1 ) ? $regelsArr ['nwnaam'][2] : $regelsArr ['naam'][2], 
      'naam4n'=> (isset ($regelsArr ['nwnaam'][3]) && strlen($regelsArr ['nwnaam'][3])>1 ) ? $regelsArr ['nwnaam'][3] : $regelsArr ['naam'][3],
      'naam1c' => $MOD_GSMOFF ['lgn_name1'],
      'naam2c' => $MOD_GSMOFF ['lgn_name2'],
      'naam3c' => $MOD_GSMOFF ['lgn_name3'],
      'naam4c' => $MOD_GSMOFF ['lgn_name4'],
	  
//      'naam' => $MOD_GSMOFF ['TH_NAME' ],

      'adres' => $MOD_GSMOFF ['lgn_adres'],
      'adres1'=> $regelsArr ['adres'][0],
      'adres2'=> $regelsArr ['adres'][1],
      'adres3'=> $regelsArr ['adres'][2],
      'adres4'=> $regelsArr ['adres'][3],
      'adres1n' => (isset ($regelsArr ['nwadres'][0]) && strlen($regelsArr ['nwadres'][0])>1 ) ? $regelsArr ['nwadres'][0] : $regelsArr ['adres'][0],
      'adres2n' => (isset ($regelsArr ['nwadres'][1]) && strlen($regelsArr ['nwadres'][1])>1 ) ? $regelsArr ['nwadres'][1] : $regelsArr ['adres'][1],
      'adres3n' => (isset ($regelsArr ['nwadres'][2]) && strlen($regelsArr ['nwadres'][2])>1 ) ? $regelsArr ['nwadres'][2] : $regelsArr ['adres'][2],
      'adres4n' => (isset ($regelsArr ['nwadres'][3]) && strlen($regelsArr ['nwadres'][3])>1 ) ? $regelsArr ['nwadres'][3] : $regelsArr ['adres'][3],
      'adres1c' => $MOD_GSMOFF ['lgn_adres1'],
      'adres2c' => $MOD_GSMOFF ['lgn_adres2'],
      'adres3c' => $MOD_GSMOFF ['lgn_adres3'],
      'adres4c' => $MOD_GSMOFF ['lgn_adres4'],
	  
      'e-mail'=> $MOD_GSMOFF ['lgn_email'],
      'email1'=> $regelsArr ['email'],  
      'email1n' => (isset ($regelsArr ['nwemail']) && strlen($regelsArr ['nwemail'])>1 ) ? $regelsArr ['nwemail']: $regelsArr ['email'],
      'email1c' => $MOD_GSMOFF['lgn_user'],  
	  
	  
      'info' => $MOD_GSMOFF['lgn_opm'],
      'infon' => (isset ($regelsArr ['info']) ) ? $regelsArr ['info']:'',	  
      'infoc' => $MOD_GSMOFF['lgn_rem'], 

	  // telefoonnummer blok	  
	  'mobiel' => $MOD_GSMOFF['lgn_gsm'],
      'mob1' => (isset ($regelsArr ['contact'][0]) && strlen($regelsArr ['contact'][0])) ? $regelsArr ['contact'][0]: '',
      'mob1n' => (isset ($regelsArr ['contact'][0]) ) ? $regelsArr ['contact'][0]: '',
      'mob1c' => $MOD_GSMOFF['lgn_num'],
	  
      'telefoon' => $MOD_GSMOFF['lgn_tel'],
      'tel1' => (isset ($regelsArr ['contact'][1])&& strlen($regelsArr ['contact'][1])) ? $regelsArr ['contact'][1] : '',
      'tel1n' => (isset ($regelsArr ['contact'][1])) ? $regelsArr ['contact'][1] : '',
      'tel1c' => $MOD_GSMOFF['lgn_num'], 
// userid blok	  
      'Userid' =>  ($regelsArr['username']!="") ? "Userid" : "",
      'userid' =>  $regelsArr['username'],
	  
      'refer' => $MOD_GSMOFF['ref'],
      'referc' => $MOD_GSMOFF['ref'],
      'refern' => $regelsArr ['refer'],

      'communicatie' => $MOD_GSMOFF ['lgn_comm' ],
      'wijze1'=> (isset($MOD_GSMOFF['lgn_com'][$regelsArr ['note']])) ? $MOD_GSMOFF['lgn_com'][$regelsArr ['note']]: $MOD_GSMOFF ['lgn_nn'],
      'wijze_opt' => (isset($MOD_GSMOFF['lgn_com'][$regelsArr ['nwnote']])) ? Gsm_option($MOD_GSMOFF['lgn_com'], $regelsArr ['nwnote']) : Gsm_option($MOD_GSMOFF['lgn_com'], 1),
// Bedrijfgsvorm blok	 
      'vorm'=> $MOD_GSMOFF['lgn_bedrijfsvorm'],
      'vorm1'=> (isset($regelsArr ['comp'])) ? Gsm_option($MOD_GSMOFF[ 'lgn_vorm' ], $regelsArr ['comp']) : Gsm_option($MOD_GSMOFF[ 'lgn_vorm' ], 1),	  
      'vorm_opt' => (isset($regelsArr ['comp'])) ? Gsm_option($MOD_GSMOFF[ 'lgn_vorm' ], $regelsArr ['nwcomp']) : Gsm_option($MOD_GSMOFF[ 'lgn_vorm' ], 1),

      'kvk_ref'=> $MOD_GSMOFF['lgn_kvkref'],
      'kvk_refc'=> $MOD_GSMOFF['lgn_kvkref'],
      'kvk_refn'=> $regelsArr ['comp_kvk'],
	  
      'vat_ref'=> $MOD_GSMOFF['lgn_vatref'],
      'vat_refc'=> $MOD_GSMOFF['lgn_vatref'],
      'vat_refn'=> $regelsArr ['comp_vat'],
	  
      'vat_verif'=> $MOD_GSMOFF['lgn_vatverif'],
      'vat_verifc'=> $MOD_GSMOFF['lgn_vatverif'],
      'vat_verifn'=> $regelsArr ['comp_verif'],
// bank blok	  
      'bank' => $MOD_GSMOFF['lgn_bank'],
      'bankc' => 'IBAN',
      'bankn' =>  $regelsArr ['bank'],
	  
      'macht_ref' => $MOD_GSMOFF['lgn_macht_ref'],
      'macht_refc' => 'Machtiging ref', 
      'macht_refn' => ( $regelsArr ['macht_ref']!="0000-00-00") ? $regelsArr ['macht_ref'] : "",

      'macht_dat' => $MOD_GSMOFF['lgn_macht_dat'],
      'macht_datc' => 'yyyy-mm-dd',	  	  
      'macht_datn' =>  ( $regelsArr ['macht_dat']!="0000-00-00") ? $regelsArr ['macht_dat'] : "",

      'geb' => $MOD_GSMOFF['lgn_geb'],
      'gebc' => 'yyyy-mm-dd',
      'gebn' =>  ( $regelsArr ['geb']!="0000-00-00") ? $regelsArr ['geb'] : "",
// deelnemer blok  
      'sinds' => $MOD_GSMOFF['lgn_sinds'],
      'sindsc' => 'yyyy-mm-dd',
      'sindsn' =>  ( $regelsArr ['sinds']!="0000-00-00") ? $regelsArr ['sinds'] : "",
	  
      'eind' => $MOD_GSMOFF['lgn_eind'],
      'eindc' => 'yyyy-mm-dd',
      'eindn' =>  ( $regelsArr ['eind']!="0000-00-00") ? $regelsArr ['eind'] : "",
	  
      'aant' => $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'],
      'aantc' => $regelsArr['syndic'].' '.$MOD_GSMOFF['lgn_notitie'],
      'aantn' => (isset ($regelsArr ['aant']) ) ? $regelsArr ['aant'] :'',
//	overige  
      'page_id' => $page_id,
      'section_id' => $section_id,
      'kopregels' => $regelsArr[ 'head' ],      
      'description' => $regelsArr['descr'],
      'selection' => $regelsArr[ 'select' ],
      'message' => message( $msg, $debug  ),
      'module' => $xmodule,
      'terug' => $MOD_GSMOFF[ 'tbl_icon' ][2],      
      'wijzig' => $MOD_GSMOFF[ 'tbl_icon' ][4],
      'return' => CH_RETURN,
    );
    $prout .= Gsm_prout ($TEMPLATE[ 8 ], $parseViewArray);
    break;  
  default: //list
    if ($debug) $msg[ 'bug' ] .= __LINE__.' access <br/>';
    $parseViewArray = array(
      'header' => strtoupper ($regelsArr ['name']),
      'return' => CH_RETURN,
      'kopregels' => $regelsArr[ 'head' ],
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ),
      'module' => $regelsArr ['module'],
      'page_id' => $page_id,
      'section_id' => $section_id,
      'selection' => $regelsArr[ 'select' ],
      'searchx' => $regelsArr[ 'search' ],
      'parameter' => $regelsArr[ 'search' ],
      'sel' => ''
      );
    $prout .= Gsm_prout ($TEMPLATE[ 9 ], $parseViewArray);
    break;  
}
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?>