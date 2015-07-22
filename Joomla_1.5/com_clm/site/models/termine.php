<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Fjodor Sch�fer
 * @email ich@vonfio.de
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelTermine extends JModel
{
	
	function _getTermine( &$options )
	{
		$sid	= JRequest::getInt('saison','1');
		$liga	= JRequest::getInt('liga','1');
		$start	= JRequest::getVar('start','1');
		$db	= JFactory::getDBO();
		if ($start == '1') $date = date("Y-m-d");
		else $date = $start;
	 
		$query = " (SELECT 'liga' AS source, li.datum AS datum, li.sid, li.name, li.nr, li.liga AS typ_id, t.id, t.name AS typ, t.durchgang AS durchgang, t.published, li.id AS ligarunde"
				." FROM #__clm_runden_termine AS li LEFT JOIN #__clm_liga AS t ON t.id = li.liga "
				." WHERE t.published != '0' AND  TO_DAYS(datum)+183 >= TO_DAYS('".$date."') )"
				
				." UNION ALL"
				
				." (SELECT 'termin', e.startdate AS datum, '1', e.name, '1', '', e.id, e.address AS typ, '1', e.published, 'event' AS ligarunde "
				." FROM #__clm_termine AS e "
				." WHERE e.published != '0' AND  TO_DAYS(e.startdate)+183 >= TO_DAYS('".$date."') )"
				
				." UNION ALL"
				
				." (SELECT 'turnier', tu.datum AS datum, tu.sid, tu.name, tu.nr, tu.turnier AS typ_id, b.id, b.name AS typ, tu.dg AS durchgang, b.published, '' "
				." FROM #__clm_turniere_rnd_termine AS tu LEFT JOIN #__clm_turniere AS b ON b.id = tu.turnier "
				." WHERE b.published != '0' AND TO_DAYS(datum)+183 >= TO_DAYS('".$date."') )"
				
				." ORDER BY datum ASC, typ_id ASC"
				;
				
		return $query;
	}
	function getTermine( $options=array() )
	{
		$query	= $this->_getTermine( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	
	function _getTermine_Detail( &$options )
	{
	
		$nr		= JRequest::getVar('nr');
		$db		= JFactory::getDBO();
		$date 	= date("Y-m-d");
	 
		$query = " SELECT host FROM #__clm_termine AS a "
				." WHERE a.id =". $nr
				." AND a.published != 0"
				;
		$db->setQuery($query);
		$termin=$db->loadObjectList();

		if (strlen($termin[0]->host) == 5)
		$query = " SELECT a.*, b.Vereinname AS hostname "
				." FROM #__clm_termine AS a "
				." LEFT JOIN dwz_vereine AS b ON b.ZPS = a.host "
				." WHERE a.id =". $nr
				." AND a.published != 0"
				;
		elseif (strlen($termin[0]->host) == 3)
			$query = " SELECT a.*, b.Verbandname AS hostname "
				." FROM #__clm_termine AS a "
				." LEFT JOIN dwz_verbaende AS b ON b.Verband = a.host "
				." WHERE a.id =". $nr
				." AND a.published != 0"
				;
		else $query = " SELECT a.*, '-' AS hostname " 
				." FROM #__clm_termine AS a " 
				." WHERE a.id =". $nr 
				." AND a.published != 0" 
				; 		
		return $query;
	}
	function getTermine_Detail( $options=array() )
	{
		$query	= $this->_getTermine_Detail( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	
	function _getSchnellmenu( &$options )
	{
		$sid	= JRequest::getInt('saison','1');
		$liga	= JRequest::getInt('liga','1');
		$start	= JRequest::getVar('start','1');
		$db	= JFactory::getDBO();
		if ($start == '1') $date = date("Y-m-d");
		else $date = $start;
	 
		$query = " (SELECT a.datum AS datum FROM #__clm_runden_termine AS a "
				." LEFT JOIN #__clm_liga AS l ON l.id = a.liga "
				." WHERE l.published != '0' AND  TO_DAYS(datum)+183 >= TO_DAYS('".$date."') )"
				." UNION ALL"
				." (SELECT b.startdate AS datum FROM #__clm_termine AS b "
				." WHERE b.published != '0' AND  TO_DAYS(b.startdate)+183 >= TO_DAYS('".$date."') )"
				." UNION ALL"
				." (SELECT c.datum AS datum FROM #__clm_turniere_rnd_termine AS c "
				." WHERE TO_DAYS(datum)+183 >= TO_DAYS('".$date."') )"
				." ORDER BY datum ASC"
				;
		
		return $query;
	}
	function getSchnellmenu( $options=array() )
	{
		$query	= $this->_getSchnellmenu( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	function _getCLMSumPlan ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$db		= JFactory::getDBO();
	$date 	= date("Y-m-d");
		$query = " SELECT a.dg,a.lid,a.sid,a.runde,a.paar,a.tln_nr,a.gegner "
			." ,t.name as dat_name, t.datum as datum "
			." ,m.name as hname, n.name as gname, m.published as hpublished, "
			." n.published as gpublished "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_liga as l ON l.sid = a.sid AND l.id = a.lid " //klkl
			." LEFT JOIN #__clm_mannschaften as m ON m.tln_nr = a.tln_nr AND m.sid = a.sid AND m.liga = a.lid "
			." LEFT JOIN #__clm_mannschaften as n ON n.tln_nr = a.gegner AND n.sid = a.sid AND n.liga = a.lid"
			." LEFT JOIN #__clm_runden_termine as t ON t.nr = (a.runde + ((a.dg - 1)*l.runden)) AND t.liga = a.lid AND t.sid = a.sid " //klkl
			." WHERE TO_DAYS(t.datum)+2 >= TO_DAYS(NOW())"
			." AND a.heim = 1"
			." ORDER BY datum ASC, a.lid ASC, a.paar ASC "
			;
		return $query;
	}

	function getCLMSumPlan ( $options=array() )
	{
		$query	= $this->_getCLMSumPlan( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	
}