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

class CLMControllerMTurniere extends JController
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add','edit' );
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish','publish' );
	}

function display()
	{
	global $mainframe, $option;
	$section	= JRequest::getVar('section');
	$db		= & JFactory::getDBO();

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' a.liga_mt = 1'; //mtmt
	$where[]=' c.archiv = 0';
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ($search) {	$where[] = 'LOWER(a.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );	}

	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}

	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY c.id '.$filter_order_Dir.', id';
	} else {
	if ($filter_order =='a.name' OR $filter_order == 'a.teil' OR $filter_order == 'a.runden' OR $filter_order == 'a.durchgang' OR $filter_order == 'a.stamm' OR $filter_order == 'a.ersatz' OR $filter_order == 'a.sl' OR $filter_order == 'c.name' OR $filter_order == 'a.mail' OR $filter_order == 'a.rnd' OR $filter_order == 'a.published' OR $filter_order == 'a.ordering') { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; }
	}

	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_liga AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. $where
		;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT a.*, c.name AS saison,c.published as saison_publish, u.name AS editor'
		. ' FROM #__clm_liga AS a'
		. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
		.$where.$orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// Statusfilter
	$lists['state']	= JHTML::_('grid.state',  $filter_state );

	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'LIGEN_SAISON' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );

	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// Suchefilter
	$lists['search']= $search;

	require_once(JPATH_COMPONENT.DS.'views'.DS.'mturniere.php');
	CLMViewMTurniere::mturniere( $rows, $lists, $pageNav, $option );
	}


function edit()
	{
	global $mainframe, $option;

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	$row 		=& JTable::getInstance( 'ligen', 'TableCLM' );
	JArrayHelper::toInteger($cid, array(0));
	
	// load the row from the db table
	$row->load( $cid[0] );

	if (CLM_usertype !== 'admin' AND $task =='add') {
		JError::raiseWarning( 500, JText::_( 'LIGEN_ADMIN' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}
	if ($task == 'edit') {
	// Prüfen ob User Berechtigung zum editieren hat
	$saison		=& JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $row->sid );
	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	if ($saison->archiv == "1" AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MTURN_ARCHIV' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}
	// Keine SL oder Admin
	if ( $row->sl !== CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MTURN_STAFFEL' ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// Neue ID
		$row->published	= 0;
	}

	// Listen
	// Heimrecht vertauscht
	$lists['heim']	= JHTML::_('select.booleanlist',  'heim', 'class="inputbox"', $row->heim );
	// Published
	$lists['published']	= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );
	// automat. Mail
	$lists['mail']	= JHTML::_('select.booleanlist',  'mail', 'class="inputbox"', $row->mail );
	// Staffelleitermail als BCC
	$lists['sl_mail']	= JHTML::_('select.booleanlist',  'sl_mail', 'class="inputbox"', $row->sl_mail );
	// Ordering für Rangliste
	$lists['order']	= JHTML::_('select.booleanlist',  'order', 'class="inputbox"', $row->order );
	// SL Liste
	$tql =  " SELECT a.jid,a.name  FROM #__clm_user as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE (a.usertype = 'sl' OR a.usertype = 'dv' OR a.usertype = 'dwz' OR a.usertype = 'admin' )"
		." AND a.published = 1 AND s.published = 1 AND s.archiv =0"
		;
	$db->setQuery($tql);
	if (!$db->query()){ $this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() ); }
	$sllist[]	= JHTML::_('select.option',  '0', JText::_( 'MTURN_TL' ), 'jid', 'name' );
	$sllist		= array_merge( $sllist, $db->loadObjectList() );
	$lists['sl']	= JHTML::_('select.genericlist',   $sllist, 'sl', 'class="inputbox" size="1"', 'jid', 'name', $row->sl );
	// Saisonliste
	$sql = "SELECT id as sid, name FROM #__clm_saison WHERE archiv = 0";
	$db->setQuery($sql);
	if (!$db->query()){ $this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() ); }
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'LIGEN_SAISON' ), 'sid', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
	$lists['saison']= JHTML::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $row->sid );
	// Rangliste
	$query = " SELECT id, Gruppe FROM #__clm_rangliste_name ";
	$db->setQuery($query);
	if (!$db->query()){ $this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() ); }
	$glist[]	= JHTML::_('select.option',  '0', JText::_( 'LIGEN_ML' ), 'id', 'Gruppe' );
	$glist		= array_merge( $glist, $db->loadObjectList() );
	$lists['gruppe']= JHTML::_('select.genericlist',   $glist, 'rang', 'class="inputbox" size="1"', 'id', 'Gruppe', $row->rang );

	require_once(JPATH_COMPONENT.DS.'views'.DS.'mturniere.php');
	CLMViewMTurniere::mturnier( $row, $lists, $option );
	}


function save()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= & JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		= & JTable::getInstance( 'ligen', 'TableCLM' );
	$msg		= JRequest::getVar( 'id');
	$sid_alt	= JRequest::getVar( 'sid_alt');
	$sid		= JRequest::getVar( 'sid');
	 
	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	//Liga-Parameter zusammenfassen
	$paramsStringArray = array();
	foreach ($row->params as $key => $value) {
		$paramsStringArray[] = $key.'='.intval($value);
	}
	$row->params = implode("\n", $paramsStringArray);
	
	// pre-save checks
	if (!$row->check()) { JError::raiseError(500, $row->getError() ); }

	$teil	= $row->teil;

	// if new item, order last in appropriate group
	$aktion = JText::_( 'LIGEN_AKTION_LEAGUE_EDIT' );
	if (!$row->id) {
	$neu_id = 1;
	$aktion = JText::_( 'LIGEN_AKTION_NEW_LEAGUE' );
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );

	// Bei ungerader Anzahl Mannschaften Teilnehmerzahl um 1 erhöhen
	if (($row->runden_modus != 4) AND ($row->runden_modus != 5)) { // vollrundig, Schweizer System
	if (($row->teil)%2 != 0) {
		$ungerade_id	= 1;
		$row->teil	= $row->teil+1;
		$tln		= $row->teil;
	JError::raiseWarning(500, JText::_( 'LIGEN_MANNSCH', true ) );
		}	}	
	if ($row->runden_modus == 4) {
		$ko_id = 0;
		$tln_ko	= $row->teil;
		while ($row->teil < pow(2,$row->runden)) { $ko_id++; $row->teil = $row->teil+1;}
		if ($ko_id > 0)  JError::raiseWarning(500, JText::_( 'MTURN_MANNSCH_KO', true ) ); 
			}	
	if ($row->runden_modus == 5) {
		$ko_id = 0;
		$tln_ko	= $row->teil;
		while ($row->teil < pow(2,$row->runden-1)) { $ko_id++; $row->teil = $row->teil+1;}
		if ($ko_id > 0)  JError::raiseWarning(500, JText::_( 'MTURN_MANNSCH_KO', true ) ); 
		}
 
	}
	$row->liga_mt	= 1; //mtmt 0 = liga  1 = mannschaftsturnier
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
		}
	$liga_man	= $row->id;
	$liga_rnd	= $row->runden;
	$liga_dg	= $row->durchgang;
	$publish	= $row->published;

	// Wenn sid gewechselt wurde, alle Daten in neue Saison verschieben
	if ($sid_alt != $sid AND $sid_alt != "") {
	JError::raiseNotice( 6000,  JText::_( 'LIGEN_SAISON_AEND' ));
	$query = " UPDATE #__clm_mannschaften "
		." SET sid = ".$sid
		." WHERE liga = ".$liga_man
		." AND sid = ".$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = " UPDATE #__clm_meldeliste_spieler "
		." SET sid = ".$sid
		." WHERE lid = ".$liga_man
		." AND sid = ".$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = " UPDATE #__clm_rnd_man "
		." SET sid = ".$sid
		." WHERE lid = ".$liga_man
		." AND sid = ".$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = " UPDATE #__clm_rnd_spl "
		." SET sid = ".$sid
		." WHERE lid = ".$liga_man
		." AND sid = ".$sid_alt
		;
	$db->setQuery($query);
	$db->query();

	$query = " UPDATE #__clm_runden_termine "
		." SET sid = ".$sid
		." WHERE liga = ".$liga_man
		." AND sid = ".$sid_alt
		;
	$db->setQuery($query);
	$db->query();
	}
	
	// Bei ungerader Anzahl Mannschaften "spielfrei" hinzufügen
	if (($row->runden_modus != 4) AND ($row->runden_modus != 5)) { // vollrundig, Schweizer System
	if ($ungerade_id == "1") {

	$query = " INSERT INTO #__clm_mannschaften "
		." ( `sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`) "
		." VALUES ('$sid','spielfrei','$liga_man','0','0','62','0','$tln','0') "
		;
	$db->setQuery($query);
	$db->query();
	JError::raiseNotice( 6000,  JText::_( 'LIGEN_MANNSCH_1' ));
		}}
	// Bei KO-System  x Mannschaften "spielfrei" hinzufügen, wenn nötig
	if (($row->runden_modus == 4) OR ($row->runden_modus == 5)) { // KO System
	for($x=1; $x< 1+$ko_id; $x++) {
	$tln_ko++;
	$query = " INSERT INTO #__clm_mannschaften "
		." ( `sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`) "
		." VALUES ('$sid','spielfrei','$liga_man','0','0','62','0','$tln_ko','0') "
		;
	$db->setQuery($query);
	$db->query();
		}
	if ($ko_id > 0) JError::raiseNotice( 6000,  $ko_id.JText::_( 'MTURN_MANNSCH_KO_1' ));
		}
	
	// Mannschaftsrunden anlegen
	if ($neu_id == "1") {
		CLMControllerMTurniere::runden($liga_man);

	// Mannschaften anlegen
	for($x=1; $x< 1+$teil; $x++) {
	$man_name = JText::_( 'LIGEN_STD_TEAM' )." ".$x;
	if ($x < 10) $man_nr = $liga_man.'0'.$x; else $man_nr = $liga_man.$x;
	$query = " INSERT INTO #__clm_mannschaften "
		." (`sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`,`published`) "
		." VALUES ('$sid','$man_name','$liga_man','1','0','0','$man_nr','$x','0','$publish') "
		;
	$db->setQuery($query);
	$db->query();
				}

	// Runden (Termine) anlegen
	for($y=1; $y< 1+$liga_dg; $y++) {
	for($x=1; $x< 1+$liga_rnd; $x++) {

	$nr	= $x + ($y-1)*$liga_rnd;
	$name	= JText::_( 'LIGEN_STD_ROUND' )." ".$x;
	if ($liga_dg > 1) {
		if ($y == 1) $name .= " (".JText::_( 'LIGEN_STD_HIN' ).")";
		if ($y == 2) $name .= " (".JText::_( 'LIGEN_STD_RUECK' ).")";
	}
	if ($row->runden_modus == 4) { // KO System
	   $name = JText::_( 'ROUND_KO_'.($liga_rnd - $x +1)); }
	if (($row->runden_modus == 5) AND $x < $liga_rnd  ) { // KO System
	   $name = JText::_( 'ROUND_KO_'.($liga_rnd - $x)); }
	if (($row->runden_modus == 5) AND $x == $liga_rnd  ) { // KO System
	   $name = JText::_( 'ROUND_KO_S3' ); }
	$query = " INSERT INTO #__clm_runden_termine "
		." (`sid`,`name`,`liga`,`nr`,`meldung`,`sl_ok`,`published` ) "
		." VALUES ('$sid','$name','$liga_man','$nr','0','0','$publish') "
		;
	$db->setQuery($query);
	$db->query();
				}}}

	$row->checkin();
	
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.'ergebnisse.php');
	CLMControllerErgebnisse::calculateRanking($sid,$liga_man);
	
	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'LIGEN_AENDERN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'LIGEN_LIGA' );
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid, 'lid' => $row->id);
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
	$row 		=& JTable::getInstance( 'ligen', 'TableCLM' );
	$row->checkin( $id);

	$msg = JText::_( 'LIGEN_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
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
	JArrayHelper::toInteger($cid);
	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );

	// Prüfen ob User Berechtigung zum löschen hat
	if ( CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MTURN_ADMIN_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	// Prüfen ob Runden gelöscht wurden
	if ($row->rnd > 0 ) {
		JError::raiseWarning(500, JText::_( 'LIGEN_RUND', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'LIGEN_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// evtl. spätere Funktion : mehrere Ligen löschen
		// $cids = implode( ',', $cid );
		// $query = 'DELETE FROM #__clm_liga'
		// ' WHERE id IN ( '. $cids .' )';

	// Ligafilter auf 0 setzen, falls gelöschte Liga das Filterkriterium ist
	$filter_lid	= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	if ($filter_lid == $row->id) { $mainframe->setUserState( "$option.filter_lid", "0" ); }

	// JIDs der Mannschaftsführer sammeln und auf unpublished setzen.
	$query = " SELECT mf FROM #__clm_mannschaften "
		." WHERE liga = ".$row->id
		;
	$db->setQuery($query);
	$mf_jid = $db->loadObjectList();

	foreach($mf_jid as $mf_jid) {
		if ($mf_jid->mf != "") {
		$query	= "UPDATE #__clm_user "
			." SET published = 0 "
			." WHERE jid = ".$mf_jid->mf
			." AND sid =".$row->sid
			." AND user_clm < 80 "
			;
		$db->setQuery($query);
		$db->query();
		
		$query	= "UPDATE #__users "
			." SET block = 1 "
			." WHERE id = ".$mf_jid->mf
			." AND gid < 24 "
			;
		$db->setQuery($query);
		$db->query();
	}}
	// Staffelleiter auf unpublish setzen
	if ($mf_jid->mf != "") {
		$query	= "UPDATE #__clm_user "
			." SET published = 0 "
			." WHERE jid = ".$row->sl
			." AND sid =".$row->sid
			." AND user_clm < 80 "
			;
		$db->setQuery($query);
		$db->query();

		$query	= "UPDATE #__users "
			." SET block = 1 "
			." WHERE id = ".$row->sl
			." AND gid < 24 "
			;
		$db->setQuery($query);
		$db->query();
		}
	// Datensätze löschen
	$query = " DELETE FROM #__clm_liga "
		." WHERE id = ".$cid[0]
		;
	$db->setQuery( $query );
	if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
	}

	$sql = "DELETE FROM #__clm_mannschaften "
		."WHERE liga = ".$cid[0]
		." AND sid = ".$row->sid
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_meldeliste_spieler "
		."WHERE lid = ".$cid[0]
		." AND sid = ".$row->sid
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_rnd_man "
		."WHERE lid = ".$cid[0]
		." AND sid = ".$row->sid
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_rnd_spl "
		."WHERE lid = ".$cid[0]
		." AND sid = ".$row->sid
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_runden_termine "
		."WHERE liga = ".$cid[0]
		." AND sid = ".$row->sid
		;
	$db->setQuery( $sql );
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'LIGEN_AKTION_LEAGUE_DEL' );
	$clmLog->params = array('cids' => $cids, 'lid' => $cid[0]);
	$clmLog->write();
	

	JError::raiseNotice( 6000,  JText::_( 'MTURN_ACCOUNTS' ));
	$msg = JText::_( 'MTURN_MSG_ALL_DATA' );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg );
	}


function publish()
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$cid		= JRequest::getVar('cid', array(), '', 'array');
	$task		= JRequest::getCmd( 'task' );
	$publish	= ($task == 'publish');
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);
	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );
	// Prüfen ob User Berechtigung zum publishen hat
	if ( $row->sl !== CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MTURN_STAFF_VER' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	if (empty( $cid )) {
		JError::raiseWarning( 500, 'No items selected' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	$cids = implode( ',', $cid );
	$query = 'UPDATE #__clm_liga'
	. ' SET published = '.(int) $publish
	. ' WHERE id IN ( '. $cids .' )'
	. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
	$db->setQuery( $query );
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() );
	}
	if (count( $cid ) == 1) {
		$row =& JTable::getInstance( 'ligen', 'TableCLM' );
		$row->checkin( $cid[0] );
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MTURN_AKTION_LEAGUE' )." ".$task;
	$table		=& JTable::getInstance( 'ligen', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('cids' => $cids, 'lid' => $table->id, 'sid' => $table->sid);
	$clmLog->write();
	

	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section);
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	CLMControllerMTurniere::order( 1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	CLMControllerMTurniere::order( -1 );
}

/**
* Moves the order of a record
* @param integer The direction to reorder, +1 down, -1 up
*/
function order( $inc )
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db		=& JFactory::getDBO();
	$cid		= JRequest::getVar('cid', array(0), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid, array(0));

	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart 	= JRequest::getVar( 'limitstart', 0, '', 'int' );

	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	$row->load( $cid[0] );
	$row->move( $inc, 'sid = '.(int) $row->sid.' AND published != 0' );

	$msg 	= JText::_( 'LIGEN_MSG_SORT' );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}

/**
* Saves user reordering entry
*/
function saveOrder(  )
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db		=& JFactory::getDBO();
	$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	$total		= count( $cid );
	$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
	JArrayHelper::toInteger($order, array(0));

	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->saison;

		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				JError::raiseError(500, $db->getErrorMsg() );
			}
		}
	}
	// execute updateOrder for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
		$row->reorder('saison = '.(int) $group);
	}
	$msg 	= 'New ordering saved';
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

function copy()
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db		= & JFactory::getDBO();
	$table		= & JTable::getInstance('ligen', 'TableCLM');
	$user		= &JFactory::getUser();
	$n		= count( $cid );
	$this->setRedirect( 'index.php?option='.$option.'&section=ligen' );

	if ( CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'LIGEN_KOPIE' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	if ($n > 0)
	{
		foreach ($cid as $id)
		{
			if ($table->load( (int)$id ))
			{
			$table->id			= 0;
			$table->name			= 'Kopie von ' . $table->name;
			$table->published		= 0;
			$table->rnd			= 0;

			if (!$table->store()) {	return JError::raiseWarning( $table->getError() );}
			}
		else {	return JError::raiseWarning( 500, $table->getError() );	}
		}
	}
	else {	return JError::raiseWarning( 500, JText::_( 'LIGEN_ITEMS' ) );}

	if ($n >1) { $msg1=JText::_( 'LIGEN_AKTION_ENTRYS' );}
		else {$msg1=JText::_( 'LIGEN_AKTION_ENTRY' );}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MTURN_AKTION_LEAGUE_COPY' );
	$table		=& JTable::getInstance( 'ligen', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('cids' => implode( ',', $cid ), 'lid' => $table->id, 'sid' => $table->sid);
	$clmLog->write();
	

	$msg = JText::_( $n." ".$msg1 );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg);
	}

function runden($liga_neu)
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db		= & JFactory::getDBO();
	$n		= count( $cid );

	if ( CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'LIGEN_NO_RUND' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	if ($n > 0 OR $liga_neu !="")
	{
	// Ligadaten und Paarungsdaten holen
	if ($liga_neu != "") { $where = $liga_neu; }
	else { $where = $cid[0]; }
	$query = " SELECT a.id as lid,a.sid,a.teil,a.durchgang,a.rnd, a.heim, a.runden, a.runden_modus "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$where
		;
	$db->setQuery($query);
	$liga=$db->loadObjectList();

		$lid 		= $liga[0]->lid;
		$sid 		= $liga[0]->sid;
		$teil 		= $liga[0]->teil;
		$dg 		= $liga[0]->durchgang;
		$heimrecht 	= $liga[0]->heim;
		$runden 	= $liga[0]->runden;
		$rnd_mode 	= $liga[0]->runden_modus;

	// Prüfen ob Runden schon erstellt wurden
	if ( $liga[0]->rnd == 1 ) {
	JError::raiseWarning( 500, JText::_( 'LIGEN_RUND_IST' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
			}

	$n = $teil; // Anzahl Spieler
	if ($n%2 != 0) { $n++; }  // Anzahl gerade machen

	for ($dg_dg = 1; $dg_dg < 1+$dg; $dg_dg++) {
	$y = 1;
	if ($dg_dg %2 != 0) {
		$dgh = 1;
		$dgg = 0;
			}
	else {
		$dgh = 0;
		$dgg = 1;
		}
	switch ($rnd_mode) {
	case 1:
	case 2:
	// Modus festlegen 1 = Normal; 2 = zentrale Endrunde
		if($rnd_mode == "1") {$rnd_one = 1;}
		if($rnd_mode == "2") {$rnd_one = $runden;}
	// Runde 1
		for ($f = 1; $f < 1+$n/2; $f++) {
			if ( $heimrecht == 0) {
				$heim = $f;
				$gast = $n-$f+1;
				}
		else {
				$heim = $n-$f+1;
				$gast = $f;
			}
			$query	= "INSERT INTO #__clm_rnd_man "
			." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
			." VALUES ('$sid','$lid','$rnd_one','$f','$dg_dg','$dgh','$heim','$gast'), "
			." ('$sid','$lid','$rnd_one','$f','$dg_dg','$dgg','$gast','$heim' )"
			;
	
			$db->setQuery($query);
			if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
		}
	// Ende Runde 1
	
	for ($p = 2; $p < $n; $p++ ) {

	// Modus festlegen 1 = Normal; 2 = zentrale Enderunde
		if($rnd_mode == "1") {$rnd_cnt = $p;}
		if($rnd_mode == "2") {$rnd_cnt = $p-1;}

	// Paarungsschleife
	if ($p%2 != 0) { $gerade = 0; $y++; }
		else { $gerade = 1; }
	///////////////
	// 1.Element //
	///////////////
		if ( $gerade == 0 ) {
			if ( $heimrecht == 0) {
				$heim = $y;
				$gast = $n;
				}
			else {
				$heim = $n;
				$gast = $y;
				}
				}
		else {
			if ( $heimrecht == 0) {
				$heim = $n;
				$gast = ($n/2)+$y;
					}
			else {
				$heim = ($n/2)+$y;
				$gast = $n;
			}
			}
		$query	= "INSERT INTO #__clm_rnd_man "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
		." VALUES ('$sid','$lid','$rnd_cnt','1','$dg_dg','$dgh','$heim','$gast'), "
		." ('$sid','$lid','$rnd_cnt','1','$dg_dg','$dgg','$gast','$heim' )"
		;
		$db->setQuery($query);
		if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
	
	///////////////////
	// ab 2. Element //
	///////////////////
	
	// ungerade Runde
	if ( $gerade == 0 ) {
		for ($z = 2; $z < ($y+1); $z++) {
			if ( $heimrecht == 0) {
				$heim = $z+$y-1;
				$gast = $p-$z-$y+2;
					}
			else {
				$heim = $p-$z-$y+2;
				$gast = $z+$y-1;
			}
		$query	= "INSERT INTO #__clm_rnd_man "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
		." VALUES ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast'), "
		." ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' )"
		;
		$db->setQuery($query);
		if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
						}
		for ($z = ($y+1); $z < (($n/2)+1); $z++) {
			if ( $heimrecht == 0) {
				$heim = $z+$y-1;
				$gast = $n+$p-$z-$y+1;
				}
			else {
				$heim = $n+$p-$z-$y+1;
				$gast = $z+$y-1;
				}
		$query	= "INSERT INTO #__clm_rnd_man "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
		." VALUES ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast'), "
		." ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' )"
		;
		$db->setQuery($query);
		if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
						}
		}
	// gerade Runde //
	else {
		for ($z = 2; $z < (($n/2)-$y+1); $z++) {
			if ( $heimrecht == 0) {
				$heim = ($n/2)+$y+$z-1;
				$gast = ($n/2)+$y-$z+1;
				}
			else {
				$heim = ($n/2)+$y-$z+1;
				$gast = ($n/2)+$y+$z-1;
				}
		$query	= "INSERT INTO #__clm_rnd_man "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
		." VALUES ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast'), "
		." ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' )"
		;
		$db->setQuery($query);
		if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
							}
		for ($z = (($n/2)-$y+1); $z < ($n/2)+1; $z++) {
			if ( $heimrecht == 0) {
				$heim = $p-($n/2)-$y+$z;
				$gast = ($n/2)+$y-$z+1;
				}
			else {
				$heim = ($n/2)+$y-$z+1;
				$gast = $p-($n/2)-$y+$z;
				}
		$query	= "INSERT INTO #__clm_rnd_man "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
		." VALUES ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast'), "
		." ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' )"
		;
		$db->setQuery($query);
		if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
							}
		}
	}
		break;
	case 3:      //Schweizer System mtmt
		$rnd_cnt = 1;
	// Runde 1
		for ($f = 1; $f < 1+$n/2; $f++) {
			if ($f%2 != 0) {
				$heim = $f;
				$gast = $n/2+$f; }
			else {
				$gast = $f;
				$heim = $n/2+$f; }
			$query	= "INSERT INTO #__clm_rnd_man "
			." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
			." VALUES ('$sid','$lid','$rnd_cnt','$f','$dg_dg','$dgh','$heim','$gast'), "
			." ('$sid','$lid','$rnd_cnt','$f','$dg_dg','$dgg','$gast','$heim' )"
			;
	
			$db->setQuery($query);
			if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
		}
	// Ende Runde 1
		break;
	case 4:      //KO System ohne kleines Finale
	case 5:      //KO System mit kleinem Finale
		$rnd_nn = 0;  // notwendige KO Runden für nn Teilnehmer
		while ($n > (pow(2,$rnd_nn))) { 
			$rnd_nn++; }
		$nn = pow(2,$rnd_nn);   // Anzahl auf Potenz von 2 setzen
		$rnd_cnt = 0;
		while ($rnd_nn > $rnd_cnt) {
			$rnd_cnt++;
			for ($f = 1; $f < 1+pow(2,($rnd_nn - $rnd_cnt)); $f++) {
				if ($rnd_cnt == 1) { $heim = $f;
									 $gast = pow(2,$rnd_nn +1 - $rnd_cnt) + 1 -$f; }
				else { $heim = 0;
					   $gast = 0; }
				$query	= "INSERT INTO #__clm_rnd_man "
				." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
				." VALUES ('$sid','$lid','$rnd_cnt','$f','$dg_dg','$dgh','$heim','$gast'), "
				." ('$sid','$lid','$rnd_cnt','$f','$dg_dg','$dgg','$gast','$heim' )"
				;
			$db->setQuery($query);
			if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
			}
		}
	    if ($rnd_mode == 5) {      //KO System mit kleinem Finale
			$rnd_cnt++;  // zusätzliche Runde
			$f = 1;      // kleines Finale ist einzige Paarung in zus. Runde 
			$query	= "INSERT INTO #__clm_rnd_man "
				." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
				." VALUES ('$sid','$lid','$rnd_cnt','$f','$dg_dg','$dgh','$heim','$gast'), "
				." ('$sid','$lid','$rnd_cnt','$f','$dg_dg','$dgg','$gast','$heim' )"
				;	
			$db->setQuery($query);
			if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
		}
 
	// Ende Runde 1
		break;
	
	default:
		die("Fehler");
	}
	//neu: 1.1.16
	//Anlegen leerer Runden, 
	//wenn die Rundenanzahl im Ligastammsatz die nötige Rundenzahl entspr. Teilnehmerzahl überschreitet
	//Das ermöglicht das manuelle Nachpflegen, was auch zwingend in einen solchen Fall notwendig ist
	if (($dg == 1) AND ($rnd_mode == "1")) {    //nur für Ligen mit einem Durchgängen zugelassen
												//nur für Standardmodus nach FIDE-Tabelle zugelassen
	while ($rnd_cnt < $runden)
	{
		$rnd_cnt++;
		for ($z = 1; $z < ($n/2)+1; $z++) {
			$query	= "INSERT INTO #__clm_rnd_man "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` ) "
		." VALUES ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgh',0,0), "
		." ('$sid','$lid','$rnd_cnt','$z','$dg_dg','$dgg',0,0 )"
		;
		$db->setQuery($query);
		if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
		}
	}		
	}
	}
	
	// Rundenbyte setzen
	$query	= "UPDATE #__clm_liga "
		." SET rnd = '1' "
		." WHERE id = $lid "
		;
	$db->setQuery($query);
	$db->query();
	}
	else {
		JError::raiseWarning( 500, JText::_( 'LIGEN_ITEMS' ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg );
	}

	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'LIGEN_AKTION_LEAGUE_ROUNDS_CREATED' );
	$table		=& JTable::getInstance( 'ligen', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('cids' => implode( ',', $cid ), 'lid' => $table->id, 'sid' => $table->sid);
	$clmLog->write();


	if($liga_neu == "") {
	$msg 	= JText::_( 'LIGEN_MSG_ROUNDS_CREATED' ).' '.$msg;
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg );
	}
	}

function del_runden()
	{
	global $mainframe;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db		=& JFactory::getDBO();
	$n		= count( $cid );

	if ( CLM_usertype !== 'admin' ) {
		JError::raiseWarning( 500, JText::_( 'LIGEN_NO_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	if ($n > 0)
	{
	// Ligadaten und Paarungsdaten holen
	$query	= "SELECT a.id as lid,a.sid,a.teil,durchgang,a.rnd "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery($query);
	$liga=$db->loadObjectList();
		$lid = $liga[0]->lid;
		$sid = $liga[0]->sid;
		
	// Daten löschen in clm_rnd_man
	$query	= "DELETE FROM #__clm_rnd_man "
		." WHERE lid = ".$lid
		." AND sid = ".$sid
		;
	$db->setQuery($query);
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }
	//neu: 1.1.16
	// Daten löschen in clm_rnd_spl
	$query	= "DELETE FROM #__clm_rnd_spl "
		." WHERE lid = ".$lid
		." AND sid = ".$sid
		;
	$db->setQuery($query);
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() ); }

	// Rundenbyte setzen
	$query	= "UPDATE #__clm_liga "
		." SET rnd = '0' "
		." WHERE id = $lid "
		;
	$db->setQuery($query);
	$db->query();
	}
	else {
		JError::raiseWarning( 500, JText::_( 'LIGEN_ITEMS' ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg );
		}
	
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'LIGEN_AKTION_LEAGUE_ROUNDS_DEL' );
	$table		=& JTable::getInstance( 'ligen', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('cids' => implode( ',', $cid ), 'lid' => $table->id, 'sid' => $table->sid);
	$clmLog->write();
	

	$msg 	= JText::_( 'LIGEN_AKTION_LEAGUE_ROUNDS_DEL' );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg);
	}

function paarung()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	global $mainframe;

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));
	// kein Verein gewählt
	if ($cid[0] < 1) {
	$msg = JText::_( 'MTURN_PA_AENDERN');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
	// Link MUSS hardcodiert sein !!!
	$mainframe->redirect( 'index.php?option='.$option.'&section=paarung&task=edit&cid[]='.$cid[0]);
	}

function wertpunkte()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	global $mainframe;

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));
	// keine Liga gewählt
	if ($cid[0] < 1) {
	$msg = JText::_( 'LIGEN_WERTUNG');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}
	// Ligadaten und Paarungsdaten holen
	$query	= "SELECT a.id as lid,a.sid,a.stamm,durchgang,a.rnd "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery($query);
	$liga=$db->loadObjectList();
		$lid = $liga[0]->lid;
		$sid = $liga[0]->sid;
		$stamm = $liga[0]->stamm;
	
	// Mannschaftsdaten sammeln
	$mdata = "SELECT a.sid, a.lid, a.runde, a.dg, a.paar, a.heim "
		." FROM #__clm_rnd_man as a "
		." WHERE a.lid = ".$lid
		;
	$db->setQuery( $mdata);
	$mdata		= $db->loadObjectList();
	
	foreach ($mdata as $mdata) {
		// Wertpunkte Heim berechnen
		$query	= "SELECT punkte, brett "
			." FROM #__clm_rnd_spl "
			." WHERE sid = ".$mdata->sid
			." AND lid = ".$mdata->lid
			." AND runde = ".$mdata->runde
			." AND paar = ".$mdata->paar
			." AND dg = ".$mdata->dg
			." AND heim = ".$mdata->heim
			;
		$db->setQuery($query);
		$sdata=$db->loadObjectList();
		$wpunkte=0;
		foreach ($sdata as $sdata) {
			$wpunkte = $wpunkte + (($stamm + 1 - $sdata->brett) * $sdata->punkte);
		}
		// Mannschaftstabelle updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET wertpunkte = ".$wpunkte
			." WHERE sid = ".$mdata->sid
			." AND lid = ".$mdata->lid
			." AND runde = ".$mdata->runde
			." AND paar = ".$mdata->paar
			." AND dg = ".$mdata->dg
			." AND heim = ".$mdata->heim
		;
		$db->setQuery($query);
		$db->query();
	}
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'LIGEN_AKTION_LEAGUE_WERTPUNKTE_SET' );
	$table		=& JTable::getInstance( 'ligen', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('cids' => implode( ',', $cid ), 'lid' => $table->id, 'sid' => $table->sid);
	$clmLog->write();
	

	$msg 	= JText::_( 'LIGEN_AKTION_LEAGUE_WERTPUNKTE_SET' );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg);
	
	}	
	
	function sortByTWZ() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		global $mainframe;

		$db 		=& JFactory::getDBO();
		$user 		=& JFactory::getUser();
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$option 	= JRequest::getCmd( 'option' );
		$section 	= JRequest::getVar( 'section' );
		JArrayHelper::toInteger($cid, array(0));
		$lid 		= $cid[0];
		// keine Liga gewählt
		if ($lid < 1) {
		$msg = JText::_( 'MTURN_SORT_TWZ');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}	
		if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return FALSE;
		}
	
		// tournament started, already some results?
		$query = "SELECT COUNT(id) FROM `#__clm_rnd_spl`"
			." WHERE lid =".$lid
			;
		$db->setQuery($query);
		$count = $db->loadResult();
		if ($count > 0) {
			JError::raiseWarning( 500, JText::_( 'MTURN_SORTING_NOT_POSSIBLE_EG' ) );
			$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}


		// load liga parameter
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

		$query = " SELECT stamm,ersatz,sid,teil FROM #__clm_liga "
			." WHERE id = ".$lid
			;
		$db->setQuery( $query);
		$anoboards=$db->loadObjectList();
		$noboards = ($anoboards[0]->stamm)+($anoboards[0]->ersatz);
		//$noboards	= $anoboards[0]->stamm;
		$sid	= $anoboards[0]->sid;
		$teil	= $anoboards[0]->teil;

		$query = " SELECT m.tln_nr, m.id, AVG(d.DWZ) as twz "
			." FROM #__clm_mannschaften AS m "
			." LEFT JOIN #__clm_meldeliste_spieler AS a ON a.sid = m.sid AND a.lid = m.liga AND (a.zps = m.zps OR a.zps = m.sg_zps) AND a.mnr = m.man_nr "
			." LEFT JOIN #__clm_dwz_spieler AS d ON d.sid = a.sid AND d.DWZ !=0 AND d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps "
			." WHERE m.liga = ".$lid
			." AND m.sid = ".$sid
			//." AND m.tln_nr IS NOT NULL "
			." AND a.snr < ".($noboards+1)
			." GROUP BY m.tln_nr"
			." ORDER BY twz DESC, tln_nr ASC"							
			;
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		if (!isset($teams)) {
			JError::raiseWarning( 500, JText::_( 'MTURN_SORTING_NOT_POSSIBLE_NM' ) );
			$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}
		
		$table	=& JTable::getInstance( 'mannschaften', 'TableCLM' );
		// $tlnr umsortieren
		$tlnr = 0;
		// alle Spieler durchgehen
		foreach ($teams as $value) {
			$tlnr++;
			$table->load($value->id);
			$table->tln_nr = $tlnr;
			$table->store();
		}
		
		require_once(JPATH_COMPONENT.DS.'controllers'.DS.'ergebnisse.php');
		CLMControllerErgebnisse::calculateRanking($sid,$lid);
	
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_( 'MTURN_AKTION_SORT_BY_TWZ' );
		$table		=& JTable::getInstance( 'ligen', 'TableCLM');
		$table->load($cid[0]);
		$clmLog->params = array('cids' => implode( ',', $cid ), 'lid' => $table->id, 'sid' => $table->sid);
		$clmLog->write();

		$msg 	= JText::_( 'MTURN_AKTION_SORT_BY_TWZ' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section , $msg);
	
	}
}