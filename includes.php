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
 
/*
 * Message section 
 */
 $regelsArr_include = array(
// voor versie display
  'modulen' => 'includes',
  'versie' => ' v20151120 '
);
/*
 * message section
 */
global $msg;
$msg = array(
  'bug' => '', // Message for debugging messages
  'app' => '', // Message for the application setting
  'err' => '', // Message for error messages
  'oke' => '', // Message for information messages
  'inf' => ''  // Message for information pdf file
); // Message for confirmation messages

if ( $debug ) {
  $msg[ 'inf' ] .= $module_description . '</br>';
}
function message( $msg, $debug=false, $func=1 ) {
  global $msg;
  $returnvalue = "";
  switch ( $func ) { 
    case 1:    //standard error display
      if ( $debug ) $returnvalue .= "<h5>" . $msg[ 'bug' ] . "</h5>";
      if ( $msg[ 'inf' ] ) $returnvalue .= "<h5>" . $msg[ 'inf' ] . "</h5>";
      if ( $msg[ 'err' ] ) $returnvalue .= "<blockquote>" . $msg[ 'err' ] . "</blockquote>";
      if ( $msg[ 'oke' ] ) $returnvalue .= "<h6>" . $msg[ 'oke' ] . "</h6>";
      break;
    case 2:   // print out display  
      $help= "\n".date("H:i:s")." ";  
      if ( $msg[ 'inf' ] ) $returnvalue .= $msg ['inf'];
      if ( $msg[ 'pdf' ] ) $returnvalue .= $msg ['pdf'];
      if ( $msg[ 'err' ] ) $returnvalue .= $msg ['err'];
      if ( $msg[ 'oke' ] ) $returnvalue .= $msg ['oke'];
      $returnvalue=$help.str_replace ("</br>", $help, $returnvalue ) ;  
      break;
    case 3:   // application versions/modules message  
      if ( $msg[ 'app' ] ) $returnvalue .= "<i>" . $msg[ 'app' ] . "</i>";
      break;
    default:   // application versions/modules message  
      $returnvalue .= "error exit<br>";    
  }
  return $returnvalue;
}
/*
 * fill template with data based on {} pair
 */
function Gsm_prout ( $template, $parseArray ) {
  $returnvalue = $template;
  foreach ( $parseArray as $key => $value ) { $returnvalue = str_replace( "{" . $key . "}", $value, $returnvalue ); } 
  return $returnvalue;
}
/*
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
    if  (strpos($owner, 'T0') !== false) { 
      $this->Image( $place['imgm'].'T0logo.png' ,135,10,45); 
    } elseif (strpos($owner, 'R0') !== false) { 
      $this->Image( $place['imgm'].'R0logo.png' ,135,10,45);
		} elseif (strpos($owner, 'A0') !== false) { 
			$this->Image( $place['imgm'].'A0logo.png' ,135,10,45);
    } else {  
      $this->Image( $place['imgm'].'logo.png' ,135,10,45);}
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
      $this->Cell($w[2],6,$row[2],'L',0,'R',$fill);
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
*/
/*
 * display array for debugging arrays
 */
function Gsm_debug($arr_in, $id = 'a', $func = 1) {
  global $msg;
  $returnvalue = "";
  switch ( $func ) {
    default:
      $i=1; 
      if (is_array($arr_in)) {
        foreach ($arr_in as $key => $value) { 
          if (is_array($value)) {
            foreach ($value as $key2 => $value2) { 
              if (is_array ($value2)) {
                foreach ($value2 as $key3 => $value3) { 
                  if ($func==1) $msg[ 'bug' ] .= $i." id ".$id."=>".$key."|".$key2.">".$key3."|".$value3."<br/>"; 
                  if ($func==2) echo $i." id ".$id."=>".$key."|".$key2.">".$key3."|".$value3."<br/>"; 
                  $i++;                  
                }
              } else {
               if ($func==1) $msg[ 'bug' ] .= $i." id ".$id."=>".$key.">".$key2."|".$value2."<br/>"; 
               if ($func==2) echo $i." id ".$id."=>".$key.">".$key2."|".$value2."<br/>"; 
               $i++; 
              }
            }
          } else { 
            if ($func==1) $msg[ 'bug' ] .= $i." id ".$id."->".$key."|".$value."<br/>"; 
            if ($func==2) echo $i." id ".$id."->".$key."|".$value."<br/>"; 
            $i++; 
          }
        }  
       } else {
        if ($func==1) $msg[ 'bug' ] .= $i." id ".$id."->".$arr_in."<br/>"; 
        if ($func==2) echo $i." id ".$id."->".$arr_in."<br/>"; 
    }
  }
  return $returnvalue;
}
/*
 * display post and get data as debugging
 */
function Gsm_post( $func = 4 ) {
  global $msg;
  $returnvalue = "";
  $returnvalue .= "<fieldset>";
  $returnvalue .= "<legend>debug</legend>";
  $returnvalue .= "<table>";
  switch ( $func ) {
    case 1:
      $returnvalue .= "<tr><td>POST</td><td></td><td></td></tr>";
      foreach ( $_POST as $key2 => $value2 ) {
        if ( is_array ($value2)) {
          foreach ( $value2 as $key3 => $value3 ) {
            $returnvalue .= "<tr><td>--" . $key2 . "</td><td> | </td><td>" . $value3 . "</td></tr>";
          }
        } else {
          $returnvalue .= "<tr><td>--" . $key2 . "</td><td> | </td><td>" . $value2 . "</td></tr>";
        }
      }
      break;
    case 2:
      $returnvalue .= "<tr><td>GET</td><td></td><td></td></tr>";
      foreach ( $_GET as $key2 => $value2 ) {
        if ( is_array ($value2)) {
          foreach ( $value2 as $key3 => $value3 ) {
            $returnvalue .= "<tr><td>--" . $key2 . "</td><td> | </td><td>" . $value3 . "</td></tr>";
          }
        } else {
          $returnvalue .= "<tr><td>--" . $key2 . "</td><td> | </td><td>" . $value2 . "</td></tr>";
        }
      }
      break;
    case 3:
    case 4:
      $returnvalue .= "<tr><td>Variables</td><td></td><td></td></tr>";
      $returnvalue .= "<tr><td>--WB_PATH</td><td> | </td><td>" . WB_PATH . "</td></tr>";
      $returnvalue .= "<tr><td>--WB_URL</td><td> | </td><td>" . WB_URL . "</td></tr>";
      $returnvalue .= "<tr><td>--TABLE_PREFIX</td><td> | </td><td>" . TABLE_PREFIX . "</td></tr>";
      $returnvalue .= "<tr><td>--SERVER['SCRIPT_NAME']</td><td> | </td><td>" . $_SERVER['SCRIPT_NAME'] . "</td></tr>";
      $returnvalue .= "<tr><td>--Admin_URL</td><td> | </td><td>" . ADMIN_URL . "</td></tr>";
      $returnvalue .= "<tr><td>--Admin_URL</td><td> | </td><td>" . ADMIN_PATH . "</td></tr>";
      $returnvalue .= "<tr><td>--CH_PATH</td><td> | </td><td>" . CH_PATH . "</td></tr>";  
      $returnvalue .= "<tr><td>--CH_MODULE</td><td> | </td><td>" . CH_MODULE . "</td></tr>";
      $returnvalue .= "<tr><td>--CH_SUFFIX</td><td> | </td><td>" . CH_SUFFIX . "</td></tr>";
      $returnvalue .= "<tr><td>--CH_DBBASE</td><td> | </td><td>" . CH_DBBASE . "</td></tr>";  
      $returnvalue .= "<tr><td>--CH_LOC</td><td> | </td><td>" . CH_LOC . "</td></tr>";
      $returnvalue .= "<tr><td>--CH_RETURN</td><td> | </td><td>" . CH_RETURN . "</td></tr>";        
      if ($func==3) {
        $returnvalue .= "<tr><td>--PAGE_TITLE</td><td> | </td><td>" . PAGE_TITLE . "</td></tr>";
        $returnvalue .= "<tr><td>--MENU_TITLE</td><td> | </td><td>" . MENU_TITLE . "</td></tr>";
        $returnvalue .= "<tr><td>--VISIBILITY</td><td> | </td><td>" . VISIBILITY . "</td></tr>";
        $returnvalue .= "<tr><td>--TEMPLATE</td><td> | </td><td>" . TEMPLATE . "</td></tr>";
      }
      $returnvalue .= "<tr><td>--MEDIA_DIRECTORY</td><td> | </td><td>" . MEDIA_DIRECTORY . "</td></tr>";
      $returnvalue .= "<tr><td>--LANGUAGE</td><td> | </td><td>" . LANGUAGE . "</td></tr>";
      if ( isset( $_SESSION[ 'USER_ID' ] ) ) {
        $returnvalue .= "<tr><td>--SESSION['USER_ID']</td><td> | </td><td>" . $_SESSION[ 'USER_ID' ] . "</td></tr>";
        $returnvalue .= "<tr><td>--SESSION['GROUP_ID']</td><td> | </td><td>" . $_SESSION[ 'GROUP_ID' ] . "</td></tr>";
        $returnvalue .= "<tr><td>--SESSION['GROUP_NAME']</td><td> | </td><td>" . $_SESSION[ 'GROUP_NAME' ][ $_SESSION[ 'GROUP_ID' ] ] . "</td></tr>";
        $returnvalue .= "<tr><td>--SESSION['USERNAME']</td><td> | </td><td>" . $_SESSION[ 'USERNAME' ] . "</td></tr>";
        $returnvalue .= "<tr><td>--SESSION['DISPLAY_NAME']</td><td> | </td><td>" . $_SESSION[ 'DISPLAY_NAME' ] . "</td></tr>";
        $returnvalue .= "<tr><td>--SESSION['EMAIL']</td><td> | </td><td>" . $_SESSION[ 'EMAIL' ] . "</td></tr>";
        $returnvalue .= "<tr><td>--SESSION['HOME_FOLDER']</td><td> | </td><td>" . $_SESSION[ 'HOME_FOLDER' ] . "</td></tr>";
      } else {
        $returnvalue .= "<tr><td>--SESSION['USER_ID']</td><td> | </td><td>no data</td></tr>";
      }
      break;
    default:
      $msg[ 'err' ] .= __LINE__.' Gsm_post default exit ' . $func . '</br>';
      Gsm_post( 1 );
      Gsm_post( 2 );
      Gsm_post( 3 );
      break;
  }
  $returnvalue .= "</table>";
  $returnvalue .= "</fieldset>";
  return $returnvalue;
}
function Gsm_opmaak($data_in, $func = 1) {
  //-------------------------------------------
  // data in 
  // function 1 Opmaak bedrag
  // function 2 opmaak bedrag for .pdf
  //-------------------------------------------
  $returnvalue = '';
  switch ($func) {
    case 1:
      $returnvalue .= "<strong>&euro;&nbsp;" . number_format($data_in, 2, ',', '.') . "</strong>";
      break;
    case 2:
      $returnvalue .= "€ " . number_format($data_in, 2, ',', '.');
      break;
    case 3:
      $returnvalue .= number_format($data_in, 0, ',', '.');
      break;
    case 8:
	  $data_in = str_replace ("," , ".", $data_in);
      $returnvalue .= number_format($data_in, 2, ',', '');
      break;	  
    case 9:
      $returnvalue .= number_format($data_in, 0, ',', '') . "%";
      break;
    default:
      break;
  } //$func
  return $returnvalue;
}
function Gsm_option($arr_in, $teselecteren = '', $func = 1) {
  //-------------------------------------------
  // array in
  // optionally select one
  // return is the result array
  // empty return: something wrong
  //-------------------------------------------
  $TEMPL_F[ 1 ] = '<option value="%s" %s>%s'.CH_CR;
  $n = 1; // max # selected
  $returnvalue = '';
  if ( $teselecteren == '' ) $n = 0;
  switch ( $func ) {
    case 1:
    default:
      foreach ( $arr_in as $key => $value ) {
        if ( $n > 0 && $teselecteren == $key ) {
          $returnvalue .= sprintf( $TEMPL_F[ 1 ], $key, 'selected', $value );
          $n--;
        } else {
          $returnvalue .= sprintf( $TEMPL_F[ 1 ], $key, '', $value );
        }
      }
      break;
  }
  return $returnvalue;
}
/*
 * paging and search string to show
 */
function Gsm_next ($sel="" , $tot = 0 ,$van = 0, $aan= 30 ) {
  global $MOD_GSMOFFM;
  // $van de eerste die getoond is
  // $aan aantal te tonen  is 
  // $tot totaal aantal
  // $sel gebruikte selectie
  $returnvalue="";
  if (strlen(trim($sel))>1 || $tot >$aan) {
    $n1 = $van +1;
    $n2 = $van + $aan;
    $n3 = $tot;
    $n4 = $n2;
    if ($n2>$tot) {$n2=$tot; $n4=0; }
    $n5= sprintf ( '<input type="hidden" name="next" value="%s" />', $n4);
 //   $returnvalue .= sprintf( $MOD_GSMOFFM['tbl_next']."</td><td>%s</td><td>%s" , $n1, $n2, $n3, '<input type="text" name="selection" value="{parameter}" placeholder="Parameter" />' , '<input class="search" type="submit" value="' . $MOD_GSMOFFM[ 'go' ] . '" />').$n5 ;
    $returnvalue .= sprintf( 'resultaten %s-%s van %s </td><td>%s</td><td>%s' , $n1, $n2, $n3, '<input type="text" name="selection" value="{parameter}" placeholder="Parameter" />' , '<input class="search" type="submit" value="Select" />').$n5 ;
    }
  return $returnvalue;
} 
/*
 * look into the directory
 */
function Gsm_get_files( $dir, $prefix="", $func=2 ) {
  global $msg;
  $allow_ext = array ( "pdf", "jpg", "zip" ); 
  $returnvalue = array();
  if ($handle = opendir($dir)) {
      while (false !== ($entry = readdir($handle))) { 
	  if( $entry == '.' || $entry == '..') continue;
	  $hlp = explode( ".", $entry );
      $extension   = end( $hlp );  
      if (in_array( $extension, $allow_ext ) ) { // extension test	
	  	if (substr ($entry,0,strlen($prefix)) == $prefix && $func==1) {
		  $returnvalue[ ] = $entry;  // selection string is in front
		} elseif (strstr($entry, $prefix) && $func==2) { 
		  $returnvalue[ ] = $entry;  // selection string is present
		} if (substr ($entry,0,strlen($prefix)) == $prefix && $func==3) {
		  unlink($dir.'/'.$entry); // selection string is in front weg ermee
        }	 
      } 
	}  
    closedir($handle);
  }
  return $returnvalue;
}
/*
 * evaluate the various string
 */
function Gsm_eval(  $input, $func=1 , $upper = 80, $lower = 0 ) {
  //-------------------------------------------
  // func = dataformat format
  // input is new entered value
  // lower = lower limit
  // higher = higher limit
  // return format returnvalue
  //-------------------------------------------
  $oke  = true;
  switch ( $func ) {
    case 1:
      // just strip spaces and length check
      $returnvalue = preg_replace( '/\s+/', ' ', trim( $input ) );
      $returnvalue = substr($returnvalue, 0, $upper);
      if ($lower !=0) {
        if ( strlen( $returnvalue ) < $lower ) $oke = false;
      }
      break;
    case 2:
      // just strip spaces and length check transfer to lowercase except first character
      $returnvalue = ucfirst( strtolower( preg_replace( '/\s+/', ' ', trim( $input ) ) ) );
      $returnvalue = substr($returnvalue, 0, $upper);
      if ($lower !=0) {      
        if ( strlen( $returnvalue ) < $lower ) $oke = false;
      }
      break;
    case 3:
      // e-mail check
      $returnvalue = strtolower( trim( $input ) ) ;
      if (empty($returnvalue)) { $oke = false; }
      elseif (!filter_var($returnvalue, FILTER_VALIDATE_EMAIL))  { $oke = false; }
        break;
    case 4:
      // just strip spaces and HTML special characters and truncate on length check 
      $returnvalue = preg_replace( '/\s+/', ' ', trim( $input ) );
      $returnvalue= str_replace ('"', '&apos;', str_replace ("'", "&apos;", $returnvalue));
	  if (strlen( $returnvalue ) > $upper) $returnvalue = substr($returnvalue, 0, $upper);
      break;
    case 5:
      // just strip spaces and but keep linebreaks 
	  $returnvalue = nl2br(trim( $input ));
      $returnvalue = preg_replace('!\s+!', ' ', $returnvalue);
      $returnvalue = str_replace ('<br /> ', NL,  $returnvalue);
      $returnvalue = str_replace ('<br />', NL,  $returnvalue);
      $returnvalue = str_replace (NL, CH_CR,  $returnvalue);
      break;  
    case 8:
      // numeric value within limits 
      $returnvalue = str_replace( ",", ".", preg_replace( '/\s+/', ' ', trim( $input ) ) );
      if ( !is_numeric( $returnvalue ) ) $oke=false;
      if ( $returnvalue < $lower ) $oke=false;
      if ( $returnvalue > $upper ) $oke=false;
      break;
    case 9:
      $originalDate= strtotime($input);
      if ($lower !=0) {
        $lowerlimit=strtotime($lower);
        $upperlimit=strtotime($upper);
        if ($originalDate < $lowerlimit)$originalDate = $lowerlimit;
        if ($originalDate > $upperlimit)$originalDate = $upperlimit;
      }
      $returnvalue = date("Y-m-d", $originalDate);
      break;
    default:
      $oke = false;
      break;
  }
  if ($oke) return $returnvalue; return '' ;
}
function Gsm_parse( $func, $arr_in ) {
  //-------------------------------------------
  // array in
  // Array out to be used in a database Insert or Update. 
  // Func 1 = insert
  //     in stead of $query = "INSERT INTO `".$table1."` (".$str_addKey.") VALUES (".$str_addVal.")"; 
  //     will allow "INSERT INTO `".$table1."` ".$returnvalue;
  // Func 2 = update
  //     $query = "UPDATE `".$table3."` SET ".$returnvalue."  WHERE .....
  // Func 3 = log entry
  //    $query = "INSERT INTO `".$tablelog."` (`log_activity`) ".$returnvalue;
  //-------------------------------------------
  $returnvalue  = '';
  $part1        = '';
  $part2        = '';
  $first        = true;
  $TEMPL_F[ 1 ] = '( %s ) VALUES ( %s )';
  $TEMPL_F[ 2 ] = ' %s ';
  $TEMPL_F[ 3 ] = "('%s')";
  switch ( $func ) {
    case 1:
      foreach ( $arr_in as $key => $value ) {
        if ( $first ) {
          $first = false;
          $part1 .= "`" . $key . "`";
          if ( $value == "NULL" ) {
            $part2 .= "NULL";
          } else {
            $part2 .= "'" . $value . "'";
          }
        } else {
          $part1 .= ", `" . $key . "`";
          if ( $value == "NULL" ) {
            $part2 .= ", NULL";
          } else {
            $part2 .= ", '" . $value . "'";
          }
        }
      }
      $returnvalue = sprintf( $TEMPL_F[ 1 ], $part1, $part2 );
      break;
    case 2:
      foreach ( $arr_in as $key => $value ) {
        if ( $first ) {
          $first = false;
          $part1 .= "`" . $key . "` = ";
          if ( $value == "NULL" ) {
            $part1 .= "NULL";
          } else {
            $part1 .= "'" . $value . "'";
          }
        } else {
          $part1 .= ", `" . $key . "` = ";
          if ( $value == "NULL" ) {
            $part1 .= "NULL";
          } else {
            $part1 .= "'" . $value . "'";
          }
        }
      }
      $returnvalue = sprintf( $TEMPL_F[ 2 ], $part1 );
      break;
    case 3:
      foreach ( $arr_in as $key => $value ) {
        if ( $first ) {
          $first = false;
          $part1 .= $key . "|" . $value;
        } else {
          $part1 .= "|" . $key . "=>" . $value;
        }
      }
      $returnvalue = sprintf( $TEMPL_F[ 3 ], $part1 );
      break;
    default:
      break;
  }
  return $returnvalue;
}
?>