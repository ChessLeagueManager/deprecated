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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');


class CLMModelSpieler extends JModel
{
	function _getCLMSpieler( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$mgl	= JRequest::getInt('mglnr');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query = "SELECT a.Spielername,l.name as liga_name,l.id as liga,a.ZPS,a.Mgl_Nr,"
		." a.DWZ as dsbDWZ,a.DWZ_Index,a.FIDE_ELO,a.FIDE_ID,"
		." a.DWZ_neu, a.I0 as SI0, a.Punkte as SPunkte, a.WE as SWe, a.EFaktor as SEFaktor, a.Partien as SPartien, a.Niveau as SNiveau, a.Leistung as SLeistung,"   //klkl 
		." d.Vereinname, n.name,n.tln_nr, m.*, s.datum as dsb_datum, s.name as s_name"      
		." FROM #__clm_dwz_spieler as a "
		." LEFT JOIN #__clm_dwz_vereine as d ON a.ZPS = d.ZPS AND d.sid = a.sid"
		." LEFT JOIN #__clm_meldeliste_spieler as m ON m.zps = a.ZPS AND m.mgl_nr = a.Mgl_Nr AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften as n ON n.zps = a.ZPS AND n.man_nr = m.mnr AND n.sid = a.sid"
		." LEFT JOIN #__clm_liga l ON l.id = n.liga AND l.sid = n.sid "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.ZPS = '$zps'"
		." AND a.Mgl_Nr = ".$mgl
		." AND a.sid = ".$sid
		." AND s.published = 1"
		." ORDER BY l.id ASC "
		;
	return $query;

	}

	function getCLMSpieler( $options=array() )
	{
		$query	= $this->_getCLMSpieler( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function getCLMLink()
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$mgl	= JRequest::getInt('mglnr');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	// Array zum speichern von Liga,Tlnr und Mannschaftsname
	$erg = array();

	// Mannschaften mit Aufstellung nach Meldeliste suchen
	$query = " SELECT a.lid as lid, l.name as liga_name, m.tln_nr as tln_nr, m.name as name FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_mannschaften as m ON ( m.zps = a.zps OR m.sg_zps = a.zps) AND m.man_nr = a.mnr AND m.sid = a.sid "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." LEFT JOIN #__clm_liga AS l ON l.id = a.lid AND l.sid = a.sid "
		." WHERE a.zps = '$zps' AND a.mgl_nr = $mgl AND s.published = 1 AND m.published = 1 AND l.published = 1 AND s.id = $sid AND status = 0 "
		." ORDER BY a.mnr ASC "
		;
	$db->setQuery($query);
	$melde = $db->loadObjectList();
	$x = 0;

	// Mannschaften mit Aufstellung nach Rangliste suchen
	$query = " SELECT l.id as lid,l.name as liga_name,l.rang, m.tln_nr as tln_nr, m.name as name FROM #__clm_rangliste_spieler as a "
		." LEFT JOIN #__clm_liga as l ON l.rang = a.Gruppe AND l.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften AS m ON m.man_nr = a.man_nr AND ( a.ZPS = m.zps OR a.ZPS = m.sg_zps) AND m.liga = l.id "
		." LEFT JOIN #__clm_rangliste_id AS r ON r.gid = l.rang AND r.sid = a.sid AND r.zps = a.ZPS "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.ZPS ='$zps' AND a.Mgl_Nr='$mgl' AND m.id IS NOT NULL "
		." AND s.published = 1 AND s.archiv = 0 AND m.published = 1 AND l.published = 1 AND r.published = 1 "
		;
	$db->setQuery($query);
	$rang_count = $db->loadObjectList();

	$result = array_merge($rang_count, $melde);

	return $result;
	}

	function _getCLMRunden ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$mgl	= JRequest::getInt('mglnr');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query = " SELECT s.gegner as Mgl_Nr,s.gzps,s.lid,s.runde,s.heim,s.weiss,s.brett,"
		." s.kampflos,s.punkte,d.Spielername,m.name,d.DWZ,a.dg,a.gegner as tln"
		." FROM #__clm_rnd_spl as s "
		." LEFT JOIN #__clm_rnd_man as a ON s.sid = a.sid AND s.lid = a.lid AND s.runde = a.runde AND s.paar = a.paar AND s.dg = a.dg AND s.tln_nr = a.tln_nr "
		." LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = s.gzps AND d.Mgl_Nr = s.gegner AND d.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.lid AND m.tln_nr = a.gegner "//AND m.zps = s.gzps
		." WHERE s.zps = '$zps'"
		." AND s.spieler =".$mgl
		." AND a.sid =".$sid
		." ORDER BY a.dg ASC, a.runde ASC "
		;
		return $query;
	}

	function getCLMRunden ( $options=array() )
	{
		$query	= $this->_getCLMRunden( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMlog( &$options )   
	{
	$sid	= JRequest::getInt('saison','1');
	$lid	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
	// DWZ-Update finden 
	$query = " SELECT a.datum, a.lid, a.nr_aktion "
		." FROM #__clm_log as a "
		." WHERE a.sid = ".$sid
		." AND (a.nr_aktion = 101 OR a.nr_aktion = 102)" 	// 101 DWZ ausgewertet; 102 DWZ gel�scht
		." ORDER BY a.lid ASC, a.datum DESC ";
		return $query;
	}

	function getCLMlog( $options=array() )
	{
		$query	= $this->_getCLMlog( $options );
		$result = $this->_getList( $query );
		return @$result;
	}	
	
	
	function _getCLMVereinsliste( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query  = 'SELECT DISTINCT a.zps, a.name FROM #__clm_vereine as a'
		." WHERE a.published = 1"
		." ORDER BY a.name ASC "
		;
		  
	return $query;
	}

	function getCLMVereinsliste( $options=array() )
	{
	$query	= $this->_getCLMVereinsliste( $options );
	$result = $this->_getList( $query );
	return @$result;
	}
	function _getCLMSaisons( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query  = ' SELECT a.name, a.id, a.archiv FROM #__clm_saison AS a'
		." ORDER BY a.name DESC "
		;
		  
	return $query;
	}

	function getCLMSaisons( $options=array() )
	{
	$query	= $this->_getCLMSaisons( $options );
	$result = $this->_getList( $query );
	return @$result;
	}
	
	
	function _getCLMSpielerliste( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query  = " SELECT DISTINCT a.Spielername, a.ZPS, a.Mgl_Nr, a.sid FROM #__clm_dwz_spieler AS a"
		." WHERE a.sid= '$sid'"
		." AND ZPS= '$zps'"
		." GROUP BY Spielername"
		." ORDER BY a.Spielername ASC "
		;
		  
	return $query;
	}

	function getCLMSpielerliste( $options=array() )
	{
	$query	= $this->_getCLMSpielerliste( $options );
	$result = $this->_getList( $query );
	return @$result;
	}
}
?>