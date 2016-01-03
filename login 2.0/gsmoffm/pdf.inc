<?php
/*
 *  @module         Office toolset legal
 *  @version        see info.php versie below
 *  @author         Gerard Smelt
 *  @copyright      2010 - 2015, Contracthulp B.V.
 *  @license        see info.php of this module
 *  @platform       see info.php of this module
 */
 
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
 
//-------------------------------------------
// Pdf generator definitions
//-------------------------------------------
$pdfclass = (dirname(__FILE__)) . '/class.fpdf.php';
require_once($pdfclass);
class PDF extends FPDF
  {
  function Header( ) {
    global $place;
    global $owner;
    global $title;
    // Logo
    if (!file_exists($place['imgm'].$owner.'logo.png')) { 
	  $this->Image( $place['imgm'].'logo.png' ,135,10,45); } 
	else { 
	  $this->Image( $place['imgm'].$owner.'logo.png' ,135,10,45); }
    $this->Ln(10);
    $this->SetXY(10, 10);
    $this->SetFont('Arial','B',10);
    $this->Cell(0,10,$title,0,0,'L');
    $this->SetXY(125, 30);
    $this->Ln(10);
  }
  function Footer() {
    global $run;
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
    $this->SetFont('Arial','B',11);
    $this->MultiCell(100,5,$para1);
    $this->Ln(1);
    $this->SetXY(135, 20);
    $this->SetFont('Arial','B',8);
    $this->MultiCell(0,5,$para2);
    $this->SetXY(135, 50);
    $this->SetFont('Arial','',8);
    $this->MultiCell(0,4,$para5);
    $this->Ln(1);
    $this->SetXY( 10, 100);
    $this->SetFont('Arial','',10);
    $this->MultiCell(100,5,$para3);
    $this->Ln(1);
     $this->SetXY( 135, 95);
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
    $this->SetFont('Arial','',10);
    // Title
    $this->SetFillColor(200,220,255);
    $this->Cell(0,6,$num.' : '.$label,0,1,'L',true);
    $this->Ln(4);
  }
  function ChapterBody($para) {
    $this->SetFont('Arial','',10);
    $this->MultiCell(0,5,$para);
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
      $this->Cell($w[2],6,$row[2],'L',0,'L',$fill);
      $this->Cell($w[3],6,$row[3],0,0,'R', (count($header)>3) ? $fill : '');
      $this->Cell($w[4],6,$row[4],0,0,'R', (count($header)>4) ? $fill : '');
      $this->Cell($w[5],6,$row[5],0,0,'R', (count($header)>5) ? $fill : '');
      $this->Ln();
      $fill = !$fill;
    }
    // Closing line
//    $this->Cell(array_sum($w),0,'','T');
//    $this->Ln();
  }
  function LoadData($file) {
    // Read file lines
    $lines = file($file);
    $data = array();
    foreach($lines as $line) { $data[] = explode(';',trim($line));}
    return $data;
  }
}