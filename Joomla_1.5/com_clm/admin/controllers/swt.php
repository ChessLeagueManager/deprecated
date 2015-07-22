<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerSWT extends JController
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask('upload','upload');
		$this->registerTask('apply','save');
		$this->registerTask('unpublish','publish');
	}

function display()
	{
	require_once(JPATH_COMPONENT.DS.'views'.DS.'swt.php');
	CLMViewSWT::SWT( $rows, $lists, $pageNav);
	}

function import_1()
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$task 		= JRequest::getVar( 'task');
	$db 		=& JFactory::getDBO();
	$datei		= JRequest::getVar('sql_datei');
	$filesDir	= JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'swt';
	$fileName	= $filesDir.DS.$datei;

	// Pr�fen ob eine Datei gew�hlt wurde
	if($datei =="0") {
	JError::raiseWarning( 500, JText::_( 'SWT_NO_DATEI' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	
	// aktuelle Saison ermitteln
	$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
	$db->setQuery($sql);
	$sid = $db->loadResult();

	/// SWT Datei in Array einlesen
	$swt[]='';
		if($fh = fopen($fileName,'rb')){
			while (!feof($fh)){
				$swt[] = ord(fgets($fh,2));
					}
			fclose($fh);
		}

	// ZPS f�r Mannschaften aus Spielerdaten
	$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19;
	for ($x=1; $x < 1+$swt[603]; $x++) {
		$man_zps[$x] ="manuell w&auml;hlen !";
		$offset += 655;
		}

	$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19;
	for ($x=0; $x < $swt[8]; $x++) {
		$zps = CLMControllerSWT::give_number( $swt,1+153+$offset,7);
		if($zps !="") { $man_zps[$swt[$offset+202]] =$zps; }
		$offset += 655;
		}

	// Filter
	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$lv	= $config->get('lv',705);
	$vl	= $config->get('vereineliste',1);
	$vs	= $config->get('verein_sort',1);
	$version= $config->get('version',0);
	$dat	= substr($lv, 1);
	$dat2	= substr($lv, 2);

	// Vereinefilter
	if($version =="0"){
		if($dat == "00"){
		$ug = (substr($lv, 0, 1)).'0000';
		$og = (substr($lv, 0, 1)).'9999';
				}
		if($dat2 =="0" AND $dat !="00") {
		$ug = (substr($lv, 0, 2)).'000';
		$og = (substr($lv, 0, 2)).'999';
				}
		if($dat2 !="0" AND $dat !="00") {
		$ug =$lv.'00';
		$og =$lv.'99';
		}
		}

	if($version =="1"){
		if($lv=="00"){	$ug =$lv; $og ="99"; } else { $ug =$lv;	$og =$lv; }}
		$sql = "SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
			." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
			." WHERE a.ZPS BETWEEN '$ug' AND '$og' "
			." AND s.archiv = 0 AND s.published = 1 ORDER BY ";
				if($vs =="1") { $sql =$sql."a.ZPS ASC";} else {  $sql =$sql." a.Vereinname ASC";}

	$db->setQuery($sql);
	$vereine = $db->loadObjectList();
	
	// Mannschaftsnamen f�r manuelle Auswahl holen wenn vorhanden
	$l_name = CLMControllerSWT::give_name( $swt,377,441);
	$sql = " SELECT m.name FROM #__clm_swt_liga AS a "
		." LEFT JOIN #__clm_swt_man AS m ON m.swt_id = a.swt_id "
		." WHERE Liga ='$l_name'"
		." ORDER BY tln_nr ASC ";
	$db->setQuery($sql);
	$name_manuell = $db->loadObjectList();

	require_once(JPATH_COMPONENT.DS.'views'.DS.'swt.php');
	CLMViewSWT::Import_1 ($spieler, $lists, $man, $swt,$man_zps,$vereine, $sid, $fileName, $name_manuell);
	}

function import_2()
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$task 		= JRequest::getVar( 'task');
	$db 		=& JFactory::getDBO();
	$step 		= JRequest::getVar( 'step');
	$fileName	= JRequest::getVar('fileName');

	// Wenn von Schritt 1 kommend dann speichern !
	if($step =="import_1"){
		$date	= & JFactory::getDate();
		$now	= $date->toMySQL();

	// Ligadaten holen
		$turnierart	= JRequest::getInt('liga2');		
		$liga_name	= JRequest::getVar('liga3');
		$anz_mann	= JRequest::getInt('liga4');
		$runden		= JRequest::getInt('liga5');
		$gesp_runden	= JRequest::getInt('liga6');
		$bretter	= JRequest::getInt('liga7');
		$spieler	= JRequest::getInt('liga8');
		$durchgaenge	= JRequest::getInt('liga9');
		$akt_durchgang	= JRequest::getInt('liga10');

	// Ligadaten verarbeiten
	$sql = " SELECT * FROM #__clm_swt_liga WHERE Liga ='$liga_name'";
	$db->setQuery($sql);
	$liga_daten = $db->loadObjectList();
		$anzahl = $liga_daten[0]->import_anzahl;
		$liga_clm = $liga_daten[0]->clm_id;
		$liga_swt = $liga_daten[0]->swt_id;
	if ($anzahl <1) {
		$sql = " REPLACE INTO #__clm_swt_liga (`swt_id`,`clm_id`,`Liga`,`Mannschaften`,`Runden`,`gesp_Runden`,`Spieler`,`Bretter`,`Turnierart`,`Durchgaenge`,`akt_DG`,`import_datum`,`import_anzahl`) "
			." VALUES ('', '$liga_clm', '$liga_name', '$anz_mann', '$runden', '$gesp_runden', '$spieler', '$bretter', '$turnierart', '$durchgaenge', '$akt_durchgang','$now','1')";
		$db->setQuery($sql);
		$db->query();
			}
//////////////
// Abfrage auf evtl. ge�nderte Anzahl Mannschaften, Spieler etc entwerfen !! Dann Tabelleneintr�ge l�schen und neu !!
//////////////

	else {	$anzahl++; 
		$sql = " UPDATE #__clm_swt_liga "
			." SET import_anzahl = '$anzahl'"
			." , import_datum = '$now'"
			." WHERE swt_id = ".$liga_swt;
		$db->setQuery($sql);
		$db->query();
		}

	// Mannschaftsdaten holen und verarbeiten
		if ($anzahl <1) {
			$sql = " SELECT swt_id FROM #__clm_swt_liga WHERE Liga ='$liga_name' LIMIT 1";
			$db->setQuery($sql);
		$temp_swt = $db->loadObjectList();
		$liga_swt = $temp_swt[0]->swt_id;
			}

	for ($x=1; $x < 1+$anz_mann; $x++) {
		$m_name	= JRequest::getVar('man_name'.$x);
		$m_zps	= JRequest::getInt('man_zps'.$x);
		$manuell_name	= JRequest::getVar('manuell_name'.$x);
			if($manuell_name !="") { $m_name = $manuell_name; }
		if ($anzahl <1) {
			$sql = " INSERT INTO #__clm_swt_man (`swt_id`,`name`,`zps`,`tln_nr`) VALUES ('$liga_swt', '$m_name', '$m_zps', '$x')";
		} else {
			$sql = " UPDATE #__clm_swt_man "
			." SET swt_id = '$liga_swt'"
			." , name = '$m_name'"
			." , zps = '$m_zps'"
			." , tln_nr = '$x'"
			." WHERE swt_id = ".$liga_swt." AND zps = ".$m_zps;
		}
		$db->setQuery($sql);
		$db->query();
	}
	}
	/////////////////////////////////
	
	// aktuelle Saison ermitteln
	$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
	$db->setQuery($sql);
	$sid = $db->loadResult();

	/// SWT Datei in Array einlesen
	$swt[]='';
	if($fh = fopen($fileName,'rb')){ while (!feof($fh)){ $swt[] = ord(fgets($fh,2)); } fclose($fh); }

	// ZPS f�r Mannschaften aus Spielerdaten
	$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19;
	for ($x=1; $x < 1+$swt[603]; $x++) {
		$man_zps[$x] ="manuell w&auml;hlen !";
		$offset += 655;
		}
	$sg_zps = array();
	$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19;
	for ($x=0; $x < $swt[8]; $x++) {
		$zps = CLMControllerSWT::give_number( $swt,1+153+$offset,7);
		if($zps !="") { $man_zps[$swt[$offset+202]] =$zps; }
		// Array zum Erkennen von SG und ihren ZPS schreiben !
		if ($zps !="" AND $sg_zps[1][$swt[$offset+202]] =="")
			{ $sg_zps[1][$swt[$offset+202]] = $zps; }

		if ($zps !="" AND $zps != $sg_zps[1][$swt[$offset+202]] AND $sg_zps[2][$swt[$offset+202]] =="" )
			{ $sg_zps[2][$swt[$offset+202]] = $zps; }

		$offset += 655;
		}
	// Filter
	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$lv	= $config->get('lv',705);
	$vl	= $config->get('vereineliste',1);
	$vs	= $config->get('verein_sort',1);
	$version= $config->get('version',0);
	$dat	= substr($lv, 1);
	$dat2	= substr($lv, 2);

	// Vereinefilter
	if($version =="0"){
		if($dat == "00"){
		$ug = (substr($lv, 0, 1)).'0000';
		$og = (substr($lv, 0, 1)).'9999';
				}
		if($dat2 =="0" AND $dat !="00") {
		$ug = (substr($lv, 0, 2)).'000';
		$og = (substr($lv, 0, 2)).'999';
				}
		if($dat2 !="0" AND $dat !="00") {
		$ug =$lv.'00';
		$og =$lv.'99';
		}
		}

	if($version =="1"){
		if($lv=="00"){	$ug =$lv; $og ="99"; } else { $ug =$lv;	$og =$lv; }}
		$sql = "SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
			." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
			." WHERE a.ZPS BETWEEN '$ug' AND '$og' "
			." AND s.archiv = 0 AND s.published = 1 ORDER BY ";
				if($vs =="1") { $sql =$sql."a.ZPS ASC";} else {  $sql =$sql." a.Vereinname ASC";}

	$db->setQuery($sql);
	$vereine = $db->loadObjectList();
	
	// Mannschaftsnamen f�r manuelle Auswahl holen wenn vorhanden
	$l_name = CLMControllerSWT::give_name( $swt,377,441);
	$sql = " SELECT m.name FROM #__clm_swt_liga AS a "
		." LEFT JOIN #__clm_swt_man AS m ON m.swt_id = a.swt_id "
		." WHERE Liga ='$l_name'"
		." ORDER BY tln_nr ASC ";
	$db->setQuery($sql);
	$name_manuell = $db->loadObjectList();

	// Spielerdaten generieren
	// gespeicherte ZPS f�r Zuordnung der Spieler auslesen 
	$sql = " SELECT m.zps FROM #__clm_swt_liga AS a "
		." LEFT JOIN #__clm_swt_man AS m ON m.swt_id = a.swt_id "
		." WHERE Liga ='$l_name'"
		." ORDER BY tln_nr ASC ";
	$db->setQuery($sql);
	$zps_db = $db->loadObjectList();

	// Array zur �bergabe der Spielerdaten an den View generieren
	$info=0;$cnt=1;
	for($y=0; $y < $swt[603]; $y++) {
		$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19 + $y*655;
		for($x=0; $x < $swt[8]; $x++) {
			$zps = CLMControllerSWT::give_number( $swt,1+153+$offset,7);
			$info="";
	if ($zps=="") { $zps = $zps_db[$y]->zps; $info =1;}
	if ($y+1 == $swt[$offset+202]) {
		//$spl_name	= utf8_decode(CLMControllerSWT::give_name( $swt,1+$offset,($offset+65)));
		$spl_name	= CLMControllerSWT::give_name( $swt,1+$offset,($offset+65));
		$spl_ges	= CLMControllerSWT::give_name( $swt,1+184+$offset,(184+$offset+1));
		$mgl		= CLMControllerSWT::give_number( $swt,1+159+$offset,4);
		$tln_man	= $swt[$offset+218];
		$man_nr		= $swt[$offset+202];
		
	// Array : Counter => Tln_nr, Man_nr, Name, Geschl., ZPS, Mgl_Nr, Info
	if ($y+1 == $swt[$offset+202]) {
		$data[$cnt] = array ( $tln_man, $man_nr, $spl_name, $spl_ges, $zps, $mgl, $info) ;
		$cnt++;
	}
		} $offset +=655; }}
		
	// manuell eingegebene Spielernamen holen !
	$sql = " SELECT Name,Nr FROM #__clm_swt_spl "
		." WHERE liga_swt ='$liga_swt'"
		." ORDER BY Nr ASC ";
	$db->setQuery($sql);
	$name_spl = $db->loadObjectList();
		$count=0;
		for($x=1; $x < 1+$swt[8]; $x++) {
			if($name_spl[$count]->Nr == $x) {
				$name_man_spl[$x] = $name_spl[$count]->Name;
				$count++;
			}
		}
	require_once(JPATH_COMPONENT.DS.'views'.DS.'swt.php');
	CLMViewSWT::Import_2 ($lists, $swt, $name_manuell, $zps_db, $vereine, $sid, $data, $liga_swt, $name_man_spl, $fileName, $sg_zps);
	}

function import_3()
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$task 		= JRequest::getVar( 'task');
	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$step 		= JRequest::getVar( 'step');
	$liga_swt	= JRequest::getInt('liga_swt');
	$fileName	= JRequest::getVar('fileName');

	// aktuelle Saison ermitteln
	$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
	$db->setQuery($sql);
	$sid = $db->loadResult();

	/// SWT Datei in Array einlesen
	$swt[]='';
	if($fh = fopen($fileName,'rb')){ while (!feof($fh)){ $swt[] = ord(fgets($fh,2)); } fclose($fh);	}

	// Array zum Erkennen von SG und ihren ZPS schreiben !
	$sg_zps = array();
	$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19;
	for ($x=0; $x < $swt[8]; $x++) {
		$zps = CLMControllerSWT::give_number( $swt,1+153+$offset,7);
		if ($zps !="" AND $sg_zps[1][$swt[$offset+202]] =="")
			{ $sg_zps[1][$swt[$offset+202]] = $zps; }

		if ($zps !="" AND $zps != $sg_zps[1][$swt[$offset+202]] AND $sg_zps[2][$swt[$offset+202]] =="" )
			{ $sg_zps[2][$swt[$offset+202]] = $zps; }
		$offset += 655;
		}

	// Datenarray aller Daten schreiben
	$info=0;$cnt=1;
	for($y=0; $y < $swt[603]; $y++) {
		$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19 + $y*655;
	for($x=0; $x < $swt[8]; $x++) {
		$zps = CLMControllerSWT::give_number( $swt,1+153+$offset,7);
		$info="";
	if ($zps=="") { $zps = $zps_db[$y]->zps; $info =1;}
	//echo "_!_".$zps_db[($swt[$offset+218])-1]->zps;
	if ($y+1 == $swt[$offset+202]) {
		$spl_name	= CLMControllerSWT::give_name( $swt,1+$offset,($offset+65));
		$spl_ges	= CLMControllerSWT::give_name( $swt,1+184+$offset,(184+$offset+1));
		$mgl		= CLMControllerSWT::give_number( $swt,1+159+$offset,4);
		$tln_man	= $swt[$offset+218];
		$man_nr		= $swt[$offset+202];
		
	// Array : Counter => Tln_nr, Man_nr, Name, Geschl., ZPS, Mgl_Nr, Info
	if ($y+1 == $swt[$offset+202]) {
		$data[$cnt] = array ( $tln_man, $man_nr, $spl_name, $spl_ges, $zps, $mgl, $info);
		$cnt++;
					}
					} $offset +=655; }}
//JError::raiseNotice( 6000,  JText::_( $liga_swt.'---5---'.print_r($data) ));
	//////////////
	// Wenn von Schritt 2 kommend dann speichern !
	//////////////
	if($step =="import_2"){
		$sql =	" DELETE FROM #__clm_swt_spl_tmp WHERE liga_swt = $liga_swt ";
			$db->setQuery($sql);
			$db->query();
		$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19;
		for($x=1; $x < 1+$swt[8]; $x++) {
			$mgl		= CLMControllerSWT::give_number( $swt,1+159+$offset,4);
			$tln_man	= $swt[$offset+218];
			$man_nr		= $swt[$offset+202];
			$offset += 655;
			$name_man	= JRequest::getVar( 'manuell_name'.$x);
			$zps_spl	= JRequest::getVar( 'zps'.$x);
			
		$sql =	" INSERT INTO #__clm_swt_spl_tmp ( liga_swt, mnr, mgl_nr, clm_zps, Nr, Name) " 
			."VALUES ( '$liga_swt','$man_nr', '$mgl', '$zps_spl', '$tln_man', '$name_man')"
			;
			$db->setQuery($sql);
			$db->query();
			}}
	//////////////
	//////////////

	// Zuordnung von fehlenden Daten mittels exakter ZPS/Mgl oder Spielernamen
	// Werte in swt_spl_tmp �bertragen und einzigartig machen
	$sql =	" DELETE FROM #__clm_swt_spl WHERE liga_swt = $liga_swt ";// AND Status <> 'ZZ'";
	$db->setQuery($sql);
	$db->query();

	// Alle Spieler einf�gen f�r die es Mgl/ZPS Daten aus der DSB DWZ DB gibt
	$sql = " INSERT INTO #__clm_swt_spl ( liga_swt, mnr, mgl_nr, clm_zps, Nr, Name, ZPS )  " 
		." SELECT a.liga_swt, a.mnr, d.Mgl_Nr, d.ZPS, a.Nr, d.Spielername, d.ZPS "
		." FROM #__clm_swt_spl_tmp as a "
		." LEFT JOIN #__clm_swt_spl_nach as s ON s.Nr = a.Nr AND s.liga_swt = a.liga_swt "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.clm_zps AND d.Mgl_Nr = a.mgl_nr) "
		." WHERE a.liga_swt = $liga_swt AND d.ZPS IS NOT NULL AND s.Nr IS NULL AND d.sid = $sid"//AND a.Status <> 'ZZ' ";
		;
	$db->setQuery($sql);
	$db->query();

	// Spieler mit g�ltiger ZPS / MglNr l�schen !!
	$sql = " DELETE FROM #__clm_swt_spl_tmp "
		." WHERE liga_swt = $liga_swt AND Nr IN "
		." ( SELECT Nr FROM #__clm_swt_spl as a "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.clm_zps AND d.Mgl_Nr = a.mgl_nr) "
		//." OR (a.Name =d.Spielername AND d.ZPS = a.clm_zps) "
		." WHERE a.liga_swt = $liga_swt AND d.ZPS IS NOT NULL AND d.sid = $sid) "
		;
	$db->setQuery($sql);
	$db->query();

	// Alle Spieler einf�gen f�r die es Namens�bereinstimmungen aus der DSB DWZ DB gibt
	$sql = " INSERT INTO #__clm_swt_spl ( liga_swt, mnr, mgl_nr, clm_zps, Nr, Name, ZPS ) " 
		." SELECT a.liga_swt, a.mnr, d.Mgl_Nr, d.ZPS, a.Nr, d.Spielername, d.ZPS "
		." FROM #__clm_swt_spl_tmp as a "
		." LEFT JOIN #__clm_swt_spl_nach as s ON s.Nr = a.Nr AND s.liga_swt = a.liga_swt "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.clm_zps AND d.Mgl_Nr = a.mgl_nr) OR (a.Name =d.Spielername AND d.ZPS = a.clm_zps) "
		." WHERE a.liga_swt = $liga_swt AND d.ZPS IS NOT NULL AND s.Nr IS NULL AND d.sid = $sid"//AND a.Status <> 'ZZ' ";
		;
	$db->setQuery($sql);
	$db->query();

	// Spieler l�schen f�r die es Namens�bereinstimmungen aus der DSB DWZ DB gibt !!
	$sql = " DELETE FROM #__clm_swt_spl_tmp "
		." WHERE liga_swt = $liga_swt AND Nr IN "
		." ( SELECT Nr FROM #__clm_swt_spl as a "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.clm_zps AND d.Mgl_Nr = a.mgl_nr) OR (a.Name =d.Spielername AND d.ZPS = a.clm_zps) "
		." WHERE a.liga_swt = $liga_swt AND d.ZPS IS NOT NULL AND d.sid = $sid) "
		;
	$db->setQuery($sql);
	$db->query();

	// wirkt erst ab dem 2.DG !!!
	// Nachgemeldete Spieler einf�gen f�r die es Daten aus der DSB DWZ DB gibt
	$sql = " INSERT INTO #__clm_swt_spl ( liga_swt, mnr, mgl_nr, clm_zps, Nr, Name, ZPS ) " 
		." SELECT a.liga_swt, a.mnr, d.Mgl_Nr, d.ZPS, a.Nr, d.Spielername, d.ZPS "
		." FROM #__clm_swt_spl_tmp as a "
		." LEFT JOIN #__clm_swt_spl_nach as s ON s.Nr = a.Nr AND s.liga_swt = a.liga_swt "
		." LEFT JOIN #__clm_dwz_spieler as d ON  d.ZPS = s.clm_zps AND d.Mgl_Nr = s.mgl_nr "
		." WHERE a.liga_swt = $liga_swt AND d.ZPS IS NOT NULL AND s.Nr IS NOT NULL AND d.sid = $sid"//AND a.Status <> 'ZZ' ";
		;
	$db->setQuery($sql);
	$db->query();

// F�r Nachmeldungen !!! UNBEDINGT !!! schon verwendete ZPS / Mgl Kombis entfernen => sonst DUPLICATE ENTRYS !!!!

	// wirkt erst ab dem 2.DG !!!
	$sql = " DELETE FROM #__clm_swt_spl_tmp "
		." WHERE liga_swt = $liga_swt AND Nr IN "
		." ( SELECT a.Nr FROM #__clm_swt_spl as a "
		." LEFT JOIN #__clm_swt_spl_nach as s ON s.Nr = a.Nr AND s.liga_swt = a.liga_swt "
		." LEFT JOIN #__clm_dwz_spieler as d ON  d.ZPS = s.clm_zps AND d.Mgl_Nr = s.mgl_nr "
		." WHERE a.ZPS IS NOT NULL AND d.sid = $sid)"
		;
	$db->setQuery($sql);
	$db->query();
	
	//////////////
	// Spieler die keine ZPS / MglNr oder keinen Namen haben zuordnen und manuelle Auswahl anbieten
	//////////////

	// Spieler ohne g�ltige ZPS / MglNr holen
	$sql = " SELECT * FROM #__clm_swt_spl_tmp "
		." WHERE liga_swt = $liga_swt ORDER BY Nr ASC ";
	$db->setQuery($sql);
	$zps_db = $db->loadObjectList();

	if(count($zps_db) > 0) {
	// Mannschaftsnamen f�r manuelle Auswahl holen wenn vorhanden
	$l_name = CLMControllerSWT::give_name( $swt,377,441);
	$sql = " SELECT m.zps FROM #__clm_swt_liga AS a "
		." LEFT JOIN #__clm_swt_man AS m ON m.swt_id = a.swt_id "
		." WHERE Liga ='$l_name'"
		." ORDER BY tln_nr ASC ";
	$db->setQuery($sql);
	$man_zps_manuell = $db->loadObjectList();

	// Array f�r Nachmelde View schreiben
	$z=0;
	foreach ($zps_db as $search) {
		$cnt = 0;
		// Eine generelle Nachmeldung anbieten 
		$man_zps[$search->Nr][$cnt] = array( $man_zps_manuell[($search->mnr)-1]->zps, 'Nachmeldung', $search->Name);
		$cnt++;
		// Zuordnung per Name probieren
		$teil = explode(",", $zps_db[$z]->Name);
		$name = $teil[0];
		$sql = " SELECT Spielername, ZPS, Mgl_Nr, Status FROM #__clm_dwz_spieler "
				." WHERE Spielername LIKE '%$name%' "
				." AND sid = $sid ";
			$db->setQuery($sql);
			$zps_clm_name = $db->loadObjectList();
		$hit=0;
		foreach($zps_clm_name as $zps_tmp) {
			if($zps_tmp->ZPS == $sg_zps[1][$search->mnr] OR $zps_tmp->ZPS == $sg_zps[2][$search->mnr]) {
				$man_zps[$search->Nr][$cnt] = array($zps_tmp->ZPS, $zps_tmp->Mgl_Nr, $zps_tmp->Spielername, $zps_tmp->Status);
					if($zps_tmp->Mgl_Nr == $search->mgl_nr AND $zps_tmp->ZPS == $search->clm_zps ){ $hit++;}
				$cnt++;
			}}
			// Zordnung per Mgl / ZPS probieren falls mit Name kein Volltreffer
			if($hit == 0 AND $search->mgl_nr >0 AND $search->clm_zps >0){
				$sql = " SELECT Spielername, ZPS, Mgl_Nr FROM #__clm_dwz_spieler "
					." WHERE ZPS = ".$zps_db[$z]->clm_zps." AND Mgl_Nr = ".$zps_db[$z]->mgl_nr
					." AND sid = $sid ";
				$db->setQuery($sql);
				$zps_clm_zps = $db->loadObjectList();
			if($zps_clm_zps[0]->ZPS !="" AND $zps_clm_zps[0]->Mgl_Nr !="" AND ($zps_clm_zps[0]->ZPS == $sg_zps[1][$search->mnr] OR $zps_clm_zps[0]->ZPS == $sg_zps[2][$search->mnr])){
				$man_zps[$search->Nr][$cnt] = array($zps_clm_zps[0]->ZPS, $zps_clm_zps[0]->Mgl_Nr, $zps_clm_zps[0]->Spielername);
			}
		} $z++;	}
	JError::raiseWarning( 500,  JText::_( 'SWT_GIBT').count($zps_db).JText::_('SWT_NO_ZUORDNEN'));
	JError::raiseNotice( 6000,  JText::_( 'SWT_DATEN'));
	} else {
		JError::raiseNotice( 6000,  JText::_( 'SWT_SPIELER_ZUORD'));
	}
	//////////////
	//////////////
	require_once(JPATH_COMPONENT.DS.'views'.DS.'swt.php');
	CLMViewSWT::Import_3 ($lists, $swt, $zps_db, $sid, $data, $liga_swt, $man_zps, $fileName, $sg_zps);
	}
	
function import_4()
	// Nachmeldungen durchf�hren
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$task 		= JRequest::getVar('task');
	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$step 		= JRequest::getVar('step');
	$count 		= JRequest::getInt('anzahl_meldungen');
	$liga_swt	= JRequest::getInt('liga_swt');

	if($count > 0) {
		
	// aktuelle Saison ermitteln
	$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
	$db->setQuery($sql);
	$sid = $db->loadResult();
	
	for($x=0; $x<$count; $x++){
	$data	= JRequest::getVar('data_'.$x);
	$teil	= explode("-", $data);
	$zps	= $teil[0];
	$mgl	= $teil[1];
	$name	= $teil[2];
	$nr	= $teil[3];

	if($mgl =="Nachmeldung") {
		// h�chste Mgl Nr suchen
		$sql = " SELECT a.Mgl_Nr FROM #__clm_dwz_spieler AS a "
			." LEFT JOIN #__clm_saison AS s ON s.id = a.sid "
			." WHERE  ZPS=$zps AND s.published =1 AND s.archiv = 0"
			." ORDER BY a.Mgl_Nr DESC LIMIT 1 ";
		$db->setQuery($sql);
		$commit_data = $db->loadObjectList();
			if($commit_data[0]->Mgl_Nr > 4999){ $nach_zps = 1+$commit_data[0]->Mgl_Nr; } else { $nach_zps = 5000; }
		
		// In SWT Nachmeldetabelle eintragen um Nachmeldung per "Nr" nachvollziehen zu k�nnen
		$sql = "  INSERT INTO #__clm_swt_spl_nach ( liga_swt, mgl_nr, clm_zps, Nr) "
			." VALUES ('$liga_swt','$nach_zps', '$zps', '$nr') "
			." ON DUPLICATE KEY UPDATE liga_swt=VALUES(liga_swt), mgl_nr=VALUES(mgl_nr), clm_zps=VALUES(clm_zps), Nr=VALUES(Nr) "
			;
		$db->setQuery($sql);
		$db->query();
		
		// In die CLM DWZ DB schreiben
// Geschlecht und Geburtsjahr noch ermitteln !
		$sql = "  INSERT INTO #__clm_dwz_spieler ( sid, ZPS, Mgl_Nr, Status, Spielername, Geschlecht, Geburtsjahr ) "
			." VALUES ('$sid', '$zps', '$nach_zps', 'N', '$name','M','1980') "
			;
		$db->setQuery($sql);
		$db->query();
	} else {
		// Mit existierender Kombination aus Mgl / ZPS nachgemeldet ! Nachvollziehbar per "Nr" Eintrag !!!
		$sql = "  INSERT INTO #__clm_swt_spl_nach ( liga_swt, mgl_nr, clm_zps, Nr) "
			." VALUES ('$liga_swt','$mgl', '$zps', '$nr') "
			." ON DUPLICATE KEY UPDATE liga_swt=VALUES(liga_swt), mgl_nr=VALUES(mgl_nr), clm_zps=VALUES(clm_zps), Nr=VALUES(Nr) "
			;
		$db->setQuery($sql);
		$db->query();
/*		
		// In die CLM DWZ DB schreiben
// Geschlecht und Geburtsjahr noch ermitteln !
		$sql = "  INSERT INTO #__clm_dwz_spieler ( sid, ZPS, Mgl_Nr, Status, Spielername, Geschlecht, Geburtsjahr ) "
			." VALUES ('$sid', '$zps', '$mgl', 'N', '$name','M','1980') "
			;
		$db->setQuery($sql);
		$db->query();
*/
	}}
	
	// Nachgemeldete Spieler einf�gen f�r die es Daten aus der DSB DWZ DB gibt
	// wirkt erst beim 2.DG !!!
	// Nachgemeldete Spieler einf�gen f�r die es Daten aus der DSB DWZ DB gibt
	$sql = " INSERT INTO #__clm_swt_spl ( liga_swt, mnr, mgl_nr, clm_zps, Nr, Name, ZPS ) " 
		." SELECT a.liga_swt, a.mnr, d.Mgl_Nr, d.ZPS, a.Nr, d.Spielername, d.ZPS "
		." FROM #__clm_swt_spl_tmp as a "
		." LEFT JOIN #__clm_swt_spl_nach as s ON s.Nr = a.Nr AND s.liga_swt = a.liga_swt "
		." LEFT JOIN #__clm_dwz_spieler as d ON  d.ZPS = s.clm_zps AND d.Mgl_Nr = s.mgl_nr "
		." WHERE a.liga_swt = $liga_swt AND d.ZPS IS NOT NULL AND s.Nr IS NOT NULL AND d.sid = $sid"//AND a.Status <> 'ZZ' ";
		;
	$db->setQuery($sql);
	$db->query();

	// Spieler mit g�ltiger ZPS / MglNr l�schen => Nachmeldung in clm_spl_nach !!
	// wirkt erst beim 2.DG !!!
	$sql = " DELETE FROM #__clm_swt_spl_tmp "
		." WHERE liga_swt = $liga_swt AND Nr IN "
		." ( SELECT a.Nr FROM #__clm_swt_spl as a "
		." LEFT JOIN #__clm_swt_spl_nach as s ON s.Nr = a.Nr AND s.liga_swt = a.liga_swt "
		." LEFT JOIN #__clm_dwz_spieler as d ON  d.ZPS = s.clm_zps AND d.Mgl_Nr = s.mgl_nr "
		." WHERE a.ZPS IS NOT NULL AND d.sid = $sid)"
		;
	$db->setQuery($sql);
	$db->query();
	}

	CLMControllerSWT::Import_5 ();
	}

function import_5()
	// Spielerrunden holen und speichern
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=& JFactory::getDBO();
	$step 		= JRequest::getVar('step');
	$fileName	= JRequest::getVar('fileName');
	$liga_swt	= JRequest::getInt('liga_swt');

	$sql = " DELETE FROM #__clm_swt_rnd_spl WHERE swt_id = ".$liga_swt;
	$db->setQuery($sql);
	$db->query();

	$sql = " SELECT * FROM #__clm_swt_liga WHERE swt_id = ".$liga_swt;
	$db->setQuery($sql);
	$liga_daten = $db->loadObjectList();
		$runden 	= $liga_daten[0]->Runden;
		$gesp_rnd 	= $liga_daten[0]->gesp_Runden;
		$durchgaenge 	= $liga_daten[0]->Durchgaenge;
		$spieler 	= $liga_daten[0]->Spieler;

	$sql = " SELECT mnr FROM #__clm_swt_spl "
		." WHERE liga_swt = ".$liga_swt
		." ORDER BY Nr ASC";
	$db->setQuery($sql);
	$mann_daten = $db->loadObjectList();

	$fx = fopen("$fileName","rb"); 
		for($h = 1; $h <= $spieler; $h++)    {
			for($f = 1; $f <= $durchgaenge; $f++) {
			for($g = 1; $g <= $runden; $g++) {
			$offset = 13383 + $zz*19;
			$zz = $zz+1;
			fseek ($fx, $offset+9); $farbe = ord(fgets($fx,2));
			fseek ($fx, $offset+10); $gegner = ord(fgets($fx,2));
				  if ($farbe == 1) {$weiss = 1;}
				  if ($farbe == 3) {$weiss = 0;}
			fseek ($fx, $offset+12); $ergebnis = ord(fgets($fx,2));
				  if ($ergebnis == 5) { $gewonnen = 0;}
				  if ($ergebnis == 10) { $gewonnen = 0.5;}
				  if ($ergebnis == 15) { $gewonnen = 1;}
			fseek ($fx, $offset+14); $paarung = ord(fgets($fx,2));
			fseek ($fx, $offset+16); $kampflos = ord(fgets($fx,2));
			fseek ($fx, $offset+19); $brett = ord(fgets($fx,2));
		$mannschaft = $mann_daten[$h-1]->MaNr;
		if ($gegner > 0 AND $g <= $gesp_rnd) {
	
		// Ergebnis auswerten
		$ergebnis = 6;
		if ($gewonnen == 0 AND $kampflos == 0) { $clm_ergebnis = 0; }
		if ($gewonnen == 1 AND $kampflos == 0) { $clm_ergebnis = 1; }
		if ($gewonnen == 0.5 AND $kampflos == 0) { $clm_ergebnis = 2; }
		if ($gewonnen == 0 AND $kampflos == 2) { $clm_ergebnis = 4; }
		if ($gewonnen == 1 AND $kampflos == 2) { $clm_ergebnis = 5; }

		$query	= "INSERT INTO #__clm_swt_rnd_spl "
			." ( `Nr`, `swt_id`,`Runde`, `DG`, `Brett`, `Weiss`, `Gegner`, `Ergebnis`,"
			." `kampflos`, `clm_ergebnis`, `Paarung`, `Mannschaft`,`Summe`) "
			." VALUES ( '$h', '$liga_swt', '$g', '$f', '$brett', '$weiss', '$gegner', '$gewonnen', "
			." '$kampflos', '$clm_ergebnis', '$paarung', '$mannschaft', '$summe')";
		$db->setQuery($query);
		$db->query();
	}}}}
	fclose($fx);
	
	CLMControllerSWT::Import_6 ();
	}
	
function import_6()
	// Mannschaftsrunden holen und speichern
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=& JFactory::getDBO();
	$fileName	= JRequest::getVar('fileName');
	$liga_swt	= JRequest::getInt('liga_swt');
	
	$sql = " DELETE FROM #__clm_swt_rnd_man WHERE liga_swt = ".$liga_swt;
	$db->setQuery($sql);
	$db->query();

	$sql = " SELECT * FROM #__clm_swt_liga WHERE swt_id = ".$liga_swt;
	$db->setQuery($sql);
	$liga_daten = $db->loadObjectList();
		$anz_mann	= $liga_daten[0]->Mannschaften;
		$runden		= $liga_daten[0]->Runden;
		$durchgaenge	= $liga_daten[0]->Durchgaenge;
		$spieler	= $liga_daten[0]->Spieler;

	$rundendaten = $runden * $durchgaenge * $spieler * 19 ;

	$fx = fopen("$fileName","rb"); 

	for($h = 1; $h <= $anz_mann; $h++)    {
		for($f = 1; $f <= $durchgaenge; $f++) {
		for($g = 1; $g <= $runden; $g++) {
			$offset = 13383 + $rundendaten + $zz*19;
			$zz = $zz+1;
			fseek ($fx, $offset+9); $farbe = ord(fgets($fx,2));
			fseek ($fx, $offset+10); $gegner = ord(fgets($fx,2));
			fseek ($fx, $offset+14); $paarung = ord(fgets($fx,2));
			$geg = $gegner -230; // Nummer hat Offset von hex(EE) = 230 (DEC) also subtrahieren
				if ($farbe == 1) {$heim = 1;}
				if ($farbe == 3) {$heim = 0;}

	$query	= "INSERT INTO #__clm_swt_rnd_man "
		." ( `liga_swt`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner`) "
		." VALUES ('$liga_swt','$g','$paarung','$f','$heim','$h','$geg') "
		;
	$db->setQuery($query);
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
		}}}
	fclose($fx);

	CLMControllerSWT::Import_7 ();
	}

function import_7()
	// Ligadaten in CLM DB importieren	
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=& JFactory::getDBO();
	$liga_swt	= JRequest::getInt('liga_swt');
	
	$sql = " SELECT a.id, a.sid FROM #__clm_liga AS a"
		." LEFT JOIN #__clm_swt_liga AS s ON s.clm_id = a.id" 
		." WHERE s.swt_id = $liga_swt ";
	$db->setQuery($sql);
	$update = $db->loadObjectList();

	// Ligadaten holen
	$sql = "SELECT * FROM #__clm_swt_liga WHERE swt_id = $liga_swt";
	$db->setQuery($sql);
	$data = $db->loadObjectList();

	// aktuelle Saison ermitteln
	$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
	$db->setQuery($sql);
	$sid = $db->loadResult();

	//////////
	// Noch kein Import durchgef�hrt !
	if(!$update) {
	// Ligadaten importieren
	$sql = " INSERT INTO #__clm_liga ( name, sid, teil, stamm, ersatz, rang, runden, durchgang, published, rnd, bem_int  ) " 
		." VALUES ('".$data[0]->Liga."','$sid','".$data[0]->Mannschaften."','".$data[0]->Bretter
		."','30','0','".$data[0]->Runden."','".$data[0]->Durchgaenge."','0','1','Import durch SWT Datei !')  "
		;
	$db->setQuery($sql);
	$db->query();

	// Importdaten mit Liga verbinden !
	// h�chste Liga ID = aktuelle ID
	$sql = "SELECT id FROM #__clm_liga WHERE sid = $sid ORDER BY id DESC LIMIT 1";
	$db->setQuery($sql);
	$update_id = $db->loadResult();

	$sql = " UPDATE #__clm_swt_liga "
		." SET clm_id = $update_id "
		." WHERE swt_id = $liga_swt "
		;
	$db->setQuery($sql);
	$db->query();

	$liga_id = $update_id;
	$update = 0;
	}
	//////////
	// Import wurde schon einmal durchgef�hrt
	else {
	$sql = " UPDATE #__clm_liga "
		." SET name = '".$data[0]->Liga."', sid = '".$sid."', teil = '".$data[0]->Mannschaften
		."',stamm = '".$data[0]->Bretter."', runden = '".$data[0]->Runden
		."', durchgang = '".$data[0]->Durchgaenge."'"
		." WHERE id = ".$data[0]->clm_id
		;
	$db->setQuery($sql);
	$db->query();

	$liga_id = $data[0]->clm_id;
	$update = 1;
	}
	CLMControllerSWT::Import_8 ($liga_id, $update, $sid);
	}

function import_8($liga_id, $update, $sid)
	// Mannschaften importieren	
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=& JFactory::getDBO();
	$liga_swt	= JRequest::getInt('liga_swt');
	$fileName	= JRequest::getVar('fileName');

	/// SWT Datei in Array einlesen
	$swt[]='';
		if($fh = fopen("$fileName",'rb')){ while (!feof($fh)){ $swt[] = ord(fgets($fh,2)); }
			fclose($fh); }

	// ZPS f�r Mannschaften aus Spielerdaten
	$sg_zps = array();
	$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19;

	for ($x=0; $x < $swt[8]; $x++) {
		$zps = CLMControllerSWT::give_number( $swt,1+153+$offset,7);
		// Array zum Erkennen von SG und ihren ZPS schreiben !
		if ($zps !="" AND $sg_zps[1][$swt[$offset+202]] =="")
			{ $sg_zps[1][$swt[$offset+202]] = $zps; }

		if ($zps !="" AND $zps != $sg_zps[1][$swt[$offset+202]] AND $sg_zps[2][$swt[$offset+202]] =="" )
			{ $sg_zps[2][$swt[$offset+202]] = $zps; }
		$offset += 655;
		}

	// Mannschaftsdaten holen
	$sql = "SELECT * FROM #__clm_swt_man WHERE swt_id = $liga_swt ORDER BY tln_nr ASC ";
	$db->setQuery($sql);
	$data = $db->loadObjectList();

	// Import wurde schon einmal durchgef�hrt
	if($update == 1){
		$sql = " DELETE FROM #__clm_mannschaften WHERE sid = $sid AND liga = $liga_id ";
		$db->setQuery($sql);
		$db->query();
		}
	// Daten schreiben
	for($x=1; $x < 1+count($data); $x++){
		$mnr = $liga_id.$x;
		// Keine Spielgemeinschaft
		if($sg_zps[2][$x] == "") {
		$sql = " INSERT INTO #__clm_mannschaften ( sid, name, liga, zps, liste, man_nr, tln_nr, bem_int, published) " 
			." VALUES ('$sid','".$data[$x-1]->name."','$liga_id','".$sg_zps[1][$x]."','1','$mnr','$x',"
			."'Import durch SWT Datei !','0') "
			;
			}
		// Spielgemeinschaft
		else {
		$sql = " INSERT INTO #__clm_mannschaften ( sid, name, liga, zps, liste, man_nr, tln_nr, sg_zps, bem_int, published) " 
			." VALUES ('$sid','".$data[$x-1]->name."','$liga_id','".$sg_zps[1][$x]."','1','$mnr','$x','".$sg_zps[2][$x]."',"
			."'Import durch SWT Datei !','0') "
			;
		}
		$db->setQuery($sql);
		$db->query();
		}
	CLMControllerSWT::Import_9 ($liga_id, $update, $sid);
	}


function import_9($liga_id, $update, $sid)
	// Meldelisten importieren	
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=& JFactory::getDBO();
	$liga_swt	= JRequest::getInt('liga_swt');

	// Ligadaten holen
	$sql = "SELECT * FROM #__clm_liga WHERE id = $liga_id ";
	$db->setQuery($sql);
	$data_lid = $db->loadObjectList();

	// Import wurde schon einmal durchgef�hrt
	if($update == 1){
		$sql = " DELETE FROM #__clm_meldeliste_spieler WHERE sid = $sid AND lid = $liga_id ";
		$db->setQuery($sql);
		$db->query();
		}
	// Daten schreiben
	for($x=0; $x < ($data_lid[0]->teil); $x++){
		// Mannschaftsdaten holen
		$sql = "SELECT * FROM #__clm_swt_spl WHERE liga_swt = $liga_swt AND mnr = ".($x+1)." ORDER BY Nr ASC ";
		$db->setQuery($sql);
		$data = $db->loadObjectList();

		$mnr = $liga_id.($x+1);
	for($y=1; $y < 1+count($data); $y++){
		$sql = " INSERT INTO #__clm_meldeliste_spieler ( sid, lid, mnr, snr, mgl_nr, zps) "
			." VALUES ('$sid','$liga_id','$mnr','$y','".$data[$y-1]->mgl_nr."','".$data[$y-1]->clm_zps."')"
			;
		$db->setQuery($sql);
		$db->query();
	}}
	CLMControllerSWT::Import_10 ($liga_id, $update, $sid);
	}

function import_10($liga_id, $update, $sid)
	// Rundendaten und Termine (Mannschaft) importieren
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=& JFactory::getDBO();
	$liga_swt	= JRequest::getInt('liga_swt');
	$date		= & JFactory::getDate();
	$now		= $date->toMySQL();

	$sql = " SELECT * FROM #__clm_swt_liga WHERE swt_id = ".$liga_swt;
	$db->setQuery($sql);
	$liga_daten = $db->loadObjectList();
		$gesp_rnd = $liga_daten[0]->gesp_Runden;

	// Import User anlegen
	$sql = " SELECT id,jid FROM #__clm_user "
		." WHERE email = 'swt_import@clm.de' "
		." AND sid =".$sid
		;
	$db->setQuery($sql);
	$clm_exist = $db->loadObjectList();
	// User existiert nicht -> anlegen
	if(count($clm_exist) < 1) {
		$row = & JTable::getInstance( 'users', 'TableCLM' );
		$row->sid		= $sid;
		$row->jid		= '9998';
		$row->name		= 'SWT-Import';
		$row->username		= 'SWT-Import Saison '.$sid;
		$row->aktive		= "1";
		$row->email		= "swt_import@clm.de";
		$row->usertype		= "sl";
		$row->user_clm		= "70";
		$row->zps		= "1";
		$row->published		= "1";
		$row->bemerkungen	= "Dieser User ist nur für Importzwecke gedacht !";
		$row->bem_int		= "Dieser User ist nur für Importzwecke gedacht !";
		// CLM User erstellen
		//$row->store();
			if (!$row->store()) { return JError::raiseWarning( 500, $row->getError() );}
	}
	// Rundendaten (Mannschaft) holen
	$sql = "SELECT * FROM #__clm_swt_rnd_man WHERE liga_swt = $liga_swt ORDER BY dg ASC, runde ASC, paar ASC, heim DESC ";
	$db->setQuery($sql);
	$data = $db->loadObjectList();

	// Import wurde schon einmal durchgeführt
	if($update == 1){
		$sql = " DELETE FROM #__clm_rnd_man WHERE sid = $sid AND lid = $liga_id ";
		$db->setQuery($sql);
		$db->query();
		
		$sql = " DELETE FROM #__clm_runden_termine WHERE sid = $sid AND liga = $liga_id ";
		$db->setQuery($sql);
		$db->query();
		}
	//$nr = 1;
	foreach($data as $data) {
		if ($data->runde <= $gesp_rnd) { $melder ="9998"; } else { $melder ="";}
		$sql = " INSERT INTO #__clm_rnd_man ( sid, lid, runde, paar, dg, heim, tln_nr, gegner,gemeldet,zeit) "
			." VALUES ('$sid','$liga_id','$data->runde','$data->paar','$data->dg',"
			." '$data->heim','$data->tln_nr','$data->gegner','$melder','$now')"
			;
		$db->setQuery($sql);
		$db->query();

		if($data->heim == 1 AND $data->paar ==1) {
			$name_rnd = "Runde ".$data->runde;
		$sql = " INSERT INTO #__clm_runden_termine ( sid, name, liga, nr, bem_int, gemeldet, zeit)"
			." VALUES ('$sid','$name_rnd','$liga_id','$data->runde','SWT Import !','$melder','$now')"
			;
		$db->setQuery($sql);
		$db->query();
		//$nr++;
		}}
	CLMControllerSWT::Import_11 ($liga_id, $update, $sid);
	}

function import_11($liga_id, $update, $sid)
	// Rundendaten (Spieler) importieren	
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=& JFactory::getDBO();
	$liga_swt	= JRequest::getInt('liga_swt');

	// Import wurde schon einmal durchgef�hrt
	if($update == 1){
		$sql = " DELETE FROM #__clm_rnd_spl WHERE sid = $sid AND lid = $liga_id ";
		$db->setQuery($sql);
		$db->query();
		}

	$sql = " INSERT INTO #__clm_rnd_spl ( sid, lid, runde, paar, dg, tln_nr, brett, heim, weiss "
		.", spieler, zps, gegner, gzps, ergebnis, kampflos, punkte ) "
		." SELECT c.sid, l.clm_id as lid, a.Runde as runde, m.paar, a.DG as dg, m.tln_nr, a.Brett as brett "
		.", m.heim, a.Weiss as weiss, s.mgl_nr as spieler, s.clm_zps as zps, t.mgl_nr as gegner "
		.", t.clm_zps as gzps, a.clm_ergebnis as ergebnis, a.kampflos, a.Ergebnis as Punkte "
		." FROM #__clm_swt_rnd_spl AS a "
		." LEFT JOIN #__clm_swt_liga AS l ON l.swt_id= a.swt_id "
		." LEFT JOIN #__clm_liga AS c ON c.id= l.clm_id "
		." LEFT JOIN #__clm_swt_spl AS s ON s.liga_swt= a.swt_id AND s.Nr = a.Nr "
		." LEFT JOIN #__clm_swt_spl AS t ON t.liga_swt= a.swt_id AND t.Nr = a.Gegner "
		." LEFT JOIN #__clm_swt_rnd_man AS m ON m.liga_swt=a.swt_id AND m.dg=a.DG AND m.runde =a.Runde AND m.tln_nr=s.mnr "
		." WHERE a.swt_id= $liga_swt "
		." ORDER BY a.DG ASC, a.Runde ASC, m.paar ASC, a.Brett ASC, m.heim DESC " 
		;
	$db->setQuery($sql);
	$db->query();

	// MP und BP aktualisieren
	CLMControllerSWT::bp_mp ($liga_id);
	
	$msg="Import erfolgreich durchgef&uuml;hrt ! Liga hat die ID #$liga_id.";
	JError::raiseNotice( 6000,  JText::_( 'SWT_DATEN_HAND' ));
	JError::raiseNotice( 6000,  JText::_( 'SWT_DATEN_PRUEFEN' ));
	JError::raiseNotice( 6000,  JText::_( 'SWT_ERGEBNISMANAGER' ));
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}
	

function give_name($swt,$start,$end)
	{

	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	// �,�,�,�,�,�,�,�,�
	//$convert = array("233" => "130", "246" => "148");
	
	$name = '';
	for ($x=$start; $x < 1+$end;$x++) {
	if ($swt[$x] != "0") {
		//if($swt[$x] > 127) { $name .= utf8_encode(chr($convert[$swt[$x]])); }
		//else {	$name .= utf8_encode(chr($swt[$x])); }
		//$name .= chr($swt[$x]);
		$name .= utf8_encode(chr($swt[$x]));
		}
	else { break; }}
		return $name;
	}


function give_number($swt,$start,$length)
	{

	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$name = '';
	for ($x=$start; $x < 1+$start+$length;$x++) {
	if ($swt[$x] != "0") { 
			$name .= chr($swt[$x]);
		}
	else { break; }}
		return $name;
	}


function back()
	{
	global $mainframe;	
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	JError::raiseWarning( 500, JText::_( 'SWT_ABBRUCH' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}


function liga ()
	{
	global $mainframe;
	// Check for request forgeries
	//JRequest::checkToken() or die( 'Invalid Token' );
	$db 		=& JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$sql = " SELECT id FROM #__clm_saison "
		." WHERE published  = 1 AND archiv = 0"
		;
	$db->setQuery( $sql );
	$sid = $db->loadObjectList();
	
	$sql = " SELECT a.id,a.name FROM #__clm_liga as a "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE s.archiv = 0 "
		." and s.published = 1"
		." and a.published = 1"
		." ORDER BY s.id ASC, a.id ASC "
		;
	$db->setQuery( $sql );
	$liga = $db->loadObjectList();

	return $liga;
	}

//
//
//
//
//
//
	
	
function upload_jfile()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$task 		= JRequest::getVar( 'task');
	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();

	$file = JRequest::getVar( 'datei', '', 'files', 'array' );

	// erlaubte Dateitypen
	$allowed =array('application/octet-stream'); 
	if (!in_array($file['type'], $allowed)) {
	JError::raiseWarning( 500, JText::_( 'SWT_FALSCH' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
					}

	// Set FTP credentials, if given
	jimport('joomla.client.helper');
	JClientHelper::setCredentialsFromRequest('ftp');

	// Make the filename safe
	jimport('joomla.filesystem.file');
	$file['name']	= JFile::makeSafe($file['name']);

// array(5) { ["name"]=>  string(10) "readme.txt" ["type"]=>  string(10) "text/plain" ["tmp_name"]=>  string(14) "/tmp/phpvuCKQ6" ["error"]=>  int(0) ["size"]=>  int(2146) }

// array(5) { ["name"]=>  string(15) "Bezirksliga.SWT" ["type"]=>  string(24) "application/octet-stream" ["tmp_name"]=>  string(14) "/tmp/phpQDjlhJ" ["error"]=>  int(0) ["size"]=>  int(89086) } 

$destDir = JPath::clean(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.swt);
$dest = JPath::clean($destDir.DS.$file['name']);

	// ggf. Verzeichnis erstellen
           if (!file_exists($destDir)) {
                jimport('joomla.filesystem.folder');
                JFolder::create( $destDir);
			            }
	// Dateien hochladen
	if (!JFile::upload($file['tmp_name'], $dest)) {
		$msg = JText::_( 'SWT_UPLOAD_NO');
							} 
		else {
		$msg = JText::_( 'SWT_UPLOAD_YES').$file['size'].' Byte';
		}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "SWT upload";
	$clmLog->params = array('cids' => $file['size']);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg);
	}

function files ()
	{
	global $mainframe;
	$option		= JRequest::getCmd('option');
	
	jimport( 'joomla.filesystem.folder' );
	$filesDir = 'components'.DS.$option.DS.'swt';
	$files = JFolder::files( $filesDir, '.SWT$|.swt$', true, true );
		$count = count($files);
		$sql = array();
	for ($x=0; $x< $count; $x++ ) {
	$link = explode(DS, $files[$x]);
		$sql[] = utf8_encode($link[3]);
					}
	return $sql;
	}


function delete_data ()
	{
	global $mainframe;
	$db =& JFactory::getDBO();

	$sql = " SELECT swt_id, Liga FROM #__clm_swt_liga ";
	$db->setQuery( $sql );
	$liga = $db->loadObjectList();

	return $liga;
	}

	
function swt_dat_del ()
	{
	global $mainframe;
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$task 		= JRequest::getVar( 'task');
	$db 		=& JFactory::getDBO();
	$swt_liga	= JRequest::getInt( 'swt_delete');
	
	if($swt_liga ==0) {
	JError::raiseWarning( 500, JText::_( 'SWT_LIGA_WAEHLEN' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	
	$sql = "DELETE FROM #__clm_swt_liga "
		."WHERE swt_id = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_man "
		."WHERE swt_id = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_rnd_man "
		."WHERE liga_swt = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_rnd_spl "
		."WHERE swt_id = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_spl "
		."WHERE liga_swt = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_spl_nach "
		."WHERE liga_swt = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_spl_tmp "
		."WHERE liga_swt = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$msg="SWT Ligadaten wurden gel&ouml;scht ! Diese Aktion hat keinen Einflu&szlig; auf bereits importierte Ligen.";
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}

	
function swt_dat_nach ()
	{
	global $mainframe;
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$task 		= JRequest::getVar( 'task');
	$db 		=& JFactory::getDBO();
	$swt_liga	= JRequest::getInt( 'swt_delete');
	
	if($swt_liga ==0) {
	$msg="Bitte w&auml;hlen Sie eine Liga aus ! Keine Aktion durchgef&uuml;hrt !";
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}
	
	$sql = "DELETE FROM #__clm_swt_spl_nach "
		."WHERE liga_swt = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_spl_tmp "
		."WHERE liga_swt = ".$swt_liga
		;
	$db->setQuery( $sql );
	$db->query();

	$msg="Die Nachmeldungen dieser Liga wurden in der SWT Datenbank gel&ouml;scht ! Diese Aktion hat keinen Einflu&szlig; auf bereits importierte Ligen.";
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}

//
//
//
//
//
//

function bp_mp($liga_clm)
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$db 		=& JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	// aktuelle Saison ermitteln
	$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
	$db->setQuery($sql);
	$sid = $db->loadResult();

	$sql = " SELECT * FROM #__clm_swt_liga WHERE clm_id = ".$liga_clm;
	$db->setQuery($sql);
	$liga_daten = $db->loadObjectList();

		$durchgaenge 	= $liga_daten[0]->Durchgaenge;
		$gesp_rnd 	= $liga_daten[0]->Runden;
		$bretter 	= $liga_daten[0]->Bretter;
		$tln 		= $liga_daten[0]->Mannschaften;

	for($d = 1; $d < 1+$durchgaenge; $d++) {
	for($x = 1; $x < 1+$gesp_rnd; $x++)    {
		for($y = 0; $y < (($bretter * $tln )/2); $y++)    {

		// Brettpunkte Heim summieren
		$query	= "SELECT SUM(punkte) as punkte "
			." FROM #__clm_rnd_spl "
			." WHERE sid = ".$sid
			." AND lid = ".$liga_clm
			." AND runde = ".$x
			." AND paar = ".($y+1)
			." AND dg = ".$d
			." AND heim = 1 "
			;
		$db->setQuery($query);
		$man=$db->loadObjectList();
		$hmpunkte=$man[0]->punkte;

		// Brettpunkte Gast summieren
		$query	= "SELECT SUM(punkte) as punkte "
			." FROM #__clm_rnd_spl "
			." WHERE sid = ".$sid
			." AND lid = ".$liga_clm
			." AND runde = ".$x
			." AND paar = ".($y+1)
			." AND dg = ".$d
			." AND heim = 0 "
			;
		$db->setQuery($query);
		$gman=$db->loadObjectList();
		$gmpunkte=$gman[0]->punkte;

	// Mannschaftspunkte Heim / Gast
	if ( $hmpunkte > $gmpunkte ) { $hman_punkte = 2; $gman_punkte = 0;}
	if ( $hmpunkte == $gmpunkte ) { $hman_punkte = 1; $gman_punkte = 1;}
	if ( $hmpunkte < $gmpunkte ) { $hman_punkte = 0; $gman_punkte = 2;}

		// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET  brettpunkte = ".$hmpunkte
			." , manpunkte = ".$hman_punkte
			." WHERE sid = ".$sid
			." AND lid = ".$liga_clm
			." AND runde = ".$x
			." AND paar = ".($y+1)
			." AND dg = ".$d
			." AND heim = 1 "
			;
		$db->setQuery($query);
		$db->query();

		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET brettpunkte = ".$gmpunkte
			." , manpunkte = ".$gman_punkte
			." WHERE sid = ".$sid
			." AND lid = ".$liga_clm
			." AND runde = ".$x
			." AND paar = ".($y+1)
			." AND dg = ".$d
			." AND heim = 0 "
			;
		$db->setQuery($query);
		$db->query();
			}}}
	}
}