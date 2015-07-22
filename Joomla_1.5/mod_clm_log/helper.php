<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_LogHelper
{
function getData(&$params)
	{
	global $mainframe;
	$db		= JFactory::getDBO();
	$user		= & JFactory::getUser();
	$jid		= $user->get('id');

	$query = " SELECT a.*, u.name as typ, v.name as vname"
		." FROM #__clm_user as a"
		." LEFT JOIN #__clm_usertype as u ON u.user_clm = a.user_clm"
		." LEFT JOIN #__clm_vereine as v ON v.ZPS = a.zps AND v.sid = a.sid"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE jid = ".$jid
		." AND s.archiv = 0 AND s.published = 1"
		;
	$db->setQuery( $query );
	$data = $db->loadObjectList();

	return $data;
	}

function getLiga(&$params)
	{
	global $mainframe;
	$db		= JFactory::getDBO();
	$user		=& JFactory::getUser();
	$jid 		= $user->get('id');

	// Konfigurationsparameter auslesen
	$config 	= &JComponentHelper::getParams( 'com_clm' );
	$meldung_verein	= $config->get('meldung_verein',1);
	$meldung_heim	= $config->get('meldung_heim',1);

	$query = "SELECT l.rang,t.meldung,l.name as lname,i.gid,p.sid,p.lid,p.runde,p.paar,p.dg,p.tln_nr,p.gegner,a.zps,  "
		." l.durchgang as durchgang, " //klkl
		." m.id,m.sid,m.name,m.liga,m.man_nr,m.published,p.gemeldet "
		." , m.liste "
		." FROM #__clm_user as a"
		." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or m.sg_zps = a.zps) AND m.sid = a.sid "
		." LEFT JOIN #__clm_saison as s ON s.id = m.sid "
		." LEFT JOIN #__clm_rnd_man as p ON ( m.tln_nr = p.tln_nr AND p.lid = m.liga AND p.sid = a.sid)  "
		." LEFT JOIN #__clm_liga as l ON ( l.id = m.liga AND l.sid = m.sid) "
		." LEFT JOIN #__clm_rangliste_id as i ON i.zps = a.zps AND i.gid = l.rang "
		//." LEFT JOIN jos_clm_runden_termine as t ON t.nr = p.runde AND t.liga = m.liga AND t.sid = a.sid "
		." LEFT JOIN #__clm_runden_termine as t ON t.nr = (p.runde + (l.runden * (p.dg - 1))) AND t.liga = m.liga AND t.sid = a.sid " //klkl
		." WHERE jid = ".$jid;
	if ($meldung_verein == 0) { $query = $query." AND mf = ".$jid;}
	if ($meldung_heim == 0) { $query = $query." AND p.heim = 1";}
		$query = $query
		." AND s.published = 1 AND s.archiv = 0 AND  l.rnd = 1 AND l.published = 1 "
		." ORDER BY m.man_nr ASC, p.dg ASC, p.runde ASC "
		;
	$db->setQuery( $query );
	$liga = $db->loadObjectList();

	return $liga;
	}

function getMannschaften(&$params)
	{
	global $mainframe;
	$db	= JFactory::getDBO();
	$user	=&JFactory::getUser();
	$jid 	= $user->get('id');

	// Konfigurationsparameter auslesen
	$config 	= &JComponentHelper::getParams( 'com_clm' );
	$meldung_verein	= $config->get('meldung_verein',1);

	$query = " SELECT COUNT(m.id) as count "
		." FROM #__clm_user as a"
		." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or m.sg_zps = a.zps)"
		." LEFT JOIN #__clm_saison as s ON s.id = m.sid "
		." LEFT JOIN #__clm_liga as l ON l.id = m.liga AND l.sid = m.sid  "
		." WHERE jid = ".$jid
		." AND s.published = 1 AND s.archiv = 0 AND m.published = 1 AND l.rnd = 1 "
		;
	if ($meldung_verein == 0) { $query = $query." AND mf = ".$jid;}
	$db->setQuery( $query );
	$count = $db->loadObjectList();

	return $count;
	}

function getMeldeliste(&$params)
	{
	global $mainframe;
	$db		= JFactory::getDBO();
	$user		= & JFactory::getUser();
	$jid 		= $user->get('id');

	$query = " SELECT m.liste, m.man_nr, m.name, m.sid, m.zps, l.name AS liganame"
		." FROM #__clm_user as a"
		." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or m.sg_zps = a.zps) AND m.sid = a.sid"
		." LEFT JOIN #__clm_liga as l ON l.sid = a.sid AND l.id = m.liga"
		." LEFT JOIN #__clm_saison as s ON s.id = m.sid "
		." WHERE jid = ".$jid
		." AND l.rang = 0 "
		." AND s.published = 1 AND s.archiv = 0 AND m.published = 1 AND m.liste < 1"
		." ORDER BY m.man_nr ASC"
		;
	$db->setQuery( $query );
	$meldeliste = $db->loadObjectList();

	return $meldeliste;
	}

function getRangliste(&$params)
	{
	global $mainframe;
	$db			= JFactory::getDBO();
	$user			=& JFactory::getUser();
	$jid =  $user->get('id');

	$query = "SELECT zps FROM #__clm_user "
		." WHERE jid =".$jid
		;
	$db->setQuery( $query );
	$zps_user = $db->loadObjectList();

	if(isset($zps_user[0]->zps)){
	$zps = $zps_user[0]->zps;


	$query = " SELECT a.sid as sid,a.rang as gid,m.zps as zps,i.id,n.Gruppe as gruppe "
		." FROM #__clm_liga as a "
		." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.id AND m.sid = a.sid "
		." LEFT JOIN #__clm_rangliste_name as n ON n.id = a.rang "
		." LEFT JOIN #__clm_rangliste_id as i ON i.gid = n.id AND i.zps = m.zps "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE m.zps =".$zps
		." AND a.rang <> 0 AND a.published = 1 AND s.published = 1 AND s.archiv = 0 AND i.id IS NULL "
		." GROUP BY n.Gruppe "
		." ORDER BY m.man_nr ASC"
		;
	$db->setQuery( $query );
	$rangliste = $db->loadObjectList();
	}
	else { $rangliste = ""; }

	return $rangliste;
	}
}