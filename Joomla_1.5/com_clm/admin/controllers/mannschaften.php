<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerMannschaften extends JController
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
	$section = JRequest::getVar('section');
	$db=& JFactory::getDBO();

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_lid		= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'string' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' c.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid.' AND c.archiv = 0'; }
	if ( $filter_lid ) {	$where[] = 'a.liga = '.(int) $filter_lid; }
	if ( $filter_vid ) {	$where[] = "a.zps = '$filter_vid'"; }
	if ($search) {	$where[] = 'LOWER(a.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );	}

	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}
	$count_man	= ( count( $where ) ? ' WHERE ZPS =1 AND ' . implode( ' AND ', $where ) : '' );
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY liga ASC, a.tln_nr '.$filter_order_Dir;
	} else {
	if ($filter_order =='a.name' OR $filter_order == 'a.man_nr' OR $filter_order == 'd.name' OR $filter_order == 'a.tln_nr' OR $filter_order == 'a.mf' OR $filter_order == 'a.liste' OR $filter_order == 'b.Vereinname' OR $filter_order == 'c.name' OR $filter_order == 'a.ordering' OR $filter_order == 'a.published' ) { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; }
	}
	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_mannschaften AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// Mannschaften ohne Verein zählen
	$query = ' SELECT COUNT(a.id) as id'
		.' FROM #__clm_mannschaften AS a '
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. $count_man//.' LIMIT '.$limitstart.','.$limit
		;
	$db->setQuery( $query);
	$counter_man = $db->loadResult();

	if($counter_man > 0){
	JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_ES_GIBT').' '.$counter_man.' '.JText::_('MANNSCHAFTEN_ERROR_MANNSCHAFT_VEREIN')); } 

	// get the subset (based on limits) of required records
	$query = ' SELECT a.*, c.name AS saison, b.Vereinname as verein, u.name AS editor, d.name AS liga_name'
		.' FROM #__clm_mannschaften AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		.' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
		.' LEFT JOIN #__users AS u ON u.id = a.checked_out'
		.' LEFT JOIN #__clm_dwz_vereine AS b ON a.zps = b.ZPS AND a.sid = b.sid'
		.' LEFT JOIN #__clm_vereine AS e ON e.zps = a.zps AND e.sid = a.sid'
	. $where
	. $orderby
	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// Filter
	// Statsusfilter
	$lists['state']	= JHTML::_('grid.state',  $filter_state );
	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_SAISON' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
	// Ligafilter
	$sql = 'SELECT a.id AS cid, a.name FROM #__clm_liga as a'
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE s.archiv = 0 ";
	$db->setQuery($sql);
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_LIGA' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHTML::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );

	// Vereinefilter laden
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.'filter_vereine.php');
	$vlist	= CLMFilterVerein::vereine_filter(0);
	$lists['vid']	= JHTML::_('select.genericlist', $vlist, 'filter_vid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );

	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
	// Suchefilter
	$lists['search']= $search;
	require_once(JPATH_COMPONENT.DS.'views'.DS.'mannschaft.php');
	CLMViewMannschaften::mannschaften( $rows, $lists, $pageNav, $option );
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
	JArrayHelper::toInteger($cid, array(0));
	$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );
	$sid = $row->sid;
	if ($task =="add"){
		$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
		$db->setQuery($sql);
		$sid = $db->loadResult();
	}

	// Prüfen ob User Berechtigung zum editieren hat
	$sql = " SELECT sl, params FROM #__clm_liga "
		." WHERE id =".$row->liga
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	if ($task == 'edit') {
	$saison		=& JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $sid );
	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	if ($saison->archiv == "1" AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_LIGA_ARCHIV' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}
	if ( $lid[0]->sl != CLM_ID AND CLM_usertype !== 'admin' ) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_MANNSCHAFT_STAFFEL' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// do stuff for new records
		$row->published = 0;
	}
	// Ligaliste
	$sql = " SELECT a.id as liga, a.name FROM #__clm_liga as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE  s.archiv = 0 AND a.sl = ".CLM_ID
		;
	// wenn User Admin
	if ( CLM_usertype === 'admin') {
	$sql = "SELECT a.id as liga, a.name FROM #__clm_liga as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE  s.archiv = 0 "
		;
					}
	$db->setQuery( $sql );
	$non_sl=$db->loadObjectList();
	// Falls kein SL einer Liga dann kann auch keine Mannschaft angelegt werden
	if (!isset($non_sl[0]->liga) AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_STAFFEL_MANNSCHAFT' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_LIGA') , 'liga', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['liga']	= JHTML::_('select.genericlist',   $ligalist, 'liga', 'class="inputbox" size="1"','liga', 'name', $row->liga );
	$lists['published']	= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

	// Vereinefilter laden
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.'filter_vereine.php');
	$vereinlist	= CLMFilterVerein::vereine_filter(0);
	$lists['verein']= JHTML::_('select.genericlist',   $vereinlist, 'zps', 'class="inputbox" size="1" ','zps', 'name', $row->zps );

	// Spielgemeinschaft
	$lists['sg']= JHTML::_('select.genericlist',   $vereinlist, 'sg_zps', 'class="inputbox" size="1" ','zps', 'name', $row->sg_zps );
	// MFliste
	if ($task == 'edit') { $where = " AND ( a.zps = '".$row->zps."' OR a.zps = '".$row->sg_zps."') AND a.published = 1";}
	else { $where = ' AND a.zps = 0 AND a.published = 1';}
	$tql = ' SELECT a.jid as mf, a.name as mfname'
		.' FROM #__clm_user AS a '
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE s.archiv = 0 "
		.$where;
	$db->setQuery($tql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$mflist[]		= JHTML::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_MANNSCHAFTFUEHRER' ), 'mf', 'mfname' );
	$mflist			= array_merge( $mflist, $db->loadObjectList() );
	$lists['mf']	= JHTML::_('select.genericlist',   $mflist, 'mf', 'class="inputbox" size="1"', 'mf', 'mfname', $row->mf );
	// Saisonliste
	if($task =="edit"){ $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE id='.$sid;} 
	else { $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv =0'; }
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );}
	if ($task !="edit") {
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_SAISON' ), 'sid', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
		} else { $saisonlist	= $db->loadObjectList(); }
	$lists['saison']= JHTML::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $row->sid );

	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $lid[0]->params);
	$lid_params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$lid_params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (isset($lid_params[pgntype])) $lists['pgntype'] = $lid_params[pgntype];   //pgn Parameterübernahme
	else $lists['pgntype']= 0;

	require_once(JPATH_COMPONENT.DS.'views'.DS.'mannschaft.php');
	CLMViewMannschaften::mannschaft( $row, $lists, $option );
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
	$row 		= & JTable::getInstance( 'mannschaften', 'TableCLM' );
	$pre_man	= JRequest::getInt( 'pre_man');

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	// pre-save checks
	if (!$row->check()) {
		JError::raiseError(500, $row->getError() );
	}

	$liga_dat	= & JTable::getInstance( 'ligen', 'TableCLM' );
	$liga_dat->load( $row->liga );

	// prüfen ob Mannschaftsnummer schon vergeben wurde
	$query = " SELECT COUNT(man_nr) as countman FROM #__clm_mannschaften "
		." WHERE zps = '".$row->zps."'"
		." AND man_nr = ".$row->man_nr
		." AND sid =".$row->sid
		;
	$db->setQuery($query);
	$count_mnr=$db->loadObjectList();

	$query = " SELECT id FROM #__clm_mannschaften "
		." WHERE zps = '".$row->zps."'"
		." AND man_nr = ".$row->man_nr
		." AND sid =".$row->sid
		." ORDER BY id ASC "
		." LIMIT 1 "
		;
	$db->setQuery($query);
	$count_id=$db->loadObjectList();

	if ($count_mnr[0]->countman > 0 AND ( !$row->id OR $count_id[0]->id != $row->id)) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_MANNSCHAFT_IST') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link );
		}

	$aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_EDIT');
	if (!$row->id) {
	$aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_CREATE');
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );
	}
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
	}

	// Wenn Meldelistenmodus dann bei Änderung der Mannschaftsnummer Meldeliste updaten
	if ($liga_dat->rang == 0 AND $pre_man != $row->man_nr) {
		$query = " UPDATE #__clm_meldeliste_spieler "
			." SET  mnr = ".$row->man_nr
			." WHERE sid = ".$row->sid
			." AND lid = ".$row->liga
			." AND mnr = ".$pre_man
			." AND zps = '".$row->zps."'"
			;
		$db->setQuery($query);
		$db->query();
	}
	$row->checkin();

	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'MANNSCHAFTEN_AENDERUNGEN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT');
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'zps' => $row->zps);
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
	$row 		=& JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->checkin( $id);

	$msg = JText::_( 'MANNSCHAFTEN_AKTION');
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

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'MANNSCHAFTEN_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
	// load the row from the db table 
	$row->load( $cid[0] );

	// Prüfen ob User Berechtigung zum editieren hat
	$sql = " SELECT sl FROM #__clm_liga "
		." WHERE id =".$row->liga
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	// Zählen ob in den zugehörigen Ligen schn Ergebnisse gemeldet wurden
	$ligen = array();
	$vorher = 0;

	foreach($cid as $id) {
		$row->load( $id );
		if($vorher != $row->liga) {
			$ligen[]=$row->liga;
			$vorher=$row->liga;
		}}
	$counter = implode( ',', $ligen );

	$query = " SELECT COUNT(id) as count FROM #__clm_rnd_man "
		.' WHERE lid IN ( '. $counter .' )'
		." AND sid =".$row->sid
		.' AND gemeldet > 0';
	$db->setQuery($query);
	$liga_count = $db->loadObjectList();

	if ( $liga_count[0]->count > 0 ) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_NO_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	if ( $lid[0]->sl != CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	else {
		if ( CLM_usertype === 'admin') {
		$cids = implode( ',', $cid );
		foreach($cid as $cid) {
			$row->load( $cid );
			$query = " DELETE FROM #__clm_meldeliste_spieler "
				.' WHERE mnr ='.$row->man_nr
				.' AND lid ='.$row->liga
				." AND sid =".$row->sid
				;
			$db->setQuery($query);
			$db->query();
			}
		$query = " DELETE FROM #__clm_mannschaften "
		. ' WHERE id IN ( '. $cids .' )';

		$db->setQuery( $query );
		if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";}

		if (count($cid) == 1) { $msg = JText::_( 'MANNSCHAFT_MSG_DEL_ENTRY' ); }
		else { $msg = count($cid).JText::_( 'MANNSCHAFT_MSG_DEL_ENTRYS' ); }
			}
		else {
			$row->load( $cid[0] );
			$del++;
			$query = " DELETE FROM #__clm_meldeliste_spieler "
				.' WHERE mnr ='.$row->man_nr
				.' AND lid ='.$row->liga
				." AND sid =".$row->sid
				;
			$db->setQuery($query);
			$db->query();

		$query = " DELETE FROM #__clm_mannschaften WHERE id = ".$cid[0];
		$msg = JText::_( 'MANNSCHAFT_MSG_DEL_ENTRY' );
			}
		}
		$db->setQuery( $query );
		if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n"; }

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_DELETE');
	$clmLog->params = array('cids' => $cids, 'zps' => $row->zps);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
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

	if (empty( $cid )) {
		JError::raiseWarning( 500, 'No items selected' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// Prüfen ob User Berechtigung zum publizieren hat
	$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0] );

	$sql = " SELECT sl FROM #__clm_liga "
		." WHERE id =".$row->liga
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	if ( $lid[0]->sl != CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_PUB' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	else {
		if ( CLM_usertype === 'admin' OR CLM_usertype === 'dv' ) {
		$cids = implode( ',', $cid );
		$query = ' UPDATE #__clm_mannschaften'
			.' SET published = '.(int) $publish
			.' WHERE id IN ( '. $cids .' )'
			.' AND ZPS !="0" '
			.' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
			}
		else {
		$query = 'UPDATE #__clm_mannschaften'
			. ' SET published = '.(int) $publish
			. ' WHERE id = '.$cid[0]
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
			}
		}
		$db->setQuery( $query );
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() );
			}
	if (count( $cid ) == 1) {
		$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
		$row->checkin( $cid[0] );
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_TEAM')." ".$task;
	$table		=& JTable::getInstance( 'mannschaften', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => $cids);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	CLMControllerMannschaften::order( 1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	CLMControllerMannschaften::order( -1 );
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

	$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0]);
	$row->move( $inc, 'liga = '.(int) $row->liga.' AND published != 0' );

	$msg 	= JText::_( 'MANNSCHAFT_MSG_SORT');
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

	$db			=& JFactory::getDBO();
	$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	$total		= count( $cid );
	$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
	JArrayHelper::toInteger($order, array(0));

	$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->liga;

		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				JError::raiseError(500, $db->getErrorMsg() );
			}
		}
	}
	// execute update Order for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
		$row->reorder('liga = '.(int) $group);
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
	$table		= & JTable::getInstance('mannschaften', 'TableCLM');
	$user		= &JFactory::getUser();
	$n		= count( $cid );
	$this->setRedirect( 'index.php?option='.$option.'&section='.$section );

	// Prüfen ob User Berechtigung zum publizieren hat
	$row = & JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0] );

	$sql = " SELECT sl FROM #__clm_liga "
		." WHERE id =".$row->liga
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	if ( $lid[0]->sl != CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_KOPIE' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	else {

	$query = ' SELECT man_nr FROM #__clm_mannschaften '
		.' WHERE sid ='.$row->sid
		.' ORDER BY man_nr DESC LIMIT 1'
		;
	$db->setQuery( $query );
	$high_mnr = $db->loadResult();

	$query = ' SELECT tln_nr FROM #__clm_mannschaften '
		.' WHERE sid ='.$row->sid
		.' ORDER BY tln_nr DESC LIMIT 1'
		;
	$db->setQuery( $query );
	$high_tlnr = $db->loadResult();

	$p=1;
	if ($n > 0)
	{
		foreach ($cid as $id)
		{
			if ($table->load( (int)$id ))
			{
			$table->id			= 0;
			$table->name			= 'Kopie von ' . $table->name;
			$table->published		= 0;
			$table->man_nr			= $high_mnr + $p;
			$table->tln_nr			= $high_tlnr + $p;
			$table->liste			= 0;
			$table->mf			= 0;
		$p++;
			if (!$table->store()) {	return JError::raiseWarning( $table->getError() );}
			}
		else {	return JError::raiseWarning( 500, $table->getError() );	}
		}
	}
	else {	return JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ITEMS' ) );}

	if ($n >1) { $msg=JText::_( 'MANNSCHAFT_MSG_COPY_ENTRYS');}
		else {$msg=JText::_( 'MANNSCHAFT_MSG_COPY_ENTRY');}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_COPY');
	$table =& JTable::getInstance( 'mannschaften', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$this->setMessage( JText::_( $n.$msg ) );
		}
	}

function meldeliste()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	global $mainframe;

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));
	// keine Meldeliste gewählt
	if ($cid[0] < 1) {
	$msg = JText::_( 'MANNSCHAFTEN_MELDELISTE');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
	// load the row from the db table
	$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
		$row->load( $cid[0] );

	// Konfigurationsparameter auslesen 
	$config = &JComponentHelper::getParams( 'com_clm' );
	$rang	= $config->get('rangliste',0);

	// load the row from the db table
	$rowliga	= & JTable::getInstance( 'ligen', 'TableCLM' );
	$liga		= $row->liga;
		$rowliga->load( $liga );

	$link = 'index.php?option='.$option.'&section='.$section;

	// Prüfen ob User Berechtigung zum publizieren hat
	if ( $rang == 0 AND $rowliga->sl != CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MELDELISTE_BEARBEITEN' ) );
		$mainframe->redirect( $link);
					}

	if ($row->zps == "0") {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_SPIELFREI' ) );
		$row->checkin();
		$rowliga->checkin();
		$mainframe->redirect( $link);
		}
	if ($row->zps == "1") {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_VEREIN' ) );
		$row->checkin();
		$rowliga->checkin();
		$mainframe->redirect( $link);
		}
/*
	if ( $row->liste >0 AND $rowliga->rang > 0) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_KONFIG_PROBLEM' ) );
		JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_MASSNAHMEN' ));
		$msg = JText::_( 'MANNSCHAFTEN_RANGLISTEN_BEARBEITEN' );
		$row->checkin();
		$rowliga->checkin();
		$mainframe->redirect( $link, $msg);
		}
*/
	if ( $rowliga->rang > 0) {
		JError::raiseWarning( 500, JText::_('MANNSCHAFTEN_NO_MELDELISTE' ));
 		JError::raiseNotice( 6000,  JText::_('MANNSCHAFTEN_MANNSCHAFT_RANG' ) );
		$msg = JText::_( 'MANNSCHAFTEN_RANG_VEREIN' );
		$row->checkin();
		$rowliga->checkin();
		$mainframe->redirect( $link, $msg);
		}

/**
	if ( $rowliga->rang == 1 AND $rang == 2) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_KONFIG_RANG' ) );
		JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_MASSNAHMEN' ));
		$msg = JText::_( 'MANNSCHAFTEN_RANGLISTEN_BEARBEITEN' );
		$row->checkin();
		$rowliga->checkin();
		$mainframe->redirect( $link, $msg);
		}
	if ( $rowliga->rang == 0 AND $rang == 1) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_KONFIG_RANG_AK' ) );
		JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_MASSNAHMEN' ));
		$msg = JText::_( 'MANNSCHAFTEN_RANG_VEREIN' );
		$row->checkin();
		$rowliga->checkin();
		$mainframe->redirect( $link);
		}
	if ( $rowliga->rang == 1) {
	$zps = $row->zps;
	$mainframe->redirect( 'index.php?option='.$option.'&section=rangliste&task=edit&cid[]='.$zps);
		}
**/
	$rowliga->checkin();
	$row->checkout( $user->get('id') );
	// Link MUSS hardcodiert sein !!!
	$mainframe->redirect( 'index.php?option='.$option.'&section=meldelisten&task=edit&cid[]='.$cid[0]);
	}

function delete_meldeliste()
	{
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	global $mainframe;

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= & JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$cid		= JRequest::getVar( 'cid');

	// Prüfen ob User Berechtigung zum löschen hat
	if ( CLM_usertype != 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_NO_MELDE_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
		}
	if (count($cid) < 1) {
	JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_LISTE_LOSCH') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}
	
	// load the row from the db table
	$row 		= & JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0]);
	$rowliga	= & JTable::getInstance( 'ligen', 'TableCLM' );
	$liga		= $row->liga;
	$rowliga->load( $liga );

	$link = 'index.php?option='.$option.'&section='.$section;

	// Wenn Rangliste dann nicht löschen
	if ( $rowliga->rang > 0) {
		JError::raiseWarning( 500, JText::_('MANNSCHAFTEN_NO_LOESCH' ));
 		JError::raiseNotice( 6000,  JText::_('MANNSCHAFTEN_MANNSCHAFT_RANG' ) );
		$msg = JText::_( 'MANNSCHAFTEN_RANG_VEREIN' );
		$row->checkin();
		$rowliga->checkin();
		$mainframe->redirect( $link, $msg);
		}

	// Prüfen ob User Berechtigung zum publizieren hat
	if ( $rowliga->sl != CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MELDE_LOESCH' ) );
		$mainframe->redirect( $link);
					}
		$zps	=$row->zps;
		$sg_zps	=$row->sg_zps;
		$man_nr	=$row->man_nr;
		$sid	=$row->sid;
		$lid	=$row->liga;

	$query	= "DELETE FROM #__clm_meldeliste_spieler"
		." WHERE ( zps = '$zps' OR zps='$sg_zps')"
		." AND  mnr = ".$man_nr
		." AND sid = ".$sid 
		." AND lid = ".$lid
		." AND status = 0 " 
		;
	$db->setQuery($query);
	$db->query();

	$date 		=& JFactory::getDate();
	$now 		= $date->toMySQL();

	$query	= "UPDATE #__clm_mannschaften"
		." SET edit_liste = ".CLM_ID
		." , edit_datum = '$now'"
		." , liste = 0"
		." WHERE sid = ".$sid
		." AND man_nr = ".$man_nr
		." AND zps = '$zps'"
			;
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_LIST_DELETE');
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'zps' => $zps, 'man' => $man_nr, 'cids' => $cid[0]);
	$clmLog->write();
	
	$msg = JText::_( 'MANNSCHAFTEN_MELDE_GELOESCHT');
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}

function save_meldeliste()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user		= & JFactory::getUser();
	$meldung	= $user->get('id');

	$db 		= & JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		= & JTable::getInstance( 'mannschaften', 'TableCLM' );
	$cid		= JRequest::getVar( 'id');
	$row->load( $cid);

	$stamm 		= JRequest::getVar( 'stamm');
	$ersatz		= JRequest::getVar( 'ersatz');
	$zps 		= JRequest::getVar( 'zps');
	$mnr 		= JRequest::getVar( 'mnr');
	$sid 		= JRequest::getVar( 'sid');
	$max 		= JRequest::getVar( 'max');
	$editor 	= JRequest::getVar( 'editor');
	$liga 		= $row->liga;
	$sg_zps		= $row->sg_zps;

	// Datum und Uhrzeit für Meldung
	$date =& JFactory::getDate();
	$now = $date->toMySQL();
	// Liste wurde bereits abgegeben
	if ($row->liste > 0) {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_EDIT');
		$query	= "UPDATE #__clm_mannschaften"
			." SET edit_liste = ".$meldung
			." , edit_datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	// Liste wurde noch nicht abgegeben
	else {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_CREATE');
		$query	= "UPDATE #__clm_mannschaften"
			." SET liste = ".$meldung
			." , datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	$db->setQuery($query);
	$db->query();

	$query	= "DELETE FROM #__clm_meldeliste_spieler"
		. " WHERE lid = $liga"
		. " AND mnr = ".$mnr
		. " AND sid = ".$sid
		."  AND ( zps = '$zps' OR zps='$sg_zps')"
		;

	$db->setQuery($query);
	$db->query();

	for ($y=1; $y< 1+($stamm+$ersatz); $y++){
	$spl	= JRequest::getVar( 'spieler'.$y);
	$block	= JRequest::getInt( 'check'.$y);

	$teil	= explode("-", $spl);
	$mgl_nr	= $teil[0];
	$tzps	= $teil[1];

	if($spl >0){
	$query	= "REPLACE INTO #__clm_meldeliste_spieler"
		." ( `sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`, `ordering`, `gesperrt`) "
		. " VALUES ('$sid','$liga','$mnr','$y','$mgl_nr','$tzps','','$block')";
	$db->setQuery($query);
	$db->query();
	}
	}

	$msg = $editor;
	switch ($task)
	{
		case 'apply':
		$msg = JText::_( 'MANNSCHAFTEN_AENDERUNGN').$tzps;
	// Link MUSS hardcodiert sein !!!
		$link = 'index.php?option='.$option.'&section=meldelisten&task=edit&cid[]='. $cid ;
		break;

		case 'save':
		default:
			$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT_GESPEICHERT' );
			$link = 'index.php?option='.$option.'&section='.$section;
		$row->checkin();
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $sid, 'lid' => $liga, 'zps' => $zps, 'cids' => $cid);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg );
	}

function apply_meldeliste()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= & JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$user		= & JFactory::getUser();
	$meldung	= $user->get('id');
	$row 		= & JTable::getInstance( 'mannschaften', 'TableCLM' );
	$cid		= JRequest::getVar( 'id');
	$row->load( $cid);

	$stamm 		= JRequest::getVar( 'stamm');
	$ersatz		= JRequest::getVar( 'ersatz');
	$zps 		= JRequest::getVar( 'zps');
	$mnr 		= JRequest::getVar( 'mnr');
	$sid 		= JRequest::getVar( 'sid');
	$max 		= JRequest::getVar( 'max');
	$editor 	= JRequest::getVar( 'editor');
	$liga 		= $row->liga;
	$sg_zps		= $row->sg_zps;

	// Datum und Uhrzeit für Meldung
	$date =& JFactory::getDate();
	$now = $date->toMySQL();
	// Liste wurde bereits abgegeben
	if ($row->liste > 0) {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_EDIT');
		$query	= "UPDATE #__clm_mannschaften"
			." SET edit_liste = ".$meldung
			." , edit_datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	// Liste wurde noch nicht abgegeben
	else {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_CREATE');
		$query	= "UPDATE #__clm_mannschaften"
			." SET liste = ".$meldung
			." , datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	$db->setQuery($query);
	$db->query();

	$query	= "DELETE FROM #__clm_meldeliste_spieler"
		. " WHERE lid = $liga"
		. " AND mnr = ".$mnr
		. " AND sid = ".$sid
		."  AND ( zps = '$zps' OR zps='$sg_zps')"
		;
	$db->setQuery($query);
	$db->query();

	for ($y=1; $y< 1+($stamm+$ersatz); $y++){
	$spl	= JRequest::getVar( 'spieler'.$y);
	$block	= JRequest::getInt( 'check'.$y);

	$teil	= explode("-", $spl);
	$mgl_nr	= $teil[0];
	$tzps	= $teil[1];

	if($spl >0){
	$query	= "REPLACE INTO #__clm_meldeliste_spieler"
		." ( `sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`, `ordering`, `gesperrt`) "
		. " VALUES ('$sid','$liga','$mnr','$y','$mgl_nr','$tzps','','$block')";
		;
	$db->setQuery($query);
	$db->query();
	}}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $sid, 'lid' => $liga, 'zps' => $zps, 'cids' => $cid);
	$clmLog->write();
	
	$msg = JText::_( 'MANNSCHAFTEN_AENDERUNGN' );
	// Link MUSS hardcodiert sein !!!
	$link = 'index.php?option=com_clm&section=meldelisten&task=edit&cid[]='. $cid ;
	$mainframe->redirect( $link, $msg );
	}

function spielfrei()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	global $mainframe;

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));
	// keine Meldeliste gewählt //
	if ($cid[0] < 1) {
	$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT_AUS');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
	// load the row from the db table
	$row =& JTable::getInstance( 'mannschaften', 'TableCLM' );
		$row->load( $cid[0] );
	$tlnr = $row->tln_nr;


	// load the row from the db table
	$rowliga	= & JTable::getInstance( 'ligen', 'TableCLM' );
	$liga		= $row->liga;
		$rowliga->load( $liga );

	$link = 'index.php?option='.$option.'&section='.$section;

	// Prüfen ob User Berechtigung zum publizieren hat
	if ( $rowliga->sl != CLM_ID AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_SPIELFREI' ) );
		$mainframe->redirect( $link);
					}

	$query	= "UPDATE #__clm_rnd_man"
		." SET gemeldet = 1 "
		." WHERE sid = ".$row->sid
		." AND lid = ".$row->liga
		." AND ( tln_nr = $tlnr OR gegner = $tlnr) "
		;
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_NO_GAMES');
	$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'man' => $tlnr, 'cids' => $cid[0]);
	$clmLog->write();
	
	$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT_SPIELF' );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section, $msg);
	}
}