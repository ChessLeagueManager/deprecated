<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_EXTHelper
{
	function getLink(&$params)
	{
	global $mainframe;
	$db	= JFactory::getDBO();

	$query = "SELECT  a.sid, a.id, a.name, a.runden, a.durchgang "
		."\n FROM #__clm_liga as a"
		."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		."\n WHERE a.published = 1"
		."\n AND s.published = 1"
		."\n AND s.archiv  != 1"
		."\n ORDER BY a.sid DESC,a.ordering ASC, a.id ASC "
		;
	$db->setQuery( $query );
	$link = $db->loadObjectList();;

	return $link;
	}

	function getCount(&$params)
	{
	global $mainframe;
	$db	= JFactory::getDBO();

	$query = "SELECT COUNT(a.id) as id "
		."\n FROM #__clm_liga as a"
		."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		."\n WHERE a.published = 1"
		."\n AND s.archiv  != 1"
		;
	$db->setQuery( $query );
	$count = $db->loadObjectList();;

	return $count;
	}

}