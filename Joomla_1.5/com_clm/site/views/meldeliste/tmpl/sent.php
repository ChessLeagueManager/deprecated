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

defined('_JEXEC') or die('Restricted access'); 

JRequest::checkToken() or die( 'Invalid Token' );
global $mainframe;
// Variablen holen
$sid 		= JRequest::getInt('sid','1');
$lid 		= JRequest::getInt('liga');
$zps 		= JRequest::getVar('zps');
$man 		= JRequest::getInt('man_nr');
$stamm		= JRequest::getInt('stamm');
$ersatz		= JRequest::getInt('ersatz');
$man_name 	= JRequest::getInt('man_name');

$user 		=& JFactory::getUser();
$meldung 	= $user->get('id');
$clmuser 	= $this->clmuser;
$access		= $this->access;

// Prüfen ob Datensatz schon vorhanden ist
	$db			= JFactory::getDBO();
	$query	= "SELECT id, liste "
		." FROM #__clm_mannschaften "
		." WHERE sid = $sid AND zps = '$zps' AND man_nr = $man AND published = 1 "
		;
	$db->setQuery( $query );
	$test=$db->loadObjectList();

if ($test[0]->id < 1) {
	$link = 'index.php?option='.$option.'&view=info';
	$msg = JText::_( 'CLUB_LIST_TEAM_DISABLED' );
	$mainframe->redirect( $link, $msg );
 			}
if ($test[0]->liste > 0) {
	$link = 'index.php?option='.$option.'&view=info';
	$msg = JText::_( 'CLUB_LIST_ALREADY_EXIST' );
	$mainframe->redirect( $link, $msg );
 			}

	$link 	= 'index.php';
	$db 	=& JFactory::getDBO();

// Datum und Uhrzeit für Meldung
	$date =& JFactory::getDate();
	$now = $date->toMySQL();

// Datensätze in Meldelistentabelle schreiben
	$query	= "UPDATE #__clm_mannschaften"
		." SET liste = ".$meldung
		." , datum = '$now'"
		." WHERE sid = ".$sid
		." AND man_nr = ".$man
		." AND zps = '$zps'"
		;
	$db->setQuery($query);
	$db->query();

for ($y=1; $y< (1+$stamm+$ersatz) ; $y++){ 
	$stm		= JRequest::getInt( 'name'.$y);
	$dwz		= JRequest::getInt( 'dwz'.$y);
	$mgl		= JRequest::getInt( 'hidden_mglnr'.$y);

	$query	= "INSERT INTO #__clm_meldeliste_spieler "
		." ( `sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`, `ordering`) "
		." VALUES ('$sid','$lid','$man','$y','$mgl','$zps','0') "
		;
	$db->setQuery($query);
	$db->query();
	}

// Log
	$date 		=& JFactory::getDate();
	$now 		= $date->toMySQL();
	$user 		=& JFactory::getUser();
	$jid_aktion 	=  ($user->get('id'));
	$aktion 	= "Meldeliste FE";

	$query	= "INSERT INTO #__clm_log "
		." ( `aktion`, `jid_aktion`, `sid` , `lid` ,`zps`,`man`, `datum`) "
		." VALUES ('$aktion','$jid_aktion','$sid','$lid','$zps','$man','$now') "
		;
	$db->setQuery($query);
	$db->query();

$msg = JText::_( 'CLUB_LIST_SEND_OK' );
$mainframe->redirect( $link, $msg );
?>