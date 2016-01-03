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
/*
 * Pdf generator definitions
 */
$pdfclass = (dirname(__FILE__)) . '/class.fpdf.php';
require_once($pdfclass);
class PDFAB extends FPDF {
  function Header( ) {
    global $place;
    global $title;
    // Logo
    $this->Image( $place['imgm'].'CHlogo.png' ,10,10);
    $this->Ln(10);
    $this->SetXY(135, 10);
    $this->SetFont('Arial','I',10);
    $this->Cell(100,10,$title,0,0,'L');
    $this->SetXY(135, 30);
    $this->Ln(10);
  }
  function Footer() {
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10, '     Page: '.$this->PageNo().'/{nb}',0,0,'R');
}
  function AdresBlock ($para1, $para2, $para3, $para4, $para5=" ", $para6=" ") {
    // adresblock
    // naam adres regels, onderwerp datum en begroeting
    $this->SetXY( 20, 55);
    $this->SetFont('Arial','',10);
    $this->MultiCell(100,5,$para1);
    $this->Ln(1);
    $this->SetXY(135, 20);
    $this->SetFont('Arial','B',8);
    $this->MultiCell(0,5,$para2);
    $this->SetXY(135, 58);
    $this->SetFont('Arial','',8);
    $this->MultiCell(0,4,$para5);
    $this->Ln(1);
    $this->SetXY( 10, 100);
    $this->SetFont('Arial','',10);
    $this->MultiCell(100,5,$para3);
    $this->Ln(1);
    $this->SetXY( 10, 86);
    $this->SetFont('Arial','',10);
    $this->MultiCell(100,5,$para6);
    $this->Ln(1);
    $this->SetXY( 10, 110);
    $this->SetFont('Arial','',10);
    $this->MultiCell(100,5,$para4);
    $this->Ln(1);
    $this->SetXY( 0, 105);
    $this->SetFont('Arial','',10);
    $this->MultiCell(100,5,'.');
    $this->Ln(1); 
  } 
  function ChapterTitle($num, $label) {
    $this->SetFont('Arial','',9);
    // Title
    $this->SetFillColor(200,220,255);
    $this->Cell(0,6,$num.' : '.$label,0,1,'L',true);
    $this->Ln(4);
  }
  function ChapterBody($para) {
    $this->SetFont('Arial','',10);
    $this->MultiCell(0,4,$para);
    $this->Ln(1);
  }
  function ChapterKlein($para) {
    $this->SetFont('Arial','',7);
    $this->MultiCell(0,3,$para);
    $this->Ln(1);
  }
  function DataTable($header, $data, $cols=array(55, 35, 35, 20, 20, 20) ) {
    // Colors, line width and bold font
    $this->SetFillColor(168,168,168);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,125,125);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Column widths
    $w = $cols;
    // Header
    for($i=0;$i<count($header);$i++)
    $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224,224,224);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
    $fill = false;
    foreach($data as $row) {
      $this->Cell($w[0],6,$row[0],'L',0,'L',$fill);
      $this->Cell($w[1],6,$row[1],'L',0,'R',$fill);
      $this->Cell($w[2],6,$row[2],'L',0,'L',$fill);
//     $this->Cell($w[3],6,$row[3],0,0,'L', (count($header)>3) ? $fill : '');
//      $this->Cell($w[4],6,$row[4],0,0,'L', (count($header)>4) ? $fill : '');
//      $this->Cell($w[5],6,$row[5],0,0,'L', (count($header)>5) ? $fill : '');
      $this->Ln();
      $fill = !$fill;
    }
  }
  function DataTable2($header, $data, $cols=array(45, 25, 30, 25) ) {
    // Colors, line width and bold font
    $this->SetFillColor(168,168,168);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,125,125);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Column widths
    $w = $cols;
    // Header
    for($i=0;$i<count($header);$i++)
    $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224,224,224);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
    $fill = false;
    foreach($data as $row) {
      $this->Cell($w[0],6,$row[0],'L',0,'L',$fill);
      $this->Cell($w[1],6,$row[1],'L',0,'L',$fill);
      $this->Cell($w[2],6,$row[2],'L',0,'R',$fill);
      $this->Cell($w[3],6,$row[3],'L',0,'L',$fill);
      $this->Ln();
      $fill = !$fill;
    }
  }
}
function Gsm_out( $Arr, $MOD_GSMOFF, $func, $place_upload='', $filename='', $edit = '' ) {
  global $msg;
  if ($place_upload =='') $filename='';
  if (strlen ($filename) <6)  $filename='';
  if (strlen ($edit) < 10 ) $edit ='';
  if (strlen($edit) > 10  && strlen ($filename) >5) $func=99; 
  $returnvalue="";
/*
  $TEMP9= '<h3>{project}</h3>
      <h4>Referentie : {dossier} Datum : {datum} </h4>
      <p>Schuldeiser : {schuldeiser}  / Debiteur : {debiteur}</br>
      Periode : {vanaf} -  {tot}</br>
      Hoofdsom : {hoofdsom}</br>
      Rente : {rente} + pm</br>
      Hoofdsom met rente : {hoofdsom_rente} + pm {incasso_ja_9}</p>{comment} 
      <p>Aan deze berekening vallen geen rechten of privileges te ontlenen.</p>';
  $TEMP1='Geachte heer, mevrouw,</br></br>Tot mij wendde zich {eiser_naam}, gevestigd te {eiser_locatie} aan de {eiser_adres1} met het verzoek 
      haar belangen te behartigen. 
      </br></br>{eiser_naam} heeft in Uw opdracht {comment}. {eiser_naam} heeft daarvoor de navolgende facturen 
      gestuurd:
      </br>{factuurblok}
      </br></br>De betalingstermijn van de facturen bedraagt {termijn} dagen. De facturen zijn niet binnen {termijn} dagen na de respectivelijke factuurdatum voldaan. 
      U bent in verzuim na het verstrijken van de betalingstermijn.
      </br></br>{eiser_naam} heeft aan u een aantal betalingsherinneringen gestuurd. Ondanks de herhaalde aanmaning en sommatie bent u niet tot betaling van de openstaande factuurbedragen overgegaan.</br></br>
      {eiser_naam} maakt thans aanspraak op vergoeding van de wettelijke handelsrente (ex artikel 6:119 a BW). De wettelijke handelsrente beloopt inmiddels een bedrag ad {rente}.
      {incasso_ja_1}
      </br></br>Aldus bent u in totaal opeisbaar verschuldigd :</br>**break**</br>
      Hoofdsom : {hoofdsom}</br>
      Wettelijke handelsrente t/m {tot} : {rente} : + pm{incasso_ja_1b}</br>**break**</br></br>
      Ik verzoek en sommeer u bovenstaand totaalbedrag ad {hoofdsom_rente_incasso} binnen 8 dagen na dagtekening van deze brief te voldoen op rekening nummer NL65RABO0145019608 t.n.v. Stichting beheer Derdengelden Advocatenkantoor Vanaken onder vermelding van dossiernummer {dossier}.
      </br>Betaling kan alleen bevrijdend worden gedaan op het genoemde rekeningnummer.
      </br></br>Bij gebreke van tijdige betaling ziet {eiser_naam} zich genoodzaakt een gerechtelijke incassoprocedure aanhangig te maken. Het spreekt voor zich dat dit voor u oplopende kosten met zich mee zal brengen.
      </br>Vooralsnog gaat {eiser_naam} er vanuit dat u het bovenstaand totaalbedrag ad {hoofdsom_rente_incasso} tijdig zult voldoen.
      </br></br>Om er zeker van te zijn dat deze brief u bereikt wordt deze brief u zowel per post, per telefax alsmede per email aangeboden.
      </br></br>In afwachting van uw spoedige betaling, teken ik onder voorbehoud van alle rechten en weren van {eiser_naam},
      </br></br>met vriendelijke groet,
      </br></br>AV Advocatuur 
      </br></br></br>{behandelaar}
      </br></br></br></br></br></br>';
   $TEMP2='Geachte heer, mevrouw,</br></br>Tot mij wendde zich {eiser_contact} van {eiser_naam}, gevestigd te {eiser_locatie} aan de {eiser_adres1} met het verzoek haar belangen te behartigen ter zake het navolgende: {comment}
      </br></br>{eiser_naam} heeft in Uw opdracht {comment}. {eiser_naam} heeft daarvoor de navolgende facturen gestuurd:
      </br>{factuurblok}
      </br></br>De betalingstermijn van de facturen bedraagt {termijn} dagen. De facturen zijn niet binnen {termijn} dagen na de factuurdatum voldaan.
      </br></br>U bent door {eiser_naam} diverse malen aangemaand en verzocht om het factuurbedrag ad {hoofdsom} te voldoen. Ondanks de herhaalde aanmaningen bent u niet tot betaling van het openstaande factuurbedrag overgegaan. U bent dus al geruime tijd in verzuim.
      </br></br>{eiser_naam} maakt thans ook aanspraak op op vergoeding van de wettelijke rente (ex. artikel 6:119 BW). De wettelijke rente beloopt inmiddels een bedrag ad {rente}.
      {incasso_ja_2}
      </br></br>Aldus bent u opeisbaar verschuldigd :</br>**break**</br>
      Hoofdsom : {hoofdsom}</br>
      Wettelijke rente t/m {tot} : {rente} : + pm {incasso_ja_2b}</br>**break**</br></br>
      Ik verzoek en sommeer u bovenstaand totaalbedrag ad {hoofdsom_rente} binnen 14 dagen na dagtekening van deze brief te voldoen op rekening nummer NL65RABO0145019608 t.n.v. Stichting beheer Derdengelden Advocatenkantoor Vanaken onder vermelding van dossiernummer {dossier}.
      {incasso_ja_2c}
      </br></br>Bij gebreke van tijdige betaling ziet {eiser_naam} zich genoodzaakt een gerechtelijke incassoprocedure aanhangig te maken. Het spreekt voor zich dat dit voor u oplopende proceskosten met zich mee zal brengen. {eiser_naam} zal de gemaakte procsskosten dan eveneens op u verhalen.
      </br></br>Vooralsnog gaat {eiser_naam} er vanuit dat u het bovenstaande totaalbedrag tijdig zult voldoen. 
      </br></br>Om er zeker van te zijn dat deze brief u bereikt wordt deze brief u zowel per aangetekende en gewone post alsmede per e-mail aangeborden.
      </br></br>In afwachting van uw spoedige betaling, teken ik onder voorbehoud van alle rechten en weren van {eiser_naam},
      </br></br>met vriendelijke groet,
      </br></br>AV Advocatuur 
      </br></br></br>{behandelaar}
      </br></br></br></br></br></br>';   
*/	  
  $TEMP [0] = "\nAan deze berekening kunt U geen rechten of privileges ontlenen.";  //Footer
  $TEMP [1] = '<tr %1$s><td>%2$s</td><td colspan="3">%3$s</td><td>%4$s</td></tr>';
  $TEMP [2] = "\n\n\n\n\n\n"; //Namen
  $TEMP [3] = "\n\n\n\n\n";   //Adres
//  $TEMP [0] = "\nAV Advocatuur is statutair gevestigd te Eindhoven en staat ingeschreven bij de kamer van koophandel Brabant ondrr KvK nr 53689399. Op al onze werkzaamheden zijn onze algemene voorwaarden van toepassing. Deze zijn terug te vinden via de website van www.avadvocatuur.com AV Advocatuur vormt een strategisch samenwerkingsverband met Incasso Contact.";
//  $TEMP [1] = '<tr %1$s><td>%2$s</td><td colspan="3">%3$s</td><td>%4$s</td></tr>';
//  $TEMP [2] = "\n\nAV Advocatuur\nMr. N.J.P Vanaken\nMr. ing. H.J.M. Smelt\nMr. E.E.V. Sweebe\nAdvocaten";
//  $TEMP [3] = "Beukenlaan 137\n5616 VD Eindhoven\nT  040-8480169\nF  040-7440120\nE  contact@avadvocatuur.com\nW  www.acadvocatuur.com"; 





// adresblokken opsplitsen 
  $help0=explode (CH_CR, $Arr[ 'tvref0adres' ]);
  $help1=explode (CH_CR, $Arr[ 'tvref1adres' ]);
  $help2=explode (CH_CR, $Arr[ 'tvref2adres' ]);
//KVK/VAT referntie opmaak
  if (strlen($Arr['tvref2kvk'] >4)) { $help3 = 'kvk:'.$Arr[ 'tvref2kvk' ]; } 
  elseif (strlen($Arr['tvref2vat'] >4)) { $help3 = 'vat:'.$Arr[ 'tvref2vat' ]; }
  else {$help3 = ''; }
// collectie parameters voor documenten samenstellen 
  $parseViewArray = array( 
    'project' => ucfirst($Arr[ 'project' ]), 
	'volgnummer' => $Arr[ 'memored_recid' ], 
    'dossier' => $Arr[ 'tvref0' ],
    'rekendatum' => $Arr[ 'tvcalcdate' ],	
    'vrager_contact' => $Arr['tvref0name'],
    'vrager_adres1' => (isset($help0[0])) ? $help0[0] : '',
    'vrager_adres2' => (isset($help0[1])) ? $help0[1] : '',
    'vrager_adres3' => (isset($help0[2])) ? $help0[2] : '',
    'vrager_adres4' => (isset($help0[3])) ? $help0[3] : '',
    'vrager_adres5' => (isset($help0[4])) ? $help0[4] : '',
	'vrager_email' => $Arr['owner_email'], 
    'nemer_naam' => $Arr['tvref1name'],
    'nemer_adres1' => (isset($help1[0])) ? $help1[0] : '',
    'nemer_adres2' => (isset($help1[1])) ? $help1[1] : '',
    'nemer_adres3' => (isset($help1[2])) ? $help1[2] : '',
    'nemer_adres4' => (isset($help1[3])) ? $help1[3] : '',
	'gever_naam' => $Arr['tvref2name'],
    'gever_adres1' => (isset($help2[0])) ? $help2[0] : '',
    'gever_adres2' => (isset($help2[1])) ? $help2[1] : '',
    'gever_adres3' => (isset($help2[2])) ? $help2[2] : '',
    'gever_adres4' => (isset($help2[3])) ? $help2[3] : '',
    'gever_adres5' => (isset($help2[4])) ? $help2[4] : '',
	'gever_comp' => $MOD_GSMOFF[ 'lgn_vorm' ][$Arr[ 'tvref2comp' ]],
	'gever_size' => ($Arr[ 'tvorkw' ]==2) ? ' met '.$MOD_GSMOFF[ 'trans_kleintjes' ][2] : ' met niet '.$MOD_GSMOFF[ 'trans_kleintjes' ][2],
	'gever_kvk' => $help3,
    'geboortedatum' => $Arr['a0'],
    'datum50' => $Arr['a1'],
    'datumaow' => $Arr['a2'],
    'vanaf_datum' => $Arr ['a3'],
    'tot_datum' => $Arr ['a4'],
    'maanden_in_dienst_bruto' => $Arr ['a5'],
    'maanden_in_dienst_korting' => $Arr ['a7'], 
    'maanden_in_dienst_overgansregeling' => $Arr ['a6'], 
    'maanden_in_dienst' => $Arr ['a8'], 
    'periodes' => $Arr ['a9'], 
    'periodes_voor50' => $Arr ['a11'],   
    'periodes_na50' => $Arr ['a10'],  
    'periodes_in120' => $Arr ['a12'],  
    'periodes_buiten120' => $Arr ['a13'],
    'bruto_loon' => $Arr ['a20'],
    'bruto_loon_periode' => $Arr ['a21'],
    'bruto_loon_13e' => $Arr ['a22'],
    'vakantiegeld' => $Arr ['a23'],
    'prestatie' => $Arr ['a24'],
    'gevaren' => $Arr ['a25'],
    'bruto_jaarsalaris_zvak' => $Arr ['a26'],
    'bruto_jaarsalaris_vak' => $Arr ['a27'],
    'bruto_jaarsalaris_mvak' => $Arr ['a28'],
    'bruto_jaarsalaris_nvt' => $Arr ['a29'],
    'bruto_maandsalaris' => $Arr ['a30'],
    'transite_periodes' => $Arr ['a35'] ,
    'transite_correctie' => $Arr ['a36'],
    'transite_totaal' => $Arr ['a37'] ,
    'transactie_berekend' =>$Arr ['a40'],
    'transactie_max' => $Arr ['a41'],
    'transactie_correctie' =>$Arr ['a42'],
    'transactie_bedrag' => $Arr ['a45'],
    'comment' =>  ( strlen( $Arr[ 'comment' ] ) > 1 ) ? $Arr[ 'comment' ] : '',

  );
  switch ( $func ) {
  /*
    case 96:
      if ($edit == '') {
        $returnvalue .= '<textarea rows="41" cols="90" name="*" >';
        $returnvalue .= $TEMP1;
        $returnvalue .= '</textarea>';
        foreach ( $parseViewArray as $key => $value ) { $returnvalue = str_replace( "{" . $key . "}", $value, $returnvalue );} 
        $returnvalue = trim($returnvalue);
        $returnvalue = preg_replace('!\s+!', ' ', $returnvalue);
        $returnvalue = str_replace ("</br>", CH_CR, $returnvalue);
        $returnvalue = str_replace (CH_CR." ", CH_CR, $returnvalue);
      } else {
        $returnvalue .= $edit;
      }
      break;
    case 97:
      if ($edit == '') {
        $returnvalue .= '<textarea rows="42" cols="90" name="*" >';
        $returnvalue .= $TEMP2;
        $returnvalue .= '</textarea>';
        foreach ( $parseViewArray as $key => $value ) { $returnvalue = str_replace( "{" . $key . "}", $value, $returnvalue );} 
        $returnvalue = preg_replace('!\s+!', ' ', $returnvalue);
        $returnvalue = str_replace ("</br>", CH_CR, $returnvalue);
        $returnvalue = str_replace (CH_CR." ", CH_CR, $returnvalue);
      } else {
        $returnvalue .= $edit;
      }
      break;
	  */
    case 98: // print standard output
      $pdf = new PDFAB();
      global $title;
	  $title = $parseViewArray ['project'];
      $pdf->AliasNbPages();
      $pdf->AddPage();
      $pdf_header = array ( );
      $pdf_data = array ( );
      $pdf_text = '';
      $pdf->AdresBlock(
        sprintf("%s\n%s\n%s\n%s\n%s\n%s", 
          $parseViewArray ['vrager_adres1'], 
          $parseViewArray ['vrager_adres2'],
          $parseViewArray ['vrager_adres3'],
          $parseViewArray ['vrager_adres4'], 
          $parseViewArray ['vrager_adres5']
		  ,''),
        $TEMP[ 2 ], 
        '', ' ', 
        $TEMP[ 3 ], 
        sprintf("Ref : %s\nDatum: %s", 
          $parseViewArray ['dossier'].' / '.$parseViewArray ['volgnummer'], 
          $parseViewArray ['rekendatum'])
      );
	  $pdf_text .= sprintf ('Dit document gemaakt op verzoek van %s en is beschikbaar gesteld via het e-mail adres: %s', $parseViewArray ['vrager_contact'], $parseViewArray ['vrager_email']).CH_CR ;
//	  $pdf_text .= $parseViewArray ['eiser_email']. CH_CR ;
	  $pdf_text .= CH_CR .sprintf ('Het betreft de berekening van de transactievergoeding voor werknemer:', '').CH_CR ;
	  $pdf_text .= $parseViewArray ['nemer_naam']. CH_CR ;
	  $pdf_text .= $parseViewArray ['nemer_adres1']. CH_CR ;
	  $pdf_text .= $parseViewArray ['nemer_adres2']. CH_CR ;
	  $pdf_text .= $parseViewArray ['nemer_adres3']. CH_CR ;
	  $pdf_text .= $parseViewArray ['nemer_adres4']. CH_CR ;
	  $pdf_text .= CH_CR .sprintf ('De werkgever:', '').CH_CR ;
	  $pdf_text .= $parseViewArray ['gever_adres1']. CH_CR ;
	  $pdf_text .= $parseViewArray ['gever_adres2']. CH_CR ;
	  $pdf_text .= $parseViewArray ['gever_adres3']. CH_CR ;
	  $pdf_text .= $parseViewArray ['gever_adres4']. CH_CR ;
	  $pdf_text .= $parseViewArray ['gever_adres5']. CH_CR ;
	  $pdf_text .= CH_CR .sprintf ('Dit is een %s %s', $parseViewArray ['gever_comp'], $parseViewArray ['gever_size']).CH_CR ;	  
	  $pdf_text .= $parseViewArray ['gever_kvk']. CH_CR ;	
	  $pdf_text .= CH_CR .sprintf ('Contact persoon : %s ', $parseViewArray ['gever_naam']).CH_CR ;	
      $pdf_text .= CH_CR .'Berekening : '.CH_CR.CH_CR ;	
	  $pdf_text .= sprintf( 'Arbeids duur : Vanaf %s tot %s', $parseViewArray ['vanaf_datum'] , $parseViewArray['tot_datum'] ).CH_CR ;	
	  $pdf_text .= sprintf( 'Bruto Maandsalaris : € %1.2f', $parseViewArray ['bruto_maandsalaris']).CH_CR ;
      $pdf_text .= sprintf( 'Transitie vergoeding : € %1.2f', $parseViewArray ['transactie_bedrag']).CH_CR ;	
	  $pdf_text .= CH_CR .'Aan deze berekening vallen geen rechten of privileges te ontlenen.'.CH_CR ;	
      $pdf_text .= CH_CR .'Bijlages'. CH_CR ;
      $arr_files = Gsm_get_files( WB_PATH. "/" . $Arr['file0']. "/" . $Arr['file1'] , $Arr[ 'memored_recid' ]."_",1 );
      foreach ( $arr_files as $key => $value ) { $pdf_text .= $value . CH_CR ;}
	  if (strlen( $Arr ['comment']) >2 ) {
        $pdf_text .= CH_CR .'Opmerking'. CH_CR ;
	    $pdf_text .= $Arr ['comment']. CH_CR ;
	  }
	  $pdf->ChapterBody( $pdf_text );
	  $pdf_text = '';	
      $pdf->AddPage();
	  $pdf_text .= CH_CR .'Details Berekening'. CH_CR ;	
	  $pdf->ChapterBody( $pdf_text );
      $pdf_text = '';	
      $pdf_cols = array( 60, 30, 80, 5);
      $pdf_header = array( "Omschrijving", "", "opmerking" );
      $line = sprintf( "%s;%s;%s", 'werknemer', $parseViewArray['nemer_naam'], '');
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", 'geboorte datum', $parseViewArray['geboortedatum'], '');
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", '50 jaar op', $parseViewArray['datum50'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );
      $line = sprintf( "%s;%s;%s", 'AOW leeftijd op', $parseViewArray['datumaow'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );
      $line = sprintf( "%s;%s;%s", '', '-', '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", 'in-dienst', $parseViewArray['vanaf_datum'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );
      $line  = sprintf( "%s;%s;%s", 'uit dienst', $parseViewArray['tot_datum'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );   
      $line = sprintf( "%s;%s;%s", 'werkgever', $parseViewArray['gever_adres1'], '');
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line  = sprintf( "%s;%s;%s", 'maanden in dienst', $parseViewArray['maanden_in_dienst_bruto'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line  = sprintf( "%s;%s;%s", 'correctie overgangsregeling', $parseViewArray['maanden_in_dienst_overgansregeling'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );    
      $line  = sprintf( "%s;%s;%s", 'correctie', $parseViewArray['maanden_in_dienst_korting'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", 'netto maanden in dienst', $parseViewArray['maanden_in_dienst'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", '', '-', '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", 'periodes in dienst', $parseViewArray['periodes'], 'periodes van 6 maanden' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line = sprintf( "%s;%s;%s", 'waarvan voor 50e levensjaar', $parseViewArray['periodes_voor50'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", 'waarvan na 50e levensjaar', $parseViewArray['periodes_na50'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );        
      $line = sprintf( "%s;%s;%s", 'waarvan in de laatste 10 jaar', $parseViewArray['periodes_in120'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line = sprintf( "%s;%s;%s", 'waarvan buiten de deze 10 jaar', $parseViewArray['periodes_buiten120'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line = sprintf( "%s;%s;%s", '', '-', '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'bruto loon', $parseViewArray['bruto_loon'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line = sprintf( "%s;%s;%s", 'aantal malen per jaar ', $parseViewArray['bruto_loon_periode'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line = sprintf( "%s;€ %1.2f;%s", '13e maand', $parseViewArray['bruto_loon_13e'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line = sprintf( "%s;%1.2f%%;%s", 'vakantiegeld %', $parseViewArray['vakantiegeld'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) );   
      $line = sprintf( "%s;€ %1.2f;%s", 'prestatiebeloning', $parseViewArray['prestatie'], 'Wordt niet meegenomen in de berekening' );
      $pdf_data[ ] = explode( ';', trim( $line ) );  
      $line = sprintf( "%s;€ %1.2f;%s", 'gevaren geld', $parseViewArray['gevaren'], 'Wordt niet meegenomen in de berekening' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'bruto jaarsalaris', $parseViewArray['bruto_jaarsalaris_zvak'], 'ex vakantiegeld' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'vakantiegeld', $parseViewArray['bruto_jaarsalaris_vak'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'bruto jaarsalaris ', $parseViewArray['bruto_jaarsalaris_mvak'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'bruto maandsalaris', $parseViewArray['bruto_maandsalaris'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", '', '-', '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%1.3f;%s", 'transitie vergoeding', $parseViewArray['transite_periodes'], 'aantal maandsalarissen' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%1.3f;%s", 'correctie overgangs regeling', $parseViewArray['transite_correctie'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%1.3f;%s", 'totaal aantal maandsalarissen', $parseViewArray['transite_totaal'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", '', '-', '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'berekende transitievergoeding', $parseViewArray['transactie_berekend'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'maximum', $parseViewArray['transactie_max'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;€ %1.2f;%s", 'correctie', $parseViewArray['transactie_correctie'], '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
      $line = sprintf( "%s;%s;%s", '', '-', '' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
	  $line = sprintf( "%s;€ %1.2f;%s", 'Transitie vergoeding', $parseViewArray['transactie_bedrag'], 'Berekend eindbedrag' );
      $pdf_data[ ] = explode( ';', trim( $line ) ); 
	  $pdf->DataTable( $pdf_header, $pdf_data, $pdf_cols );
	  $pdf_text .= CH_CR .CH_CR . 'Aan deze berekening vallen geen rechten of privileges te ontlenen.' . CH_CR;
      $pdf->Output( LEPTON_PATH. "/".$place_upload.$filename, 'F' );
      $returnvalue = $filename;
      break;
/*
    case 99: // print data modified yes or no
      $pdf = new PDFAB();
      global $title;
      $title = "uw partner in recht";
      $pdf->AliasNbPages();
      $pdf->AddPage();
      $pdf_data = array ( );
      $pdf_text = '';   
      $pdf->AdresBlock(
        sprintf("%s\n%s\n%s\n%s\n%s", 
          $parseViewArray ['debiteur_naam'], 
          $parseViewArray ['debiteur_contact'],
          $parseViewArray ['debiteur_adres1'],
          $parseViewArray ['debiteur_adres2']." ".$parseViewArray ['debiteur_adres3'], 
          $parseViewArray ['debiteur_adres4']),
        $TEMP[ 10 ], 
        '', ' ', 
        $TEMP[ 11 ], 
        sprintf("Onze ref : %s\nDatum: %s", 
          $Arr[ 'wrref0' ], 
          $Arr[ 'today' ])
      );  
      $currency_str=chr(226).chr(130).chr(172).chr(194);
      $currency_str2=chr(128).chr(160);
      $splits = explode ("**break**", $edit);
      foreach ($splits as $key => $value ) { 
        if ($key%2!=1) { 
          $pdf->ChapterBody( str_replace ($currency_str, $currency_str2, str_replace(array('<br/>', '<br>', '<br />'), '', $value )));
        } else {
          $splits_regel=explode ( '<br />' ,$value ) ;   
          $pdf_header = array( "", "Bedrag", "" );
          foreach ($splits_regel as $keyR => $valueR ) { 
            if(strlen($valueR)>2) {
              $splits_line=explode (":", str_replace ($currency_str, $currency_str2, str_replace(array('<br/>', '<br>', '<br />'), '', $valueR )));
              $line = sprintf( "%s;%s;%s;%s;%s;%s", 
                (isset($splits_line[0])) ? $splits_line[0] : "" , 
                (isset($splits_line[1])) ? trim( $splits_line[1]) : "" ,
                (isset($splits_line[2])) ? $splits_line[2] : "" , 
                (isset($splits_line[3])) ? $splits_line[3] : "" , '', '' );
              $pdf_data[] = explode( ';', trim( $line )) ;
            }
          }
          $pdf->DataTable2( $pdf_header, $pdf_data );
        }
      }
      $pdf->ChapterKLein( $TEMP [0] );
      $pdf->Output( $place_upload.$filename, 'F' );
      $returnvalue = $filename;
      break; 
	  */
    default:
      $returnvalue .= $TEMP9;
      foreach ( $parseViewArray as $key => $value ) { $returnvalue = str_replace( "{" . $key . "}", $value, $returnvalue );} 
      break;
  } //$func
  return $returnvalue;
}