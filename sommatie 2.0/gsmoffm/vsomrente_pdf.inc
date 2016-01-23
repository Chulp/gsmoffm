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
      $this->Cell($w[1],6,$row[1],'L',0,'L',$fill);
      $this->Cell($w[2],6,$row[2],'L',0,'R',$fill);
      $this->Cell($w[3],6,$row[3],0,0,'R', (count($header)>3) ? $fill : '');
      $this->Cell($w[4],6,$row[4],0,0,'R', (count($header)>4) ? $fill : '');
      $this->Cell($w[5],6,$row[5],0,0,'L', (count($header)>5) ? $fill : '');
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
function Gsm_out( $Arr, $calc, $MOD_GSMOFF, $func, $place_upload='', $filename='', $edit = '' ) {
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
  $help1=explode (CH_CR, $Arr[ 'wrref1adres' ]);
  $help2=explode (CH_CR, $Arr[ 'wrref2adres' ]);
//KVK/VAT referntie opmaak
  if (strlen($Arr['wrref2kvk'] >4)) { $help3 = 'kvk:'.$Arr[ 'wrref2kvk' ]; } 
  elseif (strlen($Arr['wrref2vat'] >4)) { $help3 = 'vat:'.$Arr[ 'wrref2vat' ]; }
  else {$help3 = ''; }
// collectie parameters voor documenten samenstellen 
  $parseViewArray = array( 
    'project' => ucfirst($Arr[ 'project' ]), 
	'volgnummer' => $Arr[ 'memored_recid' ], 
    'dossier' => $Arr[ 'wrref0' ],
    'rekendatum' => $Arr[ 'wrcalcdate' ],	
    'eiser_contact' => $Arr['wrref1name'],
    'eiser_adres1' => (isset($help1[0])) ? $help1[0] : '',
    'eiser_adres2' => (isset($help1[1])) ? $help1[1] : '',
    'eiser_adres3' => (isset($help1[2])) ? $help1[2] : '',
    'eiser_adres4' => (isset($help1[3])) ? $help1[3] : '',
    'eiser_adres5' => (isset($help1[4])) ? $help1[4] : '',
	'eiser_email' => $Arr['memored_email'], 
	'eiser_comp' => $MOD_GSMOFF[ 'lgn_vorm' ][$Arr[ 'wrref1comp' ]],
	'eiser_dienst' => str_replace ('  ', ' ',str_replace (NL, ' ', str_replace ('<br />', ' ' ,preg_replace('!\s+!', ' ', $Arr[ 'wrref1dienst' ])))),
    'debiteur_naam' => (isset($help2[0])) ? $help2[0] : '',
    'debiteur_adres1' => (isset($help2[1])) ? $help2[1] : '',
    'debiteur_adres2' => (isset($help2[2])) ? $help2[2] : '',
    'debiteur_adres3' => (isset($help2[3])) ? $help2[3] : '',
    'debiteur_adres4' => (isset($help2[4])) ? $help2[4] : '',
	'debiteur_comp' => $MOD_GSMOFF[ 'lgn_vorm' ][$Arr[ 'wrref2comp' ]],
	'debiteur_kvk' => $help3,
	/*
    'incasso_ja_9' => ( $Arr[ 'wrink' ] == 1 ) ? '</br>Incassokosten : {incasso} (Berekend over  : {berekend_over} )</br>Hoofdsom met rente en incassokosten : {hoofdsom_rente_incasso} + pm' : '',
    'incasso_ja' => ( $Arr[ 'wrink' ] == 1 ) ? '</br>Incassokosten : {incasso} (Berekend over  : {berekend_over} ),</br>Hoofdsom met rente en incassokosten : {hoofdsom_rente_incasso} + pm' : '',
    'incasso_ja_1' => ( $Arr[ 'wrink' ] == 1 ) ? '</br></br>Daarnaast wordt door {eiser_naam} aanspraak gemaakt op de buitengerechtelijke incassokosten. {eiser_naam} baseert de buitengerechtelijke incassokosten op de wet normering buitengerechtelijke incassokosten en het bijbehorend besluit. De buitengerechtelijke incassokosten bedragen {incasso}' : '',
    'incasso_ja_1b' => ( $Arr[ 'wrink' ] == 1 ) ? '</br>Buitengerechtelijke incassokosten : {incasso} </br> :--------------</br>Totaal : {hoofdsom_rente_incasso} : + pm' : '</br> :--------------</br>Totaal : {hoofdsom_rente_incasso} : + pm', 
    'incasso_ja_2' => ( $Arr[ 'wrink' ] == 1 ) ? '</br></br>Daarnaast wordt door {eiser_naam} aanspraak gemaakt op de buitengerechtelijke incassokosten. {eiser_naam} baseert de buitengerechtelijk incassokosten op de Wet normering buitengerechtelijke incassokosten en het bijbehorend besluit. De buitengerechtelijke incassokosten bedragen {incasso} en zijn na de gestelde termijn van 14 dagen verschuldigd.' : '',
    'incasso_ja_2b' => ( $Arr[ 'wrink' ] == 1 ) ? '</br>Buitengerechtelijke incassokosten	:	 pm </br> :--------------</br>Totaal : {hoofdsom_rente} : + pm' : '</br> :--------------</br>Totaal : {hoofdsom_rente_incasso} : + pm',
    'incasso_ja_2c' => ( $Arr[ 'wrink' ] == 1 ) ? '</br></br>Na het verstrijken van de gestelde termijn van 14 dagen bent u conform de Wet normering buitengerechtelijke incassokosten en het bijbehorende besluit het bedrag ad {incasso} aan buitengerechtelijke incassokosten verschuldigd. Dit bedrag is inclusief BTW.' : '',
    'datum' => $Arr[ 'today' ],
    'termijn' => $Arr ['gap']-1,
 //       'schuldeiser' => ($Arr[ 'login']) ? $help1[1] : str_replace( '|', '', $Arr[ 'wrref1' ] ) ,
 //       'debiteur' => ($Arr[ 'login']) ? $help2[1] : str_replace( '|', '', $Arr[ 'wrref2' ] ),
    'eiser_naam' => (isset($fields1[0])) ? $fields1[0] : '',
    'vanaf' => $Arr ['low_val'] ,
//        'tot' => $Arr ['high_val'],
    'hoofdsom' => "&euro;&nbsp;".number_format($Arr ['som_hoofd'], 2, ',', '.'),
    'rente' => "&euro;&nbsp;".number_format($Arr ['som_rente'], 2, ',', '.'),
    'hoofdsom_rente' => "&euro;&nbsp;".number_format($Arr ['som_totaal'], 2, ',', '.'),
    'incasso' => "&euro;&nbsp;".number_format($Arr ['som_ik'], 2, ',', '.'),
    'berekend_over' => "&euro;&nbsp;".number_format($Arr ['som_hoogste'], 2, ',', '.'),
    'hoofdsom_rente_incasso' => "&euro;&nbsp;".number_format($Arr ['som_totaalik'], 2, ',', '.'),
    'comment' =>  ( strlen( $Arr[ 'comment' ] ) > 3 ) ? $Arr[ 'comment' ] : 'werkzaamheden uitgevoerd en/of producten geleverd',
//    'behandelaar' => $Arr['door'],
//    'factuurblok'=> $factuurblok,
*/
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
          $parseViewArray ['eiser_adres1'], 
          $parseViewArray ['eiser_contact'], 		  
          $parseViewArray ['eiser_adres2'],
          $parseViewArray ['eiser_adres3'],
          $parseViewArray ['eiser_adres4'], 
          $parseViewArray ['eiser_adres5']),
        $TEMP[ 2 ], 
        '', ' ', 
        $TEMP[ 3 ], 
        sprintf("Ref : %s\nDatum: %s", 
          $parseViewArray ['dossier'].' / '.$parseViewArray ['volgnummer'], 
          $parseViewArray ['rekendatum'])
      );
	  $pdf_text .= sprintf ('Dit document gemaakt op verzoek van %s (een %s ) en is beschikbaar gesteld via het e-mail adres:',$parseViewArray ['eiser_adres1'], $parseViewArray ['eiser_comp']).CH_CR ;
	  $pdf_text .= $parseViewArray ['eiser_email']. CH_CR ;
	  $pdf_text .= CH_CR .sprintf ('Het betreft een dienst of levering ( %s ) aan de %s:', $parseViewArray ['eiser_dienst'], $parseViewArray ['debiteur_comp']).CH_CR ;
	  $pdf_text .= $parseViewArray ['debiteur_naam']. CH_CR ;
	  $pdf_text .= $parseViewArray ['debiteur_adres1']. CH_CR ;
	  $pdf_text .= $parseViewArray ['debiteur_adres2']. CH_CR ;
	  $pdf_text .= $parseViewArray ['debiteur_adres3']. CH_CR ;
	  $pdf_text .= $parseViewArray ['debiteur_adres4']. CH_CR ;
	  $pdf_text .= $parseViewArray ['debiteur_kvk']. CH_CR ;	  
	  $pdf_text .= CH_CR .'Facturen / Betalingen'. CH_CR ;	
	  $pdf->ChapterBody( $pdf_text );
      $pdf_text = '';	  
      $pdf_cols = array( 45, 25, 30, 25);
      $pdf_header = array( "Document", "Datum", "Bedrag", "Vervaldatum" );
	  $andere_volgorde = array();
      foreach ($Arr ['wrfacamt'] as $keyF => $valueF ) { 
        if($valueF !=0) {
		   $delen = explode (' ', $Arr['wrfacref'][$keyF] );
		   $deel2 = (isset($Arr['wrfacdat'][$keyF])) ? $Arr ['wrfacdat'][$keyF] : "" ;
		   $andere_volgorde [$keyF] = $delen[0]."|".$deel2;
		}
      }
	  asort( $andere_volgorde );
	  $vorige=""; $vorige_val=0; $vorige_cumul=0;
      foreach ($andere_volgorde as $keyF => $valueF ) { 
	    $delen = explode("|", $valueF);
		if ($vorige != "" && $delen[0] != $vorige && $vorige_val != $vorige_cumul ){
		  $line = sprintf( "%s;%s;%s;%s;%s;%s", 
            "-++-" , 
            $vorige , 
            sprintf( '€ %1.2f', $vorige_cumul ) , 
            "", "","");
          $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) ));   
		}
		if ($delen[0] != $vorige ) {$vorige_val=0; $vorige_cumul=0;}
		$vorige = $delen[0];
        $vorige_val = (isset($Arr['wrfacamt'][$keyF])) ? $Arr ['wrfacamt'][$keyF] : 0 ;
        $vorige_cumul = $vorige_cumul +$vorige_val;
        if(isset($Arr['wrfacref'][$keyF]) && $Arr['wrfacamt'][$keyF] !=0) {
          $line = sprintf( "%s;%s;%s;%s;%s;%s", 
            (isset($Arr['wrfacref'][$keyF])) ? $Arr ['wrfacref'][$keyF] : "" , 
            (isset($Arr['wrfacdat'][$keyF])) ? $Arr ['wrfacdat'][$keyF] : "" , 
            (isset($Arr['wrfacamt'][$keyF])) ? sprintf( '€ %1.2f', $Arr ['wrfacamt'][$keyF] ) : "" , 
            (isset($Arr['wrfacverv'][$keyF]) && $Arr['wrfacamt'][$keyF]> 0) ? $Arr ['wrfacverv'][$keyF] : "" ,  '', '' );
          $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) ));   
        }
      }
	  if ($vorige != "" && $vorige_val != $vorige_cumul ){
		$line = sprintf( "%s;%s;%s;%s;%s;%s", 
          "+/+" , 
          $vorige , 
          sprintf( '€ %1.2f', $vorige_cumul ) , 
           "", "","");
        $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) ));   
	  }
      $pdf->DataTable2( $pdf_header, $pdf_data, $pdf_cols );
	  $pdf_data = array ( );
	  $pdf_text .= CH_CR .'Berekening'. CH_CR ;	
	  $pdf->ChapterBody( $pdf_text );
      $pdf_text = '';	  	  
      $pdf_cols = array( 87, 5, 30, 3);
      $pdf_header = array( "", "", "Bedrag", "");
	  $line = sprintf( "%s;%s;€ %1.2f;", "Hoofdsom" , "" , $Arr['som_hoofd']  );
      $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) ));
	  $line = sprintf( "%s;%s;%s;", sprintf("Rente periode : %s tot %s", $Arr['low_val'] , $Arr[ 'wrcalcdate' ] ) , "" , "", "" );
      $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) ));
	  $line = sprintf( "%s;%s;€ %1.2f;", "Rente" , "" , $Arr['som_rente']  );
      $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) ));
	  $line = sprintf( "%s;%s;€ %1.2f;", "Hoofdsom met rente" , "" , $Arr['som_totaal']  );
      $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) ));
	  if ($Arr ['som_ik'] >1) {	  
	  	$line = sprintf( "%s;%s;€ %1.2f;", "Incassokosten" , "" , $Arr['som_ik']  );
	    $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) )); 
		$line = sprintf( "%s;%s;€ %1.2f;", "Hoofdsom met rente en incassokosten" , "" , $Arr['som_totaalik']  );
	    $pdf_data[] = explode( ';', str_replace (".", ",", trim( $line ) )); 
      }
      $pdf->DataTable2( $pdf_header, $pdf_data, $pdf_cols );
	  $pdf_data = array ( ); 
	  $pdf_text .= CH_CR .CH_CR . 'Aan deze berekening vallen geen rechten of privileges te ontlenen.' . CH_CR;
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
      $pdf_text .= CH_CR .'Berekening Details'. CH_CR ;	
	  $pdf->ChapterBody( $pdf_text );
      $pdf_text = '';	  
      $pdf_cols = array( 45, 20, 15, 25, 20, 50);
      $kop = array( 0 => "periode", 1 => "dagen", 2 => "%", 3 => "over",  4 => "Rente",  5 => "",  6 => "",  7 => "",  8 => "" );
      $pdf_header = array( "Periode", "Dagen", "%", "over", "rente" );
      for ( $i = 0; $i < $Arr['max']; $i++ ) {
        if ( isset( $calc[ $i ][ 0 ] ) ) {
		  /* 
		  //start debug
		  $bugout ="<br>".__LINE__." ".$i."?";
		  foreach ($calc [$i] as $k =>$v ) {$bugout .= $k.">".$v."|";}
		  echo $bugout;
		  //end debug
		  */
          if (  $calc[ $i ][ 5 ]!=0 ) { 
            $line  = sprintf( "%s;%s;%s;%s;%s;%s", 
              $calc[ $i ][ 0 ] , 
              '', 
              '', 
              sprintf( '%1.2f', $calc[ $i ][ 5 ] ), 
              '', 
              'mutatie hoofdsom', '' );
            $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
            if ( strlen (  $calc[ $i ][ 6 ]) >1 ) { 
              $hulp = explode ("|", $calc[ $i ][ 6 ]);
              foreach ($hulp as $key =>$value) {
			    if ( strlen ( $value ) >0 ) { 
                  $line = sprintf( "%s;%s;%s;%s;%s;%s", 
                    '' , 
                    '', 
                    '', 
                    '', 
                    '', 
                    $value, '' );
                  $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));    
			    }		
               } 
            }  			
            $line = sprintf( "%s;%s;%s;%s;%s;%s", 
              ( $calc[ $i ][ 0 ] == $calc[ $i ][ 1 ] ) ? $calc[ $i ][ 0 ] : $calc[ $i ][ 0 ] . ' - ' . $calc[ $i ][ 1 ], 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? $calc[ $i ][ 2 ] : '', 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? sprintf( '%1.2f%%', $calc[ $i ][ 4 ] ) : '', 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? sprintf( '%1.2f', $calc[ $i ][ 7 ] ) : '', 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? sprintf( '%1.2f', $calc[ $i ][ 8 ] ) : '', 
              '', '' );
            $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
          } else {
            $line = sprintf( "%s;%s;%s;%s;%s;%s", 
              ( $calc[ $i ][ 0 ] == $calc[ $i ][ 1 ] ) ? $calc[ $i ][ 0 ] : $calc[ $i ][ 0 ] . ' - ' . $calc[ $i ][ 1 ], 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? $calc[ $i ][ 2 ] : '', 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? sprintf( '%1.2f%%', $calc[ $i ][ 4 ] ) : '', 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? sprintf( '%1.2f', $calc[ $i ][ 7 ] ) : '', 
              ( $calc[ $i ][ 0 ] != $calc[ $i ][ 1 ] ) ? sprintf( '%1.2f', $calc[ $i ][ 8 ] ) : '', 
              str_replace ("|", " ", $calc[ $i ][ 6 ]), '' );
            $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
          }
        } else {
          $i = $Arr['max'];
        }
      }
      $line        = sprintf( "%s;%s;%s;%s;%s;%s", 'Hoofdsom', '', '', sprintf( '%1.2f', $Arr ['som_hoofd'] ), '', '' );
      $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
      $line        = sprintf( "%s;%s;%s;%s;%s;%s", 'Rente', '', '', '', sprintf( '%1.2f', $Arr ['som_rente'] ), '' );
      $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
      $line        = sprintf( "%s;%s;%s;%s;%s;%s", 'Hoofdsom met rente', '', '', sprintf( '%1.2f', $Arr ['som_totaal'] ), '', '' );
      $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
      if ( $Arr[ 'wrink' ] == 1 ) {
        $line        = sprintf( "%s;%s;%s;%s;%s;%s", 'Incassokosten', '', '', sprintf( '%1.2f', $Arr ['som_ik'] ), '', '' );
        $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
        $line        = sprintf( "%s;%s;%s;%s;%s;%s", 'Totaal', '', '€', sprintf( '%1.2f', $Arr ['som_totaalik'] ), '', '' );
        $pdf_data[ ] = explode( ';', str_replace (".", ",", trim( $line ) ));
      } 
      $pdf->DataTable( $pdf_header, $pdf_data, $pdf_cols );
      $pdf_text .= CH_CR .CH_CR . 'Aan deze berekening vallen geen rechten of privileges te ontlenen.' . CH_CR;
      $pdf->ChapterBody( $pdf_text );
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