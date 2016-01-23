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
} //defined( 'LEPTON_PATH' )
else {
  $oneback = "../";
  $root    = $oneback;
  $level   = 1;
  while ( ( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) ) ) {
    $root .= $oneback;
    $level += 1;
  } //( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) )
  if ( file_exists( $root . '/framework/class.secure.php' ) ) {
    include( $root . '/framework/class.secure.php' );
  } //file_exists( $root . '/framework/class.secure.php' )
  else {
    trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
  }
}
// end include class.secure.php
/* change history
 */
/*
 * variables
 */
$regelsArr = array(
  // voor routing
  'mode' => 9,
  'module' => 'boeken',
  // voor versie display
  'modulen' => 'xboeken',
  'versie' => ' vv20160114 ',
  // general parameters
  'app' => "boeken",
  'table' => CH_DBBASE . "_booking",
  'table_rek' => CH_DBBASE . "_rekening",
  'table_proj' => CH_DBBASE . "_bkproject",
  'file' => "booking",
  'file0' => 'media/booking',  // the directory for the documents
  'file1' => 'current',  // subdirectory with the administered documents
  'file_pre' => ( isset( $settingArr[ 'prefix' ] ) ) ? $settingArr[ 'prefix' ] : 'XX',
  'opzoek' => 'name',
  // for display  
  'seq' => ( isset( $_POST[ 'next' ] ) ) ? $regelsArr[ 'seq' ] = $_POST[ 'next' ] : 0,
  'n' => 0,
  'qty' => ( isset( $settingArr[ 'oplines' ] ) ) ? $settingArr[ 'oplines' ] : 60,
  'project' => '',
// search
  'search' => '',
  'search_mysql' => '',
// display
  'descr' => '',
  'head' => '',
  'select' => '',
  'update' => '',
  'toegift' => '',
  'rapport' => '',
  'memory' => '',
  'hash' => '',
  'recid' => '',
  'save_update' => false,
// application
  'rekening size' => 4,
  'allow_ext' => array ( "pdf", "jpg", "zip" ), 
  'fyear' => ( isset( $settingArr[ 'fjaar' ] ) ) ? date( "Y", strtotime( $settingArr[ 'fjaar' ] ) ) . "-01-01" : date( "Y" ) . "-01-01",
  'tyear' => ( isset( $settingArr[ 'tjaar' ] ) ) ? date( "Y", strtotime( $settingArr[ 'tjaar' ] ) ) . "-12-31" : "2020-12-31",
);
$regelsArr[ 'project' ]        = $regelsArr[ 'app' ] . ' - Overzicht';
$regelsArr[ 'xyear' ]          = date( "Y-m", strtotime( $regelsArr[ 'tyear' ] ) ) . "-01";
/*
 * Lay-out strings
 */
$MOD_GSMOFF[ 'LAB_BOOK' ]     = 'Boeking';
$MOD_GSMOFF[ 'LAB_REF' ]      = 'Omschrijving';
$MOD_GSMOFF[ 'LAB_DATUM' ]    = 'Datum';
$MOD_GSMOFF[ 'LAB_PROJECT' ]  = 'Project';
$MOD_GSMOFF[ 'LAB_REK' ]      = 'rekening';
$MOD_GSMOFF[ 'LAB_DEBET' ]    = 'debet';
$MOD_GSMOFF[ 'LAB_CREDIT' ]   = 'credit';
$MOD_GSMOFF[ 'LAB_REKENING' ] = 'Rekening';
$MOD_GSMOFF[ 'LAB_TREK1' ]    = 'Tegenrekening&nbsp;1';
$MOD_GSMOFF[ 'LAB_TREK2' ]    = 'Tegenrekening&nbsp;2';
$MOD_GSMOFF[ 'LAB_MISC' ]     = 'Diverse';
$MOD_GSMOFF[ 'LAB_COM' ]      = 'Opmerking';
$MOD_GSMOFF[ 'LAB_BIJL' ]     = 'Bijlage';
$MOD_GSMOFF[ 'LAB_UPDATED' ]  = 'Updated';
$MOD_GSMOFF[ 'AAN' ]          = 'aan ';
$MOD_GSMOFF[ 'ERROR_UPLOAD' ] = 'File can not be uploaded';
$MOD_GSMOFF[ 'OK_UPLOAD' ] = 'Uploaded';
// icontemp 1-19 is defined in language module
// linetemp 1-19 is defined in language module
// template 0 is in scheduler module
// template 1 is in language module
$LINETEMP[ 21 ]                = '<tr %1$s><td>&nbsp;</td><td colspan="2">%2$s</td><td align="right">%3$s</td><td>&nbsp;</td></tr>';
$LINETEMP[ 22 ]                = '<tr %1$s><td>&nbsp;</td><td colspan="2">%2$s</td><td>&nbsp;</td><td align="right">%3$s</td></tr>';
$ICONTEMP[ 20 ]                = '<a href="' . $place['pdf3'] . '%1$s/%2$s">%3$s</a> <a href="' . CH_RETURN . '&command=rm&module={module}&recid=%4$s&file=%5$s"><img src="' . $place['imgm'] . 'delete_16.png"></a></br>'.CH_CR;

$TEMPLATE[ 3 ]                 = '
  <div class="view">
  <h2>{header}</h2>
    {message}
  </div>';
$TEMPLATE[ 4 ]                 = '
  <div class="container">
  <form name="edit" method="post" enctype="multipart/form-data" action="{return}">
  <input type="hidden" name="module" value="{module}" />
  <input type="hidden" name="page_id" value="{page_id}" />
  <input type="hidden" name="section_id" value="{section_id}" />
  <input type="hidden" name="sh" value="{hash}" />
  <input type="hidden" name="update_verif" value="{update}" />
  <input type="hidden" name="recid" value="{recid}" />
  <input type="hidden" name="memory" value="{memory}" />
  <table class="inhoud" width="100%">
    <colgroup><col width="20%"><col width="5%"><col width="30%"><col width="5%"><col width="20%"><col width="20%"></colgroup>
    <tr>
      <td colspan="6" bgcolor="#bbbbbb"><strong>' . $MOD_GSMOFF[ 'LAB_BOOK' ] . '</strong></td>
    </tr><tr>
      <td class="setting_name">' . $MOD_GSMOFF[ 'LAB_REF' ] . '&nbsp;:</td>
      <td class="setting_value" colspan="5"><input type="text" name="bk_name" value="{bk_name}" /></td></td>
    </tr><tr>
      <td class="setting_name">' . $MOD_GSMOFF[ 'LAB_DATUM' ] . '&nbsp;:</td>
      <td colspan="2" ><input maxlength="10" size="15" type="text" name="bk_date" value="{bk_date}" /></td><td></td><td></td><td></td>
    </tr><tr>
      <td class="setting_name" >' . $MOD_GSMOFF[ 'LAB_PROJECT' ] . '&nbsp;:</td>
      <td class="setting_value" colspan="2" ><select name="bk_project_name">{bk_project_opt}</select></td>
    </tr><tr>
      <td bgcolor="#dddddd">&nbsp;</td>
      <td bgcolor="#dddddd" colspan="2">' . strtoupper( $MOD_GSMOFF[ 'LAB_REK' ] ) . '</td><td bgcolor="#dddddd">&nbsp;</td>
      <td bgcolor="#dddddd">' . strtoupper( $MOD_GSMOFF[ 'LAB_DEBET' ] ) . '</td>
      <td bgcolor="#dddddd">' . strtoupper( $MOD_GSMOFF[ 'LAB_CREDIT' ] ) . '</td>
    </tr><tr>
      <td class="setting_name">' . $MOD_GSMOFF[ 'LAB_REKENING' ] . '&nbsp;:</td>
      <td class="setting_value" colspan="2"><select name="bk_debet_name">{bk_debet_opt}</select></td><td>&nbsp;</td>
      <td ><input maxlength="12" size="15" type="text" name="bk_debet_amount" value="{bk_debet_amount}" /></td><td></td>
    </tr><tr>
      <td class="setting_name" colspan="2">' . $MOD_GSMOFF[ 'LAB_TREK1' ] . '&nbsp;:</td>
      <td class="setting_value" ><select name="bk_tegen1_name">{bk_tegen1_opt}</select></td><td>&nbsp;</td>
      <td></td><td>{bk_tegen1_amount}</td>
    </tr><tr>
      <td class="setting_name" colspan="2">' . $MOD_GSMOFF[ 'LAB_TREK2' ] . '&nbsp;:</td>
      <td class="setting_value" ><select name="bk_tegen2_name">{bk_tegen2_opt}</select></td><td></td>
      <td></td><td><input maxlength="12" size="15" type="text" name="bk_tegen2_amount" value="{bk_tegen2_amount}" /></td>
    </tr><tr>
      <td colspan="6" bgcolor="#bbbbbb"><strong>' . $MOD_GSMOFF[ 'LAB_MISC' ] . '</strong></td>
    </tr><tr>
      <td class="setting_name">' . $MOD_GSMOFF[ 'LAB_COM' ] . '&nbsp;:</td>
      <td class="setting_value" colspan="5"><input type="text" name="bk_comment" value="{bk_comment}" /></td></td>
    </tr><tr>
      <td class="setting_name">' . $MOD_GSMOFF[ 'LAB_BIJL' ] . '&nbsp;:</td>
      <td class="setting_value" colspan="5">{bk_book_doc}</td> 
    </tr><tr>
      <td class="setting_name">&nbsp;</td>
      <td class="setting_value" colspan="5"><input type="file" name="bk_book_doc" /></td>      
    </tr><tr>
      <td class="setting_name">' . $MOD_GSMOFF[ 'LAB_UPDATED' ] . '&nbsp;:</td>
      <td class="setting_value" colspan="5">{update}( rec {recid})</td>
    </tr>
  </table>
  </div>
  <div class="container">
  <table class="footer" width="100%">
    {selection}
  </table>
  </form>
  </div>'; //  
/*
 * various functions
 */
function func_guid() { // create guid
  if (function_exists('com_create_guid') === true) { return trim(com_create_guid(), '{}'); }
  return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
} 
function func_table_preload ( $table, $func=1, $first='') {  // Check table precense or preload parts of it
  global $database;
  global $MOD_GSMOFF; 
  $oke = true;
  $returnvalue = '';
  switch ( $func ) {
    case 1: //precense
      $check_query = "SHOW TABLES LIKE '" . $table . "'";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error0' ] . $table . " missing</br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
	  $returnvalue=$oke;
	  break;
    case 2: // preload rekening nummers
	  (is_array($first)) ?$rekeningArr = $first : $rekeningArr = array( );
      $check_query   = "SELECT * FROM `" . $table . "` ORDER BY `rekeningnummer`";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error2' ] . $table . " br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
      while ( $result = $check_results->fetchRow() ) $rekeningArr[ $result[ 'id' ] ] = $result[ 'rekeningnummer' ] . " - " . $result[ 'name' ];
      $returnvalue=$rekeningArr;
	  break;
    case 3: // preload projects
	  (is_array($first)) ? $projectArr = $first : $projectArr = array( );
      $check_query   = "SELECT * FROM `" . $table . "` ORDER BY `name`";
      $message = __LINE__ ." func ". $MOD_GSMOFF[ 'error2' ] . $table . " </br>";
      $check_results = $database->query( $check_query );
      if ( !$check_results || $check_results->numRows() == 0 )  die( $message ); 
      while ( $result = $check_results->fetchRow() ) $projectArr[ $result[ 'name' ] ] = $result[ 'name' ] . " - " . $result[ 'project' ];
      $returnvalue=$projectArr;
	  break;
    case 9: // file parameters	
      // $ first contains section	
      $settingsArr = array( );	
	  $check_query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='".$first."' ORDER BY `id`";
      $check_results = $database->query( $check_query );
      while ( $result = $check_results->fetchRow() ) $settingArr[$result['name']]= $result['value']; 
	  $check_query = "SELECT * FROM `" . CH_DBBASE ."` WHERE `section`='0' AND `table`='".$table."' ORDER BY `id`";
      $check_results = $database->query( $check_query );
      while ( $result = $check_results->fetchRow() ) $settingArr[$result['name']]= $result['value']; 
      $returnvalue=$settingArr;
	  break;
    default:
      break;
  }
  return $returnvalue;
}
/*
 *  Checking and preloading
 */
func_table_preload ( $regelsArr[ 'table' ], 1); //check precense
$rekeningArray[ 0 ] = ' -- ';
$rekeningArray = func_table_preload ( $regelsArr[ 'table_rek' ], 2 ,$rekeningArray); // preload data
$projectArray[ '    ' ] = '';
$projectArray = func_table_preload ( $regelsArr[ 'table_proj' ], 3 , $projectArray); // preload data
$settingArr=array_merge($settingArr, func_table_preload ( $regelsArr[ 'file' ], 9 , $page_id)); // Pick up settings
/*
 * debug
 */
if ( $debug ) {
  Gsm_debug( $settingArr, __LINE__ );
  Gsm_debug( $regelsArr, __LINE__ );
  Gsm_debug( $_POST, __LINE__ );
  Gsm_debug( $_GET, __LINE__ );
  Gsm_debug( $place, __LINE__ );
  Gsm_debug( $rekeningArray, __LINE__ );
  Gsm_debug( $projectArray, __LINE__ );
} //$debug
/*
 * some job to do ?
 */
if ( isset( $_POST[ 'command' ] ) ) {
  switch ( $_POST[ 'command' ] ) {
    case $MOD_GSMOFF[ 'tbl_icon' ][ 2 ]: // terug
      $regelsArr[ 'mode' ] = 9;
      break;
    case $MOD_GSMOFF[ 'tbl_icon' ][ 4 ]:
      $regelsArr[ 'save_update' ] = true;
    case $MOD_GSMOFF[ 'tbl_icon' ][ 5 ]:
      /*
       * SIPS checks
       */
      if ( !isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] ) ) {
        $msg[ 'err' ] .= $MOD_GSMOFF[ 'error4' ] . '</br>';
        unset( $_POST );
        break;
      } //!isset( $_SESSION[ 'page_h' ] ) || ( $_SESSION[ 'page_h' ] <> $_POST[ 'sh' ] )
      $regelsArr[ 'recid' ] = $_POST[ 'recid' ];
      if ( $regelsArr[ 'save_update' ] ) {
        $query   = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
        $results = $database->query( $query );
        $row     = $results->fetchRow();
        if ( $_POST[ 'update_verif' ] != $row[ 'updated' ] ) {
          $msg[ 'err' ] .= $MOD_GSMOFF[ 'error4' ] . '</br>';
          unset( $_POST );
          break;
        } //$_POST[ 'update_verif' ] != $row[ 'updated' ]
      } //$regelsArr[ 'save_update' ]
	  /*
	   * SIPs check done and record collected
	   */ 
      $hulpArr = array(
         'name' => Gsm_eval( $_POST[ 'bk_name' ] ),
        'booking_date' => Gsm_eval( $_POST[ 'bk_date' ], 9, $regelsArr[ 'tyear' ], $regelsArr[ 'fyear' ] ),
        'project' => $_POST[ 'bk_project_name' ],
        'debet_amount' => Gsm_eval( $_POST[ 'bk_debet_amount' ], 8, 999999, -999999 ),
        'tegen2_amount' => Gsm_eval( $_POST[ 'bk_tegen2_amount' ], 8, 999999, -999999 ),
        'tegen1_amount' => '0',
        'debet_id' => $_POST[ 'bk_debet_name' ],
        'tegen1_id' => $_POST[ 'bk_tegen1_name' ],
        'tegen2_id' => $_POST[ 'bk_tegen2_name' ],
        'debet_rekening' => Gsm_eval( substr( $rekeningArray[ $_POST[ 'bk_debet_name' ] ], 0, $regelsArr[ 'rekening size' ] ), 8, 9999 ),
        'tegen1_rekening' => Gsm_eval( substr( $rekeningArray[ $_POST[ 'bk_tegen1_name' ] ], 0, $regelsArr[ 'rekening size' ] ), 8, 9999 ),
        'tegen2_rekening' => Gsm_eval( substr( $rekeningArray[ $_POST[ 'bk_tegen2_name' ] ], 0, $regelsArr[ 'rekening size' ] ), 8, 9999 ),
        'boekstuk' => Gsm_eval( $_POST[ 'bk_comment' ] ),
        'zoek' => '' 
      );
      if ( $hulpArr[ 'booking_date' ] < date( "Y-m", strtotime( $regelsArr[ 'tyear' ] ) ) . "-01" ) {
        $hulpArr[ 'boekstuk' ] = str_replace( "_", "", $hulpArr[ 'boekstuk' ] );
      } //$hulpArr[ 'booking_date' ] < date( "Y-m", strtotime( $regelsArr[ 'tyear' ] ) ) . "-01"
      $hulpArr[ 'tegen1_amount' ] = Gsm_eval( $hulpArr[ 'debet_amount' ] - $hulpArr[ 'tegen2_amount' ], 8, 999999, -999999 );
      $hulpArr[ 'zoek' ]          = $settingArr[ 'opzoek' ]; // hoe ziet zoek eruit
      foreach ( $hulpArr as $key => $value ) {
        $hulpArr[ 'zoek' ] = str_replace( $key, $value, $hulpArr[ 'zoek' ] );
      } //$hulpArr as $key => $value
      // arrange tables for update or insert
      if ( $regelsArr[ 'save_update' ] ) { // save
        $query   = "UPDATE `" . $regelsArr[ 'table' ] . "` SET " . Gsm_parse( 2, $hulpArr ) . "  WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
		if ($debug) $msg[ 'bug' ] .= __LINE__ . ' '.$query.'</br>';      
        $results = $database->query( $query );
        $msg[ 'inf' ] .= $MOD_GSMOFF[ 'save' ] . ' : ' . $hulpArr[ 'name' ] . ' ' . $MOD_GSMOFF[ 'changed' ] . ' </br>';
        unset( $query );
      } //$regelsArr[ 'save_update' ]
      else { // add
        $query   = "INSERT INTO `" . $regelsArr[ 'table' ] . "` " . Gsm_parse( 1, $hulpArr );
        $results = $database->query( $query );
        $msg[ 'inf' ] .= $MOD_GSMOFF[ 'addn' ] . ' : ' . $hulpArr[ 'name' ] . ' ' . $MOD_GSMOFF[ 'added' ] . ' </br>';
        unset( $query );
      }
      if ($debug) $msg[ 'bug' ] .= __LINE__ . ' EN: bijlage checking' . '</br>';
      if ( isset( $_FILES[ "bk_book_doc" ][ "error" ] ) && $_FILES[ "bk_book_doc" ][ "error" ] == 0 ) {
	  // there is an attachment
		$upload_Ok = false; 
        $hulp        = $_FILES[ "bk_book_doc" ][ "name" ];
        $hlp         = explode( ".", $_FILES[ "bk_book_doc" ][ "name" ] );
        $extension   = end( $hlp );
        if (in_array( $extension, $regelsArr['allow_ext'] ) ) { // extension test
          if ($_FILES[ "bk_book_doc" ][ "size" ] < 6000000 ) { // file size test
            $upload_Ok = true;
			if ($debug) $msg[ 'bug' ] .= __LINE__ . ' EN: bijlage checking' . '</br>';
		  }
		}
		if ($upload_Ok ) {
		  if ( $regelsArr[ 'save_update' ] ) {
		    $query   = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
		  } else {
		    $query   = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE `booking_date` >= '" . $regelsArr[ 'fyear' ] . "' ORDER BY `updated` DESC LIMIT 1";
		  }
          $results = $database->query( $query );
          $row     = $results->fetchRow();
		  $dir_to = WB_PATH. "/" . $regelsArr['file0'] ;
		  if (!file_exists($dir_to. "/" )) { mkdir($dir_to, 0777); } // create if not exist  
		  $dir_to = WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] ;  
          if (!file_exists($dir_to. "/" )) { mkdir($dir_to, 0777); } // create if not exist  		  
          $sub='_'.$regelsArr['file_pre'].'_'; 
		  $guid4 = strtolower(substr(func_guid(), 0, 6));
		  $filename = $row['booking_date'].$guid4.$sub.$row['id'].'_'.$row['project'].'_'.$row['name'].'.'.$extension;
          if ( move_uploaded_file( $_FILES[ "bk_book_doc" ][ "tmp_name" ], $dir_to. "/". $filename))  {
            if ($debug) $msg['bug'] .= __LINE__ . ' '. $dir_to. "/". $filename.' added <br/>';
            $msg[ 'inf' ] .= $filename . ' '.$MOD_GSMOFF[ 'OK_UPLOAD' ].'</br>';		
          } //move_uploaded_file( $_FILES[ "bk_book_doc" ][ "tmp_name" ], $filename )
		  unset( $query );
		} else {
          $msg[ 'inf' ] .= ' '.$MOD_GSMOFF[ 'ERROR_UPLOAD' ].'</br>';
        }
      } else {
        if ($debug) $msg[ 'bug' ] .= 'EN: geen geaccepteerde bijlage' . '</br>';
      } //isset( $_FILES[ "bk_book_doc" ][ "error" ] ) && $_FILES[ "bk_book_doc" ][ "error" ] == 0
      $query               = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` ORDER BY `updated` DESC LIMIT 1"; // 20140423 show 1 for confirmation after edit
      $results             = $database->query( $query );
      $regelsArr[ 'mode' ] = 9;
      break;
    case $MOD_GSMOFF[ 'go' ]:
      if ( isset( $_POST[ 'next' ] ) ) {
        $regelsArr[ 'seq' ] = $_POST[ 'next' ];
      } //isset( $_POST[ 'next' ] )
      $regelsArr[ 'mode' ] = 9;
      break;

    default:
      $msg[ 'err' ] .= __LINE__ . " post: " . $_POST[ 'command' ] . '<br/>';
      $regelsArr[ 'mode' ] = 9;
      break;
  } //$_POST[ 'command' ]
} //isset( $_POST[ 'command' ] )
elseif ( isset( $_GET[ 'command' ] ) ) {
  switch ( $_GET[ 'command' ] ) {
    case 'rm':
      $arr_files = Gsm_get_files( WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] , $_GET[ 'file' ]."_".$regelsArr['file_pre']."_". $_GET[ 'recid' ]);	
      $dir_to = WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] ;  
	  foreach ( $arr_files as $key => $value ) {
	    if (file_exists ($dir_to. "/". $value) ) {
		  if ( unlink($dir_to. "/". $value))  $msg[ 'inf' ] .= $value . ' removed</br>';	
		}
	  }
    case 'view':
      $regelsArr[ 'mode' ]  = 5;
      $regelsArr[ 'recid' ] = $_GET[ 'recid' ];
      $query                = "SELECT * FROM `" . $regelsArr[ 'table' ] . "`WHERE `id`='" . $regelsArr[ 'recid' ] . "'";
      $results              = $database->query( $query );
      if ( !$results || $results->numRows() > 0 ) {
        $row                              = $results->fetchRow();
        $populateArray[ 'name' ]          = $row[ 'name' ];
        $populateArray[ 'booking_date' ]  = $row[ 'booking_date' ];
        $populateArray[ 'updated' ]       = $row[ 'updated' ];
        $populateArray[ 'boekstuk' ]      = $row[ 'boekstuk' ];
        $populateArray[ 'project' ]       = $row[ 'project' ];
        $populateArray[ 'debet_amount' ]  = $row[ 'debet_amount' ];
        $populateArray[ 'tegen1_amount' ] = $row[ 'tegen1_amount' ];
        $populateArray[ 'tegen2_amount' ] = $row[ 'tegen2_amount' ];
        $populateArray[ 'debet_id' ]      = $row[ 'debet_id' ];
        $populateArray[ 'tegen1_id' ]     = $row[ 'tegen1_id' ];
        $populateArray[ 'tegen2_id' ]     = $row[ 'tegen2_id' ];
        $populateArray[ 'recid' ]         = $row[ 'id' ];
        $populateArray[ 'bijlage' ]       = '';
      } //!$results || $results->numRows() > 0
      else {
        $regelsArr[ 'mode' ] = 9;
        unset( $query );
      }
      break;
    default:
      $msg[ 'err' ] .= __LINE__ . " get: " . $_POST[ 'command' ] . '<br/>';
      $regelsArr[ 'mode' ] = 9;
      break;
  } //$_GET[ 'command' ]
} //isset( $_GET[ 'command' ] )
// so standard display
/*
 * standard display job with or without search
 */
if ( !isset( $query ) ) {
  if ( isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 1 ) {
    $regelsArr[ 'search' ] = trim( $_POST[ 'selection' ] );
    $help                  = "%" . str_replace( ' ', '%', $regelsArr[ 'search' ] ) . "%";
    $regelsArr[ 'search_mysql' ] .= "AND `zoek` LIKE '" . $help . "'";
  } //isset( $_POST[ 'selection' ] ) && strlen( $_POST[ 'selection' ] ) >= 1
  // bepaal aantal records
  $query   = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE `booking_date` >= '" . $regelsArr[ 'fyear' ] . "'  AND `booking_date` < '" . $regelsArr[ 'xyear' ] . "'" . $regelsArr[ 'search_mysql' ] . " ORDER BY `booking_date`";
  $results = $database->query( $query );
  if ( $results ) {
    $regelsArr[ 'n' ] = $results->numRows();
  } //$results
  if ( $regelsArr[ 'n' ] == 0 && $regelsArr[ 'search_mysql' ] == '' ) { // case empty file at last one record is needed
    $hulpArr = array(
       'name' => 'openings balans',
      'debet_id' => 1,
      'tegen1_id' => 1,
      'booking_date' => $regelsArr[ 'fyear' ] 
    );
    $query2  = "INSERT INTO `" . $regelsArr[ 'table' ] . "` " . Gsm_parse( 1, $hulpArr );
    $results = $database->query( $query2 );
    // herkansing
    $results = $database->query( $query );
    if ( $results ) {
      $regelsArr[ 'n' ] = $results->numRows();
    } //$results
  } //$regelsArr[ 'n' ] == 0 && $regelsArr[ 'search_mysql' ] == ''
  if ( $regelsArr[ 'seq' ] >= $regelsArr[ 'n' ] ) {
    $regelsArr[ 'seq' ] = 0;
  } //$regelsArr[ 'seq' ] >= $regelsArr[ 'n' ]
  $query = "SELECT * FROM `" . $regelsArr[ 'table' ] . "` WHERE `booking_date` >= '" . $regelsArr[ 'fyear' ] . "' " . $regelsArr[ 'search_mysql' ] . "ORDER BY `booking_date` DESC  LIMIT " . $regelsArr[ 'seq' ] . ", " . $regelsArr[ 'qty' ];
} //!isset( $query )
// at this point the update is done and the mode and databse query are prepared database query for the relevant records prepared
if ( $debug )
  $msg[ 'bug' ] .= __LINE__ . " mode: " . $regelsArr[ 'mode' ] . ' query: ' . $query . '</br>';
/*
 * display data starting from a read query
 */
/*
 * display preparation
 */
switch ( $regelsArr[ 'mode' ] ) {
  case 5: // edit
    break;
  case 9:
  default: // default list 
    $regelsArr[ 'head' ] .= $LINETEMP[ 1 ];
    $regelsArr[ 'head' ] .= sprintf( $LINETEMP[ 2 ], $MOD_GSMOFF[ 'line_color' ][ 3 ], strtoupper( $MOD_GSMOFF[ 'LAB_DATUM' ] ), strtoupper( $MOD_GSMOFF[ 'LAB_REK' ] ), strtoupper( $MOD_GSMOFF[ 'LAB_REF' ] ), strtoupper( $MOD_GSMOFF[ 'LAB_DEBET' ] ), strtoupper( $MOD_GSMOFF[ 'LAB_CREDIT' ] ) );
    $regelsArr[ 'descr' ] .= $LINETEMP[ 1 ];
    $results = $database->query( $query );
    $i= 0;
    if ( $results && $results->numRows() > 0 ) {
      while ( $row = $results->fetchRow() ) {
	    if ( $row[ 'booking_date' ] < $regelsArr[ 'xyear' ] ) {
          $i++;
          $col = ( $i % 2 == 0 ) ? $MOD_GSMOFF[ 'line_color' ][ 2 ] : '';
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 3 ], $col, $row[ 'id' ], $row[ 'booking_date' ], $row[ 'project' ], $row[ 'name' ], '', '' );
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 21 ], $col, $rekeningArray[ $row[ 'debet_id' ] ], Gsm_opmaak( $row[ 'debet_amount' ], 8 ) );
          $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 22 ], $col, $MOD_GSMOFF[ 'AAN' ] . $rekeningArray[ $row[ 'tegen1_id' ] ], Gsm_opmaak( $row[ 'tegen1_amount' ], 8 ) );
          if ( $row[ 'tegen2_amount' ] != 0 )
            $regelsArr[ 'descr' ] .= sprintf( $LINETEMP[ 22 ], $col, $MOD_GSMOFF[ 'AAN' ] . $rekeningArray[ $row[ 'tegen2_id' ] ], Gsm_opmaak( $row[ 'tegen2_amount' ], 8 ) );
	    }
      } //$row = $results->fetchRow()
    } //$results && $results->numRows() > 0
    else {
      $regelsArr[ 'descr' ] .= $MOD_GSMOFF[ 'nodata' ];
    }
    break;
} //$regelsArr[ 'mode' ]
/*
 * display select elements
 */
switch ( $regelsArr[ 'mode' ] ) {
  case 5: // edit
    $regelsArr[ 'select' ] .= $LINETEMP[ 1 ];
    $regelsArr[ 'select' ] .= sprintf( $LINETEMP[ 2 ], '', $ICONTEMP[ 4 ], $ICONTEMP[ 2 ], '', '', $ICONTEMP[ 5 ] );
    $arr_files = Gsm_get_files( WB_PATH. "/" . $regelsArr['file0']. "/" . $regelsArr['file1'] , "_".$regelsArr['file_pre']."_" . $populateArray[ 'recid' ] );
    foreach ( $arr_files as $key => $value ) {
      $populateArray[ 'bijlage' ] .= sprintf( $ICONTEMP[ 20 ], $regelsArr['file0']. "/" . $regelsArr['file1'], $value, $value, $populateArray[ 'recid' ], substr($value,10,6 ));
      if ($debug) $msg[ 'bug' ] .= __LINE__.' EN: ' . $key . "|" . $value . '</br>';
    } //$arr_files as $key => $value
    break;
  default: // default list
    $regelsArr[ 'select' ] .= $LINETEMP[ 1 ];
    $regelsArr[ 'select' ] .= sprintf( $LINETEMP[ 9 ], 
	"", 
	"", 
	Gsm_next( $regelsArr[ 'search' ], $regelsArr[ 'n' ], 
	$regelsArr[ 'seq' ], $regelsArr[ 'qty' ] ), 
	(isset($regelsArr['filename_pdf'])) ? sprintf($ICONTEMP[18], "", $regelsArr['filename_pdf']) : "", 
	"" );
     break;
} //$regelsArr[ 'mode' ]
/*
 * the output to the screen
 */
/*
 * display
 */
switch ( $regelsArr[ 'mode' ] ) {
  case 5: // edit
    $_SESSION[ 'page_h' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
    $parseViewArray       = array(
       'header' => strtoupper( $regelsArr[ 'project' ] ),
      'kopregels' => $regelsArr[ 'head' ],
      'page_id' => $page_id,
      'zoeken' => $regelsArr[ 'search' ],
      'section_id' => $section_id,
      'message' => message( $msg, $debug ),
      'recid' => $populateArray[ 'recid' ],
      'bk_name' => $populateArray[ 'name' ],
      'bk_date' => $populateArray[ 'booking_date' ],
      'bk_comment' => $populateArray[ 'boekstuk' ],
      'bk_project_opt' => Gsm_option( $projectArray, $populateArray[ 'project' ] ),
      'bk_debet_opt' => Gsm_option( $rekeningArray, $populateArray[ 'debet_id' ] ),
      'bk_tegen1_opt' => Gsm_option( $rekeningArray, $populateArray[ 'tegen1_id' ] ),
      'bk_tegen2_opt' => Gsm_option( $rekeningArray, $populateArray[ 'tegen2_id' ] ),
      'bk_debet_amount' => $populateArray[ 'debet_amount' ],
      'bk_tegen1_amount' => $populateArray[ 'tegen1_amount' ],
      'bk_tegen2_amount' => $populateArray[ 'tegen2_amount' ],
      'bk_book_doc' => $populateArray[ 'bijlage' ],
      'update' => $populateArray[ 'updated' ],
      'hash' => $_SESSION[ 'page_h' ],
      'selection' => $regelsArr[ 'select' ],
      'memory' => $regelsArr[ 'memory' ] . "|",
      'toegift' => $regelsArr[ 'toegift' ],
      'rapportage' => $regelsArr[ 'rapport' ],
      'return' => CH_RETURN,
      'mod' => $regelsArr[ 'module' ],
      'module' => $regelsArr[ 'module' ],
      'sel' => '' 
    );
    $prout .= Gsm_prout ($TEMPLATE[ 3 ] . $TEMPLATE[ 4 ], $parseViewArray);
    break;
  default: // default list
    $_SESSION[ 'page_h' ] = sha1( MICROTIME() . $_SERVER[ 'HTTP_USER_AGENT' ] );
    $parseViewArray       = array(
       'header' => strtoupper( $regelsArr[ 'project' ] ),
      'page_id' => $page_id,
      'section_id' => $section_id,
      'kopregels' => $regelsArr[ 'head' ],
      'description' => $regelsArr[ 'descr' ],
      'message' => message( $msg, $debug ),
      'selection' => $regelsArr[ 'select' ],
      'return' => CH_RETURN,
      'parameter' => $regelsArr[ 'search' ],
      'module' => $regelsArr[ 'module' ],
      'memory' => $regelsArr[ 'memory' ] . "|",
      'toegift' => $regelsArr[ 'toegift' ],
      'recid' => $regelsArr[ 'recid' ],
      'rapportage' => $regelsArr[ 'rapport' ],
      'hash' => $_SESSION[ 'page_h' ] 
    );
    $prout .= Gsm_prout ($TEMPLATE[ 2 ], $parseViewArray);
    break;
} //$regelsArr[ 'mode' ]
if (strstr($set_mode, "vers")) {$prout .= "<small>".$regelsArr ['modulen'].$regelsArr ['versie']."</small>";}
?> 