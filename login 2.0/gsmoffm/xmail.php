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
 * variable setting
 */
$regelsArr = array(
// voor routing
  'mode' => 9,
  'module' => 'mail',
// voor versie display
  'modulen' => 'xmail',  
  'versie' => ' vv20160114',
// datastructures 
  'table_adres' => CH_DBBASE . '_adres',
  'standen' => CH_DBBASE.'_standen',
  'standtype' => '1',
// general parameters
  'owner' => (isset($settingArr[ 'logo'])) ? $settingArr[ 'logo'] : '', // ** This is the logo on the pdf **
  'app' => 'Mailing',
/*
// for display en pdf output 
  'seq' => (isset($_POST['next'])) ? $regelsArr['seq'] = $_POST['next'] : 0,
  'n' => 0,
  'qty' => (isset($settingArr['oplines'])) ? $settingArr['oplines'] : 60,
  'project' => '',
// search
  'search' => '',
  'search_mysql' => '', 
 */
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
  'tekst' => '',
  'subject' => '',
  'wrsummary' => 1,  
  'user_mail'=>'',
  'user_id'=>'', 
  'post_mail'=> 'mail@contracthulp.nl',
// search
  'matchS' => '',
  'matchR' => '',
  'match1' => '',
/*
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
  */
);
$regelsArr['project'] = $regelsArr['app'] . ' - Mailing';

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
$MOD_GSMOFF[ 'tbl_icon' ][ 13  ] = "Mail";
// extend text strings
$MOD_GSMOFF [ 'som_details' ]  = array ( '1' => 'All', '2' => 'Mail' , '3' => 'Stand');
// overrule standard function
$ICONTEMP[ 8 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][2].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][8].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 20 ]  = '<input class="'.$MOD_GSMOFF[ 'tbl_icon2' ][20].'" name="command" type="submit" value="'.$MOD_GSMOFF[ 'tbl_icon' ][ 13  ].'" style="width: 100%;" />'.CH_CR;
$ICONTEMP[ 25 ] = '<select name="%1$s">%3$s</select>'.CH_CR;
// extend standard function
$LINETEMP[86] = '<textarea rows="%2$s" cols="%3$s" name="%1$s" placeholder="%5$s" >%4$s</textarea>';
$LINETEMP[88] = '<input type="text" name="%1$s" size="%2$s"  value="%3$s" placeholder="%4$s" autocomplete="off" />';
$LINETEMP[71] = '<input type="text" name="%1$s" size="%2$s" value="%3$s"; READONLY />';
$LINETEMP[72] = '<input type="file" name="userfile" ></label>';
/*
 * various functions
 */
// empty
/*
 * pick the text pages.
 */
//$calc = array( array( ) );
// places of the includes
// it is the intention to differentiate the file using the prefix
//require_once( $place_incl . 'xmaillid_pdf.inc' );
// get content of a selected page_ID
$contentArr = array();
$message = __LINE__ ." func ". $MOD_GSMOFF[ 'error0' ] . "empty page or setting missing<br />";
if ( !isset($settingArr['page'] )) die( $message ); 
$query= "SELECT page_id, section_id, content  FROM ".TABLE_PREFIX."mod_wysiwyg WHERE page_id = '".$settingArr['page']."'";
$results = $database->query($query);
if ( !$results || $results->numRows() == 0 )  die( $message ); 
while ($row = $results->fetchRow()) { $contentArr [$row['section_id']] = $row['content'];  $regelsArr[ 'wrtekst' ] = $row['section_id'];}
if ($debug) Gsm_debug($contentArr, __LINE__);
/*
 * some job to do ?
 */
// Get the current users id and email;
$regelsArr['user_id'] = $admin->get_user_id();
$regelsArr['user_mail']= $admin->get_email();
if (isset($_POST['selection']) && strlen($_POST['selection']) >= 2) $regelsArr['match1'] = trim($_POST['selection']);
/*
 * get all match strings for rekeningen en herinneringen
 */
$query = "SELECT 
  `" . $regelsArr['table_adres'] . "`.`id`,
  `" . $regelsArr['table_adres'] . "`.`adres`,
  `" . $regelsArr['table_adres'] . "`.`email`,
  `" . $regelsArr['table_adres'] . "`.`referlist`,
  `" . $regelsArr['table_adres'] . "`.`note`,
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
  WHERE `". $regelsArr['standen']."`.`standtype`= '".$regelsArr['standtype']."'";
if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
$results = $database->query($query);
$SmatchArr= array();
$AmatchArr= array();
$EmatchArr= array();
$RmatchArr= array();
if ($results && $results->numRows() >= 1) {
  while ($row = $results->fetchRow()) {
    if ($row['standsoll']>0 && ($row['datumist'] == "0000-00-00" || $row['datumist'] == "1970-01-01")) {
	  if (strlen($regelsArr['match1'])<2 || strstr (strtoupper($row['refer']), strtoupper($regelsArr['match1']))) {
	    if (isset($SmatchArr[strtoupper($row['refer'])])) {
		  $SmatchArr[strtoupper($row['refer'])]++;
		} else { 
		  $SmatchArr[strtoupper($row['refer'])]=1;
		}
	  }
	}
	if ($row['sinds'] > '1970-01-01' && $row['eind'] <= '1970-01-01') {
	  if (strlen($regelsArr['match1'])<2 || strstr (strtoupper($row['referlist']), strtoupper($regelsArr['match1']))) {
	    if (isset($AmatchArr[strtoupper($row['id'])])) {
		  $AmatchArr[strtoupper($row['id'])]++;
		} else { 
		  $AmatchArr[strtoupper($row['id'])]=1;
		}
		if ($row['note']>1) {$hulp = $row['email'];} else { $hulp = $regelsArr['post_mail'];}
	    if (isset($EmatchArr[strtolower($hulp)])) {
		  $EmatchArr[strtolower($hulp)]++;
		} else { 
		  $EmatchArr[strtolower($hulp)]=1;
		}	
	  }
	}
  } 
} //!$results || $results->numRows() >= 1
if ($debug) Gsm_debug($SmatchArr, __LINE__);
if ($debug) Gsm_debug($AmatchArr, __LINE__);
if ($debug) Gsm_debug($EmatchArr, __LINE__);
if ($debug) Gsm_debug($RmatchArr, __LINE__);

$hulp="";
foreach ($SmatchArr as $key=>$value) { $hulp.=$key.CH_CR; } 
$regelsArr['matchS'] = str_replace ('<br />', '',  nl2br(trim($hulp))); 
$hulp="";
foreach ($RmatchArr as $key=>$value) { $hulp.=$key.CH_CR; } 
$regelsArr['matchR'] = str_replace ('<br />', '',  nl2br(trim($hulp))); 
if (isset($_POST['command'])) {
/*
 * process the input 
 */
  if (isset($_POST['subject'])) $regelsArr['subject'] =  Gsm_eval($_POST['subject'], 1, 64);
  if (isset($_POST['matchS'])) {
        // just strip spaces and but keep linebreaks 
	$hulp = nl2br(trim( $_POST['matchS'] ));
    $hulp = preg_replace('!\s+!', ' ', $hulp);
    $hulp = str_replace ('<br /> ', NL,  $hulp);
    $hulp = str_replace ('<br />', NL,  $hulp);
    $hulp = str_replace (NL, CH_CR,  $hulp);
    $regelsArr['matchS'] = $hulp; 
  }	
  if (isset($_POST['matchR'])) {
        // just strip spaces and but keep linebreaks 
	$hulp = nl2br(trim( $_POST['matchR'] ));
    $hulp = preg_replace('!\s+!', ' ', $hulp);
    $hulp = str_replace ('<br /> ', NL,  $hulp);
    $hulp = str_replace ('<br />', NL,  $hulp);
    $hulp = str_replace (NL, CH_CR,  $hulp);
    $regelsArr['matchR'] = $hulp; 
  }	
  if (isset($_POST['from'])) $regelsArr['from'] = Gsm_eval($_POST['from'], 3);
  if (isset($_POST['wrsummary'])) $regelsArr['wrsummary'] = Gsm_eval($_POST['wrsummary'], 8);  
  if (isset($_POST['wrtekst'])) $regelsArr['wrtekst'] = Gsm_eval($_POST['wrtekst'], 8);
// einde input verwerking
  if (strlen($regelsArr['subject'])>6 )  $regelsArr['mode'] = 8;
  if ($debug) Gsm_debug($regelsArr, __LINE__);
  switch ($_POST['command']) {
    case $MOD_GSMOFF['tbl_icon'][8]: //controle
	  if (isset($_POST['selection']) && strlen($_POST['selection']) >= 2) $regelsArr['search'] = trim($_POST['selection']);
	  break;
    case $MOD_GSMOFF['tbl_icon'][ 13  ]: //mail
      if ($regelsArr[ 'wrsummary' ] == 1){ //all
        $parseMailArray = array(
          'date'=> date( "j M Y"),
		  'lid' => "lid Achtse Belangen",
          'adres' => '',
          'mail'=> '',
          'date'=> date( "j M Y"),
          'tel'=> '',
        );
		$hulp = $contentArr[$regelsArr[ 'wrtekst' ]];
    	$regelsArr[ 'tekst' ] = Gsm_prout ($hulp, $parseMailArray); 
    	$MassMail = new wbmailer(true);
    	$MassMail->FromName = "Penningmeester Achtse Belangen" ; 	//	The From name of the message.
    	$MassMail->From = $regelsArr['user_mail']; 	// The From email address for the message.
    	$MassMail->AddReplyTo($regelsArr['user_mail'],$_SESSION['DISPLAY_NAME']);	// Add a "Reply-to" address.
    	$MassMail->Subject = $regelsArr[ 'subject' ];  // The Subject of the message.
        $MassMail->AddAddress($regelsArr['user_mail']);	// Add a "To" address.		
		$hulp= "rerouted from: ";
	    foreach ($EmatchArr as $key => $value)  {
		  if ($debug) {
		    $hulp .=$key."<br />";
		  } else {
		    $MassMail->AddBcc($key);	// Add a "Bcc" address
		  }
		}
		if ($debug) $regelsArr[ 'tekst' ] = $hulp. $regelsArr[ 'tekst' ];
        $MassMail->Body = $regelsArr[ 'tekst' ];  // Clients that can read HTML will view the normal Body.
    	$MassMail->AltBody = strip_tags($regelsArr[ 'tekst' ]);  // This body can be read by mail clients that do not have HTML email 
        if (isset($_FILES[ "userfile" ]["name"]) && $_FILES[ "userfile" ]["name"] !== ""){	
           $MassMail->AddAttachment($_FILES[ "userfile" ]["tmp_name"],$_FILES[ "userfile" ]["name"],'base64',$_FILES[ "userfile" ]["type"]);	// Add an attachment from a path on the filesystem.
    	}	
        $MassMail->IsHTML(true); 
    	if (!$MassMail->Send()) {
          $regelsArr['toegift'] .='Message was not sent.';
          $regelsArr['toegift'] .= 'Mailer error: ' . $MassMail->ErrorInfo;
        } 
	  } elseif ($regelsArr[ 'wrsummary' ] == 2){ // mail
	    foreach ($AmatchArr as $key => $value)  {
          $query = "SELECT * FROM `" . $regelsArr['table_adres'] . "`WHERE `adresid` = '" . $key . "'";
          if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
          $results = $database->query($query);
		  $row = $results->fetchRow();
  		  $H1= explode ("|", $row['name']);
		  $H2= explode ("|", $row['adres']);
          $parseMailArray = array(
            'lid' => sprintf ("%s %s %s %s", (isset($H1[0]))? $H1[0] : "",(isset($H1[1]))? $H1[1] : "",(isset($H1[2]))? $H1[2] : "",(isset($H1[3]))? $H1[3] : ""),
            'adres' => sprintf ("%s<br />%s<br />%s<br />%s<br />%s", (isset($H2[0]))? $H2[0] : "",(isset($H2[1]))? $H2[1] : "",(isset($H2[2]))? $H2[2] : "",(isset($H2[3]))? $H2[3] : "", (isset($H2[4]))? $H2[4] : ""),
            'mail'=> sprintf ("E-mail: %s", ($row['note']>1) ?  $row['email'] : " onbekend" ),
            'date'=> date( "j M Y"),
            'tel'=> sprintf ("%s", (strlen($row['contact'])>6) ?  str_replace("|", " ",$row['contact'] ): " Telefoon of mobiel nummer onbekend" ),
            );
		  $hulp = $contentArr[$regelsArr[ 'wrtekst' ]];
    	  $regelsArr[ 'tekst' ] = Gsm_prout ($hulp, $parseMailArray); 
          $MassMail = new wbmailer(true);
    	  $MassMail->FromName = "Achtse Belangen" ; 	//	The From name of the message.
    	  $MassMail->From = $regelsArr['user_mail']; 	// The From email address for the message.
    	  $MassMail->AddReplyTo($regelsArr['user_mail'],$_SESSION['DISPLAY_NAME']);	// Add a "Reply-to" address.
    	  $MassMail->Subject = $regelsArr[ 'subject' ];  // The Subject of the message.
		  if ($debug) {
			$MassMail->AddAddress($regelsArr['user_mail']);	// Add a "To" address.
            $hulp = "rerouted from". ($row['note']>1) ?  "(>1)".$row['email'] : "(=1)".$regelsArr['post_mail'] ;
            $regelsArr[ 'tekst' ] = $hulp ."<br />". $regelsArr[ 'tekst' ];
		  } else {
			if ($row['note']>1) { 
			   $MassMail->AddAddress($row['email']);	// Add a "To" address. $row['email'] 
			} else {
              $MassMail->AddAddress($regelsArr['post_mail']);
			}
		  }	
    	  $MassMail->Body = $regelsArr[ 'tekst' ];  // Clients that can read HTML will view the normal Body.
    	  $MassMail->AltBody = strip_tags($regelsArr[ 'tekst' ]);  // This body can be read by mail clients that do not have HTML email
          if (isset($_FILES[ "userfile" ]["name"]) && $_FILES[ "userfile" ]["name"] !== ""){	
    	    $MassMail->AddAttachment($_FILES[ "userfile" ]["tmp_name"],$_FILES[ "userfile" ]["name"],'base64',$_FILES[ "userfile" ]["type"]);	// Add an attachment from a path on the filesystem.
    	  }	
          $MassMail->IsHTML(true); 
    	  if(!$MassMail->Send()) {
            $regelsArr['toegift'] .='Message was not sent.';
            $regelsArr['toegift'] .= 'Mailer error: ' . $MassMail->ErrorInfo;
		  }
		}
	  } else {  // case 3 stand
	    // pick up the leden one by one and if they did not got a reminder/ nota in the last 3 month 
	    $updatequery= array();
        $query = "SELECT * FROM `" . $regelsArr['table_adres'] . "` ORDER BY `name`";
        if ($debug) $msg['bug'] .= __LINE__ . ' ' . $query . ' <br/>';
        $results = $database->query($query);
		while ($row = $results->fetchRow()) {
		  $issued = explode ("|", $row['aant']);
		  if (
			(!isset($issued[2]) && $row['sinds'] > '1970-01-01' && $row['eind'] <= '1970-01-01') 
			|| 
			(isset($issued[2]) && $issued[2] =="MAIL" && $issued[0]!= $regelsArr[ 'today' ] && $row['sinds'] > '1970-01-01' && $row['eind'] <= '1970-01-01')
			){ // not already issued recently // still to be improved
			if ($debug) $msg['bug'] .= __LINE__." ".$row['id']." ".$row['aant']."<br />"; 
		    // pick up de open payments for this lid
		    $query2 = "SELECT * FROM `" . $regelsArr['standen'] . "`WHERE `adresid` = '" . $row['id']. "' AND `standtype`= '".$regelsArr['standtype']."' ORDER BY `datumsoll` DESC";
		    $result2 = $database->query($query2);
		    $ditlidArr = array ();
		    if ($results && $results->numRows() >= 1) {
		      while ($row2 = $result2->fetchRow()) {
			    $pos = strpos( $regelsArr[ 'matchS' ] , $row2['refer'] );
			    if ($pos !== false) {
				  if ($row2['datumist']=="0000-00-00" || $row2['datumist']=="1970-01-01") { 
					$ditlidArr  [$row2['refer']] = $row2['standtype']. '|' .$row['id']. '|'. $row2['refer'] .'|' .$row2['datumist'] .'|' .$row2['standsoll'] .'|' .$row2['standist'].'|'.$row2['id'];
				  }
				}
			  } 
			}
			// Te processen standen zijn verzameld
		    // valt er iets te processen
		    if (count($ditlidArr)) {
			  arsort($ditlidArr);
			  $amt=0;	
			  $H0="";
			  $H3=0;
			  foreach ($ditlidArr as $key => $value) { 
				$h1 =explode ("|", $value);
				$amt =  $amt + $h1[4] - $h1[5];
				$H0.= sprintf("<p>-- Contributie %s  bedrag : Euro %s </p>", $h1[2] ,number_format($h1[4] - $h1[5], 2, ',', ' '));
				if ($h1[6] > $H3) $H3 = $h1[6];
			  } 
			  $H0.= sprintf("<p><b>Te betalen: Euro %s</b></p>", number_format($amt, 2, ',', ' '));
			  // mark het record met gedaan
			  $H1= explode ("|", $row['name']);
			  $H2= explode ("|", $row['adres']);
              $parseMailArray = array(
                'lid' => sprintf ("%s %s %s %s", (isset($H1[0]))? $H1[0] : "",(isset($H1[1]))? $H1[1] : "",(isset($H1[2]))? $H1[2] : "",(isset($H1[3]))? $H1[3] : ""),
                'adres' => sprintf ("%s<br />%s<br />%s<br />%s<br />%s", (isset($H2[0]))? $H2[0] : "",(isset($H2[1]))? $H2[1] : "",(isset($H2[2]))? $H2[2] : "",(isset($H2[3]))? $H2[3] : "", (isset($H2[4]))? $H2[4] : ""),
				'mail'=> sprintf ("E-mail: %s", ($row['note']>1) ?  $row['email'] : " onbekend" ),
				'date'=> date( "j M Y"),
				'tel'=> sprintf ("%s", (strlen($row['contact'])>6) ?  str_replace("|", " ",$row['contact'] ): " Telefoon of mobiel nummer onbekend" ),
                'tebetalen' => $H0,
                'betaalref' => sprintf ("%s-00%s-%s ",  (isset($settingArr['prefix'])) ? $settingArr['prefix'] : "Nota:", $row['id'], $H3),
                );
			  $hulp = $contentArr[$regelsArr[ 'wrtekst' ]];
    		  $regelsArr[ 'tekst' ] = Gsm_prout ($hulp, $parseMailArray); 
			  if ($row['id']<500) {
     	        $MassMail = new wbmailer(true);
    	        $MassMail->FromName = "Penningmeester Achtse Belangen" ; 	//	The From name of the message.
    	        $MassMail->From = $regelsArr['user_mail']; 	// The From email address for the message.
    	        $MassMail->AddReplyTo($regelsArr['user_mail'],$_SESSION['DISPLAY_NAME']);	// Add a "Reply-to" address.
    	        $MassMail->Subject = $regelsArr[ 'subject' ];  // The Subject of the message.
				if ($debug) {
				  $MassMail->AddAddress($regelsArr['user_mail']);	// Add a "To" address.
                  $hulp = "rerouted from". ($row['note']>1) ?  "(>1)".$row['email'] : "(=1)".$regelsArr['post_mail'] ;
                  $regelsArr[ 'tekst' ] = $hulp ."<br />". $regelsArr[ 'tekst' ];
				} else {
				  if ($row['note']>1) { 
			        $MassMail->AddAddress($row['email']);	// Add a "To" address. $row['email'] 
			      } else {
                    $MassMail->AddAddress($regelsArr['post_mail']);
			      }
				}
    	        $MassMail->Body = $regelsArr[ 'tekst' ];  // Clients that can read HTML will view the normal Body.
    	        $MassMail->AltBody = strip_tags($regelsArr[ 'tekst' ]);  // This body can be read by mail clients that do not have HTML email
                if(isset($_FILES[ "userfile" ]["name"]) && $_FILES[ "userfile" ]["name"] !== ""){	
    	          $MassMail->AddAttachment($_FILES[ "userfile" ]["tmp_name"],$_FILES[ "userfile" ]["name"],'base64',$_FILES[ "userfile" ]["type"]);	// Add an attachment from a path on the filesystem.
    	        }	
                $MassMail->IsHTML(true); 
    	        if(!$MassMail->Send()) {
                  $regelsArr['toegift'] .='Message was not sent.';
                  $regelsArr['toegift'] .= 'Mailer error: ' . $MassMail->ErrorInfo;
                } else {
				  if ($debug) {
				  } else {
				  	$mail_post= 'MAIL';
//			        if ($row['note']==1)  $mail_post= 'POST';
			        $updatequery[] = "UPDATE `" . $regelsArr['table_adres'] . "` SET `aant` = '".$regelsArr['today']."|".$H3."|". $mail_post ."|" .$row[ 'aant' ]. "' WHERE `id` = ".$row['id'];
				  }
				}
              }
	        } // if (count($ditlidArr))
		  }
		}
	    if (count($updatequery)) {foreach ( $updatequery as $key => $value ) $results = $database->query($value);}
	  }
	  break;
    default:
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['mode'] = 9;
      break;
  } //$_POST[ 'command' ]
} //isset( $_POST[ 'command' ] )
elseif (isset($_GET['command'])) {
  switch ($_GET['command']) {
    default:
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
      $regelsArr['mode'] = 9;
      break;
  } //$_GET[ 'command' ]
} //isset( $_GET[ 'command' ] )
else { // so standard display
  if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
  /******************
   * standard display job with or without search
   */
}
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ($debug) $msg['bug'] .= __LINE__ . ' mode ' . $regelsArr['mode'] . ' ' . ((isset($query)) ? $query : "") . '<br /><br />';
/*
 * data collection
 */
switch ($regelsArr['mode']) {
  default: // default list 
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
	$regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 12 ], $MOD_GSMOFF[ 'line_color' ][ 3 ], ucfirst( 'Details ' ).sprintf( $ICONTEMP[ 25 ], "wrsummary", '', Gsm_option( $MOD_GSMOFF[ 'som_details' ], $regelsArr[ 'wrsummary' ] ) ), '', '','' );	
 	if ($regelsArr[ 'wrsummary' ] == 2 && count($SmatchArr) < 7){
      $regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"MATCH STRING",'','','');
	  $regelsArr['descr'] .= sprintf ($LINETEMP[ 14 ], sprintf($LINETEMP[86], "matchR", 5, 32, $regelsArr[ 'matchR' ], "match strings"),'','','');
	}
 	if ($regelsArr[ 'wrsummary' ] == 3 && count($RmatchArr) < 7){
      $regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"MATCH STRING",'','','');
	  $regelsArr['descr'] .= sprintf ($LINETEMP[ 14 ], sprintf($LINETEMP[86], "matchS", 5, 32, $regelsArr[ 'matchS' ], "match strings"),'','','');
	}
 	$regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"FROM",'','','');
	$regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ], '',sprintf($LINETEMP[71], "from", 80, $regelsArr['user_mail']), '','' ,'');
	if ($regelsArr[ 'wrsummary' ] == 1 ){
	  $regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"BCC",'','','');
	  $hulp= sprintf ("%s adressen , %s per mail en %s per post", count ($AmatchArr) , count ($EmatchArr), count ($AmatchArr)-count ($EmatchArr));
	}
	if ($regelsArr[ 'wrsummary' ] == 2 ){
	  $regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"BCC",'','','');
	  $hulp= sprintf ("%s adressen ot %s maila ", count ($AmatchArr) , count ($SmatchArr));
	}
    $regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"ONDERWERP",'','','');
	$regelsArr['descr'] .= sprintf ($LINETEMP[ 14 ], sprintf($LINETEMP[88], "subject", 80, $regelsArr[ 'subject' ], "onderwerp"),'','','');
	if ($regelsArr[ 'wrsummary' ] == 1){
	  $regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"ATTACHMENT",'','','');
	  $regelsArr['descr'] .= sprintf ($LINETEMP[ 14 ], sprintf($LINETEMP[72], ''),'','','');
	} 
    $regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ],$MOD_GSMOFF ["line_color"][2],"INHOUD",'','','');
	$hulp= array();
	foreach ($contentArr as $key => $value) $hulp[$key]=$key;
	$regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 12 ], $MOD_GSMOFF[ 'line_color' ][ 3 ], ucfirst( 'Textselectie ' ).sprintf( $ICONTEMP[ 25 ], "wrtekst", '', Gsm_option( $hulp, $regelsArr[ 'wrtekst' ] ) ), '', '','' );	  
	$regelsArr['descr'] .= sprintf ($LINETEMP[ 12 ], '',$contentArr[$regelsArr[ 'wrtekst' ]],'','','');
	break;
} //$regelsArr[ 'mode' ]
/*
 * selection
 */
switch ($regelsArr['mode']) {
  case 8: // display met mail
      if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['select'] .= $LINETEMP[ 1 ];
    $regelsArr['select'] .= sprintf($LINETEMP[ 2 ], '', $ICONTEMP[8], '', '', '', $ICONTEMP[20]);
    break;
  case 9: // display zonder mail mogelijkheid
  default: // default
    if ($debug) $msg['bug'] .= __LINE__ . ' access <br/>';
    $regelsArr['select'] .= $LINETEMP[ 1 ];
    $regelsArr['select'] .= sprintf($LINETEMP[ 2 ], '', $ICONTEMP[8], '', '', '', '');
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