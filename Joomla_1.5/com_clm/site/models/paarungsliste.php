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

class CLMModelPaarungsliste extends JModel
{

	function _getCLMLiga( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = " SELECT a.* FROM #__clm_liga as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.id = ".$liga
			." AND a.sid = ".$sid
			." AND s.published = 1"
			;
		return $query;
	}
	function getCLMLiga( $options=array() )
	{
		$query	= $this->_getCLMLiga( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMTermin( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = "SELECT nr, name, datum, bemerkungen, published FROM #__clm_runden_termine "
			." WHERE liga = ".$liga
			." AND sid = ".$sid
			." ORDER BY nr "
			;
		return $query;
	}
	function getCLMTermin( $options=array() )
	{
		$query	= $this->_getCLMTermin( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMMannschaft( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = " SELECT * FROM #__clm_mannschaften "
			." WHERE liga = ".$liga
			." AND sid = ".$sid
			." AND published = 1 "
			." ORDER BY tln_nr "
			;
		return $query;
	}
	function getCLMMannschaft( $options=array() )
	{
		$query	= $this->_getCLMMannschaft( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMPaar ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

		$query = " SELECT teil FROM #__clm_liga "
			." WHERE id = ".$liga
			." AND sid = ".$sid
			;
		$db->setQuery( $query);
		$row_tln=$db->loadObjectList();
		$tln	= $row_tln[0]->teil;

	$query = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln, g.published as gpublished, g.rankingpos as grank, "
		." g.man_nr as gmnr, h.id as hid, h.name as hname, h.tln_nr as htln, h.rankingpos as hrank, b.wertpunkte as gwertpunkte, "
		." h.published as hpublished, h.man_nr as hmnr "
			." FROM #__clm_rnd_man as a"
			." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner"
			." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr"
			." LEFT JOIN #__clm_rnd_man AS b ON b.sid = ".$sid." AND b.lid = ".$liga." AND b.runde = a.runde AND b.dg = a.dg AND b.paar = a.paar AND b.heim = 0 "
			." WHERE g.liga = ".$liga
			." AND g.sid = ".$sid
			." AND h.liga = ".$liga
			." AND h.sid = ".$sid
			." AND a.sid = ".$sid
			." AND a.lid = ".$liga
			." AND a.heim = 1 "
			." ORDER BY a.dg ASC,a.runde ASC, a.paar ASC"
			;
		return $query;
	}
	function getCLMPaar ( $options=array() )
	{
		$query	= $this->_getCLMPaar( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMDWZSchnitt ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

		$query = " SELECT stamm,ersatz FROM #__clm_liga "
			." WHERE id = ".$liga
			;
		$db->setQuery( $query);
		$row_tln=$db->loadObjectList();
		//$tln	= ($row_tln[0]->stamm)+($row_tln[0]->ersatz);
		$tln	= $row_tln[0]->stamm;

		$query = " SELECT e.tln_nr as tlnr,AVG(d.DWZ) as dwz"
			." FROM #__clm_meldeliste_spieler as a"
			." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)"
			." LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR e.sg_zps=a.zps) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." WHERE a.lid = ".$liga
			." AND a.sid = ".$sid
			." AND e.tln_nr IS NOT NULL "
			." AND a.snr < ".($tln+1)
			." GROUP BY e.tln_nr"
			;
		return $query;
	}
	function getCLMDWZSchnitt ( $options=array() )
	{
		$query	= $this->_getCLMDWZSchnitt( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMDWZgespielt ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

		$query = " SELECT stamm,ersatz FROM #__clm_liga "
			." WHERE id = ".$liga
			;
		$db->setQuery( $query);
		$row_tln=$db->loadObjectList();
		$tln	= ($row_tln[0]->stamm)+($row_tln[0]->ersatz);

	$query = " SELECT a.sid,a.lid,a.runde,a.paar,a.dg, AVG(d.DWZ) as dwz,AVG(g.DWZ) as gdwz "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_rnd_spl AS r ON (r.sid=a.sid AND r.lid= a.lid AND r.runde=a.runde AND r.paar = a.paar AND r.dg = a.dg) "
		." LEFT JOIN #__clm_dwz_spieler AS d ON (d.ZPS = r.zps AND d.Mgl_Nr = r.spieler AND d.sid = r.sid) "
		." LEFT JOIN #__clm_dwz_spieler AS g ON (g.ZPS = r.gzps AND g.Mgl_Nr = r.gegner AND g.sid = r.sid) "
		." WHERE a.lid = $liga  AND a.sid = $sid AND a.heim = 1 AND r.heim = 1 "
		." GROUP BY a.dg ASC, a.runde ASC, a.paar ASC"
		;
		return $query;
	}
	function getCLMDWZgespielt ( $options=array() )
	{
		$query	= $this->_getCLMDWZgespielt( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSumme ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$runde	= JRequest::getInt('runde');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query = " SELECT a.dg,a.paar as paarung,a.runde as runde,a.brettpunkte as sum "
		." FROM #__clm_rnd_man as a "
			." WHERE a.lid = ".$liga
			." AND a.sid = ".$sid
			." ORDER BY a.dg ASC ,a.runde ASC, a.paar ASC, a.heim DESC "
			;
		return $query;
	}
	function getCLMSumme ( $options=array() )
	{
		$query	= $this->_getCLMSumme( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMRundensumme ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query = " SELECT a.nr, a.sl_ok as sl_ok "
		." FROM #__clm_runden_termine as a"
			." WHERE a.liga = ".$liga
			." AND a.sid = ".$sid
			." ORDER BY a.nr ASC"
			;
		return $query;
	}
	function getCLMRundensumme ( $options=array() )
	{
		$query	= $this->_getCLMRundensumme( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

}
?>
