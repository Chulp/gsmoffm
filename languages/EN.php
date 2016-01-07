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
if (defined('WB_PATH')) {  
  include(WB_PATH.'/framework/class.secure.php'); 
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
  include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
  $subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));  $dir = $_SERVER['DOCUMENT_ROOT'];
  $inc = false;
  foreach ($subs as $sub) {
    if (empty($sub)) continue; $dir .= '/'.$sub;
    if (file_exists($dir.'/framework/class.secure.php')) { 
  include($dir.'/framework/class.secure.php'); $inc = true;  break; 
    } 
  }
  if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include class.secure.php

// module description
$module_description = 'Basic Office Modules functionality (EN).';

// declare module language array

$MOD_GSMOFF = array(
  'add' => 'Nieuw',
  'added' => 'Toegevoegd ',
  'addn' => 'Toevoegen(nieuw)',
//  'addv' => 'Veld Toevoegen',
  'autocomplete' => array (
    '1' => 'Text255',
    '2' => 'Value',
    '3' => 'Amount',
    '4' => 'Text63',
    '5' => 'Text127',
    '6' => 'yyyy-mm-dd',
    '7' => 'Value 1/0'),
  'cancel' => 'Afbreken',
  'change' => 'Wijzigen',
  'changed' => 'Gewijzigd ',
  'deleted' => 'Verwijderd ',  
  'edit' => 'Aanpassen',
  'to_select' => '--- Select --', 
  'error0' => ' Oeps unexpected case : ', //replaced 'TXT_ERROR_DATABASE' 
  'error1' => 'error1DEPR Rekening ?',
  'error2' => ' Oeps missing data : ',  
  'error4' => ' Oeps sips case',
  'file_type' => array(
    'varchar(255)' => '1',
    'int(11)' => '2',
    'decimal(9,2)' => '3',
    'varchar(63)' => '4', 
    'varchar(127)' => '5',
    'date' => '6',
    'int(7)' => '7'),
  'friendly' => array('&lt;', '&gt;', '?php'),
  'go' => 'Select',
  'grootboek' => array(
	  '1' => 'Kl 1: Eigen vermogen en langlopende schulden',
	  '2' => 'Kl 2: Vaste activa en langlopende vorderingen ',
	  '3' => 'Kl 3: Voorraden en bestellingen ',
	  '4' => 'Kl 4: Kortlopende schulden en vorderingen ',
	  '5' => 'Kl 5: Liquide middelen en opvraagbare beleggingen',
	  '6' => 'Kl 6: Kosten',
	  '7' => 'Kl 7: Opbrengsten',
	  '8' => 'Kl 8: Tussen rekeningen',
	  '9' => 'kl 0: Niet in de balans opgenomen rechten en verplichtingen' ),
  'lgn_vorm' => array (
    '1' => 'Consument/Prive persoon',
    '2' => 'Niet BTW plichtige organsiatie',	
    '3' => 'Eenmanszaak',
    '4' => 'Vennootschap onder Firma',
    '5' => 'Commanditaire Vennootschap',
    '6' => 'Cooperatie',
    '7' => 'Besloten Vennootschap',
    '8' => 'Naamloze Vennootschap',
    '9' => 'Overige BTW plichtige organisatie'),
  'lgn_wacht' => 'Wachtwoord',
  'line_color' => array( 
    '0' => '',
    '1' => 'bgcolor="#eeeeee"',
    '2' => 'bgcolor="#dddddd"',
    '3' => 'bgcolor="#cccccc"',
    '4' => 'bgcolor="#bbbbbb"' ),
  'module' => 'module :',
  'nodata' => 'Geen data/informatie',
  'raw' => array('<', '>', ''),
  'ref' => 'ref',
  'rek_type' => array(
	  '1' => 'Activa',
	  '2' => 'Passiva',
	  '4' => 'Uitgaven',
	  '5' => 'Inkomsten',
	  '7' => 'Tussen rekening'),
  'rek_type_sign'=> array(
    '1' => 1,
    '2' => -1,
    '4' => 1,
    '5' => -1,
    '7' => 1 ),
  'save' => 'Opslaan',
  'som_att' => array (
    '1' => 'algemene voorwaarden',
    '2' => 'Offerte/aanbieding',	
    '3' => 'orderbevestiging',
    '4' => 'leverings document',
    '5' => 'ontvangst bevestiging',
	'6' => 'factuur',
    '7' => 'betalings herinneringen',
    '8' => 'overige correspondentie',
    '9' => 'KVK inschrijving debiteur'),
  'som_status' => array (
    '0' => 'verwijderd',
    '1' => 'invoer',	
    '2' => 'kompleet',
    '3' => 'bevestigd',
    '4' => 'in behandeling',
	'5' => 'wacht op debiteur',
    '6' => 'onbekende status',
    '7' => 'onbekende status',
    '8' => 'rechtszaak',
    '9' => 'afgewerkt'),
  'syndic' => 'Syndicus', 
  'tbl_aantal' => 'Aantal Records : ', 
  'tbl_volgorde' => 'Volgorde : ',
  'tbl_icon' => array( 
    0=>'Select', 
    1=>'Wijzigen', 
    2=>'Terug', 
    3=>'Toevoegen', 
    4=>'Opslaan', 
    5=>'Opslaan (als nieuw)', 
    6=>'Verwijderen', 
    7=>'Bereken',
    8=>'Controle', 
    9=>'Select', 
    10=>'+', 
    11=>'Print Model', 
    12=>'Betaling',  
    13=>'reserve',  
    14=>'reserve',  
    15=>'reserve',  
    16=>'reserve'
    ),
  'tbl_icon2' => array( 
    '0' => 'cancel',
    '1' => 'add',
    '2' => 'advanced',
    '3' => 'back',
    '4' => 'backup',
    '5' => 'groups',
    '6' => 'help',
    '7' => 'infobtn',
    '8' => 'languages',
    '9' => 'modify',
    '10' => 'modules',
    '11' => 'newfolder',
    '12' => 'reload',
    '13' => 'sections',
    '14' => 'search',
    '15' => 'settings',
    '16' => 'templates',
    '17' => 'upload',
    '18' => 'users',
    '19' => 'warn',
    '20' => 'submit',
	'21' => 'command' 
    ), 
  'tbl_overzicht' => 'Overzicht gemaakt : ', 
  'tbl_label' => 'veld', 
  'tbl_selectie' => 'Selectie : ',
  'tbl_value' => 'inhoud', 
  'tbl_next' => 'resultaten %s-%s van %s ', 
  
  'TH_EMAIL' => 'Email',
  'TH_NAME' =>'Name'

);
$TEMPLATE[ 0 ] = 
  '<div class="container">
    <form name="menu" method="post" action="{return}">
      {menu}
    </form>
  </div>';
$TEMPLATE[ 1 ]   = '
	<div class="container">
	<form name="menu" method="post" action="{return}">
	<table>
		<colgroup><col width="15%"><col width="25%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
		<tr><td>' . $MOD_GSMOFF[ 'module' ] . '</td>
		<td><SELECT name="module" >{module}</SELECT></td>
		<td><input type="text" name="selection" value="{parameter}" placeholder="Parameter" /></td>
		<td><input class="search" type="submit" value="' . $MOD_GSMOFF[ 'go' ] . '" /></td>
		<td>{add_needed}</td></tr>
	</table>
	</form>
	</div>';
$TEMPLATE[ 2 ] = '
  <h3>{header}</h3>
  <div class="container">
    {message}
  </div>
  <div class="container">
  <form name="view" enctype="multipart/form-data" method="post" action="{return}">
  <input type="hidden" name="module" value="{module}" />
  <input type="hidden" name="page_id" value="{page_id}" />
  <input type="hidden" name="section_id" value="{section_id}" />
  <input type="hidden" name="sh" value="{hash}" />
  <input type="hidden" name="recid" value="{recid}" />
  <input type="hidden" name="memory" value="{memory}" />
  <table class="container" width="100%">
    {kopregels}
    {description}
  </table> 
  <table class="container" width="100%">
    {selection}
    {rapportage}
  </table>
  </form>
  </div>
    {toegift}';
	
$LINETEMP[ 1 ] = '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>'.CH_CR;
$LINETEMP[ 2 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td></tr>'.CH_CR;
$LINETEMP[ 3 ] = '<tr %1$s><td><a href="' . CH_RETURN . '&command=view&module={module}&recid=%2$s">%3$s</a></td><td>%4$s</td><td>%5$s</td><td>%6$s</td><td>%7$s</td></tr>'.CH_CR;
$LINETEMP[ 4 ] = '<tr %1$s>%2$s %3$s<td>%4$s</td></tr>'.CH_CR;
$LINETEMP[ 5 ] = '<td colspan="2" class="setting_name" align="left" >%1$s&nbsp;:</td>'.CH_CR;
$LINETEMP[ 6 ] = '<tr><td colspan="2">%1$s</td><td colspan="3">%2$s</td></tr>'.CH_CR;
$LINETEMP[ 7 ] = '<thead><tr><th>%1$s</th><th>%2$s</th><th>%3$s</th><th>%4$s</th><th>%5$s</th></tr></thead>'.CH_CR;
$LINETEMP[ 8 ] = '<td colspan="2" class="setting_value" ><input maxlength="%2$s" size="%3$s" type="text" name="%1$s" value="%4$s" placeholder="%5$s" /></td>'.CH_CR;
$LINETEMP[ 9 ] = '<tr %1$s><td>%2$s %3$s</td><td>%4$s</td></tr>'.CH_CR;
$LINETEMP[ 10 ] = '<tr %1$s><td>%2$s</td><td>%3$s</td><td>%4$s</td><td colspan="2" >%5$s</td></tr>'.CH_CR;
$LINETEMP[ 11 ] = '<tr><td>%1$s</td><td colspan="2">%2$s</td></tr>'.CH_CR;
$LINETEMP[ 12 ] = '<tr %1$s><td colspan="2">%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td></tr>'.CH_CR;
$LINETEMP[ 13 ] = '<tr><td colspan="2">%1$s</td><td colspan="3">%2$s</td></tr>'.CH_CR;
$LINETEMP[ 14 ] = '<tr><td colspan="5">%1$s</td></tr>'.CH_CR;

$ICONTEMP[ 1 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][1].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 2 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][3].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][2].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 3 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][1].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][3].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 4 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][20].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][4].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 5 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][5].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 6 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][6].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 7 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][9].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][7].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 8 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][2].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][8].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 9 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][15].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][9].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 10 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][18].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][10].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 11 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][18].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][11].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 12 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][12].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 13 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][20].'" name="%2$s" type="submit" value="%1$s" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 14 ]  = '<input maxlength="12" size="12" type="text" name="select_van" value="%1$s" width="30" />%2$s'.CH_CR;
$ICONTEMP[ 15 ]  = '<input maxlength="12" size="12" type="text" name="select_tot" value="%1$s" width="30" />'.CH_CR;
$ICONTEMP[ 16 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][19].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][14].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 17 ] = '<a target="_blank" href="' . $place['pdf1'] . '%2$s"><img src="' . $place['imgm'] . 'pdf_16.png" alt="pdf document">%1$s</a>'.CH_CR;
$ICONTEMP[ 18 ] = '<a target="_blank" href="' . $place['pdf2'] . '%2$s"><img src="' . $place['imgm'] . 'pdf_16.png" alt="pdf document">%1$s</a>'.CH_CR;
$ICONTEMP[ 19 ]  = ''.CH_CR;
$ICONTEMP[ 20 ]  = '&nbsp;<input type="checkbox" name="confirm" value="yes" />'.CH_CR;
?>