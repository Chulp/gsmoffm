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
if ( defined( 'WB_PATH' ) ) {
	include( WB_PATH . '/framework/class.secure.php' );
} else {
	$oneback = "../";
	$root    = $oneback;
	$level   = 1;
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

/* change history
 * 20151207  aanpassing naar nieuw maximum (vtrantable wordt ook aangepast)
 * 20151207  aanpassing naar nieuwe AOW leeftijd stappen
 */

// end include class.secure.php
$regelsArr [ 'pensioen_base'] = 780; //=65jaar * 12 maanden;
$regelsArr [ 'pensioen_offset'] = array (  // in maanden tabel aangepast 20151207  aanpassing naar nieuwe AOW leeftijd stappen
  '1940-01-01'=>0,
  '1948-01-01'=>1,
  '1948-12-01'=>2,
  '1949-11-01'=>3,  
  '1950-10-01'=>6,
  '1951-07-01'=>9,
  '1952-04-01'=>12,
  '1953-01-01'=>16,
  '1953-09-01'=>20,
  '1955-01-01'=>24,
  '1956-01-01'=>24,
  '1957-01-01'=>24,
  '2038-01-01'=>24);
$regelsArr [ 'overgangsregeling' ] = array(
  'einde' => '2020-01-01',
  'begin' => '2015-07-01', 
  'kleintjes' => '2013-05-01' ); 
$regelsArr [ 'max_transitie'] = 75000; //depricated 20151207 
$regelsArr [ 'maxima_transitie'] = array ( //toegevoegd 20151207 
  '2015-07-01'=>75000,   //toegevoegd 20151207 
  '2016-01-01'=>76000);  //toegevoegd 20151207 
$regelsArr [ 'max_vakantie_over']= 54064.80;
