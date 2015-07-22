<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLMHelper {
	
	function getLink(&$params) {
		global $mainframe;
		$par_mt_type = $params->def('mt_type', 0);
		$db	= JFactory::getDBO();
	
		$query = "SELECT  a.sid, a.id, a.name, a.runden, a.durchgang, a.rang, a.runden_modus "
			."\n FROM #__clm_liga as a"
			."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			."\n WHERE a.published = 1"
			."\n AND a.liga_mt = ".$par_mt_type
			."\n AND s.published = 1"
			."\n AND s.archiv  != 1"
			."\n ORDER BY a.sid DESC,a.ordering ASC, a.id ASC "
			;
		$db->setQuery( $query );
		$link = $db->loadObjectList();;
	
		return $link;
	}

	function getCount(&$params) {
		global $mainframe;
		$par_mt_type = $params->def('mt_type', 0);
		$db	= JFactory::getDBO();
	
		$query = "SELECT COUNT(a.id) as id "
			."\n FROM #__clm_liga as a"
			."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			."\n WHERE a.published = 1"
			."\n AND a.liga_mt = ".$par_mt_type
			."\n AND s.archiv  != 1"
			;
		$db->setQuery( $query );
		$count = $db->loadObjectList();;
	
		return $count;
	}

	function getRunde(&$params) {
		global $mainframe;
		$liga	= JRequest::getVar( 'liga');
		$db	= JFactory::getDBO();
	
		$query = " SELECT  a.* "
			." FROM #__clm_runden_termine as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.liga =".$liga
			." AND s.published = 1"
			." AND s.archiv  != 1"
			." ORDER BY a.nr ASC"
			;
		$db->setQuery( $query );
		$runden = $db->loadObjectList();;
	
		return $runden;
	}

}