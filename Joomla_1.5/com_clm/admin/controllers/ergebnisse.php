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

class CLMControllerErgebnisse extends JController
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		//$this->registerTask( 'add','edit' );
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'apply_wertung','save_wertung' );
	}

function display()
	{
	global $mainframe, $option;
	$section	= JRequest::getVar('section');
	$db		=& JFactory::getDBO();

	// für kaskadierende Menüführung
	// Parameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$val	= $config->get('menue',1);
	if ($val == 1) {
		$runde	= JRequest::getVar( 'runde' );
		$dg	= JRequest::getVar( 'dg' );
			}
	if ($val == 1 AND $runde !="") { $mainframe->setUserState( "$option.filter_runde", "$runde" ); }
	if ($dg  !="") { $mainframe->setUserState( "$option.filter_dg", "$dg" ); }

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_lid		= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	$filter_dg		= $mainframe->getUserStateFromRequest( "$option.filter_dg",'filter_dg',0,'int' );
	$filter_runde		= $mainframe->getUserStateFromRequest( "$option.filter_runde",'filter_runde',0,'int' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' s.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ( $filter_lid ) {	$where[] = 'a.lid = '.(int) $filter_lid;

	$query = 'SELECT runden,durchgang FROM #__clm_liga WHERE id = '.$filter_lid ;
	$db->setQuery( $query );
	$rnd_filter = $db->loadObjectList();
	$rnd_filter_dg	= $rnd_filter[0]->durchgang;
	$rnd_filter_rnd	= $rnd_filter[0]->runden;
	}
	else {
	$query = 'SELECT MAX(runden) as runden, MAX(durchgang) as durchgang FROM #__clm_liga ';
	$db->setQuery( $query );
	$rnd_filter = $db->loadObjectList();
	$rnd_filter_dg	= $rnd_filter[0]->durchgang;
	$rnd_filter_rnd	= $rnd_filter[0]->runden;
		}
	// Filter einstellen für verschiedene Kombinationen von DropDown Menue
	if ( $filter_runde != 0 AND $filter_lid !=0) {
		if ( $filter_runde > $rnd_filter_rnd) {
			$filter_runde = $mainframe->setUserState( "$option.filter_runde", "1" );
			$where[] = 'a.runde = '.(int) $filter_runde;
			}
		else { $where[] = 'a.runde = '.(int) $filter_runde; }
			}
	if ( $filter_runde AND !$filter_lid) { $where[] = 'a.runde = '.(int) $filter_runde; }
	if ( $filter_dg ) {
		if ( $filter_dg > $rnd_filter_dg ) {
			$filter_dg = $mainframe->setUserState( "$option.filter_dg", "1" );
			$where[] = 'a.dg = '.(int) $filter_dg; }
		else { $where[] = 'a.dg = '.(int) $filter_dg; }}

	if ($search) {	$where[] = 'LOWER(m.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );}

	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}

	$where 		= ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY a.sid ASC,a.lid ASC,a.dg ASC ,a.runde ASC ,a.paar ASC';
	} else {
	if ($filter_order =='hname' OR $filter_order == 'gname' OR $filter_order == 'a.lid' OR $filter_order == 'a.runde' OR $filter_order == 'a.paar' OR $filter_order == 'a.dg' OR $filter_order == 's.name' OR $filter_order == 'a.gemeldet' OR $filter_order == 'u.name' ) {
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; }
	}

	// get the total number of records
	$query = 'SELECT COUNT(*) '
		.' FROM #__clm_rnd_man AS a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		.' WHERE a.heim = 1 '
		. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = "SELECT a.*,l.name as liga,l.teil,l.durchgang, "
		." s.name as saison,s.published as sid_pub, u.name as uname,m.name as hname, n.name as gname "
	.' FROM #__clm_rnd_man as a '
	.' LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet AND u.sid = a.sid '
	.' LEFT JOIN #__clm_mannschaften AS m ON (m.tln_nr = a.tln_nr AND m.liga = a.lid AND m.sid = a.sid) '
	.' LEFT JOIN #__clm_mannschaften AS n ON (n.tln_nr = a.gegner AND n.liga = a.lid AND n.sid = a.sid) '
	.' LEFT JOIN #__clm_liga AS l ON l.id = a.lid AND l.sid = a.sid'
	.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid '
	.' WHERE a.heim = 1 '
	. $where
	. $orderby	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// Filter
	// Statusfilter
	$lists['state']	= JHTML::_('grid.state',  $filter_state );
	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_SAISON_SELECT' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
	// Nur ausführen wenn Saison published = 1 !!
	if ($rows[0]->liga) {
	// Ligafilter
	$sql = 'SELECT a.id AS cid, a.name FROM #__clm_liga as a'
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE a.rnd = 1 AND a.published = 1 AND s.archiv = 0 AND s.published = 1";
	$db->setQuery($sql);
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_LIGA' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHTML::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );
	// Rundenfilter
	$sql = 'SELECT id, runde as name FROM #__clm_rnd_man '
		." WHERE  lid =".($rows[0]->lid)." AND paar =1 AND heim = 1 AND dg = 1"
		." ORDER BY runde ASC ";
	$db->setQuery($sql);
	$rlist[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_RUNDE' ), 'name', 'name' );
	$rlist		= array_merge( $rlist, $db->loadObjectList() );
	$lists['runde']	= JHTML::_('select.genericlist', $rlist, 'filter_runde', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','name', 'name', intval( $filter_runde ) );
	// Durchgangsfilter
	$dg_menu = array();
	$dg_menu[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_DURCHGANG' ), 'name', 'name' );
	$dg_menu[]	= JHTML::_('select.option',  '1', JText::_( 'ERGEBNISSE_DGA' ), 'name', 'name' );
	$dg_menu[]	= JHTML::_('select.option',  '2', JText::_( 'ERGEBNISSE_DGB' ), 'name', 'name' );
	$lists['dg_menu']	= JHTML::_('select.genericlist', $dg_menu, 'filter_dg', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','name', 'name', intval( $filter_dg ) );
	}
	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
	// Scuhefilter
	$lists['search']= $search;
	require_once(JPATH_COMPONENT.DS.'views'.DS.'ergebnisse.php');
	CLMViewErgebnisse::ergebnisse( $rows, $lists, $pageNav, $option );
}


function edit()
	{
	global $mainframe, $option;

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	JArrayHelper::toInteger($cid);

	// load the row from the db table
	$row =& JTable::getInstance( 'ergebnisse', 'TableCLM' );
	$row->load( $cid[0] );

	$sid =& JTable::getInstance( 'saisons', 'TableCLM' );
	$sid->load($row->sid);

	// Ergebnisse einer unveröffentlichten Saison nicht bearbeiten
	if ($sid->published =="0") {
	JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_SAISON' ));
	JError::raiseNotice( 6000,  JText::_( 'ERGEBNISSE_SAISON_WARTEN' ));
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// spielfreie Runde  kann nicht gemeldet / bearbeitet werden
	if ($row->gemeldet == "1") {
		JError::raiseNotice( 6000,  JText::_( 'ERGEBNISSE_SPIELFREIE' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}

	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=& JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $row->sid );
	if ($saison->archiv == "1" AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_ARCHIV' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr, a.ko_decision, a.comment," //mtmt
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor, w.name as dwz_editor, "
		." a.zeit, a.edit_zeit, u.name as melder, v.name as name_editor, "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr,m.sg_zps as sgh_zps, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, n.sg_zps as sgg_zps, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.rang, l.id as lid, l.b_wertung, l.runden_modus " //mtmt
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet AND u.sid = a.sid "
		." LEFT JOIN #__clm_user as v ON v.jid = a.editor AND v.sid = a.sid "
		." LEFT JOIN #__clm_user as w ON w.jid = a.dwz_editor AND w.sid = a.sid "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) AND n.sid = a.sid "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();

	// Prüfen ob User Berechtigung zum editieren hat
	if ( $runde[0]->sl !== CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_IHRER' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	$row->checkout( CLM_ID );

	if ( $runde[0]->hmnr > ($runde[0]->lid)*10 OR $runde[0]->gmnr > ($runde[0]->lid)*10) {
		JError::raiseNotice( 6000, JText::_( 'ERGEBNISSE_MANNSCHAFTNUMMER' ) );
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_MN_HEIM ').' '.$runde[0]->hmnr.JText::_('ERGEBNISSE_MN_GAST').' '.$runde[0]->gmnr.' !' );
	}
	// Spieler Heim
	$sql = "SELECT a.*, d.Spielername as name ";
		if($runde[0]->rang !="0") {$sql = $sql.",r.Rang ,r.man_nr as rmnr";}
		$sql = $sql
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$sql = $sql
		." WHERE a.sid = ".$runde[0]->sid
		." AND ( a.zps = '".$runde[0]->hzps."' AND a.mnr = ".$runde[0]->hmnr." )"
		." OR ( a.zps ='".$runde[0]->sgh_zps."' AND a.mnr = ".$runde[0]->hmnr." )";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr <> '0' "
				." ORDER BY r.man_nr,r.Rang"; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr <> '0' "
				." ORDER BY a.snr"; }

	$db->setQuery( $sql );
	$heim		= $db->loadObjectList();

	// Anzahl Spieler Heim
	$sql = "SELECT COUNT(a.snr) as hcount"
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) "
		." WHERE a.sid = ".$runde[0]->sid
		." AND ( a.zps = '".$runde[0]->hzps."' AND a.mnr = ".$runde[0]->hmnr." ) "
		." OR ( a.zps ='".$runde[0]->sgh_zps."' AND a.mnr = ".$runde[0]->hmnr." ) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid; }
		$sql = $sql
		." AND a.mgl_nr <> '0' "
		;
	$db->setQuery( $sql );
	$hcount		= $db->loadObjectList();

	// Bretter / Spieler ermitteln
	$sql = "SELECT a.spieler, a.gegner, a.ergebnis, a.zps, a.gzps "
		." FROM #__clm_rnd_spl as a "
		." WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.dg = ".$runde[0]->dg
		." AND heim = 1"
		." ORDER BY a.brett"
		;
	// evtl. WHERE weiss = 1

	$db->setQuery( $sql );
	$bretter	= $db->loadObjectList();

	// Ergebnisliste laden
	$sql = "SELECT a.id, a.erg_text "
		." FROM #__clm_ergebnis as a "
		;
	$db 		=& JFactory::getDBO();
	$db->setQuery( $sql );
	$ergebnis	= $db->loadObjectList();

	// Punktemodus aus #__clm_liga holen
		$query = " SELECT a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$runde[0]->lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;
////
////
////
////
////
////
////

	// Ergebnistexte nach Modus setzen
	$ergebnis[0]->erg_text = ($nieder+$antritt)." - ".($sieg+$antritt);
	$ergebnis[1]->erg_text = ($sieg+$antritt)." - ".($nieder+$antritt);
	$ergebnis[2]->erg_text = ($remis+$antritt)." - ".($remis+$antritt);
	$ergebnis[3]->erg_text = ($nieder+$antritt)." - ".($nieder+$antritt);
	if ($antritt > 0) {
		$ergebnis[4]->erg_text = "0 - ".round($antritt+$sieg)." (kampflos)";
		$ergebnis[5]->erg_text = round($antritt+$sieg)." - 0 (kampflos)";
		$ergebnis[6]->erg_text = "0 - 0 (kampflos)";
		}
	// Spieler Gast
	$sql = "SELECT a.*, d.Spielername as name";
		if($runde[0]->rang !="0") {$sql = $sql.",r.Rang,r.man_nr as rmnr ";}
		$sql = $sql
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$sql = $sql
		." WHERE a.sid = ".$runde[0]->sid
		." AND ( a.zps = '".$runde[0]->gzps."' AND a.mnr = ".$runde[0]->gmnr." ) "
		." OR ( a.zps ='".$runde[0]->sgg_zps."' AND a.mnr = ".$runde[0]->gmnr." )";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr > 0 "
				." ORDER BY r.man_nr,r.Rang"; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr > 0 "
				." ORDER BY a.snr"; }

	$db->setQuery( $sql );
	$gast		= $db->loadObjectList();

	// Anzahl Spieler Gast
	$sql = "SELECT COUNT(a.snr) as gcount"
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) "
		." WHERE a.sid = ".$runde[0]->sid
		." AND ( a.zps = '".$runde[0]->gzps."' AND a.mnr = ".$runde[0]->gmnr." ) "
		." OR ( a.zps ='".$runde[0]->sgg_zps."' AND a.mnr = ".$runde[0]->gmnr." ) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid; }
		$sql = $sql
		." AND a.mgl_nr > 0 "
		;
	$db->setQuery( $sql );
	$gcount		= $db->loadObjectList();

	if ($runde[0]->runde > 1) {
		$sql = "SELECT me.snr"
		  ." FROM #__clm_rnd_spl as a "
		  ." LEFT JOIN #__clm_mannschaften AS ma ON (ma.sid = a.sid AND ma.liga = a.lid and ma.tln_nr = a.tln_nr) "
		  ." LEFT JOIN #__clm_meldeliste_spieler AS me ON (me.sid = a.sid AND me.lid = a.lid AND me.mnr = ma.man_nr AND me.zps = a.zps AND me.mgl_nr = a.spieler) "
		  ." WHERE a.sid = ".$runde[0]->sid
		  ." AND a.lid = ".$runde[0]->lid
		  ." AND a.runde = ".($runde[0]->runde - 1)
		  ." AND a.tln_nr = ".$runde[0]->tln_nr   
		  ." AND a.dg = 1"  //.$runde[0]->dg
		  ." ORDER BY a.brett"
		  ;
		$db->setQuery( $sql );
		$hvoraufstellung	= $db->loadObjectList();
	
		$sql = "SELECT me.snr"
		  ." FROM #__clm_rnd_spl as a "
		  ." LEFT JOIN #__clm_mannschaften AS ma ON (ma.sid = a.sid AND ma.liga = a.lid and ma.tln_nr = a.tln_nr) "
		  ." LEFT JOIN #__clm_meldeliste_spieler AS me ON (me.sid = a.sid AND me.lid = a.lid AND me.mnr = ma.man_nr AND me.zps = a.zps AND me.mgl_nr = a.spieler) "
		  ." WHERE a.sid = ".$runde[0]->sid
		  ." AND a.lid = ".$runde[0]->lid
		  ." AND a.runde = ".($runde[0]->runde - 1)
		  ." AND a.tln_nr = ".$runde[0]->gegner   
		  ." AND a.dg = 1"  //.$runde[0]->dg
		  ." ORDER BY a.brett"
		  ;
		$db->setQuery( $sql );
		$gvoraufstellung	= $db->loadObjectList();
	}	

	
	require_once(JPATH_COMPONENT.DS.'views'.DS.'ergebnisse.php');
	CLMViewErgebnisse::ergebnis( $row, $runde, $heim, $hcount, $gast, $gcount, $bretter,$ergebnis, $option, $hvoraufstellung, $gvoraufstellung);
	}

function remove()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user 		=& JFactory::getUser();
	JArrayHelper::toInteger($cid);

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'ERGEBNISSE_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// Daten sammeln
	$data = "SELECT a.gemeldet,l.sl as sl,a.sid, a.lid, a.runde, a.dg, a.paar "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery( $data);
	$data		= $db->loadObjectList();

	// Prüfen ob User Berechtigung zum löschen hat
	if ( $data[0]->sl !== CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

		// Für Heimmannschaft updaten
		$query	=" UPDATE #__clm_rnd_man"
			." SET gemeldet = NULL"
			." , editor = NULL"
			." , zeit = '0000-00-00 00:00:00'"
			." , edit_zeit = '0000-00-00 00:00:00'"
			." , brettpunkte = NULL"
			." , manpunkte = NULL"
			." , ko_decision = 0"          //mtmt
			." , comment = ''"          //mtmt
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 1 "
			;
		$db->setQuery($query);
		$db->query();
		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET gemeldet = NULL"
			." , editor = NULL"
			." , zeit = '0000-00-00 00:00:00'"
			." , edit_zeit = '0000-00-00 00:00:00'"
			." , brettpunkte = NULL"
			." , manpunkte = NULL"
			." , ko_decision = 0"          //mtmt
			." , comment = ''"          //mtmt
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 0 "
			;
		$db->setQuery($query);
		$db->query();

		$query = " DELETE FROM #__clm_rnd_spl "
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 1 "
			;
		$db->setQuery($query);
		$db->query();

		$query = " DELETE FROM #__clm_rnd_spl "
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 0 "
			;
		$db->setQuery($query);
		$db->query();

		if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
				}
	
	CLMControllerErgebnisse::calculateRanking($data[0]->sid,$data[0]->lid);
	require_once(JPATH_COMPONENT.DS.'views'.DS.'runden.php');
	CLMViewRunden::dwz( $option, 0, $data[0]->sid, $data[0]->lid );
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Ergebnis gelöscht";
	$clmLog->params = array('cids' => $cids, 'sid' => $data[0]->sid, 'lid' => $data[0]->lid, 'rnd' => $data[0]->runde, 'paar' => $data[0]->paar, 'dg' => $data[0]->dg);
	$clmLog->write();

	$msg = JText::_( 'ERGEBNISSE_GELOESCHT');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg);
	}


function save()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=&JFactory::getDBO();
	$task		= JRequest::getVar( 'task');
	$user 		=&JFactory::getUser();
	$id_id 		= JRequest::getVar( 'id');
	$date		=&JFactory::getDate();

	$meldung 	= $user->get('id');
	$sid		= JRequest::getVar( 'sid');
	$lid 		= JRequest::getVar( 'lid');
	$rnd		= JRequest::getVar( 'rnd');
	$paarung	= JRequest::getVar( 'paarung');
	$dg		= JRequest::getVar( 'dg');
	$gemeldet	= JRequest::getVar( 'gemeldet');
	$hzps		= JRequest::getVar( 'hzps');
	$gzps		= JRequest::getVar( 'gzps');
	$ko_decision = JRequest::getVar( 'ko_decision');
	$comment = JRequest::getVar( 'comment');
	// Überprüfen ob Runde schon gemeldet ist
	$query	= "SELECT gemeldet, tln_nr, gegner "
		." FROM #__clm_rnd_man "
		." WHERE id = $id_id "
		;
	$db->setQuery( $query );
	$id = $db->loadObjectList();
	$id_tln = $id[0]->tln_nr;
	$id_geg = $id[0]->gegner;
	
	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.stamm, a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus, a.runden, "
		." a.man_sieg, a.man_remis, a.man_nieder, a.man_antritt, a.sieg_bed "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$stamm 		= $liga[0]->stamm;
		$sieg_bed	= $liga[0]->sieg_bed;
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;
		$man_sieg 	= $liga[0]->man_sieg;
		$man_remis 	= $liga[0]->man_remis;
		$man_nieder	= $liga[0]->man_nieder;
		$man_antritt	= $liga[0]->man_antritt;
		$runden_modus	= $liga[0]->runden_modus;
		$runden		= $liga[0]->runden;
 
	// Runde noch NICHT gemeldet
	if (!$id[0]->gemeldet) {
	
	// Datensätze in Spielertabelle schreiben
	for ($y=1; $y< (1+$stamm) ; $y++){
		$heim		= JRequest::getVar( 'heim'.$y);
		$gast		= JRequest::getVar( 'gast'.$y);
		$ergebnis	= JRequest::getVar( 'ergebnis'.$y);

	$theim	= explode("-", $heim);
	$thmgl	= $theim[0];
	$thzps	= $theim[1];

	$tgast	= explode("-", $gast);
	$tgmgl	= $tgast[0];
	$tgzps	= $tgast[1];

	if ($ergebnis > 3) { $kampflos = 1; }
		else { $kampflos = 0; }
		
	if ($ergebnis == 1)
		{ 	$erg_h = $nieder+$antritt;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 2)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = $nieder+$antritt;
		}
	if ($ergebnis == 3)
		{ 	$erg_h = $remis+$antritt;
			$erg_g = $remis+$antritt;
		}
	if ($ergebnis == 4)
		{ 	$erg_h = $antritt;
			$erg_g = $antritt;
		}
	if ($ergebnis == 5)
		{ 	$erg_h = 0;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 6)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = 0;
		}
	if ($ergebnis == 7)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 8)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 9)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	// WICHTIG wegen NULL / SELECTED Problem
	$ergebnis--;
	// ungerade Zahl für Weiss/Schwarz
	if ($y%2 != 0) {$weiss = 0; $schwarz = 1;}
	else { $weiss = 1; $schwarz = 0;}
	$query	= "INSERT INTO #__clm_rnd_spl "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler` "
		." , `zps`, `gegner`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
		." VALUES ('$sid','$lid','$rnd','$paarung','$dg','$id_tln','$y',1,'$weiss','$thmgl','$thzps',"
		." '$tgmgl','$tgzps','$ergebnis', '$kampflos','$erg_h','$meldung') "
		." , ('$sid','$lid','$rnd','$paarung','$dg','$id_geg','$y','0','$schwarz','$tgmgl','$tgzps',"
		." '$thmgl','$thzps','$ergebnis', '$kampflos','$erg_g','$meldung') "
		;
	$db->setQuery($query);
	$db->query();
	}
	// in Runden Mannschaftstabelle als gemeldet schreiben
	// Brettpunkte Heim summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man=$db->loadObjectList();
	$hmpunkte=$man[0]->punkte;
	
	// Wertpunkte Heim berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$hwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$hwpunkte = $hwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$man_kl=$db->loadObjectList();
	$man_kl_punkte=$man_kl[0]->kl;

	// Brettpunkte Gast summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$gman=$db->loadObjectList();
	$gmpunkte=$gman[0]->punkte;

	// Wertpunkte Gast berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$gwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$gwpunkte = $gwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$gman_kl=$db->loadObjectList();
	$gman_kl_punkte=$gman_kl[0]->kl;

	// Teilnehmer ID bestimmen 
	$query = " SELECT a.tln_nr,a.gegner "
		." FROM #__clm_rnd_man as a"
		." WHERE a.id = ".$id_id
			;
	$db->setQuery( $query);
	$tlnr=$db->loadObjectList();
	$tln_nr	= $tlnr[0]->tln_nr;
	$gegner	= $tlnr[0]->gegner;

	// Mannschaftspunkte Heim / Gast verteilen
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { $hman_punkte = $man_sieg; $gman_punkte = $man_nieder;}
		if ( $hmpunkte == $gmpunkte ) { $hman_punkte = $man_remis; $gman_punkte = $man_remis;}
		if ( $hmpunkte <  $gmpunkte ) { $hman_punkte = $man_nieder; $gman_punkte = $man_sieg;}
	}
	// erweiterter Standard : mehr als die H�lfte der BP -> Sieg, H�lfte der BP -> halbe MP Zahl
	if ($sieg_bed == 2) {
		if ( $hmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_sieg;}
		if ( $hmpunkte == (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_remis;}
		if ( $hmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_nieder;}
		
		if ( $gmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_sieg;}
		if ( $gmpunkte == (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_remis;}
		if ( $gmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_nieder;}
	}
	// Antrittspunkte addieren falls angetreten
	if ( $stamm > $man_kl_punkte ) { $hman_punkte = $hman_punkte + $man_antritt;}
	if ( $stamm > $gman_kl_punkte ) { $gman_punkte = $gman_punkte + $man_antritt;}

	// Datum und Uhrzeit für Meldung
	$now = $date->toMySQL();
	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET gemeldet = ".$meldung
		." , zeit = '$now'"
		." , brettpunkte = ".$hmpunkte
		." , manpunkte = ".$hman_punkte
		." , wertpunkte = ".$hwpunkte
		." , comment = '".$comment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$db->query();

	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET gemeldet = ".$meldung
		." , zeit = '$now'"
		." , brettpunkte = ".$gmpunkte
		." , manpunkte = ".$gman_punkte
		." , wertpunkte = ".$gwpunkte
		." , comment = '".$comment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();

	}

	// Runde bereits gemeldet
	else {
	// Datensätze in Spielertabelle schreiben
	for ($y=1; $y< (1+$stamm) ; $y++){ 
		$heim		= JRequest::getVar( 'heim'.$y);
		$gast		= JRequest::getVar( 'gast'.$y);
		$ergebnis	= JRequest::getVar( 'ergebnis'.$y);
	$kampflos = 0;
	
	if ($ergebnis > 3) { $kampflos = 1; }
		else { $kampflos = 0; }
		
	if ($ergebnis == 1)
		{ 	$erg_h = $nieder+$antritt;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 2)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = $nieder+$antritt;
		}
	if ($ergebnis == 3)
		{ 	$erg_h = $remis+$antritt;
			$erg_g = $remis+$antritt;
		}
	if ($ergebnis == 4)
		{ 	$erg_h = $antritt;
			$erg_g = $antritt;
		}
	if ($ergebnis == 5)
		{ 	$erg_h = 0;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 6)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = 0;
		}
	if ($ergebnis == 7)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 8)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 9)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	// WICHTIG wegen NULL / SELECTED Problem
	$ergebnis--;

	$theim	= explode("-", $heim);
	$thmgl	= $theim[0];
	$thzps	= $theim[1];

	$tgast	= explode("-", $gast);
	$tgmgl	= $tgast[0];
	$tgzps	= $tgast[1];

	// Heim updaten
	$query	= "UPDATE #__clm_rnd_spl "
		." SET spieler = ".$thmgl
		." , zps = '$thzps'"
		." , gegner = ".$tgmgl
		." , gzps = '$tgzps'"
		." , ergebnis = ".$ergebnis
		." , kampflos = ".$kampflos
		." , punkte = ".$erg_h
		." , tln_nr = ".$id_tln
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$db->query();

	// Gast updaten
	$query	= "UPDATE #__clm_rnd_spl "
		." SET spieler = ".$tgmgl
		." , zps = '$tgzps'"
		." , gegner = ".$thmgl
		." , gzps = '$thzps'"
		." , ergebnis = ".$ergebnis
		." , kampflos = ".$kampflos
		." , punkte = ". $erg_g
		." , tln_nr = ".$id_geg
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();

	}
	// Prüfen ob Turnierergebnis geändert wurde. Wenn ja dann keine MP oder BP updaten !
	$query = " SELECT COUNT(dwz_edit) as edit FROM #__clm_rnd_spl "
		." WHERE dwz_edit IS NOT NULL "
		." AND sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		;
	$db->setQuery( $query );
	$counter = $db->loadResult();

	if($counter =="0") {
	// Brettpunkte Heim summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man=$db->loadObjectList();
	$hmpunkte=$man[0]->punkte;
	
	// Wertpunkte Heim berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$hwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$hwpunkte = $hwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	// Anzahl kampflose Partien (Heim) zählen
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$man_kl=$db->loadObjectList();
	$man_kl_punkte=$man_kl[0]->kl;

	// Brettpunkte Gast summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$gman=$db->loadObjectList();
	$gmpunkte=$gman[0]->punkte;

	// Wertpunkte Gast berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$gwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$gwpunkte = $gwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	// Anzahl kampflose Partien (Gast) zählen
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$gman_kl=$db->loadObjectList();
	$gman_kl_punkte=$gman_kl[0]->kl;
	}
	// Nachricht absetzen als Hinweis das Ergebnis nicht geändert wurde
	else {
	JError::raiseNotice( 6000, JText::_( 'ERGEBNISSE_ME_WERTUNG') );
	}

	// Teilnehmer ID bestimmen 
	$query = " SELECT a.tln_nr,a.gegner "
		." FROM #__clm_rnd_man as a"
		." WHERE a.id = ".$id_id
			;
	$db->setQuery( $query);
	$tlnr=$db->loadObjectList();
	$tln_nr	= $tlnr[0]->tln_nr;
	$gegner	= $tlnr[0]->gegner;

	// Mannschaftspunkte Heim / Gast
	$hman_punkte = 0;
	$gman_punkte = 0;
	if ( $hmpunkte > 0 OR $gmpunkte > 0) {
		// Mannschaftspunkte Heim / Gast
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { $hman_punkte = $man_sieg; $gman_punkte = $man_nieder;}
		if ( $hmpunkte == $gmpunkte ) { $hman_punkte = $man_remis; $gman_punkte = $man_remis;}
		if ( $hmpunkte <  $gmpunkte ) { $hman_punkte = $man_nieder; $gman_punkte = $man_sieg;}
	}
	// erweiterter Standard : mehr als die Hälfte der BP -> Sieg, Hälfte der BP -> halbe MP Zahl
	if ($sieg_bed == 2) {
		if ( $hmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_sieg;}
		if ( $hmpunkte == (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_remis;}
		if ( $hmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_nieder;}
		
		if ( $gmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_sieg;}
		if ( $gmpunkte == (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_remis;}
		if ( $gmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_nieder;}
	}
	// Antrittspunkte addieren falls angetreten
	if ( $stamm > $man_kl_punkte ) { $hman_punkte = $hman_punkte + $man_antritt;}
	if ( $stamm > $gman_kl_punkte ) { $gman_punkte = $gman_punkte + $man_antritt;}
	}
	// Datum und Uhrzeit für Editorzeit
	$now = $date->toMySQL();
	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET editor = ".$meldung
		." , edit_zeit = '$now'";
		if($counter =="0") {
			$query = $query
			." , brettpunkte = ".$hmpunkte
			." , manpunkte = ".$hman_punkte
			." , wertpunkte = ".$hwpunkte;
			}
		$query = $query
		." , comment = '".$comment."'"
		." , tln_nr = ".$tln_nr
		." , gegner = ".$gegner
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$db->query();
	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET editor = ".$meldung
		." , edit_zeit = '$now'";
		if($counter =="0") {
			$query = $query
			." , brettpunkte = ".$gmpunkte
			." , manpunkte = ".$gman_punkte
			." , wertpunkte = ".$gwpunkte;
			}
		$query = $query
		." , comment = '".$comment."'"
		." , tln_nr = ".$gegner
		." , gegner = ".$tln_nr
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();
	}
	if (($runden_modus == 4) OR ($runden_modus == 5)) {    // KO Turnier
		//echo "<br>99runden_modus: ".$runden_modus; 
		// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
		." SET ko_decision = ".$ko_decision
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
			." AND heim = 1 "
		;
		$db->setQuery($query);
		$db->query();
		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET ko_decision = ".$ko_decision
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
			." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();
		if (($runden_modus == 4) OR ($runden_modus == 5 and $rnd < $runden)) {    // KO Turnierif ($ko_decision == 1) {
			if ($hmpunkte > $gmpunkte) $ko_par = 2;			// Sieger Heim nach Brettpunkte
			elseif ($hmpunkte < $gmpunkte) $ko_par = 3;		// Sieger Gast nach Brettpunkte
			elseif ($hwpunkte > $gwpunkte) $ko_par = 2;		// Sieger Heim nach Wertpunkte
			else $ko_par = 3;								// Sieger Gast nach Wertpunkte
		}	
		elseif ($ko_decision == 2) $ko_par = 2;				// Sieger Heim nach Blitz-Entscheid
		elseif ($ko_decision == 4) $ko_par = 2;				// Sieger Heim nach Los-Entscheid
		else $ko_par = 3;									// Sieger Gast nach Blitz-,Los-Entscheid
		if ($ko_par == 2) { $ko_heim = $rnd; $ko_gast = $rnd -1; }
		else { $ko_heim = $rnd -1; $ko_gast = $rnd; }
		// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_mannschaften"
			." SET rankingpos = ".$ko_heim
			." WHERE sid = ".$sid
			." AND liga = ".$lid
			." AND tln_nr = ".$tln_nr
		;
		$db->setQuery($query);
		$db->query();

		$query	= "UPDATE #__clm_mannschaften"
			." SET rankingpos = ".$ko_gast
			." WHERE sid = ".$sid
			." AND liga = ".$lid
			." AND tln_nr = ".$gegner
		;
		$db->setQuery($query);
		$db->query();	
	}

	CLMControllerErgebnisse::calculateRanking($sid,$lid);
	require_once(JPATH_COMPONENT.DS.'views'.DS.'runden.php');
	CLMViewRunden::dwz( $option, 0, $sid, $lid );

	switch ($task)
	{
		case 'apply':
		$msg = JText::_( 'ERGEBNISSE_AENDERUNG' );
		$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='.$id_id;
			break;
		case 'save':
		default:
		$row =& JTable::getInstance( 'ergebnisse', 'TableCLM' );
		$row->checkin( $id_id);
		$msg = JText::_( 'ERGEBNISSE_GESPEICHERT' );
		$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	if (!$id[0]->gemeldet) {
		$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_GEMELDET' );
	} else { 
		$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_EDIT' );
	}
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paarung, 'dg' => $dg);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg );
	}


function cancel()
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$id		= JRequest::getVar('id');	
	$row 		=& JTable::getInstance( 'ergebnisse', 'TableCLM' );
	$row->checkin( $id);

	$msg = JText::_( 'ERGEBNISSE_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}

function wertung()
	{
	global $mainframe, $option;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token !!' );

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	JArrayHelper::toInteger($cid);
	// load the row from the db table
	$row =& JTable::getInstance( 'ergebnisse', 'TableCLM' );
	$row->load( $cid[0] );

	$sid =& JTable::getInstance( 'saisons', 'TableCLM' );
	$sid->load($row->sid);

	// Ergebnisse einer unveröffentlichten Saison nicht bearbeiten
	if ($sid->published =="0") {
	JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_SAISON_NO' ));
	JError::raiseNotice( 6000,  JText::_( 'ERGEBNISSE_SAISON_WARTEN' ));
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// spielfreie Runde  kann nicht gemeldet / bearbeitet werden
	if ($row->gemeldet == "1") {
		JError::raiseNotice( 6000,  JText::_( 'ERGEBNISSE_TW_RUNDEN' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr,"
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor as dwz_edit, w.name as dwz_editor, "
		." a.zeit, a.edit_zeit, u.name as melder, v.name as name_editor, "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.b_wertung"
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet AND u.sid = a.sid "
		." LEFT JOIN #__clm_user as v ON v.jid = a.editor AND v.sid = a.sid "
		." LEFT JOIN #__clm_user as w ON w.jid = a.dwz_editor AND w.sid = a.sid "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();
	// Prüfen ob Ergebnis bereits gemeldet wurde
	if ($runde[0]->gemeldet < 1) {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_DWZ' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	// Prüfen ob User Berechtigung zum editieren hat
	if ( $runde[0]->sl !== CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_DWZ_BEARBEIT' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	$row->checkout( CLM_ID );

	// Bretter / Spieler ermitteln
	$sql = "SELECT a.spieler, a.gegner, a.ergebnis, a.dwz_edit, d.Spielername as hname, e.Spielername as gname "
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = a.zps AND d.Mgl_Nr = a.spieler AND d.sid = a.sid "
		." LEFT JOIN #__clm_dwz_spieler as e ON e.ZPS = a.gzps AND e.Mgl_Nr = a.gegner AND e.sid = a.sid "
		." WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.dg = ".$runde[0]->dg
		." AND heim = 1"
		." ORDER BY a.brett"
		;
	$db->setQuery( $sql );
	$bretter	= $db->loadObjectList();

	// Ergebnisliste laden
	$sql = "SELECT a.id, a.eid,a.erg_text "
		." FROM #__clm_ergebnis as a "
		;
	$db->setQuery( $sql );
	$ergebnis=$db->loadObjectList();
	
	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$runde[0]->lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;

	// Ergebnistexte nach Modus setzen
	$ergebnis[0]->erg_text = ($nieder+$antritt)." - ".($sieg+$antritt);
	$ergebnis[1]->erg_text = ($sieg+$antritt)." - ".($nieder+$antritt);
	$ergebnis[2]->erg_text = ($remis+$antritt)." - ".($remis+$antritt);
	$ergebnis[3]->erg_text = ($nieder+$antritt)." - ".($nieder+$antritt);
	if ($antritt > 0) {
		$ergebnis[4]->erg_text = "0 - ".round($antritt+$sieg)." (kampflos)";
		$ergebnis[5]->erg_text = round($antritt+$sieg)." - 0 (kampflos)";
		$ergebnis[6]->erg_text = "0 - 0 (kampflos)";
		}

	// Listen zur manuellen Änderung des Mannschaftsergebnisses generieren
	$sql = "SELECT a.brettpunkte as bp, a.wertpunkte as wp "
		." FROM #__clm_rnd_man as a "
		." WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.tln_nr = ".$runde[0]->tln_nr
		." AND a.dwz_editor > 0"
		;
	$db->setQuery( $sql );
	$list_heim = $db->loadObjectList();

	$sql = "SELECT a.brettpunkte as bp, a.wertpunkte as wp "
		." FROM #__clm_rnd_man as a "
		." WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.gegner = ".$runde[0]->tln_nr
		." AND a.dwz_editor > 0"
		;
	$db->setQuery( $sql );
	$list_gast = $db->loadObjectList();

	$wlist[]	= JHTML::_('select.option',  '-1', JText::_( 'ERGEBNISSE_WAHL' ), 'jid', 'name' );
	$wlist[]	= JHTML::_('select.option',  '0', JText::_( '0' ), 'jid', 'name' );
	for($x=1; $x< (1+(($sieg+$antritt)*$runde[0]->stamm)); $x++) {
	$wlist[]	= JHTML::_('select.option',  $x-(0.5), $x-(0.5), 'jid', 'name' );
	$wlist[]	= JHTML::_('select.option',  $x, $x, 'jid', 'name' );
			}
	$lists['weiss']		= JHTML::_('select.genericlist',   $wlist, 'w_erg', 'class="inputbox" size="1"', 'jid', 'name', $list_heim[0]->bp );
	$lists['schwarz']	= JHTML::_('select.genericlist',   $wlist, 's_erg', 'class="inputbox" size="1"', 'jid', 'name', $list_gast[0]->bp );
	$lists['weiss_w']	= $list_heim[0]->wp;
	$lists['schwarz_w']	= $list_gast[0]->wp;

	require_once(JPATH_COMPONENT.DS.'views'.DS.'ergebnisse.php');
	CLMViewErgebnisse::wertung( $row, $runde,$bretter,$ergebnis, $option, $lists);
	}

function save_wertung()
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= & JFactory::getDBO();
	$task 		= JRequest::getVar('task');
	$user 		= & JFactory::getUser();
	$id_id 		= JRequest::getVar('id');
	$date 		= & JFactory::getDate();

	$meldung 	= $user->get('id');
	$sid		= JRequest::getVar('sid');
	$lid 		= JRequest::getVar('lid');
	$rnd		= JRequest::getVar('rnd');
	$paarung	= JRequest::getVar('paarung');
	$dg		= JRequest::getVar('dg');
	$hzps		= JRequest::getVar('hzps');
	$gzps		= JRequest::getVar('gzps');

	$w_erg		= JRequest::getVar('w_erg');
	$s_erg		= JRequest::getVar('s_erg');
	$ww_erg		= JRequest::getVar('ww_erg');
	$sw_erg		= JRequest::getVar('sw_erg');
	
	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.stamm, a.sieg, a.sieg_bed, a.remis, a.nieder, a.antritt, a.runden_modus, "
		." a.man_sieg, a.man_remis, a.man_nieder, a.man_antritt "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$stamm 		= $liga[0]->stamm;
		$sieg 		= $liga[0]->sieg;
		$sieg_bed	= $liga[0]->sieg_bed;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;
		$man_sieg 	= $liga[0]->man_sieg;
		$man_remis 	= $liga[0]->man_remis;
		$man_nieder	= $liga[0]->man_nieder;
		$man_antritt	= $liga[0]->man_antritt;

	// Arrays zur Punktevergabe
	$heim_erg = array();
		$heim_erg[-1]="NULL";
		$heim_erg[0]=$nieder+$antritt;
		$heim_erg[1]=$sieg+$antritt;
		$heim_erg[2]=$remis+$antritt;
		$heim_erg[3]=$antritt;
		$heim_erg[4]="0";
		$heim_erg[5]=$sieg+$antritt;
		$heim_erg[6]="0";
		$heim_erg[7]="0";
		$heim_erg[8]="0";

	$gast_erg = array();
		$gast_erg[-1]="NULL";
		$gast_erg[0]=$sieg+$antritt;
		$gast_erg[1]=$nieder+$antritt;
		$gast_erg[2]=$remis+$antritt;
		$gast_erg[3]=$antritt;
		$gast_erg[4]=$sieg+$antritt;
		$gast_erg[5]="0";
		$gast_erg[6]="0";
		$gast_erg[7]="0";
		$gast_erg[8]="0";

	// Anzahl kampflose Partien (Heim) z�hlen
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$man_kl=$db->loadObjectList();
	$man_kl_punkte=$man_kl[0]->kl;

	// Anzahl kampflose Partien (Gast) z�hlen
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$gman_kl=$db->loadObjectList();
	$gman_kl_punkte=$gman_kl[0]->kl;

	$count_einzel = 0;
	// Datensätze in Spielertabelle schreiben
	for ($y=1; $y< (1+$stamm) ; $y++){
		$ergebnis	= JRequest::getVar( 'ergebnis'.$y);

	if ($ergebnis > 3) { $kampflos = 1; }
	else { $kampflos = 0; }

	// Wenn Ergebnis nicht verändert dann Original verwenden
	$change = 0;
	if ($ergebnis =="-1") {
	$change = 1;
	$query	= "SELECT ergebnis, kampflos FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$original	= $db->loadObjectList();
	$org_erg 	= $original[0]->ergebnis;
	$kampflos 	= $original[0]->kampflos;
		}
	// Counter für geänderte Einzelergebnisse
	else { $count_einzel++; }

	// Ergebnis verändert, Eingabe verwenden
	// Heim updaten
	$query	= "UPDATE #__clm_rnd_spl ";
	if ($change == "1") {
		$query = $query
		." SET dwz_edit = NULL"
		." , dwz_editor = NULL"
		." , punkte = ".$heim_erg[$org_erg]
		." , kampflos = ".$kampflos;
		} else {
		$query = $query
		." SET dwz_edit = ".$ergebnis
		." , dwz_editor = ".$meldung
		." , punkte = ".$heim_erg[$ergebnis]
		." , kampflos = ".$kampflos;
		}
		$query = $query
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$db->query();

	// Gast updaten
	$query	= "UPDATE #__clm_rnd_spl ";
	if ($change == "1") {
		$query = $query
		." SET dwz_edit = NULL"
		." , dwz_editor = NULL"
		." , punkte = ".$gast_erg[$org_erg]
		." , kampflos = ".$kampflos;
		} else {
		$query = $query
		." SET dwz_edit = ".$ergebnis
		." , dwz_editor = ".$meldung
		." , punkte = ".$gast_erg[$ergebnis]
		." , kampflos = ".$kampflos;
		}
		$query = $query
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();
	}

	// Optionales Mannschaftsergebnis prüfen ggf. Nachricht absetzen
	if($w_erg + $s_erg > $stamm ) {
	JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_ME_HOCH' ) );
	$err=1;
	}
	if($w_erg =="-1" OR $s_erg =="-1" OR $err =="1") {
		if($w_erg =="-1" AND $s_erg !="-1" ) {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_GEAENDERT_HM' ) );
		}
		if($w_erg !="-1" AND $s_erg =="-1" ) {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_GEAENDERT_GM' ) );
		}
		if($count_einzel > 0) {
		JError::raiseNotice( 6000,  JText::_( 'ERGEBNISSE_EE' ));
		}
	// Brettpunkte Heim summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man		= $db->loadObjectList();
	$hmpunkte	= $man[0]->punkte;

	// Wertpunkte Heim berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$hwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$hwpunkte = $hwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	// Brettpunkte Gast summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$gman		= $db->loadObjectList();
	$gmpunkte	= $gman[0]->punkte;
	
	// Wertpunkte Gast berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$gwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$gwpunkte = $gwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	} else {
	$hmpunkte = $w_erg;
	$gmpunkte = $s_erg;
	$hwpunkte = $ww_erg;
	$gwpunkte = $sw_erg;
	}
	// Mannschaftspunkte Heim / Gast
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { $hman_punkte = $man_sieg; $gman_punkte = $man_nieder;}
		if ( $hmpunkte == $gmpunkte ) { $hman_punkte = $man_remis; $gman_punkte = $man_remis;}
		if ( $hmpunkte <  $gmpunkte ) { $hman_punkte = $man_nieder; $gman_punkte = $man_sieg;}
	}
	// erweiterter Standard : mehr als die H�lfte der BP -> Sieg, H�lfte der BP -> halbe MP Zahl
	if ($sieg_bed == 2) {
		if ( $hmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_sieg;}
		if ( $hmpunkte == (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_remis;}
		if ( $hmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_nieder;}
		
		if ( $gmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_sieg;}
		if ( $gmpunkte == (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_remis;}
		if ( $gmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_nieder;}
	}

	// Antrittspunkte addieren falls angetreten
	if ( $stamm > $man_kl_punkte ) { $hman_punkte = $hman_punkte + $man_antritt;}
	if ( $stamm > $gman_kl_punkte ) { $gman_punkte = $gman_punkte + $man_antritt;}

	// Datum und Uhrzeit für Meldung
	$now = $date->toMySQL();

	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man";
		// Wenn nichts geändert wurde (keine Einzelergebnis, keine Mannschaftswertung)
		if($w_erg =="-1" AND $s_erg =="-1" AND $count_einzel =="0") {
			JError::raiseNotice( 6000,  JText::_( 'ERGEBNISSE_TW_GELOESCHT' ));
			$query = $query
			." SET dwz_editor = NULL"
			." , dwz_zeit = '0000-00-00 00:00:00'";
		} else {
			$query = $query
			." SET dwz_editor = ".$meldung
			." , dwz_zeit = '$now'";
			}
		$query = $query
		." , brettpunkte = '".$hmpunkte."'"
		." , manpunkte = '".$hman_punkte."'"
		." , wertpunkte = '".$hwpunkte."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$db->query();

	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man";
		if($w_erg =="-1" AND $s_erg =="-1" AND $count_einzel =="0") {
			$query = $query
			." SET dwz_editor = NULL"
			." , dwz_zeit = '0000-00-00 00:00:00'";
		} else {
			$query = $query
			." SET dwz_editor = ".$meldung
			." , dwz_zeit = '$now'";
			}
		$query = $query
		." , brettpunkte = '".$gmpunkte."'"
		." , manpunkte = '".$gman_punkte."'"
		." , wertpunkte = '".$gwpunkte."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();

	$msg = JText::_( 'ERGEBNISSE_AW' );
	$link = 'index.php?option='.$option.'&section='.$section;

	switch ($task)
	{
		case 'apply_wertung':
		$msg = JText::_( 'ERGEBNISSE_TW_ANGEWENDET' );
		$link = 'index.php?option='.$option.'&section='.$section.'&task=wertung&cid[]='.$id_id;
			break;
		case 'save_wertung':
		default:
		$row =& JTable::getInstance( 'ergebnisse', 'TableCLM' );
		$row->checkin( $id_id);
		$msg = JText::_( 'ERGEBNISSE_TW_GESPEICHERT' );
		$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_VALUATION' );
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paarung, 'dg' => $dg);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg );
	}

function delete_wertung()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		=&JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$user 		=&JFactory::getUser();
	$id_id 		= JRequest::getVar( 'id');
	$date 		=&JFactory::getDate();

	$meldung 	= $user->get('id');
	$sid		= JRequest::getVar( 'sid');
	$lid 		= JRequest::getVar( 'lid');
	$rnd		= JRequest::getVar( 'rnd');
	$paarung	= JRequest::getVar( 'paarung');
	$dg		= JRequest::getVar( 'dg');
	$hzps		= JRequest::getVar( 'hzps');
	$gzps		= JRequest::getVar( 'gzps');

	$liga_sl	=& JTable::getInstance( 'ligen', 'TableCLM' );
	$liga_sl->load( $lid );

	// Prüfen ob User Berechtigung zum löschen hat
	if ( $liga_sl->sl !== CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_DWZ_LOESCHEN' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	// Datum und Uhrzeit für Editorzeit
	$now = $date->toMySQL();

	// Mannschaftsergebnis holen
		$stamm 		= $liga_sl->stamm;
		$sieg 		= $liga_sl->sieg;
		$sieg_bed	= $liga_sl->sieg_bed;
		$remis 		= $liga_sl->remis;
		$nieder		= $liga_sl->nieder;
		$antritt	= $liga_sl->antritt;
		$man_sieg 	= $liga_sl->man_sieg;
		$man_remis 	= $liga_sl->man_remis;
		$man_nieder	= $liga_sl->man_nieder;
		$man_antritt	= $liga_sl->man_antritt;

	// Arrays zur Punktevergabe
	$heim_erg = array();
		$heim_erg[-1]="NULL";
		$heim_erg[0]=$nieder+$antritt;
		$heim_erg[1]=$sieg+$antritt;
		$heim_erg[2]=$remis+$antritt;
		$heim_erg[3]=$antritt;
		$heim_erg[4]="0";
		$heim_erg[5]=$sieg+$antritt;
		$heim_erg[6]="0";
		$heim_erg[7]="0";
		$heim_erg[8]="0";

	$gast_erg = array();
		$gast_erg[-1]="NULL";
		$gast_erg[0]=$sieg+$antritt;
		$gast_erg[1]=$nieder+$antritt;
		$gast_erg[2]=$remis+$antritt;
		$gast_erg[3]=$antritt;
		$gast_erg[4]=$sieg+$antritt;
		$gast_erg[5]="0";
		$gast_erg[6]="0";
		$gast_erg[7]="0";
		$gast_erg[8]="0";

	// Anzahl kampflose Partien (Heim) zählen
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$man_kl=$db->loadObjectList();
	$man_kl_punkte=$man_kl[0]->kl;

	// Anzahl kampflose Partien (Gast) zählen
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$gman_kl=$db->loadObjectList();
	$gman_kl_punkte=$gman_kl[0]->kl;

	// Ergebnisse holen
	$query	= "SELECT ergebnis, brett FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		." ORDER BY brett ASC "
		;
	$db->setQuery($query);
	$original	= $db->loadObjectList();

	// Ergebnisse summieren
	for ($y=0; $y< ($liga_sl->stamm) ; $y++){
	$hmpunkte = $hmpunkte + $heim_erg[$original[$y]->ergebnis];
	$gmpunkte = $gmpunkte + $gast_erg[$original[$y]->ergebnis];
	$hwpunkte = $hwpunkte + ($heim_erg[$original[$y]->ergebnis] * ($liga_sl->stamm + 1 - $original[$y]->brett));
	$gwpunkte = $gwpunkte + ($gast_erg[$original[$y]->ergebnis] * ($liga_sl->stamm + 1 - $original[$y]->brett));
	}
	
	// Mannschaftspunkte Heim / Gast
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { $hman_punkte = $man_sieg; $gman_punkte = $man_nieder;}
		if ( $hmpunkte == $gmpunkte ) { $hman_punkte = $man_remis; $gman_punkte = $man_remis;}
		if ( $hmpunkte <  $gmpunkte ) { $hman_punkte = $man_nieder; $gman_punkte = $man_sieg;}
	}
	// erweiterter Standard : mehr als die Hälfte der BP -> Sieg, Hälfte der BP -> halbe MP Zahl
	if ($sieg_bed == 2) {
		if ( $hmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_sieg;}
		if ( $hmpunkte == (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_remis;}
		if ( $hmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_nieder;}
		
		if ( $gmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_sieg;}
		if ( $gmpunkte == (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_remis;}
		if ( $gmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_nieder;}
	}
	// Antrittspunkte addieren falls angetreten
	if ( $stamm > $man_kl_punkte ) { $hman_punkte = $hman_punkte + $man_antritt;}
	if ( $stamm > $gman_kl_punkte ) { $gman_punkte = $gman_punkte + $man_antritt;}

	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET dwz_editor = NULL"
		." , dwz_zeit = '0000-00-00 00:00:00'"
		." , brettpunkte = '".$hmpunkte."'"
		." , manpunkte = '".$hman_punkte."'"
		." , wertpunkte = '".$hwpunkte."'"
		." , comment = '".$comment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$db->query();

	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET dwz_editor = NULL"
		." , dwz_zeit = '0000-00-00 00:00:00'"
		." , brettpunkte = '".$gmpunkte."'"
		." , manpunkte = '".$gman_punkte."'"
		." , wertpunkte = '".$gwpunkte."'"
		." , comment = '".$comment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();

	// Datensätze in Spielertabelle schreiben
	$query	=" UPDATE #__clm_rnd_spl "
		." SET dwz_editor = NULL"
		." , dwz_edit = NULL"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		;
	$db->setQuery($query);
	$db->query();

	$msg = JText::_( 'ERGEBNISSE_AW_GELOESCHT' );
	$link = 'index.php?option='.$option.'&section='.$section;

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_VALUATION_DEL' );
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paarung, 'dg' => $dg);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg );
	}

function back()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$link = 'index.php?option='.$option.'&section=runden';
	$mainframe->redirect( $link, $msg );
	}

function gast_kampflos()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$link		= 'index.php?option='.$option.'&section='.$section;

	$gast =JText::_( 'ERGEBNISSE_MSG_GUEST' );
	CLMControllerErgebnisse::kampflos($gast);

	$msg	= JText::_( 'ERGEBNISSE_MSG_GUEST_KL' );
	$mainframe->redirect( $link, $msg );
	}

function heim_kampflos()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$link		= 'index.php?option='.$option.'&section='.$section;

	$gast = JText::_( 'ERGEBNISSE_MSG_HOME' );
	CLMControllerErgebnisse::kampflos($gast);

	$msg	= JText::_( 'ERGEBNISSE_MSG_HOME_KL' );
	$mainframe->redirect( $link, $msg );
	}

function kampflos($gast)
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= &JFactory::getDBO();
	$link		= 'index.php?option='.$option.'&section='.$section;
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	JArrayHelper::toInteger($cid);

	// load the row from the db table
	$row =& JTable::getInstance( 'ergebnisse', 'TableCLM' );
	$row->load( $cid[0] );
		$sid	= $row->sid;
		$lid 	= $row->lid;
		$rnd	= $row->runde;
		$paar	= $row->paar;
		$dg	= $row->dg;

	$liga_sl	=& JTable::getInstance( 'ligen', 'TableCLM' );
	$liga_sl->load( $row->lid );
		$bretter	= $liga_sl->stamm;
		$sieg		= $liga_sl->sieg;
		$antritt	= $liga_sl->antritt;
		$man_sieg	= $liga_sl->man_sieg;
		$man_antritt	= $liga_sl->man_antritt;

	if ( $liga_sl->sl !== CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_LIGEN_ARBEIT' ) );
		$mainframe->redirect( $link);
					}

	$query	=" SELECT a.sid,a.lid,a.runde, a.paar,a.dg,a.heim,a.tln_nr, a.gegner, m.zps as hzps, g.zps as gzps FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.lid AND m.sid = a.sid AND m.tln_nr = a.tln_nr "
		." LEFT JOIN #__clm_mannschaften as g ON g.liga = a.lid AND g.sid = a.sid AND g.tln_nr = a.gegner "
		." WHERE a.lid = $lid AND a.runde = $rnd AND a.paar = $paar AND a.dg = $dg"
		." AND ( m.zps =0 OR g.zps = 0) AND a.heim = 1"
		;
	$db->setQuery($query);
	$data	= $db->loadObjectList();

	// Wenn "Spielfrei" kampflos gesetzt wurde
	if ( ($data[0]->hzps =="0" AND $gast == "heim") OR ( $data[0]->gzps =="0" AND $gast == "gast")) {
		JError::raiseWarning( 500, JText::_( 'ERGEBNISSE_SPIELFREI' ) );
		$mainframe->redirect( $link);
					}
	// Datum und Uhrzeit für Meldezeitpunkt
	$date		=&JFactory::getDate();
	$now		= $date->toMySQL();
	$user		=&JFactory::getUser();
	$meldung	= $user->get('id');

	$brett_punkte	= $bretter * ($sieg + $antritt);
	$man_punkte	= $man_sieg + $man_antritt;

	$query	= "UPDATE #__clm_rnd_man"
		." SET brettpunkte = '".$brett_punkte."'"
		." , manpunkte = '".$man_punkte."'"
		." , zeit = '$now'"
		." , gemeldet = '$meldung'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paar
		." AND dg = ".$dg;
	if($gast=="heim") { $query = $query." AND heim = 1 ";}
		else { $query = $query." AND heim = 0 ";}

	$db->setQuery($query);
	$db->query();

	$query	= "UPDATE #__clm_rnd_man"
		." SET brettpunkte = '0'"
		." , manpunkte = '0'"
		." , zeit = '$now'"
		." , gemeldet = '$meldung'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paar
		." AND dg = ".$dg;
	if($gast=="heim") { $query = $query." AND heim = 0 ";}
		else { $query = $query." AND heim = 1 ";}

	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_KL' );
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paar, 'dg' => $dg);
	$clmLog->write();
	
	}

	/**
	* errechnet/aktualisiert Rangliste/Punktesummen eines Turniers
	*/
	function calculateRanking($sid,$liga) {
	
		//echo "calculate sid:".$sid." liga:".$liga; //die();
		$date		=&JFactory::getDate();
		$db			= JFactory::getDBO();
		
		$query = " SELECT a.tln_nr,a.zps as zps, a.sg_zps as sgzps, a.man_nr as man_nr, a.name, "
				." l.teil, l.stamm, l.liga_mt, l.runden_modus, l.man_sieg, l.tiebr1, l.tiebr2, l.tiebr3 "
			." FROM #__clm_mannschaften as a "
			." LEFT JOIN #__clm_liga as l ON l.id =".$liga
			." WHERE a.liga = ".$liga
			." AND a.sid = ".$sid
			." ORDER BY a.tln_nr "
			;
		$db->setQuery($query);
		$team	=$db->loadObjectList();
		$runden_modus	= $team[0]->runden_modus;
		//if ($runden_modus == 1) return;
		$man_sieg		= $team[0]->man_sieg;
		$man_remis		= $team[0]->man_remis;
		$brett_sieg		= $team[0]->sieg;
		$brett_remis	= $team[0]->remis;
		$liga_stamm 	= $team[0]->stamm;
		
		// "spielfrei(e)" Mannschaft suchen
		$query = " SELECT COUNT(id) as anzahl FROM #__clm_mannschaften as a "
			." WHERE a.liga = ".$liga
			." AND a.sid = ".$sid
			." AND a.name = 'spielfrei'"
			." ORDER BY a.tln_nr "
			;
		$db->setQuery($query);
		$spielfreiNumber	=$db->loadObjectList();
		$query = " SELECT a.tln_nr FROM #__clm_mannschaften as a "
			." WHERE a.liga = ".$liga
			." AND a.sid = ".$sid
			." AND a.name = 'spielfrei'"
			." ORDER BY a.tln_nr "
			;
		$db->setQuery($query);
		$spielfreiList	=$db->loadObjectList();
		if (($spielfreiNumber[0]->anzahl >= 1) AND ($runden_modus > 2)) {
			// Datum und Uhrzeit für Meldung
			$now = $date->toMySQL();
			// letzte gemeldete Runde suchen
			$query = "SELECT tln_nr, gegner, brettpunkte, manpunkte, dg, runde FROM `#__clm_rnd_man`"
					. " WHERE lid = ".$liga." AND brettpunkte IS NOT NULL"
					;
			$db->setQuery( $query );
			$maxData = $db->loadObjectList();
			$dg_max	= 0;
			$runde_max	= 0;
			foreach ($maxData as $key => $value) {
				if (($dg_max < $value->dg) OR (($dg_max == $value->dg) AND ($runde_max < $value->runde))) {
					$dg_max	= $value->dg;
					$runde_max	= $value->runde;
				}
			}
			foreach ($spielfreiList as $key => $spielfrei) {
				// Paarungen mit "spielfrei" Mannschaft suchen
				$query = "SELECT a.*, m.zps as zps, n.zps as gzps FROM `#__clm_rnd_man` as a"
					." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.lid AND m.sid = a.sid AND m.tln_nr = a.tln_nr"
					." LEFT JOIN #__clm_mannschaften as n ON n.liga = a.lid AND n.sid = a.sid AND n.tln_nr = a.gegner"
					. " WHERE a.lid = ".$liga
					. " AND a.sid = ".$sid
					. " AND a.tln_nr = ".$spielfrei->tln_nr   //.") OR (a.gegner =".$spielfrei."))"
					;
				if (($runden_modus == 4) OR ($runden_modus == 5))
					$query .= " AND a.dg = ".$dg_max." AND a.runde = ".$runde_max;
				if ($runden_modus == 3)	
					$query .= " AND ((a.dg < ".$dg_max.") OR ( a.dg = ".$dg_max." AND a.runde <= ".$runde_max." ))";
				$db->setQuery( $query );
				$spielfreiData = $db->loadObjectList();
				// Loop über Paarungen mit "spielfrei" Mannschaft
				foreach ($spielfreiData as $key => $value) {
					// Paarungen mit "spielfrei" Mannschaft updaten in clm_rnd_man
					$query = "UPDATE `#__clm_rnd_man`"
						. " SET manpunkte = 0, brettpunkte = 0, gemeldet = 62, zeit = '$now'";
					if (($runden_modus == 4) OR ($runden_modus == 5)) 
						$query .= " , ko_decision = 1";	
					$query .= " WHERE lid = ".$liga." AND sid = ".$sid
						. " AND dg = ".$value->dg." AND runde = ".$value->runde
						. " AND tln_nr = ".$value->tln_nr." AND paar = ".$value->paar
						;
					$db->setQuery($query);
					$db->query();
					
					$query = "UPDATE `#__clm_rnd_man`"
						. " SET manpunkte = ".$man_sieg.", brettpunkte = ".$liga_stamm.", gemeldet = 62, zeit = '$now'";
					if (($runden_modus == 4) OR ($runden_modus == 5)) 
						$query .= " , ko_decision = 1";	
					$query .= " WHERE lid = ".$liga." AND sid = ".$sid
						. " AND dg = ".$value->dg." AND runde = ".$value->runde
						. " AND gegner = ".$value->tln_nr." AND paar = ".$value->paar
						;
					$db->setQuery($query);
					$db->query();
					// KO Turnier: Sieger ist für nächste Runde qualifiziert
					if (($runden_modus == 4) OR ($runden_modus == 5)) {
					$query = "UPDATE `#__clm_mannschaften`"
						. " SET rankingpos = ".$value->runde
						. " WHERE liga = ".$liga." AND sid = ".$sid
						. " AND tln_nr = ".$value->gegner
						;
					$db->setQuery($query);
					$db->query();
					}
					// Paarungen mit "spielfrei" Mannschaften updaten in clm_rnd_spl
					if ($value->heim == 0) {$heim = 0; $gast = 1;}     // Setzen Heim/Gast 
					else {$heim = 1; $gast = 0;}
				  for ($y=1; $y< ($liga_stamm +1) ; $y++){
					if ($y%2 != 0) {$weiss = 0; $schwarz = 1;}		// ungerade Zahl für Weiss/Schwarz 
					else { $weiss = 1; $schwarz = 0;}
					// 1.Satz - zuerst testen, ob satz schon existiert 
					$query = "SELECT COUNT(id) as anzahl FROM `#__clm_rnd_spl`"
						. " WHERE lid = '$liga' AND sid = '$sid'"
						. " AND dg = '$value->dg' AND runde = '$value->runde'"
						. " AND tln_nr = '$value->tln_nr' AND paar = '$value->paar' AND brett = '$y'"
						;
					$db->setQuery( $query );
					$testData = $db->loadObjectList();
					if ($testData[0]->anzahl == 0) {
						$query	= "INSERT INTO #__clm_rnd_spl "
							." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler` "
							." , `zps`, `gegner`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
							." VALUES ('$sid','$liga','$value->runde','$value->paar','$value->dg','$value->tln_nr','$y','$heim','$weiss',0,'$value->zps',"
							." 0,'$value->gzps',8, 1,0,62) "
						;
						$db->setQuery($query);
						$db->query();
					} else {
						$query	= "UPDATE #__clm_rnd_spl "
							. " SET heim = '$heim', weiss = '$weiss', spieler = 0, zps = '$value->zps', gegner = 0, gzps = '$value->gzps',"
							. " ergebnis = 8, kampflos = 1, punkte = 1, gemeldet = 62"
							. " WHERE lid = ".$liga." AND sid = ".$sid
							. " AND dg = ".$value->dg." AND runde = ".$value->runde
							. " AND gegner = ".$value->tln_nr." AND paar = ".$value->paar
						;
						$db->setQuery($query);
						$db->query();
					} 
					// 2.Satz - zuerst testen, ob satz schon existiert 
					$query = "SELECT COUNT(id) as anzahl FROM `#__clm_rnd_spl`"
						. " WHERE lid = '$liga' AND sid = '$sid'"
						. " AND dg = '$value->dg' AND runde = '$value->runde'"
						. " AND tln_nr = '$value->gegner' AND paar = '$value->paar' AND brett = '$y'"
						;
					$db->setQuery( $query );
					$testData = $db->loadObjectList();
					//echo "<br>testData: ".$testData->anzahl; var_dump($testData);
					if ($testData[0]->anzahl == 0) {
						$query	= "INSERT INTO #__clm_rnd_spl "
							." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler` "
							." , `zps`, `gegner`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
							." VALUES ('$sid','$liga','$value->runde','$value->paar','$value->dg','$value->gegner','$y','$gast','$schwarz',0,'$value->gzps',"
							." 0,'$value->zps',8, 1,1,62) "
						;
						$db->setQuery($query);
						$db->query();
					} else {
						$query	= "UPDATE #__clm_rnd_spl "
							. " SET heim = '$gast', weiss = '$schwarz', spieler = 0, zps = '$value->gzps', gegner = 0, gzps = '$value->zps',"
							. " ergebnis = 8, kampflos = 1, punkte = 0, gemeldet = 62"
							. " WHERE lid = ".$liga." AND sid = ".$sid
							. " AND dg = ".$value->dg." AND runde = ".$value->runde
							. " AND gegner = ".$value->tln_nr." AND paar = ".$value->paar
						;
						$db->setQuery($query);
						$db->query();
					} 
				}
			}
		
		} 
	}	

		if (($runden_modus == 4) OR ($runden_modus == 5)) return;
		
		// alle FW in Array schreiben
		$arrayFW = array();
		$arrayFW[1] = $team[0]->tiebr1;
		$arrayFW[2] = $team[0]->tiebr2;
		$arrayFW[3] = $team[0]->tiebr3;
		// für alle Spieler Datensätze mit Summenwert 0 anlegen
		// TODO: da gab es einen eigenen PHP-Befehl für?!
		$array_PlayerMPunkte = array();
		$array_PlayerBPunkte = array();
		$array_PlayerBerlWertung = array();
		$array_PlayerBuch = array();
		$array_PlayerBuchOpp = array();
		$array_PlayerSoBe = array();
		$array_PlayerBuSum = array();
		$array_PlayerWins = array();
		for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
			$array_PlayerMPunkte[$s] = 0;
			$array_PlayerBPunkte[$s] = 0;
			$array_PlayerBerlWertung[$s] = 0;
			$array_PlayerBuch[$s] = 0;
			$array_PlayerSoBe[$s] = 0;
			$array_PlayerBuSum[$s] = 0;
			$array_PlayerWins[$s] = 0;
		}
		
		// alle Matches in DatenArray schreiben
		$query = "SELECT tln_nr, gegner, brettpunkte, manpunkte FROM `#__clm_rnd_man`"
				. " WHERE lid = ".$liga." AND brettpunkte IS NOT NULL"
				;
		$db->setQuery( $query );
		$matchData = $db->loadObjectList();
		// alle Matches in DatenArray schreiben
		$query = "SELECT tln_nr, brett, punkte FROM `#__clm_rnd_spl`"
				. " WHERE lid = ".$liga." AND sid = ".$sid." AND punkte IS NOT NULL"
				;
		$db->setQuery( $query );
		$einzelData = $db->loadObjectList();
		
		// Punkte/Siege
		// alle Matches durchgehen -> Spieler erhalten Punkte und Wins
		foreach ($matchData as $key => $value) {
			if ($value->manpunkte == $man_sieg) { // Mannschaftssieg
				$array_PlayerWins[$value->tln_nr] += 1;
			}
			$array_PlayerMPunkte[$value->tln_nr] += $value->manpunkte;
			$array_PlayerBPunkte[$value->tln_nr] += $value->brettpunkte;
		}
		
		// Berliner Wertung
		// alle Einzels durchgehen -> Mannschaften erhalten Wertpunkte
		foreach ($einzelData as $key => $valuee) {
			$array_PlayerBerlWertung[$valuee->tln_nr] += $valuee->punkte * ($liga_stamm + 1 - $valuee->brett);
		}
	
		// Buchholz & Sonneborn-Berger
		// erneut alle Matches durchgehen -> Spieler erhalten Feinwertungen
		foreach ($matchData as $key => $value) {
			// Buchholz
			if (in_array(1, $arrayFW) OR in_array(2, $arrayFW) OR in_array(11, $arrayFW)) { // beliebige Buchholz als TieBreaker gewünscht?
				$array_PlayerBuchOpp[$value->tln_nr][] = $array_PlayerBPunkte[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
			}
			
			// Sonneborn-Berger
			if (in_array(3, $arrayFW)) { // SoBe als ein TieBreaker gewünscht?
				if ($value->manpunkte == $man_remis) { // remis
					$array_PlayerSoBe[$value->tln_nr] += ($array_PlayerBPunkte[$value->gegner]/2);
				} elseif ($value->manpunkte == $man_sieg) { // Sieger
					$array_PlayerSoBe[$value->tln_nr] += $array_PlayerBPunkte[$value->gegner];
				}
			}
		}
	
		// Buchholz
		if (in_array(1, $arrayFW)) { // normale Buchholz als TieBreaker gewünscht?
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
			}
		} elseif (in_array(11, $arrayFW)) { // Buchholz mit Streichresultat
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]);
			}
		}
	
		// BuchholzSumme
		if (in_array(2, $arrayFW)) { // Buchholz-Summe als TieBreaker gewünscht?
			// erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen
			foreach ($matchData as $key => $value) {
				//echo "<br>matchdata: "; var_dump($value);
				//echo "<br>BuSum: "; var_dump($array_PlayerBuSum);
				//echo "<br>Buch: "; var_dump($array_PlayerBuch);
				$array_PlayerBuSum[$value->tln_nr] += $array_PlayerBuch[$value->gegner];
			}
		}
		
		// alle Spieler durchgehen und updaten (kein vorheriges Löschen notwendig)
		for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
			// den TiebrSummen ihre Werte zuordnen
			for ($tb=1; $tb<=3; $tb++) {
				$sumTiebr[$tb] = 0;
				switch ($arrayFW[$tb]) {
					case 1: // buchholz
						$sumTiebr[$tb] = $array_PlayerBuch[$s];
						break;
					case 2: // bhhlz.-summe
						$sumTiebr[$tb] = $array_PlayerBuSum[$s];
						break;
					case 3: // sobe
						$sumTiebr[$tb] = $array_PlayerSoBe[$s];
						break;
					case 4: // wins
						$sumTiebr[$tb] = $array_PlayerWins[$s];
						break;
					case 5: // brettpunkte
						$sumTiebr[$tb] = $array_PlayerBPunkte[$s];
						break;
					case 6: // berliner wertung
						$sumTiebr[$tb] = $array_PlayerBerlWertung[$s];
						break;
					case 11: // bhhlz mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuch[$s];
						break;
					default:
						$sumTiebr[$tb] = 0;
				}
			}
			$query = "UPDATE #__clm_mannschaften"
					. " SET summanpunkte = ".$array_PlayerMPunkte[$s].", sumbrettpunkte = ".$array_PlayerBPunkte[$s].", sumwins = ".$array_PlayerWins[$s].", "
					. " sumTiebr1 = ".$sumTiebr[1].", sumTiebr2 = ".$sumTiebr[2].", sumTiebr3 = ".$sumTiebr[3]
					. " WHERE liga = ".$liga
					. " AND sid = ".$sid
					. " AND tln_nr = ".$s
					;
			$db->setQuery($query);
			$db->query();
			
		}
	//}	//die();
	// function setRankingPositions() {
	
		$query = "SELECT id"
			." FROM `#__clm_mannschaften`"
			." WHERE liga = ".$liga
			." AND sid = ".$sid
			." ORDER BY summanpunkte DESC, sumtiebr1 DESC, sumtiebr2 DESC, sumtiebr3 DESC, tln_nr ASC"
			;
		
		$db->setQuery( $query );
		$players = $db->loadObjectList();		 
		$table	=& JTable::getInstance( 'mannschaften', 'TableCLM' );
		// rankingPos umsortieren
		$rankingPos = 0;
		// alle Spieler durchgehen
		foreach ($players as $value) {
			$table->load($value->id);
			if ($table->name != "spielfrei") {
				$rankingPos++;
				$table->rankingpos = $rankingPos; }
			else {$table->rankingpos = 0; }
			$table->store();
		} 
	}

}